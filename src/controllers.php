<?php

use \PHPExcel;
use \PHPExcel_IOFactory;
use Adapter\GoogleBooksApiAdapter;
use Form\Type\FindType;
use Form\Type\UploadType;
use Model\Util;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Exception\BookNotFoundException;

//Request::setTrustedProxies(array('127.0.0.1'));

$app->get('/', function () use ($app) {
    $form = $app['form.factory']->createBuilder(new FindType())->getForm()->createView();
    $upload = $app['form.factory']->createBuilder(new UploadType())->getForm()->createView();

    return $app['twig']->render('books/index.html.twig', ['form' => $form, 'upload' => $upload]);
})
->bind('homepage')
;

$app->post('/find', function (Request $request) use ($app) {
    try {
        $form = $app['form.factory']->createBuilder(new FindType())->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();
            $api = new GoogleBooksApiAdapter(['api_key' => 'AIzaSyDfR5cB9PNeD-fn6FtEs12n5CsbFXQQgDU']);
            $book = $api->findOne($data['isbn']);
            if ($request->isXmlHttpRequest()) {

                    $formattedResponse = "<p>Titulo: <strong>" . $book['title']. "</strong></p>
                    <p>Autor: " .$book['authors']. "</p>
                    <p>Publicado por: " . $book['publisher'] . "</p>
                    <p>Descripcion: " . $book['description'] ."</p>
                     <p><a href='" . $book['imageLink'] ."'>Ver Imagen</a></p>";

                return new JsonResponse($formattedResponse);
            } else {
                return $app['twig']->render('books/show.html.twig', ['books' => $book]);
            }
        }
    }
    catch (BookNotFoundException $e)
    {
       return new JsonResponse("<strong>No se consigui&oacute; el libro buscado</strong>");
    }
})->bind('find')
;

$app->post('/uploader', function (Request $request) use ($app) {
    $upload = $app['form.factory']->createBuilder(new UploadType())->getForm();
    $upload->handleRequest($request);

    if ($upload->isValid()) {
        $file = $request->files->get($upload->getName());
        $path = ROOT . 'web/upload/';
        $filename = $file['file']->getClientOriginalName();
        $file['file']->move($path, $filename);
        $excel = PHPExcel_IOFactory::load($path . $filename);
        $sheet = $excel->getActiveSheet();
        $highestRow = $sheet->getHighestRow();
        $isbns = [];
        for ($row = 1; $row <= $highestRow; ++$row) {
            $isbns[] = $sheet->getCellByColumnAndRow(0, $row)->getValue();
        }
        $api = new GoogleBooksApiAdapter(['api_key' => 'AIzaSyDfR5cB9PNeD-fn6FtEs12n5CsbFXQQgDU']);
        $books = $api->find($isbns);
        $phpExcel = new PHPExcel();
        $phpExcel->getProperties()->setCreator('Piero Recchia')
            ->setLastModifiedBy('Piero Recchia')
            ->setTitle('Office 2007 XLSX Test Document')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
            ->setKeywords('office 2007 openxml php')
            ->setCategory('Test result file');
        $phpExcel->setActiveSheetIndex(0);
        $phpExcel->getActiveSheet()->setCellValue('A1', 'Titulo');
        $phpExcel->getActiveSheet()->setCellValue('B1', 'Autor');
        $phpExcel->getActiveSheet()->setCellValue('C1', 'Editorial');
        $phpExcel->getActiveSheet()->setCellValue('D1', 'Descripcion');
        $phpExcel->getActiveSheet()->setCellValue('E1', 'Numero de Paginas');
        $phpExcel->getActiveSheet()->setCellValue('F1', 'Imagen');

        $i = 2;
        foreach ($books as $book) {
            $phpExcel->getActiveSheet()->setCellValue('A' . $i, $book['title']);
            $phpExcel->getActiveSheet()->setCellValue('B' . $i, $book['authors']);
            $phpExcel->getActiveSheet()->setCellValue('C' . $i, $book['publisher']);
            $phpExcel->getActiveSheet()->setCellValue('D' . $i, $book['description']);
            $phpExcel->getActiveSheet()->setCellValue('E' . $i, $book['pageCount']);
            $phpExcel->getActiveSheet()->setCellValue('F' . $i, $book['imageLink']);
            $i++;
        }
        $phpExcel->setActiveSheetIndex(0);

        $writer = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel2007');
        $file = ROOT . 'web/upload/books.xlsx';
        $writer->save($file);

        /**return $app->sendFile($file, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-length' => filesize($file),
            'Cache-Control' => 'max-age=0',
            'Cache-Control' => 'max-age=1',
            'Expires' => 'Mon, 26 Jul 1997 05:00:00 GMT',
            'Last-Modified' => gmdate('D, d M Y H:i:s') . ' GMT',
            'Cache-Control' => 'cache, must-revalidate',
            'Pragma' => 'public'
        ], 'attachment');*/

        /**$writer = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel2007');
        $writer->save('php://output');
        exit;**/
        /**$file = ROOT . 'web/upload/books.xlsx';
        $writer->save($file);

        $stream = function () use ($file) {
            readfile($file);
        };

        return $app->stream($stream, 200 , [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-length' => filesize($file),
            'Content-Disposition' => 'attachment;filename=' . $file,
        ]);**/

        return new JsonResponse(json_encode(['response' => 'File was successfully uploaded!', 'isbns' => $isbns]));
    } else {
        return new JsonResponse(json_encode(['response' => 'File is invalid!', 'errors' => Util::getFormErrorMessages($upload)]));
    }
})->bind('upload')
;

$app->error(function (\Exception $e, Request $request, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    // 404.html, or 40x.html, or 4xx.html, or error.html
    $templates = [
        'errors/' . $code . '.html.twig',
        'errors/' . substr($code, 0, 2) . 'x.html.twig',
        'errors/' . substr($code, 0, 1) . 'xx.html.twig',
        'errors/default.html.twig',
    ];

    return new Response($app['twig']->resolveTemplate($templates)->render(['code' => $code]), $code);
});
