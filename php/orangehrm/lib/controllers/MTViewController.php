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

require_once ROOT_PATH . '/lib/models/maintenance/bugs.php';
require_once ROOT_PATH . '/lib/models/maintenance/modules.php';
require_once ROOT_PATH . '/lib/models/maintenance/UserGroups.php';
require_once ROOT_PATH . '/lib/models/maintenance/Users.php';

class MTViewController {

	var $indexCode;
	var $message;
	var $pageID;
	var $headingInfo;


	function MTViewController() {

		/*if(!isset($_SESSION['fname'])) {

			header("Location: ./relogin.htm");
			exit();
		}*/
	}

	function xajaxObjCall($value,$mtcode,$cntrl) {

		switch ($mtcode) {

			case 'CPW' :
							$use   = new Users();
							$pass  = $use->filterChangeUsers($_SESSION['user']);
							if(md5($value) == $pass[0][2])
								return true;
							else
								return  false;
							break;
		}

	}



	function viewList($getArr,$postArr) {

		$form_creator = new FormCreator($getArr,$postArr);
		$form_creator ->formPath ='/mtview.php';

		if ((isset($getArr['mtcode'])) && ($getArr['mtcode'] != '')) {
			$form_creator ->popArr['headinginfo'] = $this ->getHeadingInfo(trim($getArr['mtcode']));
		}

		$form_creator ->popArr['currentPage'] = $currentPage =(isset($postArr['pageNO'])) ? (int)$postArr['pageNO'] : 1;

		if (isset($postArr['captureState'])&& ($postArr['captureState']=="SearchMode"))
	    {
			$choice=$postArr['loc_code'];
		    $strName=trim($postArr['loc_name']);
		    $form_creator ->popArr['message'] = $this ->  getInfo(trim($getArr['mtcode']),$currentPage,$strName,$choice);
	    } else
			$form_creator ->popArr['message'] = $this ->  getInfo(trim($getArr['mtcode']),$currentPage);


   		if (isset($postArr['captureState'])&& ($postArr['captureState']=="SearchMode"))
			$form_creator ->popArr['temp'] = $this ->  countList(trim($getArr['mtcode']),$strName,$choice);
		else
			$form_creator ->popArr['temp'] = $this ->  countList(trim($getArr['mtcode']));

		$form_creator->display();
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
		}else  if(($this->indexCode) == 'CPW') {

			$this-> user = new Users();
			$message = $this->user-> getListOfUsers($pageNO,$schStr,$mode);
			return $message;

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
		}else if (($this->indexCode) == 'CPW') {

			$this-> user = new Users();
			$message = $this->user-> countUsers($schStr,$mode);

			return $message;
		}


	}

