<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Core\Service;

use OrangeHRM\Core\Exception\CSVUploadFailedException;
use OrangeHRM\Core\Import\CsvDataImportFactory;
use OrangeHRM\Core\Traits\LoggerTrait;
use Throwable;

class CsvDataImportService
{
    use LoggerTrait;

    /**
     * @param string $fileContent
     * @param string $importType
     * @param array $headerValues
     * @return array
     * @throws CSVUploadFailedException
     */
    public function import(string $fileContent, string $importType, array $headerValues): array
    {
        $this->getLogger()->error('CsvDataImportService::import - Starting import');
        $this->getLogger()->error('CsvDataImportService::import - File content length: ' . strlen($fileContent));
        $this->getLogger()->error('CsvDataImportService::import - Expected header count: ' . count($headerValues));
        
        $factory = new CsvDataImportFactory();
        $instance = $factory->getImportClassInstance($importType);

        try {
            $employeesDataArray = $this->getEmployeeArrayFromCSV($fileContent, $headerValues);
            $this->getLogger()->error('CsvDataImportService::import - CSV parsed successfully. Rows: ' . count($employeesDataArray));
        } catch (CSVUploadFailedException $e) {
            $this->getLogger()->error('CsvDataImportService::import - CSV parsing failed: ' . $e->getMessage());
            throw $e;
        } catch (\Exception $e) {
            $this->getLogger()->error('CsvDataImportService::import - Unexpected error during CSV parsing: ' . $e->getMessage());
            throw CSVUploadFailedException::validationFailedWithMessage("CSV parsing failed: " . $e->getMessage());
        }

        $rowsImported = 0;
        $failList = [];
        
        // Validate header row - allow partial match for backward compatibility
        $csvHeader = $employeesDataArray[0] ?? [];
        $expectedHeaderCount = count($headerValues);
        $csvHeaderCount = count($csvHeader);
        
        $this->getLogger()->error('CsvDataImportService::import - CSV header count: ' . $csvHeaderCount);
        $this->getLogger()->error('CsvDataImportService::import - CSV header: ' . json_encode($csvHeader));
        
        // Check if CSV has at least one data row
        if (sizeof($employeesDataArray) < 2) {
            $this->getLogger()->error('CsvDataImportService::import - CSV has less than 2 rows');
            throw CSVUploadFailedException::validationFailedWithMessage(
                "CSV VALIDATION ERROR: CSV file must contain at least a header row and one data row"
            );
        }
        
        // Check if the CSV header matches the expected header
        // For backward compatibility, check if the first N columns match where N is min(csvHeaderCount, expectedHeaderCount)
        // Also trim whitespace and compare case-insensitively for better compatibility
        $headerMatches = true;
        $columnsToCheck = min($csvHeaderCount, $expectedHeaderCount);
        $mismatchedColumn = null;
        
        // Normalize headers: trim, lowercase for comparison
        $normalizedCsvHeader = array_map(function($col) {
            return strtolower(trim($col));
        }, $csvHeader);
        
        $normalizedExpectedHeader = array_map(function($col) {
            return strtolower(trim($col));
        }, $headerValues);
        
        $this->getLogger()->error('CsvDataImportService::import - Checking ' . $columnsToCheck . ' columns');
        
        // If CSV has fewer columns than expected, that's OK for backward compatibility
        // But we still need to validate that the columns that exist match
        for ($i = 0; $i < $columnsToCheck; $i++) {
            $csvColumn = $normalizedCsvHeader[$i] ?? '';
            $expectedColumn = $normalizedExpectedHeader[$i] ?? '';
            
            // Skip empty columns in CSV (allow for flexibility)
            if (empty($csvColumn) && $i >= $csvHeaderCount) {
                continue;
            }
            
            // Compare normalized values
            if ($csvColumn !== $expectedColumn) {
                $headerMatches = false;
                $mismatchedColumn = [
                    'position' => $i + 1,
                    'csv' => $csvHeader[$i] ?? '(empty)',
                    'expected' => $headerValues[$i] ?? '(empty)',
                    'normalized_csv' => $csvColumn,
                    'normalized_expected' => $expectedColumn
                ];
                $this->getLogger()->error('CsvDataImportService::import - Header mismatch at column ' . $i . ': CSV="' . $csvColumn . '" Expected="' . $expectedColumn . '"');
                break;
            }
        }
        
        if (!$headerMatches) {
            $errorMsg = "CSV VALIDATION ERROR: Header mismatch at column {$mismatchedColumn['position']}. ";
            $errorMsg .= "Found: '{$mismatchedColumn['csv']}' | Expected: '{$mismatchedColumn['expected']}'. ";
            if ($csvHeaderCount < $expectedHeaderCount) {
                $errorMsg .= "Your CSV has {$csvHeaderCount} columns, but {$expectedHeaderCount} are expected. ";
                $errorMsg .= "Missing columns: " . implode(', ', array_slice($headerValues, $csvHeaderCount));
            } elseif ($csvHeaderCount > $expectedHeaderCount) {
                $errorMsg .= "Your CSV has {$csvHeaderCount} columns, but only {$expectedHeaderCount} are expected. ";
                $errorMsg .= "Extra columns found. Please remove columns after: " . $headerValues[$expectedHeaderCount - 1];
            } else {
                $errorMsg .= "Please check column {$mismatchedColumn['position']} - it should be '{$mismatchedColumn['expected']}' but you have '{$mismatchedColumn['csv']}'.";
            }
            $this->getLogger()->error('CsvDataImportService::import - Throwing validation error: ' . $errorMsg);
            throw CSVUploadFailedException::validationFailedWithMessage($errorMsg);
        }
        
        $this->getLogger()->error('CsvDataImportService::import - Header validation passed');
        
        // Header validation already throws exception if it fails, so we can proceed
        for ($i = 1; $i < sizeof($employeesDataArray); $i++) {
            try {
                $result = $instance->import($employeesDataArray[$i]);
            } catch (Throwable $e) {
                $this->getLogger()->error($e->getMessage());
                $this->getLogger()->error($e->getTraceAsString());
                $result = false;
            }
            if ($result) {
                $rowsImported++;
            } else {
                $failList[] = $i + 1; // since the first row contains headers
            }
        }
        return ['success' => $rowsImported, 'failed' => count($failList), 'failedRows' => $failList];
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
        $this->getLogger()->error('getEmployeeArrayFromCSV - Starting CSV parsing');
        $this->getLogger()->error('getEmployeeArrayFromCSV - File content length: ' . strlen($fileContent));
        $this->getLogger()->error('getEmployeeArrayFromCSV - First 200 chars: ' . substr($fileContent, 0, 200));
        
        // Remove BOM (Byte Order Mark) if present (common in Excel exports)
        $fileContent = preg_replace('/^\xEF\xBB\xBF/', '', $fileContent);
        
        $stream = fopen('php://memory', 'r+');
        if ($stream === false) {
            $this->getLogger()->error('getEmployeeArrayFromCSV - Failed to open memory stream');
            throw CSVUploadFailedException::validationFailedWithMessage("CSV VALIDATION ERROR: Failed to process CSV file");
        }
        
        fwrite($stream, $fileContent);
        rewind($stream);
        $employeesDataArray = [];

        $rowNumber = 0;
        $maxColumns = count($headerValues);
        
        while (($data = fgetcsv($stream, 0, ",")) !== false) {
            $rowNumber++;
            $dataCount = count($data);
            
            $this->getLogger()->error("getEmployeeArrayFromCSV - Row {$rowNumber}: {$dataCount} columns");
            
            // More lenient validation: allow rows with fewer columns (will be padded)
            // Only reject if row has MORE columns than expected (likely a formatting issue)
            if ($dataCount > $maxColumns) {
                fclose($stream);
                $rowType = $rowNumber === 1 ? 'header' : "data row {$rowNumber}";
                $this->getLogger()->error("getEmployeeArrayFromCSV - Row {$rowNumber} has too many columns: {$dataCount} > {$maxColumns}");
                throw CSVUploadFailedException::validationFailedWithMessage(
                    "CSV VALIDATION ERROR: {$rowType} has {$dataCount} columns, but maximum {$maxColumns} columns are allowed. Please check row {$rowNumber} for extra commas or formatting issues."
                );
            }
            
            // Pad data array to match header count if needed (for backward compatibility)
            while (count($data) < $maxColumns) {
                $data[] = null;
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
        
        $this->getLogger()->error('getEmployeeArrayFromCSV - Parsed ' . count($employeesDataArray) . ' rows');
        
        if (count($employeesDataArray) === 0) {
            $this->getLogger()->error('getEmployeeArrayFromCSV - No rows parsed from CSV');
            throw CSVUploadFailedException::validationFailedWithMessage("CSV VALIDATION ERROR: CSV file appears to be empty or invalid");
        }
        
        return $employeesDataArray;
    }
}
