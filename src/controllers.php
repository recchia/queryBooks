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
use Doctrine\DBAL;

//Request::setTrustedProxies(array('127.0.0.1'));

$app->get('/', function () use ($app) {

    $sql = 'SELECT ba_name FROM books_api';
    $booksApi = $app['dbs']['mysql']->fetchAll($sql);

    $apiArray = [];
    $count=0;
    while(count($booksApi) != $count)
    {
        $apiArray[$count] = $booksApi[$count]['ba_name'];
        $count++;
    }

    $form = $app['form.factory']->createBuilder(new FindType($apiArray))->getForm()->createView();
    $upload = $app['form.factory']->createBuilder(new UploadType())->getForm()->createView();

    return $app['twig']->render('books/index.html.twig', ['form' => $form, 'upload' => $upload]);
})
->bind('homepage')
;

$app->post('/find', function (Request $request) use ($app) {
    try {

        $sql = 'SELECT ba_name FROM books_api';
        $booksApi = $app['dbs']['mysql']->fetchAll($sql);

        $apiArray = [];
        $count=0;
        while(count($booksApi) != $count)
        {
            $apiArray[$count] = $booksApi[$count]['ba_name'];
            $count++;
        }

        $form = $app['form.factory']->createBuilder(new FindType($apiArray))->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();

            if($data['api'] == 0) {

                $sql = "SELECT ba_key FROM books_api WHERE ba_name='Google Books Api'";
                $key = $app['dbs']['mysql']->fetchColumn($sql);

                $api = new GoogleBooksApiAdapter(['api_key' => $key]);
                $book = $api->findOne($data['isbn']);
                if ($request->isXmlHttpRequest()) {

                    if (is_null($book['pageCount'])) {
                        $book['pageCount'] = "N/A";
                    }

                    $formattedResponse = "<p>ISBN 10: " . $book['ISBN_10'] . "<br />
                ISBN 13: " . $book['ISBN_13'] . "</p>
                <p>T&iacute;tulo: <strong>" . $book['title'] . "</strong></p>
                <p>Autor: " . $book['authors'] . "</p>
                <p>Publicado por: " . $book['publisher'] . "</p>
                <p>Descripci&oacute;n: " . $book['description'] . "</p>
                <p>N&uacute;mero de p&aacute;ginas: " . $book['pageCount'] . "</p>
                <p><a href='" . $book['imageLink'] . "'>Ver Im&aacute;gen</a></p>";

                    return new JsonResponse($formattedResponse);
                } else {
                    return $app['twig']->render('books/show.html.twig', ['books' => $book]);
                }
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
    try {
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
            $phpExcel->getActiveSheet()->setCellValue('A1', 'ISBN_10');
            $phpExcel->getActiveSheet()->setCellValue('B1', 'ISBN_13');
            $phpExcel->getActiveSheet()->setCellValue('C1', 'Titulo');
            $phpExcel->getActiveSheet()->setCellValue('D1', 'Autor');
            $phpExcel->getActiveSheet()->setCellValue('E1', 'Editorial');
            $phpExcel->getActiveSheet()->setCellValue('F1', 'Descripcion');
            $phpExcel->getActiveSheet()->setCellValue('G1', 'Numero de Paginas');
            $phpExcel->getActiveSheet()->setCellValue('H1', 'Imagen');

            $i = 2;
            foreach ($books as $book) {
                $phpExcel->getActiveSheet()->setCellValue('A' . $i, $book['ISBN_10']);
                $phpExcel->getActiveSheet()->setCellValue('B' . $i, $book['ISBN_13']);
                $phpExcel->getActiveSheet()->setCellValue('C' . $i, $book['title']);
                $phpExcel->getActiveSheet()->setCellValue('D' . $i, $book['authors']);
                $phpExcel->getActiveSheet()->setCellValue('E' . $i, $book['publisher']);
                $phpExcel->getActiveSheet()->setCellValue('F' . $i, $book['description']);
                $phpExcel->getActiveSheet()->setCellValue('G' . $i, $book['pageCount']);
                $phpExcel->getActiveSheet()->setCellValue('H' . $i, $book['imageLink']);
                $i++;
            }
            $phpExcel->setActiveSheetIndex(0);

            $writer = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel2007');
            $file = ROOT . 'web/upload/books.xlsx';
            $writer->save($file);

            $filename = 'books' . '.xlsx';
            $file = ROOT . 'web/upload/' . $filename;
            $writer->save($file);

            return new JsonResponse('http://books.linio/upload/' . $filename);
        } else {
            return new JsonResponse(
                json_encode(['response' => 'File is invalid!', 'errors' => Util::getFormErrorMessages($upload)])
            );
        }
    }
    catch (Exception $e)
    {
        return new JsonResponse($e->getMessage());
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
