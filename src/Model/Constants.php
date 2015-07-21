<?php
/**
 * Created by PhpStorm.
 * User: Santiago
 * Date: 7/21/2015
 * Time: 9:18 AM
 */

namespace Model;

class Constants
{
    /**
     * ################################################################################################################
     *                                          Database info
     * ################################################################################################################
     */
        const DATABASE_SERVICE              =   'dbs';
        const DATABASE_MYSQL                =   'mysql';

    /**
     * ################################################################################################################
     *                                          Book table database info
     * ################################################################################################################
     */
        const BOOK_TABLE                    =   'linio_books';
        const BOOK_ID                       =   'lb_id';
        const BOOK_ISBN10                   =   'lb_isbnTen';
        const BOOK_ISBN13                   =   'lb_isbnThirteen';
        const BOOK_TITLE                    =   'lb_title';
        const BOOK_PUBLISHER                =   'lb_publisher';
        const BOOK_DESCRIPTION              =   'lb_description';
        const BOOK_NUMPAGES                 =   'lb_pages';
        const BOOK_IMAGELINK                =   'lb_imageLink';

    /**
     * ################################################################################################################
     *                                          Authors table database info
     * ################################################################################################################
     */
        const AUTHOR_TABLE                  =   'author';
        const AUTHOR_ID                     =   'auth_id';
        const AUTHOR_NAME                   =   'auth_name';

    /**
     * ################################################################################################################
     *                                          Api table database info
     * ################################################################################################################
     */
        const API_TABLE                     =   'books_api';
        const API_ID                        =   'ba_id';
        const API_NAME                      =   'ba_name';
        const API_KEY                       =   'ba_key';

    /**
     * ################################################################################################################
     *                                          Documents table database info
     * ################################################################################################################
     */
        const DOCUMENT_TABLE                =   'documents';
        const DOCUMENT_ID                   =   'doc_id';
        const DOCUMENT_NAME                 =   'doc_name';

    /**
     * ################################################################################################################
     *                                          Database Foreign keys
     * ################################################################################################################
     */
        const BOOK_FK_ID                    =   'lb_id_fk';
        const AUTHOR_FK_ID                  =   'auth_id_fk';
        const DOCUMENT_FK_ID                =   'doc_id_fk';

    /**
     * ################################################################################################################
     *                                          Relationship tables
     * ################################################################################################################
     */
        const BOOK_AUTHOR_RELATION_TABLE    =   'lb_author';
        const BOOK_DOC_RELATION_TABLE       =   'lb_doc';

