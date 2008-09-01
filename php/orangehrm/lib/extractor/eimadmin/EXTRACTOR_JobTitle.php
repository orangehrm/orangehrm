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
	}

	function parseAddData($postArr) {
        return $this->_parseCommonData($postArr);
	}

	function parseEditData($postArr) {
        $jobTitle = $this->_parseCommonData($postArr);
        $jobTitle->setJobId($postArr['txtJobTitleID']);
		return $jobTitle;
	}

    /**
     * Parse common data for edit and update
     * @return JobTitle object
     */
    private function _parseCommonData($postArr) {
        $jobTitle = new JobTitle();
        $jobTitle->setJobName(CommonFunctions::escapeHtml(trim($postArr['txtJobTitleName'])));
        $jobTitle->setJobDesc(CommonFunctions::escapeHtml(trim($postArr['txtJobTitleDesc'])));
        $jobTitle->setJobComm(CommonFunctions::escapeHtml(trim($postArr['txtJobTitleComments'])));
        $jobTitle->setJobSalGrd(trim($postArr['cmbPayGrade']));

        $jobSpecId = trim($postArr['cmbJobSpecId']);
        if (CommonFunctions::isValidId($jobSpecId)) {
            $jobTitle->setJobSpecId($jobSpecId);
        }

        return $jobTitle;
    }

}
?>
