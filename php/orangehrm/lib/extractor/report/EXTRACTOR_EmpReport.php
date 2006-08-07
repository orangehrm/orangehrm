<?
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

require_once ROOT_PATH . '/lib/exception/ExceptionHandler.php';
require_once ROOT_PATH . '/lib/models/report/EmpReport.php';

class EXTRACTOR_EmpReport {
	
		
	function EXTRACTOR_EmpReport() {

		$this->empreport = new EmpReport();
	}

	function parseAddData($postArr) {	
			
			$this->empreport->setRepID($this->empreport->getLastRecord());
			$this->empreport->setRepName(trim($postArr['txtRepName']));
			
			$criteriaArr = $postArr['chkcriteria'];
			
			$criteriaStr = '';
			
			if(in_array('EMPNO',$criteriaArr)){
				$criteriaStr .= 'EMPNO=' . $postArr['txtRepEmpID'];
			}
			if(in_array('AGE',$criteriaArr)){
				
				switch ($postArr['cmbAgeCode']) {
					
					case '<' 	 :	$criteriaStr .= '|AGE=<=' .$postArr['txtEmpAge1'];
									 break;  
									 
					case '>' 	 :	$criteriaStr .= '|AGE=>=' .$postArr['txtEmpAge1'];
									 break; 
									 
					case 'range' :	$criteriaStr .= '|AGE=range='.$postArr['txtEmpAge1'] .'=' .$postArr['txtEmpAge2'];    
				}
			 
			}
			if(in_array('PAYGRD',$criteriaArr)){
				$criteriaStr .= '|PAYGRD=' . $postArr['cmbSalGrd'];
			}
			if(in_array('QUL',$criteriaArr)){
				$criteriaStr .= '|QUL=' . $postArr['TypeCode'];
			}
			if(in_array('EMPSTATUS',$criteriaArr)){
				$criteriaStr .= '|EMPSTATUS=' . $postArr['cmbEmpType'];
			}
			
			if(in_array('SERPIR',$criteriaArr)){
				
				switch ($postArr['cmbSerPerCode']) {
					
					case '<' 	 :	$criteriaStr .= '|SERPIR=<=' .$postArr['Service1'];
									 break;  
									 
					case '>' 	 :	$criteriaStr .= '|SERPIR=>='.$postArr['Service1'];
									 break; 
									 
					case 'range' :	$criteriaStr .= '|SERPIR=range='.$postArr['Service1'] . '=' .$postArr['Service2'];
								  
				}
			 
			}
				
			if(in_array('JOBTITLE',$criteriaArr)){
				$criteriaStr .= '|JOBTITLE=' . $postArr['cmbDesig'];
			}
			$this->empreport->setRepCriteriaDefString($criteriaStr);
			
			
			$field   = $postArr['checkfield'];
			$fieldStr = '';
			for($c=0;count($field)>$c;$c++)
				if($c==count($field)-1)
					$fieldStr = $fieldStr.$field[$c];
				else 
					$fieldStr = $fieldStr.$field[$c].'|';	
			
			$this->empreport->setRepFieldDefString($fieldStr);
			
			return $this->empreport;
	}	
			
			
	function parseEditData($postArr) {	

			$this->empreport->setRepID(trim($postArr['txtRepID']));
			$this->empreport->setRepName(trim($postArr['txtRepName']));
			
			$criteriaArr = $postArr['chkcriteria'];
			
			$criteriaStr = '';
			
			if(in_array('EMPNO',$criteriaArr)){
				$criteriaStr .= 'EMPNO=' . $postArr['txtRepEmpID'];
			}
			if(in_array('AGE',$criteriaArr)){
				
				switch ($postArr['cmbAgeCode']) {
					
					case '<' 	 :	$criteriaStr .= '|AGE=<=' .$postArr['txtEmpAge1'];
									 break;  
									 
					case '>' 	 :	$criteriaStr .= '|AGE=>=' .$postArr['txtEmpAge1'];
									 break; 
									 
					case 'range' :	$criteriaStr .= '|AGE=range='.$postArr['txtEmpAge1'] .'=' .$postArr['txtEmpAge2'];    
									 
				
								  
				}
			 
			}
			if(in_array('PAYGRD',$criteriaArr)){
				$criteriaStr .= '|PAYGRD=' . $postArr['cmbSalGrd'];
			}
			if(in_array('QUL',$criteriaArr)){
				$criteriaStr .= '|QUL=' . $postArr['TypeCode'];
			}
			if(in_array('EMPSTATUS',$criteriaArr)){
				$criteriaStr .= '|EMPSTATUS=' . $postArr['cmbEmpType'];
			}
			
			if(in_array('SERPIR',$criteriaArr)){
				
				switch ($postArr['cmbSerPerCode']) {
					
					case '<' 	 :	$criteriaStr .= '|SERPIR=<=' .$postArr['Service1'];
									 break;  
									 
					case '>' 	 :	$criteriaStr .= '|SERPIR=>='.$postArr['Service1'];
									 break; 
									 
					case 'range' :	$criteriaStr .= '|SERPIR=range='.$postArr['Service1'] . '=' .$postArr['Service2'];
								  
				}
			 
			}
					
			if(in_array('JOBTITLE',$criteriaArr)){
				$criteriaStr .= '|JOBTITLE=' . $postArr['cmbDesig'];
			}
			$this->empreport->setRepCriteriaDefString($criteriaStr);
			
			
			$field   = $postArr['checkfield'];
			$fieldStr = '';
			for($c=0;count($field)>$c;$c++)
				if($c==count($field)-1)
					$fieldStr = $fieldStr.$field[$c];
				else 
					$fieldStr = $fieldStr.$field[$c].'|';	
			
			$this->empreport->setRepFieldDefString($fieldStr);
			
			return $this->empreport;
		
	}


	function reloadData($postArr) {	
						
			$this->TypeCode			=	(trim($postArr['TypeCode']));
			
			return $this;
	}
}
	
?>
