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
class PimCsvImportForm extends BaseForm {

	private $pimCsvDataImportService;
	
	public function getPimCsvDataImportService() {
		if (is_null($this->pimCsvDataImportService)) {
			$this->pimCsvDataImportService = new PimCsvDataImportService();
		}
		return $this->pimCsvDataImportService;
	}

	public function configure() {

		$this->setWidgets(array(
		    'csvFile' => new sfWidgetFormInputFile(),
		));

		$this->setValidators(array(
		    'csvFile' => new sfValidatorFile(array('required' => false)),
		));
		$this->widgetSchema->setNameFormat('pimCsvImport[%s]');
	}

	public function save() {

		$file = $this->getValue('csvFile');
		if (!empty($file)) {
			if (!($this->isValidResume($file))) {
				$resultArray['messageType'] = 'csvimport.warning';
				$resultArray['message'] = __('Failed to Import: File Type Not Allowed');
				return $resultArray;
			}
			return $this->getPimCsvDataImportService()->import($file);
		}
	}

	public function isValidResume($file) {

		$validFile = false;
		$originalName = $file->getOriginalName();
		$fileType = $file->getType();
		$allowedImageTypes[] = "text/csv";
		$allowedImageTypes[] = 'text/comma-separated-values';
		$allowedImageTypes[] = "application/csv";
		if (($file instanceof sfValidatedFile) && $originalName != "") {
			if (in_array($fileType, $allowedImageTypes)) {
				$validFile = true;
			} else if ($file->getOriginalExtension() == '.csv') {
				$validFile = true;
			}
		}

		return $validFile;
	}

}

?>
