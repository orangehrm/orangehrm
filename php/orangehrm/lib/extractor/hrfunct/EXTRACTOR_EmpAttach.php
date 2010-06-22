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

require_once ROOT_PATH . '/lib/models/hrfunct/EmpAttach.php';

class EXTRACTOR_EmpAttach {
	
	var $txtEmpID;
	var $seqNO;
	var $txtAttDesc;
	
	function EXTRACTOR_EmpAttach() {

		$this->attachment = new EmpAttach();
	}

	function parseData($postArr) {	
			$this->attachment->setEmpId(CommonFunctions::cleanParam($postArr['txtEmpID']));
			if (isset($_FILES['ufile']) && ($_FILES['ufile']['size']>0)) {
					//file info
					$fileName = $_FILES['ufile']['name'];
					$tmpName  = $_FILES['ufile']['tmp_name'];
					$fileSize = $_FILES['ufile']['size'];
					$fileType = $_FILES['ufile']['type'];

                    if (strlen($fileName) > 100) {
                        $fileName = substr($fileName, 0, 100);
                    }

					//file read
					$fp = fopen($tmpName,'r');
					$contents = fread($fp,filesize($tmpName));
					$contents = addslashes($contents);
					fclose($fp);
					
					if(!get_magic_quotes_gpc())
						$fileName=addslashes($fileName);
						
					$this->attachment->setEmpAttFilename($fileName);
					$this->attachment->setEmpAttSize($fileSize);
					$this->attachment->setEmpAttachment($contents);
					$this->attachment->setEmpAttType($fileType);
					$this->attachment->setEmpAttId($this->attachment->getLastRecord(CommonFunctions::cleanParam($postArr['txtEmpID'])));
			} elseif($postArr['attSTAT'] == "EDIT") {
					$this->attachment->setEmpAttId(CommonFunctions::cleanParam($postArr['seqNO']));
			}  else return null;
				
				
				$this->attachment->setEmpAttDesc(CommonFunctions::cleanParam($postArr['txtAttDesc'], 200));
				
				return $this->attachment;
			
	}

	function reloadData($postArr) {	
	
		$this->txtEmpID		=	CommonFunctions::cleanParam($postArr['txtEmpID']);
		$this->txtAttDesc	=	CommonFunctions::cleanParam($postArr['txtAttDesc']);
		$this->seqNO		=	CommonFunctions::cleanParam($postArr['seqNO']);
		
		return $this;
	}
}
?>
