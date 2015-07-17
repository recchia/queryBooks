<?php
/**
 * Created by PhpStorm.
 * User: Santiago
 * Date: 7/14/2015
 * Time: 12:43 PM
 */

namespace Model;

use Silex\Application;

/**
 * Class DBConnection
 *
 * @package Model
 */
class DBConnection
{
    /**
     * @var Application
     */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     *
     * Validates if book exists by using its isbn13
     *
     * @param $isbn
     *
     * @return bool
     */
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

    /**
     *
     * Validates if books exists in the database by using its isbn10
     *
     * @param $isbn
     *
     * @return bool
     */
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

    /**
     * Validates if author exists in the database
     *
     * @param string $author
     *
     * @return bool
     */
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

    /**
     * Finds all Apis in the database
     *
     * @return array
     */
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

    /**
     * Finds all documents in the database
     *
     * @return array
     */
    public function findAllDocuments()
    {
        $sql = 'SELECT doc_name FROM documents';
        $documents = $this->app['dbs']['mysql']->fetchAll($sql);

        $documentsArray = [];
        $count = 0;
        while (count($documents) != $count)
        {
            $documentsArray[$count] = $documents[$count]['doc_name'];
            $count++;
        }

        return $documentsArray;

    }

    /**
     * Finds an api key in the database by using its name
     *
     * @param string $apiName
     *
     * @return string
     */
    public function findApiKey($apiName)
    {
        $sql = "SELECT ba_key FROM books_api WHERE ba_name='" .$apiName . "'";
        $key = $this->app['dbs']['mysql']->fetchColumn($sql);
        return $key;
    }

    /**
     * Finds book data from database by its isbn13
     *
     * @param $isbn
     *
     * @return Book
     */
    public function findDataByISBN13($isbn)
    {
        $sql = "SELECT * FROM linio_books, author, lb_author WHERE lb_isbnThirteen = '" . $isbn . "' AND lb_id_fk = lb_id" .
            " AND auth_id = auth_id_fk";
        $bookDB = $this->app['dbs']['mysql']->fetchAssoc($sql);
        $book = Book::buildComplete($bookDB['LB_isbnTen'], $bookDB['LB_isbnThirteen'], $bookDB['LB_title'],
            $bookDB['auth_name'], $bookDB['LB_publisher'], $bookDB['LB_description'], $bookDB['LB_pages'],
            $bookDB['LB_imageLink']);
        return $book;
    }

    /**
     * Finds a books data by its isbn10
     *
     * @param $isbn
     *
     * @return Book
     */
    public function findDataByISBN10($isbn)
    {
        $sql = "SELECT * FROM linio_books, author, lb_author WHERE lb_isbnTen = '" . $isbn . "' AND lb_id_fk = lb_id" .
            "AND auth_id = auth_id_fk";
        $bookDB = $this->app['dbs']['mysql']->fetchAssoc($sql);
        $book = Book::buildComplete($bookDB['LB_isbnTen'], $bookDB['LB_isbnThirteen'], $bookDB['LB_title'],
            $bookDB['auth_name'], $bookDB['LB_publisher'], $bookDB['LB_description'], $bookDB['LB_pages'],
            $bookDB['LB_imageLink']);
        return $book;
    }

    /**
     * Searches the database for books by using isbn13,
     * adds the isbns of the books that werent found into the isbnsNotFound variable
     *
     * @param array $isbns
     * @param array $isbnsNotFound
     *
     * @return array
     */
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

    /**
     * Gets a books id from the database
     *
     * @param Book $book
     *
     * @return integer
     */
    public function getBookId($book)
    {
        $sql = "SELECT LB_id FROM linio_books WHERE LB_isbnThirteen = '" .$book->getIsbn13() ."'";
        $id = $this->app['dbs']['mysql']->fetchColumn($sql);
        return $id;
    }

    /**
     * Adds multiple authors to the database
     *
     * @param string $authors
     *
     * @return array
     */

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

    /**
     * Adds a single author to the database
     *
     * @param string $author
     *
     * @return integer
     */
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

    /**
     * Builds an authors insert array
     *
     * @param $author
     *
     * @return array
     */
    public function buildInsertAuthorArray($author)
    {
        $insertData = [];
        $insertData['auth_id'] = null;
        $insertData['auth_name'] = $author;
        return $insertData;
    }

    /**
     * Adds a new book to the database
     *
     * @param Book $book
     */
    public function addNewBook($book)
    {
        if (!$this->bookExistsByISBN13($book->getIsbn13())) {
            $sql = 'linio_books';
            $bookData = $book->getInsertArray();
            $this->app['dbs']['mysql']->insert($sql, $bookData);

            $bookId = $this->getBookId($book);
            $authorsIds = $this->addSingleAuthor($book->getAuthors());

            $insertData = [];
            $sql = "lb_author";
            $insertData['lb_id_fk'] = $bookId;
            $insertData['auth_id_fk'] = $authorsIds;
            $this->app['dbs']['mysql']->insert($sql, $insertData);
        }

    }
}