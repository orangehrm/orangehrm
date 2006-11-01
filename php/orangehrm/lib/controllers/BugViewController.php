<?php
/*
// OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures 
// all the essential functionalities required for any enterprise. 
// Copyright (C) 2006 hSenid Software International Pvt. Ltd, http://www.hsenid.com

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

require_once OpenSourceEIM . '/lib/Models/bugs/bugs.php';
require_once OpenSourceEIM . '/lib/Models/bugs/modules.php';
require_once OpenSourceEIM . '/lib/Models/bugs/UserGroups.php';
require_once OpenSourceEIM . '/lib/Models/bugs/Users.php';

class BugViewController {

	var $indexCode;
	var $message;
	var $pageID;
	var $headingInfo;
	
		
	function BugViewController() {
		
	}
	
    function delParser($indexCode,$arrList) {
        $this->indexCode=$indexCode;

        if (($this->indexCode) == 'MOD') {

			$this-> modules = new Modules();
			$this-> modules->delModules($arrList);

		} else if (($this->indexCode) == 'VER') {

			$this-> versions = new Versions();
			$this-> versions->delVersions($arrList);

		} else if (($this->indexCode) == 'DVR') {

			$this-> dbversions = new dbVersions();
			$this-> dbversions->deldbVersions($arrList);

		} else if (($this->indexCode) == 'FVR') {

			$this-> fileversions = new fileVersions();
			$this-> fileversions->delFileVersions($arrList);

		} else if (($this->indexCode) == 'USG') {

			$this-> userGroups = new UserGroups();
			$this-> userGroups->delUserGroups($arrList);

		} else if (($this->indexCode) == 'USR') {

			$this-> user = new Users();
			$this-> user->delUsers($arrList);
		}
    }


	function getInfo($indexCode,$pageNO,$schStr='',$mode=0) {
		$this->indexCode = $indexCode;
					
	if (($this->indexCode) == 'BUG') {
			
			$this-> buginfo = new Bugs();
			$message = $this-> buginfo -> getListOfBugs($pageNO,$schStr,$mode);
			
			return $message;			

		}else if (($this->indexCode) == 'MOD') {
			
			$this-> moduleinfo = new Modules();
			$message = $this->moduleinfo-> getListOfModules($pageNO,$schStr,$mode);
			
			return $message;			
		}else if (($this->indexCode) == 'VER') {
			
			$this-> versioninfo = new Versions();
			$message = $this->versioninfo-> getListOfVersions($pageNO,$schStr,$mode);
			
			return $message;			
		}else if (($this->indexCode) == 'DVR') {
			
			$this-> dbversioninfo = new dbVersions();
			$message = $this->dbversioninfo-> getListOfdbVersions($pageNO,$schStr,$mode);
			
			return $message;			
		}else if (($this->indexCode) == 'FVR') {
			
			$this-> fileversioninfo = new fileVersions();
			$message = $this->fileversioninfo-> getListOfFileVersions($pageNO,$schStr,$mode);
			
			return $message;			
		}else if (($this->indexCode) == 'USG') {
			
			$this-> userGroups = new UserGroups();
			$message = $this->userGroups-> getListOfUserGroups($pageNO,$schStr,$mode);
			
			return $message;			
		}else if (($this->indexCode) == 'USR') {
			
			$this-> user = new Users();
			$message = $this->user-> getListOfUsers($pageNO,$schStr,$mode);
			
			return $message;			
		}						
	}
	
	function getPageID($indexCode) {
	
		$this->indexCode = $indexCode;	
							
		if (($this->indexCode) == 'BUG') {
					
			$this->pageID = './bugs';
			return $this->pageID;
			
		}else if (($this->indexCode) == 'MOD') {
					
			$this->pageID = './modules';
			return $this->pageID;
			
		}else if (($this->indexCode) == 'VER') {
					
			$this->pageID = './versions';
			return $this->pageID;
			
		}else if (($this->indexCode) == 'DVR') {
					
			$this->pageID = './dbversions';
			return $this->pageID;
			
		}else if (($this->indexCode) == 'FVR') {
					
			$this->pageID = './fileversions';
			return $this->pageID;

		}else if (($this->indexCode) == 'USG') {
					
			$this->pageID = './usergroups';
			return $this->pageID;

		}else if (($this->indexCode) == 'USR') {
					
			$this->pageID = './users';
			return $this->pageID;
		}
	} 
	
	
	function getHeadingInfo($indexCode) {
		
		$this->indexCode = $indexCode;					
		
		if (($this->indexCode) == 'BUG') {

			$this->headingInfo = array ('Bug ID','Bug Name',0,'Bugs');
			return $this->headingInfo;
        }else if (($this->indexCode) == 'MOD') {

			$this->headingInfo = array ('Module ID','Module Name',1,'Modules');
			return $this->headingInfo;
        }  
        else if (($this->indexCode) == 'VER') {

			$this->headingInfo = array ('Version ID','Version Name',1,'Versions');
			return $this->headingInfo;
        }else if (($this->indexCode) == 'DVR') {

			$this->headingInfo = array ('dbVersion ID','dbVersion Name',1,'DB Versions');
			return $this->headingInfo;
        }else if (($this->indexCode) == 'FVR') {

			$this->headingInfo = array ('File Version ID','File Version Name',1,'File Versions');
			return $this->headingInfo;

        }else if (($this->indexCode) == 'USG') {

			$this->headingInfo = array ('User Group ID','User Group Name',1,'User Groups');
			return $this->headingInfo;

        }else if (($this->indexCode) == 'USR') {

			$this->headingInfo = array ('User ID','User Name',1,'Users');
			return $this->headingInfo;
        }                

	}	
	
	function getPageName($indexCode) {
		
		$this->indexCode = $indexCode;
		return $this->getPageID();
	}


	function countList($indexCode,$schStr='',$mode=0) {
		$this->indexCode = $indexCode;
					
	if (($this->indexCode) == 'BUG') {
			
			$this-> buginfo = new Bugs();
			$message = $this-> buginfo -> countBugs($schStr,$mode);
			
			return $message;			

		}else if (($this->indexCode) == 'MOD') {
			
			$this-> moduleinfo = new Modules();
			$message = $this->moduleinfo-> countModules($schStr,$mode);
			
			return $message;			
		}else if (($this->indexCode) == 'VER') {
			
			$this-> versioninfo = new Versions();
			$message = $this->versioninfo-> countVersions($schStr,$mode);
			
			return $message;			
		}else if (($this->indexCode) == 'DVR') {
			
			$this-> dbversioninfo = new dbVersions();
			$message = $this->dbversioninfo-> countdbVersions($schStr,$mode);
			
			return $message;			
		}else if (($this->indexCode) == 'FVR') {
			
			$this-> fileversioninfo = new fileVersions();
			$message = $this->fileversioninfo-> countFileVersions($schStr,$mode);
			
			return $message;			
		}else if (($this->indexCode) == 'USG') {
			
			$this-> userGroups = new UserGroups();
			$message = $this->userGroups-> countUserGroups($schStr,$mode);
			
			return $message;			

		}else if (($this->indexCode) == 'USR') {
			
			$this-> user = new Users();
			$message = $this->user-> countUsers($schStr,$mode);
			
			return $message;			
		}						
	}
	
}
?>
