<?php
/**
 * Created by PhpStorm.
 * User: Santiago
 * Date: 7/22/2015
 * Time: 9:04 AM
 */

namespace Model;

use \PHPExcel;
use \PHPExcel_IOFactory;

class ExcelWorker
{
    public static function createExcelDocument($books, $filename)
    {
        $phpExcel = new PHPExcel();
            $phpExcel->getProperties()->setCreator('Linio Books')
                ->setLastModifiedBy('Linio')
                ->setTitle('Office 2007 XLSX Test Document')
                ->setSubject('Office 2007 XLSX Test Document')
                ->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
                ->setKeywords('office 2007 openxml php')
                ->setCategory('Test result file');
            $phpExcel->setActiveSheetIndex(0);
            $phpExcel->getActiveSheet()->setCellValue('A1', 'ISBN_10');
            $phpExcel->getActiveSheet()->setCellValue('B1', 'ISBN_13');
            $phpExcel->getActiveSheet()->setCellValue('C1', 'Titulo');
            $phpExcel->getActiveSheet()->setCellValue('D1', 'Autor');
            $phpExcel->getActiveSheet()->setCellValue('E1', 'Editorial');
            $phpExcel->getActiveSheet()->setCellValue('F1', 'Descripcion');
            $phpExcel->getActiveSheet()->setCellValue('G1', 'Numero de Paginas');
            $phpExcel->getActiveSheet()->setCellValue('H1', 'Imagen');

            $i = 2;
            foreach ($books as $book) {
                $phpExcel->getActiveSheet()->setCellValue('A' . $i, $book->getIsbn10());
                $phpExcel->getActiveSheet()->setCellValue('B' . $i, $book->getIsbn13());
                $phpExcel->getActiveSheet()->setCellValue('C' . $i, $book->getTitle());
                $phpExcel->getActiveSheet()->setCellValue('D' . $i, $book->getAuthors());
                $phpExcel->getActiveSheet()->setCellValue('E' . $i, $book->getPublisher());
                $phpExcel->getActiveSheet()->setCellValue('F' . $i, $book->getDescription());
                $phpExcel->getActiveSheet()->setCellValue('G' . $i, $book->getPageCount());
                $phpExcel->getActiveSheet()->setCellValue('H' . $i, $book->getImageLink());
                $i++;
            }
            $phpExcel->setActiveSheetIndex(0);

            $writer = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel2007');
            $file = ROOT . 'web/upload/' . $filename;
            $writer->save($file);

    }

    public static function getISBNSFromExcelDocument($file, $path, $filename)
    {
        $file['file']->move($path, $filename);
        $excel = PHPExcel_IOFactory::load($path . $filename);
        $sheet = $excel->getActiveSheet();
        $highestRow = $sheet->getHighestRow();
        $isbns = [];

        for ($row = 1; $row <= $highestRow; ++$row) {
            $isbns[] = $sheet->getCellByColumnAndRow(0, $row)->getValue();
        }

        return $isbns;
    }
}