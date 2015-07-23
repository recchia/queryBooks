<?php


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
use Model\ExcelWorker;
use Model\Constants;

//Request::setTrustedProxies(array('127.0.0.1'));

/**
 * Find book function
 */
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

/**
 * Find Book Function
 */
$app->post('/find', function (Request $request) use ($app) {
    try {
        $database = new DBConnection($app);
        $apiArray = $database->findAllApis();
        $form = $app['form.factory']->createBuilder(new FindType($apiArray))->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();

            $apiInfo = $database->findApiInfoFromName($apiArray[$data['api']]);
            $apiClass = new  $apiInfo[Constants::API_CLASSNAME](['api_key' => $apiInfo[Constants::API_KEY]]);
            $book = $apiClass->findOne($data['isbn']);
            if ($request->isXmlHttpRequest()) {

                return new JsonResponse( Util::getFindOneBookReturnMessage($book));
            } else {

                return $app['twig']->render('books/show.html.twig', ['books' => $book]);
            }
        }
    }
    catch (BookNotFoundException $e)
    {
       return new JsonResponse(Constants::BOOKNOTFOUNDEXCEPTION_MSG);
    }
})->bind('find')
;

/**
 * Upload Excel Function
 */
$app->post('/uploader', function (Request $request) use ($app) {
    try {
        ini_set('max_execution_time', 100000);
        $upload = $app['form.factory']->createBuilder(new UploadType())->getForm();
        $upload->handleRequest($request);

        if ($upload->isValid()) {
            $file = $request->files->get($upload->getName());
            $path = ROOT . 'web/upload/';
            $filename = $file['file']->getClientOriginalName();
            $isbns = ExcelWorker::getISBNSFromExcelDocument($file, $path, $filename);

            $database = new DBConnection($app);
            $isbnsNotFound = $database->findISBN13NotInDatabase($isbns);
            $apiArray = $database->findAllApis();
            $count = 0;
            foreach($apiArray as $api)
            {
                if (!is_null($isbnsNotFound))
                {
                    $apiInfo = $database->findApiInfoFromName($api);
                    $apiClass= new $apiInfo[Constants::API_CLASSNAME](['api_key' => $apiInfo[Constants::API_KEY]]);
                    $apiClass->find($isbnsNotFound, $app);
                }
                $count++;
            }

            $filename= trim($filename,".xlsx") . time() . ".xlsx";
            $database->saveFile($filename,$isbns);

            return new JsonResponse(Util::getUploaderReturnMessage($filename));
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

/**
 * Download Excel Function
 */
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

            $books = $database->findBooksFromFilename($filename);
            ExcelWorker::createExcelDocument($books, $filename);

            return new JsonResponse(Constants::EXCEL_DOWNLOAD_LOCATION . $filename);
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

$app->get('/refreshCombo', function(Request $request) use ($app) {

    try
    {
        $database = new DBConnection($app);
        $docArray = $database->findAllDocuments();

        return new JsonResponse($docArray);
    }
    catch (Exception $e)
    {
        return new JsonResponse($e->getMessage());
    }

})->bind('refreshCombo');

/**
 * Get errors function
 */
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
