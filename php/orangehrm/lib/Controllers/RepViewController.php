<?
require_once ROOT_PATH . '/lib/confs/Conf.php';
require_once ROOT_PATH . '/lib/dao/DMLFunctions.php';
require_once ROOT_PATH . '/lib/dao/SQLQBuilder.php';
require_once ROOT_PATH . '/lib/common/CommonFunctions.php';
require_once ROOT_PATH . '/lib/exception/ExceptionHandler.php';

require_once ROOT_PATH . '/lib/models/report/EmpReport.php';
require_once ROOT_PATH . '/lib/models/report/EmpRepUserGroups.php';
require_once ROOT_PATH . '/lib/models/report/ReportGenerator.php';


class RepViewController {
	
	function RepViewController() {
		
	}
	
	function viewList($getArr,$postArr) {

		$form_creator = new FormCreator($getArr,$postArr);
		$form_creator ->formPath ='/repview.php'; 
		
		if ((isset($getArr['repcode'])) && ($getArr['repcode'] != '')) {
			$form_creator ->popArr['headinginfo'] = $this ->getHeadingInfo(trim($getArr['repcode']));
		}
				 
		$form_creator ->popArr['currentPage'] = $currentPage =(isset($postArr['pageNO'])) ? (int)$postArr['pageNO'] : 1;
 				
		if (isset($postArr['captureState'])&& ($postArr['captureState']=="SearchMode")) {
			$choice=$postArr['loc_code'];
		    $strName=trim($postArr['loc_name']);
		    $form_creator ->popArr['message'] = $this ->  getInfo(trim($getArr['repcode']),$currentPage,$strName,$choice);
	    } else 
			$form_creator ->popArr['message'] = $this ->  getInfo(trim($getArr['repcode']),$currentPage);
			
   		if (isset($postArr['captureState'])&& ($postArr['captureState']=="SearchMode")) 				
			$form_creator ->popArr['temp'] = $this ->  countList(trim($getArr['repcode']),$strName,$choice);
		else
			$form_creator ->popArr['temp'] = $this ->  countList(trim($getArr['repcode']));
			
		$form_creator->display();
	}
	
    function delParser($indexCode,$arrList) {
        $this->indexCode=$indexCode;

        switch ($this->indexCode)  {
        	
        	case 'EMPDEF' :
	
	            $this->report = new EmpReport();
	            $this->report->delReports($arrList);
	            break;
		}
    }

    function assignData($index,$object) {
    	
    	switch($index) {
    		
    		case 'EMPVIEW' : 	$repgen = new ReportGenerator();
    							$repgen = $object;
    							    							
								$sqlQ = $repgen->reportQueryBuilder();
								
	  	 	
								$dbConnection = new DMLFunctions();
								$message2 = $dbConnection -> executeQuery($sqlQ);
										
								$i=0;
								while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {
										 	
									for($c=0;count($repgen->field)>$c;$c++)
									   	$arrayDispList[$i][$c] = $line[$c];
								   	$i++;
								    }
								
									$repgen->reportDisplay($arrayDispList);
									
									break;
    	}
    }
    
	function selectIndexId($pageNO,$schStr,$mode) {
		
		switch ($this->indexCode) {
					
		case 'EMPVIEW' :
		
			$this-> reports = new EmpReport();
			$message = $this-> reports -> getListofUserGroupReports($_SESSION['userGroup'],$pageNO,$schStr,$mode);
			return $message;
			break;
			
		case 'EMPDEF' :
		
			$this-> reports = new EmpReport();
			$message = $this-> reports ->  getListofUserGroupReports($_SESSION['userGroup'],$pageNO,$schStr,$mode);
			return $message;
			break;
		}
	}
	
	
	function getHeadingInfo($indexCode) {
		
		$this->indexCode = $indexCode;
		
		switch ($this->indexCode) {
		
		case 'EMPDEF' :
		
			$this->headingInfo = array ('Report ID','Report Name',1,'Employee Reports','Deletion might affect Company Hierarchy');
			return $this->headingInfo;
			break;
						
		case 'EMPVIEW' :
		
			$this->headingInfo = array ('Report ID','Report Name',0,'Employee Reports','Deletion might affect Company Hierarchy');
			return $this->headingInfo;
			break;
        }

	}
	
