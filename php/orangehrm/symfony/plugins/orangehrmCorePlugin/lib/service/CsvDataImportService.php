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
class CsvDataImportService extends BaseService {

	public function import($file, $importType) {

		$factory = new CsvDataImportFactory();
		$instance = $factory->getImportClassInstance($importType);
		$rowsImported = 0;
		$row = 1;
		if (($handle = fopen($file->getTempName(), "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				$num = count($data);
				$temp = array();
				$row++;
				if ($row != 2) {
					for ($c = 0; $c < $num; $c++) {
						$temp[] = $data[$c];
					}
					$result = $instance->import($temp);
					if($result) {
						$rowsImported++;
					}
				}
			}
			fclose($handle);
		}
		return $rowsImported;
	}

}

?>
