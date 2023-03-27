<?php

namespace App\Service;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SpreadsheetService {

    public function export(string $title, $datas, $header) : void 
    {
        $streamedResponse = new StreamedResponse();
        $streamedResponse->setCallback(function () use ($title, $datas, $header) {

            // Generating SpreadSheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle($title);

            // Generating First Row with column name
            $sheet->fromArray($header);

            // Generating other rows with datas
            $count = 2;
            foreach ($datas as $data) {
                $sheet->fromArray($data, null, 'A' . $count);
                $count++;
            }

            // Write and send created spreadsheet
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');

            // This exit(); is required to prevent errors while opening the generated .xlsx
            exit();
        });

        // Puting headers on response and sending it
        $streamedResponse->setStatusCode(Response::HTTP_OK);
        $streamedResponse->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $streamedResponse->headers->set('Content-Disposition', 'attachment; filename="' . $title . '.xlsx"');
        $streamedResponse->send();

        return;

        // https://stackoverflow.com/questions/72012601/symfony-6-creating-excel-with-phpspreadsheet-and-downloading-it-asynchronously
    }
}