	function getInfo($indexCode,$pageNO,$schStr='',$mode=0) {
	
		$this->indexCode = $indexCode;
		return $this->selectIndexId($pageNO,$schStr,$mode);
	
	}
	
	function countList($index,$schStr='',$mode=0) {
		
		$this->indexCode=$index;
					
		switch ($this->indexCode) {

			case 'EMPDEF' :
		
			$this-> report = new EmpReport();
			$message = $this-> report -> countReports($schStr,$mode);
			return $message;
			break;

			case 'EMPVIEW' :
		
			$this-> report = new EmpReport();
			$message = $this-> report -> countReports($schStr,$mode);
			return $message;
			break;
		}
	}

	function addData($index,$object) {
		
			switch ($index) {
				
				case 'EMPDEF'  :		$report = new EmpReport();
										$report = $object;
										$id = $report -> getRepID();
										$res = $report -> addReport();
										
										$repusg = new EmpRepUserGroup();
									
										$repusg -> setRepCode($id);
										$repusg -> setUserGroupID($_SESSION['userGroup']);
										$repusg -> addRepUserGroup();
										break;
			}
			
			// Checking whether the $message Value returned is 1 or 0
			if ($res) { 
				
				switch($index) {
					
					case 'EMPDEF' :
								header("Location: ./CentralController.php?repcode=RUG&id=$id");
								break;
								
					default:
								$showMsg = "Addition%Successful!"; //If $message is 1 setting up the 
								
								$repcode = $index;
								
								header("Location: ./CentralController.php?message=$showMsg&repcode=$repcode&VIEW=MAIN");
				}
				
			} else {
				
				$showMsg = "Addition%Unsuccessful!";
				
				$repcode = $index;
				header("Location: ./CentralController.php?msg=$showMsg&capturemode=addmode&repcode=$repcode");
			}
		}
		

	function updateData($index,$id,$object) {		
		
			switch ($index) {
				
				case 'EMPDEF'  :		$report = new EmpReport();
										$report = $object;
										$res = $report -> updateReport();
										break;
			}
									
			// Checking whether the $message Value returned is 1 or 0
			if ($res) { 
				$showMsg = "Updation%Successful!"; //If $message is 1 setting up the 

				$repcode = $index;
				
				header("Location: ./CentralController.php?message=$showMsg&repcode=$repcode&VIEW=MAIN");
			} else {
				
				$showMsg = "Updation%Unsuccessful!";
				
				$repcode = $index;
				header("Location: ./CentralController.php?msg=$showMsg&id=$id&capturemode=updatemode&repcode=$repcode");
			}
	}

	function addUserGroups($objectArr) {
		
		$repusg = new EmpRepUserGroup();
		
			for($c=0;count($objectArr)>$c;$c++) {
				$repusg = $objectArr[$c];
				$repusg -> addRepUserGroup();
			}
		
	}
	
