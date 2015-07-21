<?php
/**
 * Created by PhpStorm.
 * User: Santiago
 * Date: 7/14/2015
 * Time: 12:43 PM
 */

namespace Model;

use Silex\Application;
use Symfony\Component\Intl\Exception\NotImplementedException;

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
        $sql = "SELECT COUNT(*) FROM linio_books WHERE LB_isbnThirteen = ?";
        $count = $this->app['dbs']['mysql']->fetchColumn($sql, array($isbn), 0);

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
        $sql = "SELECT COUNT(*) FROM linio_books WHERE LB_isbnTen = ?";
        $count = $this->app['dbs']['mysql']->fetchColumn($sql, array($isbn), 0);
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
    public function authorExistsByName($author)
    {
        $sql = "SELECT COUNT(*) FROM author WHERE auth_name = ?";
        $count = $this->app['dbs']['mysql']->fetchColumn($sql, array($author), 0);
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
    public function findApiKeyByName($apiName)
    {
        $sql = "SELECT ba_key FROM books_api WHERE ba_name= ?";
        $key = $this->app['dbs']['mysql']->fetchColumn($sql, array($apiName), 0);
        return $key;
    }

    /**
     * Finds book data from database by its isbn13
     *
     * @param $isbn
     *
     * @return Book
     */
    public function findBookDataByISBN13($isbn)
    {
        $sql = "SELECT * FROM linio_books, author, lb_author WHERE lb_isbnThirteen = ? AND lb_id_fk = lb_id
            AND auth_id = auth_id_fk";
        $bookDB = $this->app['dbs']['mysql']->fetchAssoc($sql, array($isbn));
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
    public function findBookDataByISBN10($isbn)
    {
        $sql = "SELECT * FROM linio_books, author, lb_author WHERE lb_isbnTen = ? AND lb_id_fk = lb_id
             AND auth_id = auth_id_fk";
        $bookDB = $this->app['dbs']['mysql']->fetchAssoc($sql, array($isbn));
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
    public function findBookArrayByISBN13(array $isbns, array &$isbnsNotFound)
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
                $book = $this->findBookDataByISBN13($isbn);
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
    public function findBookIdByISBN13($book)
    {
        $sql = "SELECT LB_id FROM linio_books WHERE LB_isbnThirteen = ?";
        $id = $this->app['dbs']['mysql']->fetchColumn($sql, array($book->getIsbn13()));
        return $id;
    }

    /**
     * Adds multiple authors to the database
     *
     * @param string $authors
     *
     * @return array
     */

    public function insertMultipleAuthors($authors)
    {
        $authorsIds = [];
        foreach($authors as $author)
        {
            if (!$this->authorExistsByName($author))
            {
                $sql = "author";
                $authorData = $this->buildInsertAuthorArray($author);
                $this->app['dbs']['mysql']->insert($sql, $authorData);
            }
            $sql = "SELECT auth_id FROM author where auth_name = ?";
            $id = $this->app['dbs']['mysql']->fetchColumn($sql, array($author));
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
    public function insertSingleAuthor($author)
    {
        if (!$this->authorExistsByName($author)) {
            $sql = "author";
            $authorData = $this->buildInsertAuthorArray($author);
            $this->app['dbs']['mysql']->insert($sql, $authorData);
        }
        $sql = "SELECT auth_id FROM author where auth_name = ?";
        $id = $this->app['dbs']['mysql']->fetchColumn($sql, array($author));
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
        $insertData['auth_name'] = trim($author,"'");
        return $insertData;
    }

    /**
     * Adds a new book to the database
     *
     * @param Book $book
     */
    public function insertNewBook($book)
    {
        if (!$this->bookExistsByISBN13($book->getIsbn13())) {
            $sql = 'linio_books';
            $bookData = $book->getInsertArray();
            $this->app['dbs']['mysql']->insert($sql, $bookData);

            $bookId = $this->findBookIdByISBN13($book);
            $authorsIds = $this->insertSingleAuthor($book->getAuthors());

            $insertData = [];
            $sql = "lb_author";
            $insertData['lb_id_fk'] = $bookId;
            $insertData['auth_id_fk'] = $authorsIds;
            $this->app['dbs']['mysql']->insert($sql, $insertData);
        }

    }

    /**
     * Gets a book info by its id
     *
     * @param $bookId
     *
     * @return Book
     */
    public function findBookInfoById($bookId)
    {
        $sql = "select * from linio_books, author, lb_author where lb_id =? AND lb_id_fk = lb_id
             AND auth_id = auth_id_fk";
        $bookDB = $this->app['dbs']['mysql']->fetchAssoc($sql, array($bookId));
        $book = Book::buildComplete($bookDB['LB_isbnTen'], $bookDB['LB_isbnThirteen'], $bookDB['LB_title'],
            $bookDB['auth_name'], $bookDB['LB_publisher'], $bookDB['LB_description'], $bookDB['LB_pages'],
            $bookDB['LB_imageLink']);
        return $book;

    }

    /**
     * Gets books array associated with a file
     *
     * @param $filename
     *
     * @return array
     */
    public function findBooksFromFilename($filename)
    {
        if(!is_null($filename))
        {
            $sql = "select lb_isbnTen, lb_isbnThirteen, lb_title, auth_name, lb_publisher, lb_description, lb_pages,
            lb_imageLink from linio_books as lb, author as auth, lb_author as lba, documents as doc, lb_doc as lbd
            where doc.doc_name=? and doc.doc_id = lbd.doc_id_fk and lb.LB_id = lbd.lb_id_fk
            and lba.lb_id_fk = lb.LB_id and lba.auth_id_fk = auth.auth_id";
            $booksDBArray = $this->app['dbs']['mysql']->fetchAll($sql, array($filename));
            $booksArray = [];
            foreach ($booksDBArray as $bookDB)
            {
                $book = Book::buildComplete($bookDB['lb_isbnTen'], $bookDB['lb_isbnThirteen'], $bookDB['lb_title'],
                    $bookDB['auth_name'], $bookDB['lb_publisher'], $bookDB['lb_description'], $bookDB['lb_pages'],
                    $bookDB['lb_imageLink']);
                $booksArray[] = $book;
            }
            return $booksArray;
        }
        return null;
    }

    /**
     * Inserts a document into the database
     *
     * @param $filename
     *
     * @return bool
     */
    public function insertDocument($filename)
    {
        if (!is_null($filename))
        {
            $insertData = [];
            $sql = "documents";
            $insertData['doc_id'] = null;
            $insertData['doc_name'] = $filename;
            $this->app['dbs']['mysql']->insert($sql,$insertData);
            return true;
        }
        return false;
    }

    /**
     * Inserts into book document relation table
     *
     * @param $bookId
     * @param $docId
     *
     * @return bool
     */
    public function insertBookDoc($bookId, $docId)
    {
        if (!is_null($bookId) &&  !is_null($docId))
        {
            $insertData = [];
            $sql = "lb_doc";
            $insertData['lb_id_fk'] = $bookId;
            $insertData['doc_id_fk'] = $docId;
            $this->app['dbs']['mysql']->insert($sql,$insertData);
            return true;
        }
        return false;
    }

    /**
     * Gets a documents id by its filename
     *
     * @param $filename
     *
     * @return string
     */
    public function findDocumentID($filename)
    {
        $sql = "Select doc_id from documents where doc_name = ?";
        $id = $this->app['dbs']['mysql']->fetchColumn($sql, array($filename));
        return $id;
    }

    /**
     * Checks if relationship exists
     *
     * @param $bookId
     * @param $docId
     *
     * @return bool
     */
    public function relationshipBookDocExists($bookId, $docId)
    {
        $sql = "select count(*) from lb_doc where lb_id_fk= ? and doc_id_fk= ?";
        $count = $this->app['dbs']['mysql']->fetchColumn($sql, array($bookId, $docId));
        if ($count > 0)
        {
            return true;
        }
        return false;
    }

    /**
     * Saves file and its books in database
     *
     * @param $filename
     * @param $isbns
     *
     * @return bool
     */
    public function saveFile($filename, $isbns)
    {
        if(!is_null($filename && !is_null($isbns)))
        {
            if($this->insertDocument($filename))
            {
                $docId = $this->findDocumentID($filename);
                $book = new Book();
                foreach ($isbns as $isbn)
                {
                    if($this->bookExistsByISBN13($isbn)) {
                        $book->setIsbn13($isbn);
                        $bookId = $this->findBookIdByISBN13($book);
                        if (!$this->relationshipBookDocExists($bookId,$docId)) {
                            $this->insertBookDoc($bookId, $docId);
                        }
                    }
                }
                return true;
            }
        }
        return false;
    }
}