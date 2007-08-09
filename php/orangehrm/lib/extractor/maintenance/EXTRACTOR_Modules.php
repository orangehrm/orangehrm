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

require_once ROOT_PATH . '/lib/models/maintenance/modules.php';

class EXTRACTOR_Modules {

	function EXTRACTOR_Modules() {

		$this->new_module = new Modules();
	}

	function parseAddData($postArr) {

			$this->new_module ->setModuleName(trim($postArr['txtName']));
			$this->new_module ->setOwner(trim($postArr['txtOwner']));
			$this->new_module ->setOwnerEmail(trim($postArr['txtEmail']));
			$this->new_module ->setVersion(trim($postArr['cmbVersion']));
			$this->new_module ->setDescription(trim($postArr['txtDescription']));

			return $this->new_module;
	}

	function parseEditData($postArr) {

			$this->new_module ->setModuleId(trim($postArr['txtID']));
			$this->new_module ->setModuleName(trim($postArr['txtName']));
			$this->new_module ->setOwner(trim($postArr['txtOwner']));
			$this->new_module ->setOwnerEmail(trim($postArr['txtEmail']));
			$this->new_module ->setVersion(trim($postArr['cmbVersion']));
			$this->new_module ->setDescription(trim($postArr['txtDescription']));

			return $this->new_module;
	}

}
?>