function reDirect($getArr,$object = null) {

		$form_creator = new FormCreator($getArr);

		switch ($getArr['mtcode']) {


			case 'BUG' :	
							$screenParam = array('uniqcode' => 'BUG');
		                  	$tokenGenerator = CSRFTokenGenerator::getInstance();
		                  	$tokenGenerator->setKeyGenerationInput($screenParam);
		                  	$token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
		                  	
							$form_creator ->formPath = '/templates/maintenance/bugs.php';
							$bug = new Bugs();

							$form_creator->popArr['module'] = $bug->getAlias('module');
							$form_creator ->popArr['token'] = $token;
							break;

			case 'DVR' :	$form_creator ->formPath = '/templates/maintenance/dbversions.php';
							$dbvers = new DbVersions();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $dbvers->getLastRecord();
							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $dbvers->filterdbVersions($getArr['id']);
							}

							break;

		   case 'MOD'  :	$form_creator ->formPath = '/templates/maintenance/modules.php';
							$modls = new Modules();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['version'] = $modls->getVersionList();
							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $modls->filterModules($getArr['id']);
								$form_creator ->popArr['version'] = $modls->getVersionList();
							}

							break;

		    case 'USG'  :	$form_creator ->formPath = '/templates/maintenance/usergroups.php';
							$usrgrp = new UserGroups();

							if($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $usrgrp->filterUserGroups($getArr['id']);

							}

							break;

			case 'VER'  :	$form_creator ->formPath = '/templates/maintenance/versions.php';
							$vers = new Versions();
							//$form_creator ->popArr['date'] = $vers->getDate();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $vers->getLastRecord();
								$form_creator ->popArr['db']    = $vers ->getdbVersionList();
								$form_creator ->popArr['file']  = $vers ->getFileVersionList();

							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $vers->filterVersions($getArr['id']);
								$form_creator ->popArr['db']    = $vers ->getdbVersionList();
								$form_creator ->popArr['file']  = $vers ->getFileVersionList();
							}
					         break;

			case 'FVR' :	$form_creator ->formPath = '/templates/maintenance/fileversions.php';
							$filever = new fileVersions();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $filever->getLastRecord();
								$form_creator ->popArr['modlist'] = $filever->getModuleList();
							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $filever->filterfileVersions($getArr['id']);
								$form_creator ->popArr['modlist'] = $filever->getModuleList();
							}

							break;

			case 'USR' :	$form_creator ->formPath = '/templates/maintenance/users.php';
							$user= new Users();

							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['emplist'] = $user->getEmployeeCodes();
								$form_creator ->popArr['uglist'] = $user->getUserGroupCodes();
							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $user->filterUsers($getArr['id']);
								$form_creator ->popArr['emplist'] = $user->getEmployeeCodes();
								$form_creator ->popArr['uglist'] = $user->getUserGroupCodes();
								//$form_creator ->popArr['editArr'] = $user->filterChangeUsers($getArr['id']);
							}

							break;

			case 'CPW' :	$form_creator ->formPath = '/templates/maintenance/changeusers.php';
							$chuser= new Users();

							if($getArr['capturemode'] == 'updatemode') {

								$form_creator ->popArr['emplist'] = $chuser->getEmployeeCodes();
								//$form_creator ->popArr['uglist'] = $chuser->getUserGroupCodes();
								$form_creator ->popArr['editArr'] = $chuser->filterChangeUsers($getArr['id']);
							}

							break;

			case 'UGR' :	$form_creator ->formPath = '/templates/maintenance/ugrights.php';
								$urights = new Rights();
								$usergroup = new UserGroups();

							$form_creator->popArr['ugDet'] = $usergroup->filterUserGroups($getArr['id']);
							$form_creator->popArr['modlist'] = $urights->getAllModules();

							if(isset($getArr['editID'])) {
								    $arr[0]=$getArr['id'];
								    $arr[1]=$getArr['editID'];
								$form_creator->popArr['editArr'] = $urights->filterRights($arr);

							} else {
								$form_creator->popArr['modlistUnAss'] = $urights->getModuleCodes($getArr['id']);
							}

							$form_creator->popArr['modlistAss'] = $urights->getAssRights($getArr['id']);

							break;

		}
		$form_creator->display();
	}

	function assignData($index,$object,$action) {

			switch ($index) {

				case 'UGR'  :		$ugrights = new Rights();
									$ugrights = $object;
									if($action == 'ADD')
										$ugrights->addRights();
									elseif($action == 'EDIT')
										$ugrights->updateRights();
									break;
			}
	}

	function delAssignData($index,$postArr,$getArr) {

			switch ($index) {

				case 'UGR'  :
									$urights = new Rights();

								    $urights->clearRights($getArr['id']);
								      break;
			}
	}

	function addData($index,$object) {

		switch ($index) {

			   case 'BUG'  :	    $bug = new Bugs();
									$bug = $object;
									$res = $bug -> addBugs();
									break;

			   case 'DVR'  :	    $dbvers = new DbVersions();
									$dbvers = $object;
									$res = $dbvers -> adddbVersions();
									break;

			   case 'MOD'  :	    $modls = new Modules();
									$modls = $object;
									$res = $modls -> addModules();
									break;

				case 'USG' :	    $usrgrp = new UserGroups();
									$usrgrp = $object;
									$res = $usrgrp -> addUserGroups();
									$id = $usrgrp->getUserGroupID();
									break;

				case 'VER' :	    $vers = new Versions();
									$vers = $object;
									$res = $vers -> addVersions();
									break;

				case 'FVR'  :		$filever = new fileVersions();
									$filever = $object;
									$res = $filever -> addfileVersions();
									break;

				case 'USR'  :		$user= new Users();
									$user = $object;
									$res = $user -> addUsers();
									break;
			}


			// Checking whether the $message Value returned is 1 or 0
			if ($res) {

				switch ($index) {

					case 'BUG' :
								$showMsg = "Bug%Sent,%Processed";

								$mtcode = $index;
								header("Location:./CentralController.php?message=$showMsg&mtcode=$mtcode&capturemode=addmode");

								break;

					case 'USG' : header("Location: ./CentralController.php?mtcode=UGR&id=$id");
								break;

					default :
								$showMsg = "Addition%Successful!";

								$mtcode = $index;
								header("Location: ./CentralController.php?message=$showMsg&mtcode=$mtcode&VIEW=MAIN");

				}

			} else {

				switch ($index) {

					case 'BUG' :
								$showMsg = "Bug%Sent,%Rejected";

								$mtcode = $index;
								header("Location:./CentralController.php?message=$showMsg&mtcode=$mtcode&capturemode=addmode");

						break;

					default :
						$showMsg = "Addition%Unsuccessful!";

						$mtcode = $index;
						header("Location: ./CentralController.php?msg=$showMsg&capturemode=addmode&mtcode=$mtcode");
						break;
				}
			}
		}


	function updateData($index,$id,$object) {

			switch ($index) {

			   case 'BUG'  :	    $bug = new Bugs();
									$bug = $object;
									$res = $bug -> updateBugs();
									break;

				case 'DVR'  :		$dbvers = new DbVersions();
									$dbvers = $object;
									$res = $dbvers -> updatedbVersions();
									break;

				case 'MOD'  :	    $modls = new Modules();
									$modls = $object;
									$res = $modls -> updateModules();
									break;

				case 'USG'  :	    $usrgrp = new UserGroups();
									$usrgrp = $object;
									$res = $usrgrp -> updateUserGroups();
									break;

				case 'VER'  :	    $vers = new Versions();
									$vers = $object;
									$res = $vers -> updateVersions();
									break;

				case 'FVR'  :		$filever = new fileVersions();
									$filever = $object;
									$res = $filever -> updatefileVersions();
									break;

				case 'USR'  :		$user= new Users();
									$user = $object;
									$res = $user -> updateUsers();
									break;

				case 'CPW'  :       $chuser = new Users();
									$chuser =$object;
									$res = $chuser -> updateChangeUsers();
									break;
			}

			// Checking whether the $message Value returned is 1 or 0
			if(is_array($res)) {
				$addRes = $res[0];
				$emailRes = $res[1];

				if($addRes) {
					$showMsg = "UPDATE_SUCCESS";

					if($emailRes)
						$showMsg .= "Email%Sent";
					else
						$showMsg .= "Email%Failure";

					$mtcode = $index;
					header("Location: ./CentralController.php?message=$showMsg&mtcode=$mtcode&VIEW=MAIN");

				} else {
					$showMsg = "UPDATE_FAILURE";

					if($emailRes)
						$showMsg .= "Email%Sent";
					else
						$showMsg .= "Email%Failure";

					$mtcode = $index;
					header("Location: ./CentralController.php?msg=$showMsg&capturemode=addmode&mtcode=$mtcode");
				}

			} elseif($res) {

				$showMsg = "UPDATE_SUCCESS"; //If $message is 1 setting up the

				$mtcode = $index;

				if ($mtcode == 'CPW') {
					header("Location: ./CentralController.php?msg=$showMsg&id=$id&capturemode=updatemode&mtcode=$mtcode");
				} else {
					header("Location: ./CentralController.php?message=$showMsg&mtcode=$mtcode&VIEW=MAIN");
				}
			} else {

				$showMsg = "UPDATE_FAILURE";

				$mtcode = $index;

				if ($mtcode == 'CPW') {
					header("Location: ./CentralController.php?msg=$showMsg&id=$id&capturemode=updatemode&mtcode=$mtcode");
				} else {
					header("Location: ./CentralController.php?msg=$showMsg&id=$id&capturemode=updatemode&mtcode=$mtcode");
				}
			}

	}

}
?>
