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
     *                                          Google Books API Info
     * ################################################################################################################
     */
        const GOOGLE_BOOKS_QUERY            =   'isbn:';
        const GOOGLE_BOOKS_LABEL_INDUSTRY   =   'industryIdentifiers';
        const GOOGLE_BOOKS_LABEL_IDENTIFIER =   'identifier';
        const GOOGLE_BOOKS_LABEL_AUTHORS    =   'authors';
        const GOOGLE_BOOKS_LABEL_MODELDATA  =   'modelData';
        const GOOGLE_BOOKS_LABEL_IMAGELINKS =   'imageLinks';
        const GOOGLE_BOOKS_LABEL_THUMBNAIL  =   'thumbnail';
        const GOOGLE_BOOKS_LABEL_TITLE      =   'title';
        const GOOGLE_BOOKS_LABEL_PUBLISHER  =   'publisher';
        const GOOGLE_BOOKS_LABEL_DESCRIPTION=   'description';
        const GOOGLE_BOOKS_LABEL_PAGECOUNT  =   'pageCount';
        const GOOGLE_BOOKS_LABEL_API_KEY    =   'api_key';
        const GOOGLE_BOOKS_LABEL_LANGRES    =   'langRestrict';
        const GOOGLE_BOOKS_LANGRESTRICT     =   'es';


    /**
     * ################################################################################################################
     *                                          Database info
     * ################################################################################################################
     */
        const DATABASE_SERVICE              =   'dbs';
        const DATABASE_MYSQL                =   'mysql';
        const DATABASE_DRIVER               =   'pdo_mysql';
        const DATABASE_HOST                 =   'localhost';
        const DATABASE_NAME                 =   'querybooksdb';
        const DATABASE_USER                 =   'website';
        const DATABASE_PASSWORD             =   'liniobooksapi';
        const DATABASE_CHARSET              =   'utf8mb4';
    /**
     * ################################################################################################################
     *                                          Excel Info
     * ################################################################################################################
     */
        const EXCEL_CREATOR                 =   'Linio Books';
        const EXCEL_LASTMODIFIED            =   'Linio Books';
        const EXCEL_TITLE                   =   'Linio Books Search Results';
        const EXCEL_SUBJECT                 =   'Books';
        const EXCEL_DESCRIPTION             =   'Linio Books Search Results Powered by Google';
        const EXCEL_KEYWORDS                =   'office 2007 books';
        const EXCEL_CATEGORY                =   'Search Result File';
        const EXCEL_CELL_A1                 =   'ISBN_10';
        const EXCEL_CELL_B1                 =   'ISBN_13';
        const EXCEL_CELL_C1                 =   'Titulo';
        const EXCEL_CELL_D1                 =   'Autor';
        const EXCEL_CELL_E1                 =   'Editorial';
        const EXCEL_CELL_F1                 =   'Descripcion';
        const EXCEL_CELL_G1                 =   'Numero de Paginas';
        const EXCEL_CELL_H1                 =   'Imagen';
        const EXCEL_DOWNLOAD_LOCATION       =   'http://books.linio/upload/';

    /**
     * ################################################################################################################
     *                                          Error Messages
     * ################################################################################################################
     */
        const BOOKNOTFOUNDEXCEPTION_MSG     =   '<strong>No se consigui&oacute; el libro buscado</strong>';

    /**
     * ################################################################################################################
     *                                          Book table database info
     * ################################################################################################################
     */
        const BOOK_TABLE                    =   'linio_books';
        const BOOK_ID                       =   'LB_id';
        const BOOK_ISBN10                   =   'LB_isbnTen';
        const BOOK_ISBN13                   =   'LB_isbnThirteen';
        const BOOK_TITLE                    =   'LB_title';
        const BOOK_PUBLISHER                =   'LB_publisher';
        const BOOK_DESCRIPTION              =   'LB_description';
        const BOOK_NUMPAGES                 =   'LB_pages';
        const BOOK_IMAGELINK                =   'LB_imageLink';

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
        const API_CLASSNAME                 =   'ba_classname';

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
                                                " FROM ". Constants::DOCUMENT_TABLE .
                                                " WHERE ". Constants::DOCUMENT_NAME ." = ?";
        /**
         * Selects Api info by its name
         */
        const QUERY_SELECTAPIINFOBYNAME     =   "SELECT ". Constants::API_KEY .", "
                                                . Constants::API_CLASSNAME .
                                                " FROM ". Constants::API_TABLE .
                                                " WHERE ". Constants::API_NAME . " = ?";


}