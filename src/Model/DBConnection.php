<?php
/**
 * Created by PhpStorm.
 * User: Santiago
 * Date: 7/14/2015
 * Time: 12:43 PM
 */

namespace Model;

use Silex\Application;

class DBConnection
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function findAllApis()
    {
        $sql = 'SELECT ba_name FROM books_api';
        $booksApi = $this->app['dbs']['mysql']->fetchAll($sql);

        $apiArray = [];
        $count=0;
        while(count($booksApi) != $count)
        {
            $apiArray[$count] = $booksApi[$count]['ba_name'];
            $count++;
        }

        return $apiArray;
    }

    public function findApiKey($apiName)
    {
        $sql = "SELECT ba_key FROM books_api WHERE ba_name='" .$apiName . "'";
        $key = $this->app['dbs']['mysql']->fetchColumn($sql);
        return $key;
    }
}