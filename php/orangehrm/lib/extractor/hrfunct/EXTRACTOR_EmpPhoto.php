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

require_once ROOT_PATH . '/lib/models/hrfunct/EmpPhoto.php';

class EXTRACTOR_EmpPhoto {

	function EXTRACTOR_EmpPhoto() {

		$this->photo = new EmpPicture();
	}

	function parseData() {

			if($_FILES['photofile']['size']>0 && stristr($_FILES['photofile']['type'],'image') != false) {
					//file info
					$fileName = $_FILES['photofile']['name'];
					$tmpName  = $_FILES['photofile']['tmp_name'];
					$fileSize = $_FILES['photofile']['size'];
					$fileType = $_FILES['photofile']['type'];

                    if (strlen($fileName) > 100) {
                        $fileName = substr($fileName, 0, 100);
                    }
                    
					//file read
					$contents = file_get_contents($tmpName);

					$this->photo->setEmpPicture($contents);
					$this->photo->setEmpFilename($fileName);
					$this->photo->setEmpPicType($fileType);
					$this->photo->setEmpPicSize($fileSize);

					return $this->photo;
			} else {
				return null;
			}
	}

}
?>
