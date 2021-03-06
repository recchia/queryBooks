<?php
/**
 * Created by PhpStorm.
 * User: Santiago
 * Date: 7/15/2015
 * Time: 11:01 AM
 */

namespace Model;

class Book
{
    /**
     * @var string
     */
    protected $isbn10;

    /**
     * @var string
     */
    protected $isbn13;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $authors;

    /**
     * @var string
     */
    protected $publisher;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $pageCount;

    /**
     * @var string
     */
    protected $imageLink;

    public function __construct()
    {

    }

    /**
     * @param $isbn10
     * @param $isbn13
     * @param $title
     * @param $authors
     * @param $publisher
     * @param $description
     * @param $pageCount
     * @param $imageLink
     *
     * @return Book
     */
    public static function buildComplete($isbn10, $isbn13, $title, $authors, $publisher, $description, $pageCount, $imageLink)
    {

        $instance = new self();
        $instance->setIsbn10($isbn10);
        $instance->setIsbn13($isbn13);
        $instance->setTitle($title);
        $instance->setAuthors($authors);
        $instance->setPublisher($publisher);
        $instance->setDescription($description);
        $instance->setPageCount($pageCount);
        $instance->setImageLink($imageLink);
        return $instance;
    }


    /**
     * @return string
     */
    public function getIsbn10()
    {
        return $this->isbn10;
    }

    /**
     * @param string $isbn10
     */
    public function setIsbn10($isbn10)
    {
        if (is_null($isbn10))
        {
            $this->isbn10 = "N/A";
        }
        else {
            $this->isbn10 = $isbn10;
        }
    }

    /**
     * @return string
     */
    public function getIsbn13()
    {
        return $this->isbn13;
    }

    /**
     * @param string $isbn13
     */
    public function setIsbn13($isbn13)
    {
        if (is_null($isbn13))
        {
            $this->isbn13 = "N/A";
        }
        else {
            $this->isbn13 = $isbn13;
        }
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        if (is_null($title))
        {
            $this->title = "N/A";
        }
        else {
            $this->title = trim($title,"'");
        }
    }

    /**
     * @return string
     */
    public function getAuthors()
    {
        return $this->authors;
    }

    /**
     * @param string $authors
     */
    public function setAuthors($authors)
    {
        if (is_null($authors))
        {
            $this->authors = "N/A";
        }
        else {
            $this->authors = trim($authors,"'");
        }
    }

    /**
     * @return string
     */
    public function getPublisher()
    {
        return $this->publisher;
    }

    /**
     * @param string $publisher
     */
    public function setPublisher($publisher)
    {
        if (is_null($publisher))
        {
            $this->publisher = "N/A";
        }
        else {
            $this->publisher = $publisher;
        }
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        if (is_null($description))
        {
            $this->description = "N/A";
        }
        else {
            $this->description = $description;
        }
    }

    /**
     * @return string
     */
    public function getPageCount()
    {
        return $this->pageCount;
    }

    /**
     * @param string $pageCount
     */
    public function setPageCount($pageCount)
    {
        if (is_null($pageCount))
        {
            $this->pageCount = "N/A";
        }
        else {
            $this->pageCount = $pageCount;
        }
    }

    /**
     * @return string
     */
    public function getImageLink()
    {
        return $this->imageLink;
    }

    /**
     * @param string $imageLink
     */
    public function setImageLink($imageLink)
    {
        if (is_null($imageLink))
        {
            $this->imageLink = "N/A";
        }
        else {
            $this->imageLink = $imageLink;
        }
    }

    /**
     * Gets insert array for the Book
     *
     * @return array
     */
    public function getInsertArray()
    {
        $insertData = [];
        $insertData[Constants::BOOK_ID] = null;
        $insertData[Constants::BOOK_ISBN10] = $this->getIsbn10();
        $insertData[Constants::BOOK_ISBN13] = $this->getIsbn13();
        $insertData[Constants::BOOK_TITLE] = $this->getTitle();
        $insertData[Constants::BOOK_PUBLISHER] = $this->getPublisher();
        $insertData[Constants::BOOK_DESCRIPTION] = $this->getDescription();
        $insertData[Constants::BOOK_NUMPAGES] = $this->getPageCount();
        $insertData[Constants::BOOK_IMAGELINK] = $this->getImageLink();
        return $insertData;
    }
}