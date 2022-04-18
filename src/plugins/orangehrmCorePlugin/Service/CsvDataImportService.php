<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 *
 */

namespace OrangeHRM\Core\Service;

use OrangeHRM\Core\Exception\CSVUploadFailedException;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Core\Import\CsvDataImportFactory;
use OrangeHRM\Core\Traits\Service\TextHelperTrait;

class CsvDataImportService
{
    use TextHelperTrait;

    /**
     * @param string $fileContent
     * @param string $importType
     * @param array $headerValues
     * @return int
     * @throws DaoException|CSVUploadFailedException
     */
    public function import(string $fileContent, string $importType, array $headerValues): int
    {
        $factory = new CsvDataImportFactory();
        $instance = $factory->getImportClassInstance($importType);

        $employeesDataArray = $this->getEmployeeArrayFromCSV($fileContent, $headerValues);

        $rowsImported = 0;
        if ($headerValues == $employeesDataArray[0]) {
            for ($i = 1; $i < sizeof($employeesDataArray); $i++) {
                $result = $instance->import($employeesDataArray[$i]);
                if ($result) {
                    $rowsImported++;
                }
            }
        }
        return $rowsImported;
    }

    /**
     * Returns a multidimensional array where one array matches a row of the CSV
     * @param string $fileContent
     * @param array $headerValues
     * @return array
     * @throws CSVUploadFailedException
     */
    public function getEmployeeArrayFromCSV(string $fileContent, array $headerValues): array
    {
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $fileContent);
        rewind($stream);
        $employeesDataArray = [];

        while (($data = fgetcsv($stream, 1000, ",")) !== false) {
            //Each data row should have the same amount of elements as the headerValues array
            //E.g. for data array: ["Devi","","DS","","","","","","","","","","","","","","","","","","devi@admin.com",""]
            if (count($data) !== count($headerValues)) {
                fclose($stream);
                throw CSVUploadFailedException::validationFailed();
            }

            foreach ($data as $key => $datum) {
                if (preg_match('/[\n\r\t\v\x00]/', $datum)) {
                    $parsedData = str_replace(["\n", "\r", "\t", "\v", "\x00"], ' ', $datum);
                    $data[$key] = trim($parsedData);
                }
            }
            $employeesDataArray[] = $data;
        }
        fclose($stream);
        return $employeesDataArray;
    }
}
