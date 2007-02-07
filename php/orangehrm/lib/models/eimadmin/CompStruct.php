<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 hSenid Software International Pvt. Ltd, http://www.hsenid.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 *
 * @author	:	S.H.Mohanjith <moha@mohanjith.net> <mohanjith@beyondm.net>
 * @Date		:	July 4th, 2006
 *
 */

/*
 *
 * --------------------------------------------------------------------------
 * 								REQUEST !
 * --------------------------------------------------------------------------
 *
 * Please try to comment on what ever you make a change on.
 * Clearly state out what you want it to do.
 *
 * I beg you. Please !
 *
 * Mohanjith
 *
 * --------------------------------------------------------------------------
 *
 */

require_once ROOT_PATH . '/lib/dao/SQLQBuilder.php';
require_once ROOT_PATH . '/lib/dao/DMLFunctions.php';

class CompStruct {

	/*
	 *
	 * This class will be handling the data storage, retrival and basic displaying
	 * of the Company Structure.
	 * The data structure used is Tree structure.
	 *
	 * Idea form SitePoint <http://www.sitepoint.com/article/hierarchical-data-database/2>
	 *
	 * Algorithm : Modified Preorder Tree Traversal
	 *
	 */

	var $id, $rgt, $lft, $addStr, $strDesc, $addParnt, $location;
	var $hierachyStrings, $hierachyArr;

	function setid ($val) {
		$this->id = $val;
	}

	function setrgt ($val) {
		$this->rgt = $val;
	}

	function setlft ($val) {
		$this->lft = $val;
	}

	function setaddStr ($val) {
		$this->addStr = $val;
	}

	function setstrDesc ($val) {
		$this->strDesc = $val;
	}

	function setaddParnt ($val) {
		$this->addParnt = $val;
	}

	function setlocation ($val) {
		$this->location = $val;
	}

	function addCompStruct () {

		/*
		 *
		 * This function adds a node to the Company Structure.
		 *
		 */

		/*
		 *
		 * Idea form SitePoint <http://www.sitepoint.com>
		 *
		 */

		$sqlString1=sprintf("UPDATE hs_hr_compstructtree SET rgt=rgt+2 WHERE rgt>%d", $this->rgt-1);
		$sqlString2=sprintf("UPDATE hs_hr_compstructtree SET lft=lft+2 WHERE lft>%d", $this->rgt-1);
		$sqlString3=sprintf("INSERT INTO hs_hr_compstructtree SET lft=%d, rgt=%d, title='%s', Description='%s', parnt=%d, loc_code='%s'", $this->rgt, $this->rgt+1, mysql_real_escape_string($this->addStr), mysql_real_escape_string($this->strDesc), mysql_real_escape_string($this->addParnt), $this->location);

		/*
		 *
		 * Execute the query
		 * We just need to know if it was successful. That's all.
		 *
		 */

		$dbConnection = new DMLFunctions();

		$message2 = $dbConnection -> executeQuery($sqlString1);
		$message2 = $dbConnection -> executeQuery($sqlString2);
		$message2 = $dbConnection -> executeQuery($sqlString3);

	return true;
	}

	function deleteCompStruct () {

		/*
		 *
		 * This function removes a node and all its child nodes from the Company Structure.
		 *
		 */

		/*
		 *
		 * Build the SQL query string.
		 *
		 */

		$change=($this->rgt - $this->lft + 1); // Change in left and rignt from the delete. Think a bit!
		$sqlString1=sprintf("DELETE FROM hs_hr_compstructtree WHERE rgt<%d AND lft>%d", $this->rgt+1, $this->lft-1);
		$sqlString2=sprintf("UPDATE hs_hr_compstructtree SET lft=lft-%d WHERE lft>%d", $change, $this->lft-1);
		$sqlString3=sprintf("UPDATE hs_hr_compstructtree SET rgt=rgt-%d WHERE rgt>%d", $change, $this->rgt-1);

		/*
		 *
		 * Execute the query
		 * We just need to know if it was successful. That's all.
		 *
		 */

		$dbConnection = new DMLFunctions();

		$message2 = $dbConnection -> executeQuery($sqlString1);
		$message2 = $dbConnection -> executeQuery($sqlString2);
		$message2 = $dbConnection -> executeQuery($sqlString3);

	return true;
	}

