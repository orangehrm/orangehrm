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

require_once ROOT_PATH . '/lib/exception/ExceptionHandler.php';
require_once ROOT_PATH . '/lib/models/report/EmpReport.php';
require_once ROOT_PATH . '/lib/common/LocaleUtil.php';
require_once ROOT_PATH . '/lib/common/CommonFunctions.php';

class EXTRACTOR_EmpReport {


	function EXTRACTOR_EmpReport() {

		$this->empreport = new EmpReport();
	}

	function parseAddData($postArr) {

			$this->empreport->setRepName(CommonFunctions::escapeHtml(trim($postArr['txtRepName'])));

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

			// Service Period

			if(in_array('SERPIR',$criteriaArr)){

				switch ($postArr['cmbSerPerCode']) {

					case '<' 	 :	$criteriaStr .= '|SERPIR=<=' .$postArr['Service1'];
									 break;

					case '>' 	 :	$criteriaStr .= '|SERPIR=>='.$postArr['Service1'];
									 break;

					case 'range' :	$criteriaStr .= '|SERPIR=range='.$postArr['Service1'] . '=' .$postArr['Service2'];

				}

			}

			// Service Period Ends

			// Joined Date

			if(in_array('JOIDAT',$criteriaArr)){

				switch ($postArr['cmbJoiDatCode']) {

					case '<' 	 :	$criteriaStr .= '|JOIDAT=<=' . LocaleUtil::getInstance()->convertToStandardDateFormat($postArr['Join1']);
									 break;

					case '>' 	 :	$criteriaStr .= '|JOIDAT=>=' . LocaleUtil::getInstance()->convertToStandardDateFormat($postArr['Join1']);
									 break;

					case 'range' :	$criteriaStr .= '|JOIDAT=range=' . LocaleUtil::getInstance()->convertToStandardDateFormat($postArr['Join1']) .
					 				'=' . LocaleUtil::getInstance()->convertToStandardDateFormat($postArr['Join2']);

				}

			}

			// Joined Date Ends

			if(in_array('JOBTITLE',$criteriaArr)){
				$criteriaStr .= '|JOBTITLE=' . $postArr['cmbDesig'];
			}

			if(in_array('LANGUAGE',$criteriaArr)){
				$criteriaStr .= '|LANGUAGE=' . $postArr['cmbLanguage'];
			}

			if(in_array('SKILL',$criteriaArr)){
				$criteriaStr .= '|SKILL=' . $postArr['cmbSkill'];
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
			$this->empreport->setRepName(CommonFunctions::escapeHtml(trim($postArr['txtRepName'])));

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

			// Joined Date

			if(in_array('JOIDAT',$criteriaArr)){

				switch ($postArr['cmbJoiDatCode']) {

					case '<' 	 :	$criteriaStr .= '|JOIDAT=<=' . LocaleUtil::getInstance()->convertToStandardDateFormat($postArr['Join1']);
									 break;

					case '>' 	 :	$criteriaStr .= '|JOIDAT=>=' . LocaleUtil::getInstance()->convertToStandardDateFormat($postArr['Join1']);
									 break;

					case 'range' :	$criteriaStr .= '|JOIDAT=range=' . LocaleUtil::getInstance()->convertToStandardDateFormat($postArr['Join1']) .
									'=' . LocaleUtil::getInstance()->convertToStandardDateFormat($postArr['Join2']);

				}

			}

			// Joined Date Ends

			if(in_array('JOBTITLE',$criteriaArr)){
				$criteriaStr .= '|JOBTITLE=' . $postArr['cmbDesig'];
			}

			if(in_array('LANGUAGE',$criteriaArr)){
				$criteriaStr .= '|LANGUAGE=' . $postArr['cmbLanguage'];
			}

			if(in_array('SKILL',$criteriaArr)){
				$criteriaStr .= '|SKILL=' . $postArr['cmbSkill'];
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
