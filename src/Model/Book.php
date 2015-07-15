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
        $instance->isbn10 = $isbn10;
        $instance->isbn13 = $isbn13;
        $instance->title = $title;
        $instance->authors = $authors;
        $instance->publisher = $publisher;
        $instance->description = $description;
        $instance->pageCount = $pageCount;
        $instance->imageLink = $imageLink;
        return $instance;
    }

    /**
     * @param $title
     * @param $authors
     * @param $publisher
     * @param $description
     * @param $pageCount
     * @param $imageLink
     *
     * @return Book
     */
    public static function buildWithoutIsbn( $title, $authors, $publisher, $description, $pageCount, $imageLink)
    {
        $instance = new self();
        $instance->title = $title;
        $instance->authors = $authors;
        $instance->publisher = $publisher;
        $instance->description = $description;
        $instance->pageCount = $pageCount;
        $instance->imageLink = $imageLink;
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
        $this->isbn10 = $isbn10;
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
        $this->isbn13 = $isbn13;
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
        $this->title = $title;
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
        $this->authors = $authors;
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
        $this->publisher = $publisher;
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
        $this->description = $description;
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
        $this->pageCount = $pageCount;
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
        $this->imageLink = $imageLink;
    }

    /**
     * @return array
     */
    public function getInsertArray()
    {
        $insertData = [];
        $insertData['LB_id'] = null;
        $insertData['LB_isbnTen'] = $this->getIsbn10();
        $insertData['LB_isbnThirteen'] = $this->getIsbn13();
        $insertData['LB_title'] = $this->getTitle();
        $insertData['LB_publisher'] = $this->getPublisher();
        $insertData['LB_description'] = $this->getDescription();
        $insertData['LB_pages'] = $this->getPageCount();
        $insertData['LB_imageLink'] = $this->getImageLink();
        return $insertData;
    }
}