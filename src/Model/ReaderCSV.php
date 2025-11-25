<?php

namespace Uphf\GestionAbsence\Model;

class ReaderCSV
{
    public static function readCSV(string $filename)
    {
        $csvFile = fopen($filename, "r");

        $headers = fgetcsv($csvFile);
        $data = [];

        while (($row = fgetcsv($csvFile)) !== false) {
            $data[] = array_combine($headers, $row);
        }

        fclose($csvFile);

        print_r($data);
    }
}

// TODO