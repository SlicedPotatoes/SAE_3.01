<?php

namespace Uphf\GestionAbsence\Model;

/**
 * Classe permettant de lire les fichiers .csv t les traduires en array
 */

class ReaderCSV
{

    /**
     * @param  string  $filename
     * @param  string  $deliminator
     * @param  string  $enclosure
     * @param  string  $escape
     *
     * @return array
     */
    public static function readCSV(
      string $filename,
      string $deliminator = ';',
      string $enclosure = '"',
      string $escape = '\\'
    ) : array
    {
        $csvFile = fopen($filename, "r");

        /**
         * Le headers permets d'assimilé les noms des collones comme des clefs pour les arrays
         */
        $headers = fgetcsv($csvFile, 0, $deliminator, $enclosure, $escape);
        if ($headers === false)
        {
            fclose($csvFile);
            return [];
        }

        $data = [];

        while (($row = fgetcsv($csvFile, 0, $deliminator, $enclosure, $escape)) !== false)
        {
            /**
             * Si la ligne est vide
             */
            if ($row === [null] || $row === [])
            {
                continue;
            }

            $data[] = array_combine($headers, $row);
        }

        fclose($csvFile);

        return $data;
    }

    public static function isCSV($filename) : bool
    {
        /**
         * Vérifie que le fichier est un fichier csv
         */
        $allowed = array('csv', 'CSV');
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if (in_array($ext, $allowed))
        {
            return true;
        }
        return false;
    }
}