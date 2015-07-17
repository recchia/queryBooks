<?php

namespace Adapter;

use Exception\ApiException;
use Exception\BookNotFoundException;
use Google_Client;
use Google_Service_Books;
use Interfaces\AdapterInterface;
use Google_Service_Exception;
use Model\Book;
use Silex\Application;
use Model\DBConnection;


/**
 * Description of GoogleApiBooksAdapter.
 *
 * @author recchia
 */
class GoogleBooksApiAdapter implements AdapterInterface
{
    /**
     * @var Google_Client
     */
    protected $client;

    /**
     * @var Google_Service_Books
     */
    protected $booksApi;

    /**
     * @var array
     */
    protected $params = [];

    /**
     * GoogleApiBooksAdapter constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->client = new Google_Client();
        $this->client->setApplicationName('');
        $this->client->setDeveloperKey($config['api_key']);
        $this->booksApi = new Google_Service_Books($this->client);
        $this->params['langRestrict'] = 'es';
    }

    /**
     * Find one book by ISBN.
     *
     * @param string $isbn
     *
     * @return Book
     *
     * @throws BookNotFoundException
     */
    public function findOne($isbn)
    {
        try {
            $q = 'isbn:' . $isbn;
            //$book = [];
            $result = $this->booksApi->volumes->listVolumes($q, $this->params);
            $items = $result->getItems();
            if (count($items) > 0) {
                $volumeInfo = $items[0]->getVolumeInfo();

                if(strlen($volumeInfo['industryIdentifiers'][0]['identifier']) == 13)
                {
                    if (isset($volumeInfo['industryIdentifiers'][0])) {
                        $isbn13 = $volumeInfo['industryIdentifiers'][0]['identifier'];
                    }
                    else
                    {
                        $isbn13 = "N/A";
                    }
                    if (isset($volumeInfo['industryIdentifiers'][1])) {
                        $isbn10 = $volumeInfo['industryIdentifiers'][1]['identifier'];
                    }
                   else
                   {
                       $isbn10 = "N/A";
                   }
                }
                else
                {
                    if (isset($volumeInfo['industryIdentifiers'][1])) {
                        $isbn13 = $volumeInfo['industryIdentifiers'][1]['identifier'];
                    }
                    else
                    {
                        $isbn13 = "N/A";
                    }
                    if (isset($volumeInfo['industryIdentifiers'][0])) {
                        $isbn10 = $volumeInfo['industryIdentifiers'][0]['identifier'];
                    }
                    else
                    {
                        $isbn10 = "N/A";
                    }
                }
                $author = (is_array($volumeInfo['authors'])) ? implode(', ', $volumeInfo['authors']) : $volumeInfo['authors'];
                $imageLink = (!empty($volumeInfo['modelData']['imageLinks']['thumbnail'])) ? $volumeInfo['modelData']['imageLinks']['thumbnail'] : '';

                $book = Book::buildComplete($isbn10, $isbn13, $volumeInfo['title'], $author, $volumeInfo['publisher'],
                    $volumeInfo['description'], $volumeInfo['pageCount'], $imageLink);

                return $book;
            } else {
                throw new BookNotFoundException("Google Book Api can't find ISBN: " . $isbn);
            }
        } catch (Google_Service_Exception $e) {
            throw new ApiException($e->getMessage());
        }
    }

    /**
     * Find books by isbns
     *
     * @param array $isbns
     *
     * @param Application $app
     *
     * @return array
     */
    public function find(array $isbns, Application $app)
    {
        if (is_array($isbns)) {
            $data = [];
            $database = new DBConnection($app);
            foreach ($isbns as $isbn) {
                $q = 'isbn:' . $isbn;
                $result = $this->booksApi->volumes->listVolumes($q, $this->params);
                $items = $result->getItems();
                if (count($items) > 0) {
                    $volumeInfo = $items[0]->getVolumeInfo();

                    if(strlen($volumeInfo['industryIdentifiers'][0]['identifier']) == 13)
                    {
                        if (isset($volumeInfo['industryIdentifiers'][0])) {
                            $isbn13 = $volumeInfo['industryIdentifiers'][0]['identifier'];
                        }
                        else
                        {
                            $isbn13 = "N/A";
                        }
                        if (isset($volumeInfo['industryIdentifiers'][1])) {
                            $isbn10 = $volumeInfo['industryIdentifiers'][1]['identifier'];
                        }
                        else
                        {
                            $isbn10 = "N/A";
                        }
                    }
                    else
                    {
                        if (isset($volumeInfo['industryIdentifiers'][1])) {
                            $isbn13 = $volumeInfo['industryIdentifiers'][1]['identifier'];
                        }
                        else
                        {
                            $isbn13 = "N/A";
                        }
                        if (isset($volumeInfo['industryIdentifiers'][0])) {
                            $isbn10 = $volumeInfo['industryIdentifiers'][0]['identifier'];
                        }
                        else
                        {
                            $isbn10 = "N/A";
                        }
                    }
                    $author = (is_array($volumeInfo['authors'])) ? implode(', ', $volumeInfo['authors']) : $volumeInfo['authors'];
                    $imageLink = (!empty($volumeInfo['modelData']['imageLinks']['thumbnail'])) ? $volumeInfo['modelData']['imageLinks']['thumbnail'] : '';

                    $book = Book::buildComplete($isbn10, $isbn13, $volumeInfo['title'], $author, $volumeInfo['publisher'],
                        $volumeInfo['description'], $volumeInfo['pageCount'], $imageLink);
                    $database->addNewBook($book);
                    $data[] = $book;
                }
            }

            return $data;
        }
    }
}