	function delUserGroups($postArr,$getArr) {

			$repusg = new EmpRepUserGroup();
			
		      $arr[0]=$postArr['chkdel'];
		      $size = count($arr[0]);
		      
		      for($c=0 ; $size > $c ; $c++)
		          if($arr[0][$c]!=NULL)
		             $arr[1][$c]=$getArr['id'];
		
		      $repusg -> delRepUserGroup($arr);
    }
		
	
	function reDirect($getArr,$postArr,$object = null) {

		$form_creator = new FormCreator($getArr,$postArr);

		switch ($getArr['repcode']) {

			case 'EMPDEF' :	
							$form_creator ->formPath = '/templates/report/emprepinfo.php'; 
			
							$form_creator->popArr['arrAgeSim'] = array ('Less Than' => '>','Greater Than' =>'<','Range' =>'range');
							//$form_creator->popArr['arrEmpType']= array( 'Permanent', 'Expatriate', 'Contract', 'Temporary' , 'Others');
							
							$report = new EmpReport();
							$empinfo = new EmpInfo();
							$empqual = new EmpQualification();
							$jobtit   = new JobTitle();
							$salgrd = new SalaryGrades();
							$quli = new QualificationType;
							$empstat = new EmploymentStatus();

							$form_creator ->popArr['grdlist'] = $salgrd ->getSalGrdCodes();
							$form_creator-> popArr['typlist'] = $quli ->getQualificationTypeCodes();
							$form_creator-> popArr['deslist'] = $jobtit ->getJobTit();
							$form_creator-> popArr['arrEmpType'] = $empstat ->getEmpStat();
							
							
							if($getArr['capturemode'] == 'addmode') {
								$form_creator ->popArr['newID'] = $report->getLastRecord();
							} elseif($getArr['capturemode'] == 'updatemode') {
								$form_creator ->popArr['editArr'] = $edit = $report->filterReport($getArr['id']);
								$criteria_value = explode('|',$edit[0][2]);
								
							for($c=0;count($criteria_value)>$c;$c++) {
								$crit_data = explode("=",$criteria_value[$c]);
								$criteriaChkBox[$c] = $crit_data[0];
								
								for($d=1;count($crit_data)>$d;$d++)
									$crit_form_data[$crit_data[0]][$d-1] = $crit_data[$d];
							}
							
								$form_creator->popArr['editCriteriaChk'] = $criteriaChkBox;
								$form_creator->popArr['editCriteriaData'] = $crit_form_data;
								
								$form_creator->popArr['fieldList'] = explode('|',$edit[0][3]);
							}
							
							if($object != null){
								$form_creator-> popArr['empqual']      =   $empqual->getQualifications($object -> TypeCode);
							}
				
							break;
							
			case 'EMPVIEW' :
								$report = new EmpReport();
								$repgen = new ReportGenerator();

								$form_creator ->popArr['editArr'] = $edit = $report->filterReport($getArr['id']);
								
								$repgen ->repID = $edit[0][0];
								$repgen ->repName = $edit[0][1];
								
								$criteria = explode('|',$edit[0][2]);
								
								for($c=0;count($criteria)>$c;$c++) {
									$crit_value = explode("=",$criteria[$c]);
									
									$repgen -> criteria[$crit_value[0]] = '';
									for($d=1;count($crit_value)>$d;$d++)
										if($d==count($crit_value)-1)
											$repgen -> criteria[$crit_value[0]] .= $crit_value[$d];
										else
											$repgen -> criteria[$crit_value[0]] .= $crit_value[$d] . "|";
								}
								
								$field = explode('|',$edit[0][3]);
								
								for($c=0;count($field)>$c;$c++) {
									$repgen -> field[$field[$c]] = 1;
								}

								$sqlQ = $repgen->reportQueryBuilder();
	
								$dbConnection = new DMLFunctions();
								$message2 = $dbConnection -> executeQuery($sqlQ);
										
								$i=0;
								while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {
										 	
									for($c=0;count($repgen->field)>$c;$c++)
									   	$arrayDispList[$i][$c] = $line[$c];
								   	$i++;
								    }
								
								$repgen->reportDisplay($arrayDispList);
								return;
			
							break;
			
			case 'RUG' :	$form_creator ->formPath = '/templates/report/repusg.php'; 
							$emprepgroup = new EmpRepUserGroup();
							
							$form_creator ->popArr['repDet'] = $emprepgroup -> getReportInfo();
							$form_creator->popArr['usgAll'] = $emprepgroup -> getAllUserGroups();
							$form_creator ->popArr['repUsgAss'] = $emprepgroup ->getAssignedUserGroup($getArr['id']);

							if(isset($getArr['addForm']) && $getArr['addForm']=='ADD')
								$form_creator ->popArr['usgUnAss'] = $emprepgroup ->getUnAssUserGroups($getArr['id']);
								
							break;

		}
				
		$form_creator->display();							
	}
		
}