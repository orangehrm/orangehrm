<?php
/*
// OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
// all the essential functionalities required for any enterprise.
// Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com

// OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
// the GNU General Public License as published by the Free Software Foundation; either
// version 2 of the License, or (at your option) any later version.

// OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
// without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// See the GNU General Public License for more details.

// You should have received a copy of the GNU General Public License along with this program;
// if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
// Boston, MA  02110-1301, USA
*/

require_once ROOT_PATH . '/lib/models/eimadmin/JobTitle.php';

class EXTRACTOR_JobTitle {

	function EXTRACTOR_JobTitle() {

		$this->parent_jobtit = new JobTitle();
	}

	function parseAddData($postArr) {

			$this->parent_jobtit -> setJobName(trim($postArr['txtJobTitleName']));
			$this->parent_jobtit -> setJobDesc(trim($postArr['txtJobTitleDesc']));
			$this->parent_jobtit -> setJobComm(trim($postArr['txtJobTitleComments']));
			$this->parent_jobtit -> setJobSalGrd(trim($postArr['cmbPayGrade']));

			return $this->parent_jobtit;
	}

	function parseEditData($postArr) {

			$this->parent_jobtit -> setJobId($postArr['txtJobTitleID']);
			$this->parent_jobtit -> setJobName(trim($postArr['txtJobTitleName']));
			$this->parent_jobtit -> setJobDesc(trim($postArr['txtJobTitleDesc']));
			$this->parent_jobtit -> setJobComm(trim($postArr['txtJobTitleComments']));
			$this->parent_jobtit -> setJobSalGrd(trim($postArr['cmbPayGrade']));

			return $this->parent_jobtit;
	}

}
?>
