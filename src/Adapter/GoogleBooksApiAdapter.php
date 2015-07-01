<?php

namespace Adapter;

use Exception\ApiException;
use Exception\BookNotFoundException;
use Google_Client;
use Google_Service_Books;
use Interfaces\AdapterInterface;
use Google_Service_Exception;

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
     * @return array
     *
     * @throws BookNotFoundException
     */
    public function findOne($isbn)
    {
        try {
            $q = 'ISBN=' . $isbn;
            $book = [];
            $result = $this->booksApi->volumes->listVolumes($q, $this->params);
            $items = $result->getItems();
            if (count($items) > 0) {
                $volumeInfo = $items[0]->getVolumeInfo();
                $book['title'] = $volumeInfo['title'];
                $book['authors'] = (is_array($volumeInfo['authors'])) ? implode(', ', $volumeInfo['authors']) : $volumeInfo['authors'];
                $book['publisher'] = $volumeInfo['publisher'];
                $book['description'] = $volumeInfo['description'];
                $book['pageCount'] = $volumeInfo['pageCount'];
                $book['imageLink'] = (!empty($volumeInfo['modelData']['imageLinks']['thumbnail'])) ? $volumeInfo['modelData']['imageLinks']['thumbnail'] : '';

                return $book;
            } else {
                throw new BookNotFoundException("Google Book Api can't find ISBN: " . $isbn);
            }
        } catch (Google_Service_Exception $e) {
            throw new ApiException($e->getMessage());
        }
    }

    /**
     * Find books by ISBN.
     *
     * @param array $isbns
     *
     * @return array
     */
    public function find(array $isbns)
    {
        if (is_array($isbns)) {
            $data = [];
            foreach ($isbns as $isbn) {
                $q = 'ISBN=' . $isbn;
                $book = [];
                $result = $this->booksApi->volumes->listVolumes($q, $this->params);
                $items = $result->getItems();
                if (count($items) > 0) {
                    $volumeInfo = $items[0]->getVolumeInfo();
                    $book['title'] = $volumeInfo['title'];
                    $book['authors'] = (is_array($volumeInfo['authors'])) ? implode(', ', $volumeInfo['authors']) : $volumeInfo['authors'];
                    $book['publisher'] = $volumeInfo['publisher'];
                    $book['description'] = $volumeInfo['description'];
                    $book['pageCount'] = $volumeInfo['pageCount'];
                    $book['imageLink'] = (!empty($volumeInfo['modelData']['imageLinks']['thumbnail'])) ? $volumeInfo['modelData']['imageLinks']['thumbnail'] : '';
                    $data[] = $book;
                }
            }

            return $data;
        }
    }
}
