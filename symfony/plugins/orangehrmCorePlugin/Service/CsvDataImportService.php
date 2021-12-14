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

use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Core\Import\CsvDataImportFactory;

class CsvDataImportService
{
    /**
     * @param string $fileContent
     * @param string $importType
     * @return int
     * @throws DaoException
     */
    public function import(string $fileContent, string $importType, array $headerValues): int
    {
        $factory = new CsvDataImportFactory();
        $instance = $factory->getImportClassInstance($importType);
        $rowsImported = 0;
        $lines = explode("\n", $fileContent);
        $employeesDataArray = array_map('str_getcsv', $lines);
        if ($headerValues == $employeesDataArray[0]) {
            for ($i = 1; $i < sizeof($employeesDataArray) - 1; $i++) {
                $result = $instance->import($employeesDataArray[$i]);
                if ($result) {
                    $rowsImported++;
                }
            }
        }
        return $rowsImported;
    }
}