    /**
     * ################################################################################################################
     *                                          SQL Queries
     * ################################################################################################################
     */
        /**
         * Counts the number of books by isbn13
         */
        const QUERY_COUNTBOOKSBYISBN13      =   "SELECT COUNT(*) ".
                                                "FROM ". Constants::BOOK_TABLE.
                                                " WHERE " . Constants::BOOK_ISBN13." = ?";
        /**
         * Counts the number of books by isbn10
         */
        const QUERY_COUNTBOOKSBYISBN10      =   "SELECT COUNT(*) ".
                                                "FROM ". Constants::BOOK_TABLE.
                                                " WHERE " . Constants::BOOK_ISBN10. " = ?";
        /**
         * Counts the number of relationships a document has with a book
         */
        const QUERY_COUNTBOOKDOCRELATION    =   "SELECT COUNT(*) ".
                                                "FROM ". Constants::BOOK_DOC_RELATION_TABLE .
                                                " WHERE ". Constants::BOOK_FK_ID ."= ? AND "
                                                .Constants::DOCUMENT_FK_ID ."= ?";
        /**
         * Counts authors by name
         */
        const QUERY_COUNTAUTHORSBYNAME      =   "SELECT COUNT(*) ".
                                                "FROM ". Constants::AUTHOR_TABLE .
                                                " WHERE " . Constants::AUTHOR_NAME ." = ?";
        /**
         * Selects all of the apis names in the database
         */
        const QUERY_SELECTALLAPISNAME       =   "SELECT ". Constants::API_NAME ." ".
                                                "FROM " .Constants::API_TABLE;
        /**
         * Selects all of the documents name in a database
         */
        const QUERY_SELECTALLDOCUMENTSNAME  =   "SELECT ". Constants::DOCUMENT_NAME .
                                                " FROM ". Constants::DOCUMENT_TABLE;
        /**
         * Selects an api keys by its name
         */
        const QUERY_SELECTAPIKEYBYNAME      =   "SELECT ". Constants::API_KEY .
                                                " FROM ". Constants::API_TABLE .
                                                " WHERE ". Constants::API_NAME ."= ?";
        /**
         * Selects all of a books data by isbn13
         */
        const QUERY_SELECTBOOKDATABYISBN13  =   "SELECT * ".
                                                "FROM ". Constants::BOOK_TABLE .","
                                                . Constants::AUTHOR_TABLE .","
                                                . Constants::BOOK_AUTHOR_RELATION_TABLE .
                                                " WHERE ".Constants::BOOK_ISBN13 ."= ? AND "
                                                . Constants::BOOK_FK_ID ." = ". Constants::BOOK_ID ." AND "
                                                . Constants::AUTHOR_ID ." = ". Constants::AUTHOR_FK_ID;
        /**
         * Selects all of a books data by isbn10
         */
        const QUERY_SELECTBOOKDATABYISBN10  =   "SELECT * ".
                                                "FROM ". Constants::BOOK_TABLE .","
                                                . Constants::AUTHOR_TABLE .","
                                                . Constants::BOOK_AUTHOR_RELATION_TABLE .
                                                " WHERE ". Constants::BOOK_ISBN10 . " = ? AND "
                                                . Constants::BOOK_FK_ID ." = ". Constants::BOOK_ID . " AND "
                                                . Constants::AUTHOR_ID ." = ". Constants::AUTHOR_FK_ID;
        /**
         * Selects book id by its isbn13
         */
        const QUERY_SELECTBOOKIDBYISBN13    =   "SELECT ". Constants::BOOK_ID .
                                                " FROM ". Constants::BOOK_TABLE .
                                                " WHERE ". Constants::BOOK_ISBN13 ." = ?";
        /**
         * Selects an authors id by its name
         */
        const QUERY_SELECTAUTHORIDBYNAME    =   "SELECT ". Constants::AUTHOR_ID .
                                                " FROM ". Constants::AUTHOR_TABLE .
                                                " WHERE ". Constants::AUTHOR_NAME ." = ?";
        /**
         * Selects all of a books info by its id
         */
        const QUERY_SELECTBOOKINFOBYID      =   "SELECT * ".
                                                "FROM ". Constants::BOOK_TABLE .", "
                                                . Constants::AUTHOR_TABLE .", "
                                                . Constants::BOOK_AUTHOR_RELATION_TABLE .
                                                " WHERE ". Constants::BOOK_ID . " = ? AND "
                                                . Constants::BOOK_FK_ID ." = ". Constants::BOOK_ID . " AND "
                                                . Constants::AUTHOR_ID ." = ". Constants::AUTHOR_FK_ID;
        /**
         * Selects all of the books info related to a document by its name
         */
        const QUERY_SELECTBOOKINFOBYDOCNAME =   "SELECT ". Constants::BOOK_ISBN10 .", "
                                                . Constants::BOOK_ISBN13 .", "
                                                . Constants::BOOK_TITLE .", "
                                                . Constants::AUTHOR_NAME .", "
                                                . Constants::BOOK_PUBLISHER .", "
                                                . Constants::BOOK_DESCRIPTION .", "
                                                . Constants::BOOK_NUMPAGES .", "
                                                . Constants::BOOK_IMAGELINK .
                                                " FROM ". Constants::BOOK_TABLE ." AS lb, "
                                                . Constants::AUTHOR_TABLE ." AS auth, "
                                                . Constants::BOOK_AUTHOR_RELATION_TABLE ." AS lba, "
                                                . Constants::DOCUMENT_TABLE ." AS doc, "
                                                . Constants::BOOK_DOC_RELATION_TABLE ." AS lbd ".
                                                "WHERE doc.". Constants::DOCUMENT_NAME ."=? AND ".
                                                "doc.". Constants::DOCUMENT_ID ." = lbd.". Constants::DOCUMENT_FK_ID ." AND ".
                                                "lb.". Constants::BOOK_ID ." = lbd.". Constants::BOOK_FK_ID ." AND ".
                                                "lba.". Constants::BOOK_FK_ID ." = lb.". Constants::BOOK_ID ." AND ".
                                                "lba.". Constants::AUTHOR_FK_ID ." = auth.". Constants::AUTHOR_ID;
        /**
         * Selects a documents id by its name
         */
        const QUERY_SELECTDOCIDBYDOCNAME    =   "SELECT ". Constants::DOCUMENT_ID .
                                                "FROM ". Constants::DOCUMENT_TABLE .
                                                " WHERE ". Constants::DOCUMENT_NAME ." = ?";


}