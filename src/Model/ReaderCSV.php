<?php

namespace Uphf\GestionAbsence\Model;

/**
 * Classe permettant de lire les fichiers .csv t les traduires en array
 */

class ReaderCSV
{

    /**
     * Méthode générique permettant de lire n'importe qu'elle fichier csv
     *
     * @warning !!! ATTENTION : Il est capable de lire certain format d'image, merci d'utiliser isCSV avant d'autres opérations !!!
     *
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

            if (count($row) !== count($headers)) continue;
            $data[] = array_combine($headers, $row);
        }

        fclose($csvFile);

        return $data;
    }

    /**
     * Méthode permettant de vérifier que le fichier est un fichier csv
     *
     * @param $filename
     * @return bool
     */
    public static function isCSV($filename) : bool
    {
        $allowed = array('csv', 'CSV');
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if (in_array($ext, $allowed))
        {
            return true;
        }
        return false;
    }

    /**
     * Méthode permettant de vérifier que les colones des données correspondent bien à un format souhaité
     *
     * @param $data
     * @param $columns
     * @return bool
     */
    public static function haveCollum($data, $columns) : bool
    {
        if (!(count($data[0]) === count($columns))) { return false; }

        for ($i = 1; $i < count($columns); $i++)
        {
            if (!array_key_exists($columns[$i], $data[0]))
            {
                return false;
            }
        }
        return true;
    }
}