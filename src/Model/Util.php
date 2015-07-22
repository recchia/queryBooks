<?php

namespace Model;

use Symfony\Component\Form\Form;
use Model\Book;

class Util
{
    public static function getFormErrorMessages(Form $form)
    {
        $errors = [];
        foreach ($form->getErrors(true, true) as $error) {
            $errors = $error->getMessage();
        }

        return $errors;
    }

    /**
     * Builds the return string when finding one book
     *
     * @param $book Book
     *
     * @return string
     */
    public static function getFindOneBookReturnMessage($book)
    {
        $formattedResponse = "<p>ISBN 10: " . $book->getIsbn10() . "<br />
                ISBN 13: " . $book->getIsbn13() . "</p>
                <p>T&iacute;tulo: <strong>" . $book->getTitle() . "</strong></p>
                <p>Autor: " . $book->getAuthors() . "</p>
                <p>Publicado por: " . $book->getPublisher() . "</p>
                <p>Descripci&oacute;n: " . $book->getDescription() . "</p>
                <p>N&uacute;mero de p&aacute;ginas: " . $book->getPageCount() . "</p>
                <p><a href='" . $book->getImageLink() . "'>Ver Im&aacute;gen</a></p>";

        return $formattedResponse;

    }

    public static function getUploaderReturnMessage($filename)
    {
        $message = '<strong>Documento guardado como: '. $filename .', lo puede conseguir
            en la parte de Descarga de Archivos</strong>';
        return $message;
    }
}
