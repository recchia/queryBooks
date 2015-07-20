<?php

use \PHPExcel;
use \PHPExcel_IOFactory;
use Adapter\GoogleBooksApiAdapter;
use Form\Type\FindType;
use Form\Type\UploadType;
use Form\Type\DownloadType;
use Model\Util;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Exception\BookNotFoundException;
use Doctrine\DBAL;
use Model\DBConnection;
use Model\Book;

//Request::setTrustedProxies(array('127.0.0.1'));

$app->get('/', function () use ($app) {

    $database = new DBConnection($app);
    $apiArray = $database->findAllApis();
    $docArray = $database->findAllDocuments();

    $form = $app['form.factory']->createBuilder(new FindType($apiArray))->getForm()->createView();
    $upload = $app['form.factory']->createBuilder(new UploadType())->getForm()->createView();
    $download = $app['form.factory']->createBuilder(new DownloadType($docArray))->getForm()->createView();

    return $app['twig']->render('books/index.html.twig', ['form' => $form, 'upload' => $upload, 'download' => $download]);
})
->bind('homepage')
;

$app->post('/download', function (Request $request) use ($app) {

    try
    {
        ini_set('max_execution_time', 100000);
        $database = new DBConnection($app);
        $documentsArray = $database->findAllDocuments();

        $download = $app['form.factory']->createBuilder(new DownloadType($documentsArray))->getForm();
        $download ->handleRequest($request);
        if ($download->isValid()){
            $data = $download->getData();

            $filename = $documentsArray[$data['files']];

            $books = $database->getBooksFromFilename($filename);

            $phpExcel = new PHPExcel();
            $phpExcel->getProperties()->setCreator('Linio Books')
                ->setLastModifiedBy('Linio')
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
                $phpExcel->getActiveSheet()->setCellValue('A' . $i, $book->getIsbn10());
                $phpExcel->getActiveSheet()->setCellValue('B' . $i, $book->getIsbn13());
                $phpExcel->getActiveSheet()->setCellValue('C' . $i, $book->getTitle());
                $phpExcel->getActiveSheet()->setCellValue('D' . $i, $book->getAuthors());
                $phpExcel->getActiveSheet()->setCellValue('E' . $i, $book->getPublisher());
                $phpExcel->getActiveSheet()->setCellValue('F' . $i, $book->getDescription());
                $phpExcel->getActiveSheet()->setCellValue('G' . $i, $book->getPageCount());
                $phpExcel->getActiveSheet()->setCellValue('H' . $i, $book->getImageLink());
                $i++;
            }
            $phpExcel->setActiveSheetIndex(0);

            $writer = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel2007');
            $file = ROOT . 'web/upload/' . $filename;
            $writer->save($file);
            return new JsonResponse('http://books.linio/upload/' . $filename);
        } else {
            return new JsonResponse(
                json_encode(['response' => 'File is invalid!', 'errors' => Util::getFormErrorMessages($download)])
            );
        }
    }
    catch (Exception $e)
    {
        return new JsonResponse($e->getMessage());
    }
})->bind('download')
;

$app->post('/find', function (Request $request) use ($app) {
    try {
        $database = new DBConnection($app);
        $apiArray = $database->findAllApis();

        $form = $app['form.factory']->createBuilder(new FindType($apiArray))->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();

            if($data['api'] == 0) {

                $key = $database->findApiKey('Google Books Api');

                $api = new GoogleBooksApiAdapter(['api_key' => $key]);
                $book = $api->findOne($data['isbn']);
                if ($request->isXmlHttpRequest()) {

                    if (is_null($book->getPageCount())) {
                        $book->setPageCount("N/A");
                    }

                    $formattedResponse = "<p>ISBN 10: " . $book->getIsbn10() . "<br />
                ISBN 13: " . $book->getIsbn13() . "</p>
                <p>T&iacute;tulo: <strong>" . $book->getTitle() . "</strong></p>
                <p>Autor: " . $book->getAuthors() . "</p>
                <p>Publicado por: " . $book->getPublisher() . "</p>
                <p>Descripci&oacute;n: " . $book->getDescription() . "</p>
                <p>N&uacute;mero de p&aacute;ginas: " . $book->getPageCount() . "</p>
                <p><a href='" . $book->getImageLink() . "'>Ver Im&aacute;gen</a></p>";

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
        ini_set('max_execution_time', 100000);
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

            $database = new DBConnection($app);
            $isbnsNotFound = [];
            $booksLinio = $database->findBookArray($isbns,$isbnsNotFound);

            if(!is_null($isbnsNotFound)) {
                $key = $database->findApiKey('Google Books Api');
                $api = new GoogleBooksApiAdapter(['api_key' => $key]);
                $api->find($isbnsNotFound, $app);
            }

            $filename= trim($filename,".xlsx") . time() . ".xlsx";
            $database->saveFile($filename,$isbns);

            return new JsonResponse('<strong>Documento guardado como: '. $filename .', lo puede conseguir
            en la parte de Descarga de Archivos</strong>');
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
