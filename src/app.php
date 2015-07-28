<?php

use Silex\Application;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\HttpFragmentServiceProvider;
use Silex\Provider\RoutingServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Model\Constants;

$app = new Application();
$app->register(new RoutingServiceProvider());
$app->register(new ValidatorServiceProvider());
$app->register(new ServiceControllerServiceProvider());
$app->register(new TwigServiceProvider());
$app->register(new HttpFragmentServiceProvider());
$app->register(new FormServiceProvider());
$app->register(new TranslationServiceProvider(), ['locale' => 'es']);
$app['twig'] = $app->extend('twig', function ($twig, $app) {
    // add custom globals, filters, tags, ...

    $twig->addFunction(new \Twig_SimpleFunction('asset', function ($asset) use ($app) {
        return $app['request_stack']->getMasterRequest()->getBasepath() . '/' . ltrim($asset, '/');
    }));

    return $twig;
});

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'dbs.options'  => array(
        Constants::DATABASE_MYSQL => array(
            'driver'    =>  Constants::DATABASE_DRIVER,
            'host'      =>  Constants::DATABASE_HOST,
            'dbname'    =>  Constants::DATABASE_NAME,
            'user'      =>  Constants::DATABASE_USER,
            'password'  =>  Constants::DATABASE_PASSWORD,
            'charset'   =>  Constants::DATABASE_CHARSET,
        ),
    ),
));

define('ROOT', __DIR__ . '/../');

return $app;
