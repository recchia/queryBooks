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
        $count = $this->app[Constants::DATABASE_SERVICE][Constants::DATABASE_MYSQL]
            ->fetchColumn(Constants::QUERY_COUNTBOOKSBYISBN13, array($isbn), 0);

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
        $count = $this->app[Constants::DATABASE_SERVICE][Constants::DATABASE_MYSQL]
            ->fetchColumn(Constants::QUERY_COUNTBOOKSBYISBN10, array($isbn), 0);
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
        $count = $this->app[Constants::DATABASE_SERVICE][Constants::DATABASE_MYSQL]
            ->fetchColumn(Constants::QUERY_COUNTAUTHORSBYNAME, array($author), 0);
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
        $booksApi = $this->app[Constants::DATABASE_SERVICE][Constants::DATABASE_MYSQL]
            ->fetchAll(Constants::QUERY_SELECTALLAPISNAME);

        $apiArray = [];
        $count=0;
        while(count($booksApi) != $count)
        {
            $apiArray[$count] = $booksApi[$count][Constants::API_NAME];
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
        $documents = $this->app[Constants::DATABASE_SERVICE][Constants::DATABASE_MYSQL]
            ->fetchAll(Constants::QUERY_SELECTALLDOCUMENTSNAME);

        $documentsArray = [];
        $count = 0;
        while (count($documents) != $count)
        {
            $documentsArray[$count] = $documents[$count][Constants::DOCUMENT_NAME];
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
        $key = $this->app[Constants::DATABASE_SERVICE][Constants::DATABASE_MYSQL]
            ->fetchColumn(Constants::QUERY_SELECTAPIKEYBYNAME, array($apiName), 0);
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
        $bookDB = $this->app[Constants::DATABASE_SERVICE][Constants::DATABASE_MYSQL]
            ->fetchAssoc(Constants::QUERY_SELECTBOOKDATABYISBN13, array($isbn));
        $book = Book::buildComplete(
            $bookDB[Constants::BOOK_ISBN10],
            $bookDB[Constants::BOOK_ISBN13],
            $bookDB[Constants::BOOK_TITLE],
            $bookDB[Constants::AUTHOR_NAME],
            $bookDB[Constants::BOOK_PUBLISHER],
            $bookDB[Constants::BOOK_DESCRIPTION],
            $bookDB[Constants::BOOK_NUMPAGES],
            $bookDB[Constants::BOOK_IMAGELINK]
        );
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
        $bookDB = $this->app[Constants::DATABASE_SERVICE][Constants::DATABASE_MYSQL]
            ->fetchAssoc(Constants::QUERY_SELECTBOOKDATABYISBN10, array($isbn));
        $book = Book::buildComplete(
            $bookDB[Constants::BOOK_ISBN10],
            $bookDB[Constants::BOOK_ISBN13],
            $bookDB[Constants::BOOK_TITLE],
            $bookDB[Constants::AUTHOR_NAME],
            $bookDB[Constants::BOOK_PUBLISHER],
            $bookDB[Constants::BOOK_DESCRIPTION],
            $bookDB[Constants::BOOK_NUMPAGES],
            $bookDB[Constants::BOOK_IMAGELINK]
        );
        return $book;
    }

    /**
     * Searches the database for books by using isbn13,
     * adds the isbns of the books that weren't found into the isbnsNotFound variable
     *
     * @param array $isbns
     * @param array $isbnsNotFound
     *
     * @return array
     */
    public function findISBN13NotInDatabase(array $isbns)
    {
        $isbnsNotFound = [];

        foreach ($isbns as $isbn)
        {
            if (!$this->bookExistsByISBN13($isbn))
            {
                $isbnsNotFound[] = $isbn;
            }
        }

        return $isbnsNotFound;
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
        $id = $this->app[Constants::DATABASE_SERVICE][Constants::DATABASE_MYSQL]
            ->fetchColumn(Constants::QUERY_SELECTBOOKIDBYISBN13, array($book->getIsbn13()));
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
            $authorsIds[] = $this->insertSingleAuthor($author);
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
            $authorData = $this->buildInsertAuthorArray($author);
            $this->app[Constants::DATABASE_SERVICE][Constants::DATABASE_MYSQL]
                ->insert(Constants::AUTHOR_TABLE, $authorData);
        }
        $id = $this->app[Constants::DATABASE_SERVICE][Constants::DATABASE_MYSQL]
            ->fetchColumn(Constants::QUERY_SELECTAUTHORIDBYNAME, array($author));
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
        $insertData[Constants::AUTHOR_ID] = null;
        $insertData[Constants::AUTHOR_NAME] = trim($author,"'");
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
            $bookData = $book->getInsertArray();
            $this->app[Constants::DATABASE_SERVICE][Constants::DATABASE_MYSQL]
                ->insert(Constants::BOOK_TABLE, $bookData);

            $bookId = $this->findBookIdByISBN13($book);
            $authorsIds = $this->insertSingleAuthor($book->getAuthors());

            $insertData = [];
            $insertData[Constants::BOOK_FK_ID] = $bookId;
            $insertData[Constants::AUTHOR_FK_ID] = $authorsIds;
            $this->app[Constants::DATABASE_SERVICE][Constants::DATABASE_MYSQL]
                ->insert(Constants::BOOK_AUTHOR_RELATION_TABLE, $insertData);
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
        $bookDB = $this->app[Constants::DATABASE_SERVICE][Constants::DATABASE_MYSQL]
            ->fetchAssoc(Constants::QUERY_SELECTBOOKINFOBYID, array($bookId));
        $book = Book::buildComplete(
            $bookDB[Constants::BOOK_ISBN10],
            $bookDB[Constants::BOOK_ISBN13],
            $bookDB[Constants::BOOK_TITLE],
            $bookDB[Constants::AUTHOR_NAME],
            $bookDB[Constants::BOOK_PUBLISHER],
            $bookDB[Constants::BOOK_DESCRIPTION],
            $bookDB[Constants::BOOK_NUMPAGES],
            $bookDB[Constants::BOOK_IMAGELINK]
        );
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
            $booksDBArray = $this->app[Constants::DATABASE_SERVICE][Constants::DATABASE_MYSQL]
                ->fetchAll(Constants::QUERY_SELECTBOOKINFOBYDOCNAME, array($filename));
            $booksArray = [];
            foreach ($booksDBArray as $bookDB)
            {
                $book = Book::buildComplete(
                    $bookDB[Constants::BOOK_ISBN10],
                    $bookDB[Constants::BOOK_ISBN13],
                    $bookDB[Constants::BOOK_TITLE],
                    $bookDB[Constants::AUTHOR_NAME],
                    $bookDB[Constants::BOOK_PUBLISHER],
                    $bookDB[Constants::BOOK_DESCRIPTION],
                    $bookDB[Constants::BOOK_NUMPAGES],
                    $bookDB[Constants::BOOK_IMAGELINK]
                );
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
            $insertData[Constants::DOCUMENT_ID] = null;
            $insertData[Constants::DOCUMENT_NAME] = $filename;
            $this->app[Constants::DATABASE_SERVICE][Constants::DATABASE_MYSQL]
                ->insert(Constants::DOCUMENT_TABLE,$insertData);
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
            $insertData[Constants::BOOK_FK_ID] = $bookId;
            $insertData[Constants::DOCUMENT_FK_ID] = $docId;
            $this->app[Constants::DATABASE_SERVICE][Constants::DATABASE_MYSQL]
                ->insert(Constants::BOOK_DOC_RELATION_TABLE,$insertData);
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
        $id = $this->app[Constants::DATABASE_SERVICE][Constants::DATABASE_MYSQL]
            ->fetchColumn(Constants::QUERY_SELECTDOCIDBYDOCNAME, array($filename));
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
        $count = $this->app[Constants::DATABASE_SERVICE][Constants::DATABASE_MYSQL]
            ->fetchColumn(Constants::QUERY_COUNTBOOKDOCRELATION, array($bookId, $docId));
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