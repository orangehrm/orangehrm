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

require_once ROOT_PATH . '/lib/models/hrfunct/EmpPassPort.php';

class EXTRACTOR_EmpPassPort {

	private $empId;
	private $empPPSeqNo;
	private $empNationality;
	private $empI9Status;
	private $empI9ReviewDat;
	private $empPPIssDat;
	private $empPPExpDat;
	private $emppassportflag;
	private $emppassComm;
	private $empPPNo;


	public function __construct() {

		$this->pport = new EmpPassPort();
	}

	public function parseData($postArr) {

		$postArr['txtI9ReviewDat']=LocaleUtil::getInstance()->convertToStandardDateFormat($postArr['txtI9ReviewDat']);
		$postArr['txtPPIssDat']=LocaleUtil::getInstance()->convertToStandardDateFormat($postArr['txtPPIssDat']);
		$postArr['txtPPExpDat']=LocaleUtil::getInstance()->convertToStandardDateFormat($postArr['txtPPExpDat']);

		$this->pport->setEmpId($postArr['txtEmpID']);
		$this->pport->setEmpPPSeqNo(trim($postArr['txtPPSeqNo']));
		$this->pport->setEmpPPNo(trim($postArr['txtPPNo']));
		$this->pport->setEmpPPIssDat(self::_handleEmptyDates($postArr['txtPPIssDat']));
		$this->pport->setEmpPPExpDat(self::_handleEmptyDates($postArr['txtPPExpDat']));
		$this->pport->setEmpPPComment(trim($postArr['txtComments']));
		$this->pport->setEmppassportflag($postArr['PPType']);
		$this->pport->setEmpI9Status($postArr['txtI9status']);
		$this->pport->setEmpI9ReviewDat(self::_handleEmptyDates($postArr['txtI9ReviewDat']));
		$this->pport->setEmpNationality($postArr['cmbPPCountry']);

		return $this->pport;
	}

	public function reloadData($postArr) {

		$postArr['txtI9ReviewDat']=LocaleUtil::getInstance()->convertToStandardDateFormat($postArr['txtI9ReviewDat']);
		$postArr['txtPPIssDat']=LocaleUtil::getInstance()->convertToStandardDateFormat($postArr['txtPPIssDat']);
		$postArr['txtPPExpDat']=LocaleUtil::getInstance()->convertToStandardDateFormat($postArr['txtPPExpDat']);

		$this->txtEmpID		=	($postArr['txtEmpID']);
		$this->txtPPSeqNo	=	(trim($postArr['txtPPSeqNo']));
		$this->txtPPNo		=	(trim($postArr['txtPPNo']));
		$this->txtPPIssDat	=	self::_handleEmptyDates($postArr['txtPPIssDat']);
		$this->txtPPExpDat	=	self::_handleEmptyDates($postArr['txtPPExpDat']);
		$this->txtComments	=	(trim($postArr['txtComments']));
		$this->PPComment	=	(trim($postArr['PPComment']));
		$this->txtI9status	=	($postArr['txtI9status']);
		$this->PPType		=	($postArr['PPType']);
		$this->cmbPPCountry	=	($postArr['cmbPPCountry']);
		$this->txtI9ReviewDat	= self::_handleEmptyDates($postArr['txtI9ReviewDat']);

		return $this;
	}

	private static function _handleEmptyDates($date) {

		$date = trim($date);

	    if ($date == "" || $date == "YYYY-mm-DD" || $date == "0000-00-00") {
			return "null";
	    } else {
	        return "'".$date."'";
	    }

	}

}
?>