	function updateCompStruct() {

		$updateTable = '`hs_hr_compstructtree`';

		$updateFields[] = '`title`';
		$updateFields[] = '`Description`';
		$updateFields[] = '`loc_code`';

		$updateValues[] = "'".$this->addStr."'";
		$updateValues[] = "'".$this->strDesc."'";
		$updateValues[] = $this->location;

		$updateConditions[] = '`ID` = '.$this->id;

		$sqlQueryBuilder = new SQLQBuilder();

		$sqlString1 = $sqlQueryBuilder->simpleUpdate($updateTable, $updateFields, $updateValues, $updateConditions);

		$dbConnection = new DMLFunctions();

		$message2 = $dbConnection -> executeQuery($sqlString1);
	}

	function displayTree ( $root ) {

		/*
		 *
		 * This function displays all the nodes and their children in the
		 * form of a indented node title.
		 *
		 */

		$sqlString = sprintf("SELECT `lft`, `rgt` FROM `hs_hr_compstructtree` WHERE `title`='%s'", mysql_real_escape_string($root));

		//echo $sqlString;

		$dbConnection = new DMLFunctions();

		$message2 = $dbConnection->executeQuery($sqlString);

		if ( !$message2 ) {
			echo 'No data found!';
			return false;
		};

		$row = mysql_fetch_array($message2);

		$sqlString =sprintf("SELECT ID, lft, rgt FROM hs_hr_compstructtree WHERE lft BETWEEN %d AND %d  ORDER BY lft ASC;", $row['lft'], $row['rgt']);

		$message2 = $dbConnection -> executeQuery($sqlString);

		unset($relations);

		while ($dummyrun = mysql_fetch_array($message2)) {

			$relations[$dummyrun['ID']]=$dummyrun['rgt'];

		}

		$right = array();

		$sqlString =sprintf("SELECT * FROM hs_hr_compstructtree WHERE lft BETWEEN %d AND %d  ORDER BY lft ASC;", $row['lft'], $row['rgt']);

		$message2 = $dbConnection -> executeQuery($sqlString);

		unset($tree);

		while ( $row = mysql_fetch_array( $message2 ) ) {

			/*
			 *
      		 * only check stack if there is one
			 *
			 */

      		if ( count( $right ) > 0 ) {

           		/*
                 * check if we should remove a node from the stack
				 *
                 */

           		while ( $right[count($right)-1]<$row['rgt'] ) {

               		 $rx=array_pop($right);

           		}
       		}

			/*
			 *
			 * display indented node title
			 *
			 */

			if ( ($row['parnt'] != 0) && ( $relations[$row['parnt']] == ($row['rgt']+1) ) ) {
				$isLast = true;
			} else {
				$isLast = false;
			}

			$tree[] = array($row, 'depth'=>count($right), 'isLast'=>$isLast);

       		/*
       		 *
       		 * add this node to the stack
       		 *
       		 */

       		$right[] = $row['rgt'];
		}
		if (isset($tree)) {
			return $tree;
		} else {
			return false;
		}
	}

	public function buildAllWorkStations() {
		$sqlBuilder = new SQLQBuilder();

		$arrFields[0] = '`title`';
		$arrFields[1] = '`id`';
		$arrFields[2] = '`parnt`';

		$arrTable = "`hs_hr_compstructtree`";

		$query = $sqlBuilder->simpleSelect($arrTable, $arrFields);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection -> executeQuery($query);

		$resArr = null;

		if ($result) {
			while ($row = mysql_fetch_assoc($result)) {
				$resArr[$row['id']] = $row;
			}
		}

		$this->hierachyArr = $resArr;
	}

	public function fetchHierarchString($id) {
		if (isset($this->hierachyStrings[$id]) && !empty($this->hierachyStrings[$id])) {
			return $this->hierachyStrings[$id];
		}

		$this->hierachyStrings[$id] = $this->_buildHierarchString($id);

		return $this->hierachyStrings[$id];
	}

	private function _buildHierarchString($id) {
		$str = "";
		while (isset($this->hierachyArr[$id]['parnt']) && ($this->hierachyArr[$id]['parnt'] != 0)) {
			$id = $this->hierachyArr[$id]['parnt'];
			$str = $this->hierachyArr[$id]['title']." - ".$str;
		}
		return substr($str, 0, -3);
	}

}
