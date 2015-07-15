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

    public function bookExistsByISBN13($isbn)
    {
        $sql = "SELECT COUNT(*) FROM linio_books WHERE LB_isbnThirteen = '" .$isbn ."'";
        $count = $this->app['dbs']['mysql']->fetchColumn($sql);

        if ($count > 0)
        {
            return true;
        }
        return false;

    }

    public function bookExistsByISBN10($isbn)
    {
        $sql = "SELECT COUNT(*) FROM linio_books WHERE LB_isbnTen = '" .$isbn."'";
        $count = $this->app['dbs']['mysql']->fetchColumn($sql);
        if ($count > 0)
        {
            return true;
        }
        return false;
    }

    public function authorExists($author)
    {
        $sql = "SELECT COUNT(*) FROM author WHERE auth_name = '" . $author . "'";
        $count = $this->app['dbs']['mysql']->fetchColumn($sql);
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
            " AND auth_id = auth_id_fk";
        $bookDB = $this->app['dbs']['mysql']->fetchAssoc($sql);
        $book = [];
        $book['ISBN_10'] = $bookDB['LB_isbnTen'];
        $book['ISBN_13'] = $bookDB['LB_isbnThirteen'];
        $book['title'] = $bookDB['LB_title'];
        $book['authors'] = $bookDB['auth_name'];
        $book['publisher'] = $bookDB['LB_publisher'];
        $book['description'] = $bookDB['LB_description'];
        $book['pageCount'] = $bookDB['LB_pages'];
        $book['imageLink'] = $bookDB['LB_imageLink'];
        return $book;
    }

    public function findDataByISBN10($isbn)
    {
        $sql = "SELECT * FROM linio_books, author, lb_author WHERE lb_isbnTen = '" . $isbn . "' AND lb_id_fk = lb_id" .
            "AND auth_id = auth_id_fk";
        $bookDB = $this->app['dbs']['mysql']->fetchAssoc($sql);
        $book = [];
        $book['ISBN_10'] = $bookDB['LB_isbnTen'];
        $book['ISBN_13'] = $bookDB['LB_isbnThirteen'];
        $book['title'] = $bookDB['LB_title'];
        $book['authors'] = $bookDB['auth_name'];
        $book['publisher'] = $bookDB['LB_publisher'];
        $book['description'] = $bookDB['LB_description'];
        $book['pageCount'] = $bookDB['LB_pages'];
        $book['imageLink'] = $bookDB['LB_imageLink'];
        return $book;
    }

    public function findBookArray(array $isbns, array &$isbnsNotFound)
    {
        $books = [];

        foreach ($isbns as $isbn)
        {

            if (!$this->bookExistsByISBN13($isbn))
            {
                $isbnsNotFound[] = $isbn;
            }
            else
            {
                $book = $this->findDataByISBN13($isbn);
                $books[] = $book;
            }
        }

        return $books;
    }

    public function getBookId($book)
    {
        $sql = "SELECT LB_id FROM linio_books WHERE LB_isbnThirteen = '" .$book['ISBN_13'] ."'";
        $id = $this->app['dbs']['mysql']->fetchColumn($sql);
        return $id;
    }

    public function addMultipleAuthors($authors)
    {
        $authorsIds = [];
        foreach($authors as $author)
        {
            if (!$this->authorExists($author))
            {
                $sql = "author";
                $authorData = $this->buildInsertAuthorArray($author);
                $this->app['dbs']['mysql']->insert($sql, $authorData);
            }
            $sql = "SELECT auth_id FROM author where auth_name = '" . $author ."'";
            $id = $this->app['dbs']['mysql']->fetchColumn($sql);
            $authorsIds[] = $id;
        }
        return $authorsIds;
    }

    public function addSingleAuthor($author)
    {
        if (!$this->authorExists($author)) {
            $sql = "author";
            $authorData = $this->buildInsertAuthorArray($author);
            $this->app['dbs']['mysql']->insert($sql, $authorData);
        }
        $sql = "SELECT auth_id FROM author where auth_name = '" . $author ."'";
        $id = $this->app['dbs']['mysql']->fetchColumn($sql);
        return $id;
    }

    public function buildInsertAuthorArray($author)
    {
        $insertData = [];
        $insertData['auth_id'] = null;
        $insertData['auth_name'] = $author;
        return $insertData;
    }

    public function buildInsertBookArray($book)
    {
        $insertData = [];
        $insertData['LB_id'] = null;
        $insertData['LB_isbnTen'] = $book['ISBN_10'];
        $insertData['LB_isbnThirteen'] = $book['ISBN_13'];
        $insertData['LB_title'] = $book['title'];
        $insertData['LB_publisher'] = $book['publisher'];
        $insertData['LB_description'] = $book['description'];
        $insertData['LB_pages'] = $book['pageCount'];
        $insertData['LB_imageLink'] = $book['imageLink'];
        return $insertData;
    }

    public function addNewBook($book)
    {
        if (!$this->bookExistsByISBN13($book['ISBN_13'])) {
            $sql = 'linio_books';
            $bookData = $this->buildInsertBookArray($book);
            $this->app['dbs']['mysql']->insert($sql, $bookData);

            $bookId = $this->getBookId($book);

            if (is_array($book['authors'])) {
                $authorsIds = $this->addMultipleAuthors($book['authors']);
            } else {
                $authorsIds = $this->addSingleAuthor($book['authors']);
            }
            $insertData = [];

            if (is_array($authorsIds))
            {
                foreach ($authorsIds as $id)
                {
                    $sql = "lb_author";
                    $insertData['lb_id_fk'] = $bookId;
                    $insertData['auth_id_fk'] = $id;
                    $this->app['dbs']['mysql']->insert($sql, $insertData);
                }
            }
            else
            {
                $sql = "lb_author";
                $insertData['lb_id_fk'] = $bookId;
                $insertData['auth_id_fk'] = $authorsIds;
                $this->app['dbs']['mysql']->insert($sql, $insertData);
            }
        }

    }
}