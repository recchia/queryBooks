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

    public function bookExistsByISBN13($book)
    {
        $sql = "SELECT COUNT(*) FROM linio_books WHERE LB_isbnThirteen = ?";
        $count = $this->app['dbs']['mysql']->fetchColumn($sql, $book['ISBN_13']);
        if ($count > 0)
        {
            return true;
        }
        return false;

    }

    public function bookExistsByISBN10($book)
    {
        $sql = "SELECT COUNT(*) FROM linio_books WHERE LB_isbnTen = ?";
        $count = $this->app['dbs']['mysql']->fetchColumn($sql, $book['ISBN_10']);
        if ($count > 0)
        {
            return true;
        }
        return false;
    }

    public function authorExists($author)
    {
        $sql = "SELECT COUNT(*) FROM author WHERE auth_name = ?";
        $count = $this->app['dbs']['mysql']->fetchColumn($sql, $author);
        if ($count > 0)
        {
            return true;
        }
        return false;
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

    public function findDataByISBN13($isbn)
    {
        $sql = "SELECT * FROM linio_books, author, lb_author WHERE lb_isbnThirteen = '" . $isbn . "' AND lb_id_fk = lb_id" .
            "AND auth_id = auth_id_fk";
        $book = $this->app['dbs']['mysql']->fetchColumn($sql);
        return $book;
    }

    public function findDataByISBN10($isbn)
    {
        $sql = "SELECT * FROM linio_books, author, lb_author WHERE lb_isbnTen = '" . $isbn . "' AND lb_id_fk = lb_id" .
            "AND auth_id = auth_id_fk";
        $book = $this->app['dbs']['mysql']->fetchColumn($sql);
        return $book;
    }

    public function findBookArray(array $isbns, array &$isbnsNotFound)
    {
        $books = [];

        foreach ($isbns as $isbn)
        {
            $book = $this->findDataByISBN13($isbn);
            if (is_null($book))
            {
                $isbnsNotFound[] = $isbn;
            }
            else {
                $books[] = $book;
            }
        }

        return $books;
    }

    public function getBookId($book)
    {
        $sql = "SELECT LB_id FROM linio_books WHERE LB_isbnThirteen = ?";
        $id = $this->app['dbs']['mysql']->fetchColumn($sql, $book['ISBN_13']);
        return $id;
    }

    public function addNewBookInfo($book)
    {
        $sql = "INSERT INTO linio_books('LB_id', 'LB_isbnTen', 'LB_isbnThirteen', 'LB_title', 'LB_publisher', ".
            "'LB_description', 'LB_pages', 'LB_imageLink') VALUES (null,'" .$book['ISBN_10'] ."','".$book['ISBN_13'] ."'
            ,'".$book['title']. "','". $book['publisher'] ."','" .$book['description'] ."'
            ,".$book['pageCount'] .",'" .$book['imageLink'] ."')";

        $this->app['dbs']['mysql']->insert($sql);

        $bookId = $this->getBookId($book);

        if (is_array($book['authors']))
        {
            $authorsIds = [];
            foreach($book['authors'] as $author)
            {
                if (!$this->authorExists($author))
                {
                    $sql = "INSERT INTO AUTHOR ('auth_id', 'auth_name') VALUES (null , '".$author."')";
                    $this->app['dbs']['mysql']->insert($sql);
                    $sql = "SELECT auth_id FROM author where auth_name = ?";
                    $id = $this->app['dbs']['mysql']->fetchColumn($sql, $author);
                    $authorsIds[] = $id;
                }
            }
        }
    }
}