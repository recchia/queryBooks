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
            $phpExcel->getProperties()->setCreator(Constants::EXCEL_CREATOR)
                ->setLastModifiedBy(Constants::EXCEL_LASTMODIFIED)
                ->setTitle(Constants::EXCEL_TITLE)
                ->setSubject(Constants::EXCEL_SUBJECT)
                ->setDescription(Constants::EXCEL_DESCRIPTION)
                ->setKeywords(Constants::EXCEL_KEYWORDS)
                ->setCategory(Constants::EXCEL_CATEGORY);
            $phpExcel->setActiveSheetIndex(0);
            $phpExcel->getActiveSheet()->setCellValue('A1', Constants::EXCEL_CELL_A1);
            $phpExcel->getActiveSheet()->setCellValue('B1', Constants::EXCEL_CELL_B1);
            $phpExcel->getActiveSheet()->setCellValue('C1', Constants::EXCEL_CELL_C1);
            $phpExcel->getActiveSheet()->setCellValue('D1', Constants::EXCEL_CELL_D1);
            $phpExcel->getActiveSheet()->setCellValue('E1', Constants::EXCEL_CELL_E1);
            $phpExcel->getActiveSheet()->setCellValue('F1', Constants::EXCEL_CELL_F1);
            $phpExcel->getActiveSheet()->setCellValue('G1', Constants::EXCEL_CELL_G1);
            $phpExcel->getActiveSheet()->setCellValue('H1', Constants::EXCEL_CELL_H1);

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