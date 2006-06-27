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

require_once ROOT_PATH . '/lib/models/eimadmin/CorpTit.php';

class EXTRACTOR_CorpTit{
	
	function EXTRACTOR_CorpTit() {

		$this->parent_corptit = new CorpTitle();
	}

	function parseAddData($postArr) {	
			
			$this->parent_corptit -> setCtId($this->parent_corptit->getLastRecord());
			$this->parent_corptit -> setCtDesc(trim($postArr['txtCorpDesc']));
			$this->parent_corptit -> setCtTopLev(isset($postArr['chkCorpTopLev'])?'1':'0');
			$this->parent_corptit -> setCtHead(isset($postArr['chkCorpHead'])?'1':'0');
			$this->parent_corptit -> setCtNxtUpg(trim($postArr['cmbCorpNxtUpg']));
			$this->parent_corptit -> setCtHeadCnt(isset($postArr['txtCorpHeadCount']) ? trim($postArr['txtCorpHeadCount']) : '');
			$this->parent_corptit -> setCtSalGrdId(trim($postArr['cmbCorpSalGrd']));
			$this->parent_corptit -> setCtRevDat(null);
			$this->parent_corptit -> setCtHirachId(null);
			
			return $this->parent_corptit;
	}

	function parseEditData($postArr) {	
			
			$this->parent_corptit -> setCtId(trim($postArr['txtCorpID']));
			$this->parent_corptit -> setCtDesc(trim($postArr['txtCorpDesc']));
			$this->parent_corptit -> setCtTopLev(isset($postArr['chkCorpTopLev'])?'1':'0');
			$this->parent_corptit -> setCtHead(isset($postArr['chkCorpHead'])?'1':'0');
			$this->parent_corptit -> setCtNxtUpg(trim($postArr['cmbCorpNxtUpg']));
			$this->parent_corptit -> setCtHeadCnt(isset($postArr['txtCorpHeadCount']) ? trim($postArr['txtCorpHeadCount']) : '');
			$this->parent_corptit -> setCtSalGrdId(trim($postArr['cmbCorpSalGrd']));
			$this->parent_corptit -> setCtRevDat(null);
			$this->parent_corptit -> setCtHirachId(null);
			
			return $this->parent_corptit;
	}
	
}
?>
