<?php
/*
* OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
* all the essential functionalities required for any enterprise.
* Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
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
*/

require_once ROOT_PATH . '/lib/confs/Conf.php';
require_once ROOT_PATH . '/lib/dao/DMLFunctions.php';
require_once ROOT_PATH . '/lib/exception/ExceptionHandler.php';
require_once ROOT_PATH . '/lib/confs/sysConf.php';
require_once ROOT_PATH . '/lib/models/eimadmin/encryption/KeyHandler.php';
require_once ROOT_PATH . '/lib/dao/CryptoQuery.php';

class SQLQBuilder {

	/* Flages */
	var $flg_select;

	/* Flages */
	var $flg_insert;

	/* Flages */
	var $flg_update;

	/* Flages */
	var $flg_delete;

	/* Table Nmae */
	var $table_name;
	var $table2_name;

	/* Arrays */
	var $arr_insert;
	var $arr_insertfield;

	/* Arrays */
	var $arr_select;
	var $arr_select2;

	/* Arrays */
	var $arr_delete;

	/* Arrays */
	var $arr_update;

	/* Arrays */
	var $arr_updateRecList;



	var $SQL1; // for return the SQL query String
	var $arrayFieldList; // for the extraction of Array Field List
	var $field;
	var $sysConst;
/*
	Constructor for the SQLQBuilder
*/
	function SQLQBuilder() {
		$confObj = new Conf();

		new MySQLClass($confObj);
	}

	function quoteCorrect($arr) {
		if (is_array($arr)) {
			foreach ($arr as $value) {
				if ($value != 'null') {

					$tempArr[] = $this->quoteCorrectString($value);

				} else {
					$tempArr[] = $value;
				}
			}
			return $tempArr;
		}

		$value=$arr;

		$temp = $this->quoteCorrectString($value, false);

		return $temp;
	}

	function quoteCorrectString($value, $quote=true, $smart=false) {
		if ($smart) {
			if (preg_match("/^'/", $value) == 1) {
				$quote = true;
			} else {
				$quote = false;
			}
		}
		$temp = preg_replace(array("/^'/", "/'$/"), array("", ""), trim($value));

		if (get_magic_quotes_gpc()) {
			$temp=stripslashes($temp);
		}

		$temp = mysql_real_escape_string(trim($temp));

		if ($quote) {
			$temp = "'$temp'";
		}

		return $temp;
	}

/*	Function passresultSetMessage Will
	will get the SQLFormat Object as an input
	Parameter and extract the Object's Instance
	Variables and by using them Build up the
	SELECT Query
*/

	function selectFilter($arrComp='',$arrFlt='', $sortField=0) {

		$arrayFieldList = $this->arr_select;
		$countArrSize = count($arrayFieldList);
		$SQL1 = 'SELECT ';
		for ($i=0;$i<count($arrayFieldList); $i++)
			if ($i == ($countArrSize - 1))   //String Manipulation
				$SQL1 = $SQL1 . $arrayFieldList[$i] . ' ';
			else

				$SQL1 = $SQL1 . $arrayFieldList[$i] . ', ';

		$SQL1 = $SQL1 . ' FROM ' . strtolower($this->table_name) . ' WHERE ' . $this->field . ' NOT IN (SELECT ';

		if(isset($this->field2))
			$SQL1 = $SQL1 . $this->field2 . ' FROM ' . strtolower($this->table2_name);
		else
			$SQL1 = $SQL1 . $this->field . ' FROM ' . strtolower($this->table2_name);

		if(is_array($arrComp)) {
			$SQL1 = $SQL1 . ' WHERE ';
			for($c=0;count($arrComp)>$c;$c++)
    				if ($c == (count($arrComp) - 1))   //String Manipulation
    					$SQL1 = $SQL1 . $arrComp[$c][0] . " = '" . mysql_real_escape_string($arrComp[$c][1]) . '\' ';
    				 else
    					$SQL1 = $SQL1 . $arrComp[$c][0] . " = '" . mysql_real_escape_string($arrComp[$c][1]) . '\'  AND ';
			}

		if(is_array($arrFlt)) {
			$SQL1 = $SQL1 . ') AND ';
			for($c=0;count($arrFlt)>$c;$c++) {
    			if ($c == (count($arrFlt) - 1))  { //String Manipulation
    				$SQL1 = $SQL1 . $arrFlt[$c][0] . ' = ' . $this->quoteCorrectString($arrFlt[$c][1], false, true) . ' ';
    			} else {
    				$SQL1 = $SQL1 . $arrFlt[$c][0] . ' = ' . $this->quoteCorrectString($arrFlt[$c][1], false, true) . '  AND ';
    			}
			}
			$SQL1 = $SQL1 . ' ORDER BY '. $arrayFieldList[0];
		} else {
			$SQL1 = $SQL1 . ') ORDER BY '. $arrayFieldList[$sortField];
		}

			//$exception_handler = new ExceptionHandler();
	  	 	//$exception_handler->logW($SQL1);
		return $SQL1; //returning the SQL1 which has the SQL Query
	}

	function passResultSetMessage($page=0, $schStr='',$schField=-1, $sortField = 0, $sortOrder = 'ASC', $schArr=false, $specialSearch=null) {

		if ($this->flg_select == 'true') { // check whether the flg_select is 'True'

			$arrayFieldList = $this->arr_select; //assign the sql_format->arr_select instance variable to arrayFieldList
			$countArrSize = count($arrayFieldList); // check the array size
			$SQL1 = 'SELECT ';
			for ($i=0;$i<count($arrayFieldList); $i++) {

				if ($i == ($countArrSize - 1))  { //String Manipulation

					$SQL1 = $SQL1 . $arrayFieldList[$i] . ' ';

				} else {

					$SQL1 = $SQL1 . $arrayFieldList[$i] . ', ';

				}
			}

				$SQL1 = $SQL1 . ' FROM ' . strtolower($this->table_name); //Tail of the SQL statement

				if($schField!=-1)
				{
                	$SQL1 = $SQL1 . ' WHERE ';

					if ($schArr) {
						for ($i = 0; $i < count($schField) ; $i++) {
						 if($schField[$i]!=-1) {
                    		$SQL1 = $SQL1 . $arrayFieldList[$schField[$i]] . ' LIKE \'%' . trim(mysql_real_escape_string($schStr[$i])) .'%\' AND ';
						 }
						}
						$SQL1 = substr($SQL1,0,-1-4);
					} else {
						$SQL1 = $SQL1 . $arrayFieldList[$schField] . ' LIKE \'%' . trim(mysql_real_escape_string($schStr)) .'%\'';
					}

					if (isset($specialSearch)) {
                		$SQL1 = $SQL1." AND ";
                		$SQL1 = $SQL1.$specialSearch;
                	}

                } else if (isset($specialSearch)) {
                	$SQL1 = $SQL1 . ' WHERE '.$specialSearch;
                }

				//echo $SQL1;
				//exit;
            $SQL1 = $SQL1. ' ORDER BY '. $arrayFieldList[$sortField].' '.$sortOrder;// Sort order is ASC or DESC as passed by the arguement. Default ASC.

            if($page!=0) {
       			$sysConst = new sysConf();

            	$SQL1 = $SQL1 . ' LIMIT ' .(($page-1) * $sysConst->itemsPerPage) . ',' .$sysConst->itemsPerPage;
            }
			//$exception_handler = new ExceptionHandler();
	  	 	//$exception_handler->logW($SQL1);

			return $SQL1; //returning the SQL1 which has the SQL Query

		} else {

			$exception_handler = new ExceptionHandler();
	  	 	$exception_handler->dbexInvalidSQL();
			echo "ERROR"; // put Exception Handling
			exit;

		}
	}



	function passResultFilter($page,$str='',$mode=0) {
		
			$arrayFieldList = $this->arr_select;
			$countArrSize = count($arrayFieldList);

			$SQL1 = $this->_buildSelect($arrayFieldList);

			$SQL1 .= ' FROM ' . strtolower($this->table_name) . ' WHERE ' . $this->field . ' NOT IN (SELECT ' . $this->field . ' FROM ' . strtolower($this->table2_name) . ' ) ';

				if($mode!=0)
				{
                $SQL1 = $SQL1 . ' AND ';
                    switch($mode)
                            {
                              case 1 : $SQL1 = $SQL1 . $arrayFieldList[0] . ' LIKE \'%' . trim(mysql_real_escape_string($str)) .'%\'';
                                        break;
                              case 2:  $SQL1 = $SQL1 . $arrayFieldList[1] . ' LIKE \'%' . trim(mysql_real_escape_string($str)) .'%\'';
                                        break;
                            }
                }

            $SQL1 = $SQL1. ' ORDER BY '. $arrayFieldList[0];

            if($page!=0) {
       			$sysConst = new sysConf();

            	$SQL1 = $SQL1 . ' LIMIT ' .(($page-1) * $sysConst->itemsPerPage) . ',' .$sysConst->itemsPerPage;
            }
			//$exception_handler = new ExceptionHandler();
	  	 	//$exception_handler->logW($SQL1);

		return $SQL1; //returning the SQL1 which has the SQL Query
	}

	function countResultFilter($str='',$mode=0) {
			$arrayFieldList = $this->arr_select;
			$SQL1 = 'SELECT count(*) FROM ' . strtolower($this->table_name) . ' WHERE ' . $this->field . ' NOT IN (SELECT ' . $this->field . ' FROM ' . strtolower($this->table2_name) . ' ) ';

				if($mode!=0)
				{
                $SQL1 = $SQL1 . ' AND ';
                    switch($mode)
                            {
                              case 1 : $SQL1 = $SQL1 . $arrayFieldList[0] . ' LIKE \'%' . trim(mysql_real_escape_string($str)) .'%\'';
                                        break;
                              case 2:  $SQL1 = $SQL1 . $arrayFieldList[1] . ' LIKE \'%' . trim(mysql_real_escape_string($str)) .'%\'';
                                        break;
                            }
                }

			//$exception_handler = new ExceptionHandler();
	  	 	//$exception_handler->logW($SQL1);

		return $SQL1; //returning the SQL1 which has the SQL Query
	}

/*
	Function addNewRecordFeature1 Will
	will get the SQLFormat Object as an input
	Parameter and extract the Object's Instance
	Variables and by using them Build up the
	INSERT Query
*/

	function addNewRecordFeature1($quoteCorrect = true) {
		
		/* For Encryption : Begins */
		$encOn = KeyHandler::KeyExists();
		if ($encOn && CryptoQuery::isEncTable($this->table_name)) {
    		$this->arr_insert = CryptoQuery::prepareEncryptFields($this->arr_insertfield, $this->arr_insert);
		}
		/* For Encryption : Ends */
		
		if ($this->flg_insert == 'true') { // check whether the flg_insert is 'True'

			$arrayFieldList = $this->arr_insert; //assign the sql_format->arr_select instance variable to arrayFieldList
			$countArrSize = count($arrayFieldList); // check the array size

			if($quoteCorrect) {
				$arrayFieldList = $this->quoteCorrect($arrayFieldList);
			}

			$SQL1 = 'INSERT INTO ' . strtolower($this->table_name) . ' VALUES (';

			for ($i=0;$i<count($arrayFieldList); $i++) {

				if ($i == ($countArrSize - 1))  { //String Manipulation

					$SQL1 = $SQL1 . $arrayFieldList[$i] . ' ';

				} else {

					$SQL1 = $SQL1 . $arrayFieldList[$i] . ', ';

				}
			}

			$SQL1 = $SQL1 . ')';

			//$exception_handler = new ExceptionHandler();
	  	 	//$exception_handler->logW($SQL1);

			return $SQL1; //returning the SQL1 which has the SQL Query


		} else {

			$exception_handler = new ExceptionHandler();
	  	 	$exception_handler->dbexInvalidSQL();
			echo "ERROR"; // put Exception Handling
			exit;

		}

	}

	function addNewRecordFeature2($quoteCorrect = true, $duplicateInsert = false, $insertIgnore = false) {

		if ($this->flg_insert == 'true') { // check whether the flg_insert is 'True'

			$arrayFieldList = $this->arr_insertfield;
			$arrayRecordList = $this->arr_insert; //assign the sql_format->arr_select instance variable to arrayFieldList
			$countArrSize = count($arrayFieldList); // check the array size

			if($quoteCorrect) {
				$arrayRecordList = $this->quoteCorrect($arrayRecordList);
			}

			/* For Encryption : Begins */
			$encOn = KeyHandler::KeyExists();
			if ($encOn && CryptoQuery::isEncTable($this->table_name)) {
				$arrayRecordList = CryptoQuery::prepareEncryptFields($arrayFieldList, $arrayRecordList);
			}
			/* For Encryption : Ends */
					
            if ($insertIgnore) {
                $SQL1 = 'INSERT IGNORE INTO ';
            } else {
                $SQL1 = 'INSERT INTO ';
            }
			$SQL1 = $SQL1 . strtolower($this->table_name) . ' ( ';

			for ($i=0;$i<count($arrayRecordList); $i++) {
				if ($i == ($countArrSize - 1))  { //String Manipulation
					$SQL1 = $SQL1 . $arrayFieldList[$i] . ' ';
				} else {
					$SQL1 = $SQL1 . $arrayFieldList[$i] . ', ';
				}
			}

			$SQL1 = $SQL1 . ' ) VALUES (';

			for ($i=0;$i<count($arrayRecordList); $i++) {
				if ($i == ($countArrSize - 1))  { //String Manipulation
					$SQL1 = $SQL1 . $arrayRecordList[$i] . ' ';
				} else {
					$SQL1 = $SQL1 . $arrayRecordList[$i] . ', ';
				}
			}

			$SQL1 = $SQL1 . ')';

			if($duplicateInsert) {

				$SQL1 = $SQL1 . ' ON DUPLICATE KEY UPDATE ';

				for ($i = 0; $i<count($arrayFieldList); $i++) {
					if ($i == ($countArrSize - 1))  { //String Manipulation
						$SQL1 = $SQL1 . $arrayFieldList[$i] . '=' . $arrayRecordList[$i];
					} else {
						$SQL1 = $SQL1 . $arrayFieldList[$i] . '=' . $arrayRecordList[$i]. ', ';
					}
				}
			}

			//$exception_handler = new ExceptionHandler();
	  	 	//$exception_handler->logW($SQL1);

			return $SQL1; //returning the SQL1 which has the SQL Query

		} else {

			$exception_handler = new ExceptionHandler();
	  	 	$exception_handler->dbexInvalidSQL();
			echo "ERROR"; // put Exception Handling
			exit;

		}

	}

/*
	Function selectOneRecordOnly Will
	will get the SQLFormat Object as an input
	Parameters as FieldName and extract the Object's Instance
	Variables and by using them Build up the
	SELECT Query
*/

	function selectOneRecordOnly($n=0,$str='') {

		$str = $this->quoteCorrect($str);

		if ($this->flg_select == 'true') { // check whether the flg_select is 'True'

			$arrayFieldList = $this->arr_select; //assign the sql_format->arr_select instance variable to arrayFieldList
			$SQL1 = 'SELECT MAX(' . $arrayFieldList[0] . ') FROM ' . strtolower($this->table_name); //Tail of the SQL statement

			if($n>0) {
				$SQL1 = $SQL1 . ' WHERE ' . $arrayFieldList[1] . '=' . $str[0];
                  for($c = 1 ; $c < $n ; $c++)
                    $SQL1 = $SQL1 . ' AND '. $arrayFieldList[($c+1)] . '=' . $str[$c];
                }



			return $SQL1; //returning the SQL1 which has the SQL Query

		} else {

			$exception_handler = new ExceptionHandler();
	  	 	$exception_handler->dbexInvalidSQL();
			echo "ERROR"; // put Exception Handling
			exit;

		}
	}
//////////////////
	function selectRecords($filID,$field) {

		if ($this->flg_select == 'true') { // check whether the flg_select is 'True'

			$arrayFieldList = $this->arr_select; //assign the sql_format->arr_select instance variable to arrayFieldList
			$countArrSize = count($arrayFieldList); // check the array size
			$SQL1 = 'SELECT ';
			for ($i=0;$i<count($arrayFieldList); $i++) {

				if ($i == ($countArrSize - 1))  { //String Manipulation

					$SQL1 = $SQL1 . $arrayFieldList[$i] . ' ';

				} else {

					$SQL1 = $SQL1 . $arrayFieldList[$i] . ', ';

				}
			}

				$SQL1 = $SQL1 . ' FROM ' . strtolower($this->table_name) . ' WHERE ' . $field . '=' . "'" . mysql_real_escape_string($filID). "'";  //Tail of the SQL statement
				//echo $SQL1;
				//exit;

			return $SQL1; //returning the SQL1 which has the SQL Query

		} else {

			$exception_handler = new ExceptionHandler();
	  	 	$exception_handler->dbexInvalidSQL();
			echo "ERROR"; // put Exception Handling
			exit;

		}
	}

	function selectOneRecordFiltered($filID, $num=0, $orderBy=false, $order='ASC') {

		if ($this->flg_select == 'true') { // check whether the flg_select is 'True'

			$arrayFieldList = $this->arr_select; //assign the sql_format->arr_select instance variable to arrayFieldList
			
			/* For Encryption : Begins */
			$encOn = KeyHandler::KeyExists();
			if ($encOn && CryptoQuery::isEncTable($this->table_name)) {
				$arrayFieldList = CryptoQuery::prepareDecryptFields($arrayFieldList);
			}
			/* For Encryption : Ends */	
			
			$countArrSize = count($arrayFieldList); // check the array size
			$SQL1 = 'SELECT ';
			for ($i=0;$i<count($arrayFieldList); $i++) {

				if ($i == ($countArrSize - 1))  { //String Manipulation

					$SQL1 = $SQL1 . $arrayFieldList[$i] . ' ';

				} else {

					$SQL1 = $SQL1 . $arrayFieldList[$i] . ', ';

				}
			}

			if($num==0)
				$SQL1 = $SQL1 . ' FROM ' . strtolower($this->table_name) . ' WHERE ' . $arrayFieldList[0] . '=' . "'" . $filID . "'";  //Tail of the SQL statement
            else {
            	$filID = $this->quoteCorrect($filID);
            	$SQL1 = $SQL1 . ' FROM ' . strtolower($this->table_name) . ' WHERE ' . $arrayFieldList[0] . '=' . $filID[0];
                for($c = 1 ; $c <= $num ; $c++)
               		$SQL1 = $SQL1 . ' AND '. $arrayFieldList[$c] . "=" . $filID[$c];
           }

           if (is_numeric($orderBy)) {
           		$SQL1 .= " ORDER BY {$arrayFieldList[$orderBy]} {$order}";
           }
				//echo $SQL1;
				//exit;

			//$exception_handler = new ExceptionHandler();
	  	 	//$exception_handler->logW($SQL1);

			return $SQL1; //returning the SQL1 which has the SQL Query
		} else {
			$exception_handler = new ExceptionHandler();
	  	 	$exception_handler->dbexInvalidSQL();
			echo "ERROR"; // put Exception Handling
			exit;
		}
	}

function filterNotEqualRecordSet($filID) {

		if ($this->flg_select == 'true') { // check whether the flg_select is 'True'

			$arrayFieldList = $this->arr_select; //assign the sql_format->arr_select instance variable to arrayFieldList
			$countArrSize = count($arrayFieldList); // check the array size
			$SQL1 = 'SELECT ';
			for ($i=0;$i<count($arrayFieldList); $i++) {

				if ($i == ($countArrSize - 1))  { //String Manipulation

					$SQL1 = $SQL1 . $arrayFieldList[$i] . ' ';

				} else {

					$SQL1 = $SQL1 . $arrayFieldList[$i] . ', ';

				}
			}

				$SQL1 = $SQL1 . ' FROM ' . strtolower($this->table_name) . ' WHERE ' . $arrayFieldList[0] . '<>' . "'" . mysql_real_escape_string($filID). "'";  //Tail of the SQL statement

			return $SQL1; //returning the SQL1 which has the SQL Query

		} else {

			$exception_handler = new ExceptionHandler();
	  	 	$exception_handler->dbexInvalidSQL();
			echo "ERROR"; // put Exception Handling
			exit;

		}
}

	function queryAllInformation() {


		if ($this->flg_select == 'true') { // check whether the flg_select is 'True'

			$arrayFieldList = $this->arr_select; //assign the sql_format->arr_select instance variable to arrayFieldList
			$countArrSize = count($arrayFieldList); // check the array size
			$SQL1 = 'SELECT ';
			for ($i=0;$i<count($arrayFieldList); $i++) {

				if ($i == ($countArrSize - 1))  { //String Manipulation

					$SQL1 = $SQL1 . $arrayFieldList[$i] . ' ';

				} else {

					$SQL1 = $SQL1 . $arrayFieldList[$i] . ', ';

				}
			}

				$SQL1 = $SQL1 . ' FROM ' . strtolower($this->table_name); //Tail of the SQL statement

				//echo $SQL1;
				//exit;

			return $SQL1; //returning the SQL1 which has the SQL Query

		} else {

			$exception_handler = new ExceptionHandler();
	  	 	$exception_handler->dbexInvalidSQL();
			echo "ERROR"; // put Exception Handling
			exit;

		}


	}

	function addUpdateRecord1($num = 0, $quoteCorrect = true) {

		if ($this->flg_update == 'true') { // check whether the flg_insert is 'True'

			$arrayFieldList = $this->arr_update; //assign the sql_format->arr_select instance variable to arrayFieldList
			$arrayRecordSet = $this->arr_updateRecList;
			$countArrSize = count($arrayFieldList); // check the array size

			if ($quoteCorrect) {
				$arrayRecordSet = $this->quoteCorrect($arrayRecordSet);
			}
			
			/* For Encryption : Begins */
			$encOn = KeyHandler::KeyExists();
			if ($encOn && CryptoQuery::isEncTable($this->table_name)) {
				$arrayRecordSet = CryptoQuery::prepareEncryptFields($arrayFieldList, $arrayRecordSet);
			}
			/* For Encryption : Ends */	
				
			$SQL1 = 'UPDATE ' . strtolower($this->table_name) . ' SET ';

			for ($i = $num + 1; $i<count($arrayFieldList); $i++) {

				if ($i == ($countArrSize - 1))  { //String Manipulation

					$SQL1 = $SQL1 . $arrayFieldList[$i] . '= ' . $arrayRecordSet[$i];

				} else {

					$SQL1 = $SQL1 . $arrayFieldList[$i] . '=' . $arrayRecordSet[$i]. ', ';

				}
			}

			$SQL1 = $SQL1 . ' WHERE ' . $arrayFieldList[0] . '=' . $arrayRecordSet[0] ;

			for($c=1; $c <= $num ; $c++)
                $SQL1 = $SQL1 . ' AND ' . $arrayFieldList[$c] . '=' . $arrayRecordSet[$c];

			//$exception_handler = new ExceptionHandler();
	  	 	//$exception_handler->logW($SQL1);

			return $SQL1; //returning the SQL1 which has the SQL Query

		} else {

			$exception_handler = new ExceptionHandler();
	  	 	$exception_handler->dbexInvalidSQL();
			echo "ERROR"; // put Exception Handling
			exit;

		}

	}

	function deleteRecord($arrID) {

		if ($this->flg_delete == 'true') { // check whether the flg_select is 'True'

			$arrayFieldList = $this->arr_delete; //assign the sql_format->arr_select instance variable to arrayFieldList
			$countArrSize = count($arrayFieldList); // check the array size

			$SQL1 = 'DELETE FROM ' . strtolower($this->table_name) . ' WHERE ';

            for($j=0;$j<sizeof($arrID[0]);$j++)
            {
    			for ($i=0;$i<count($arrayFieldList); $i++) {

    				if ($i == ($countArrSize - 1))  { //String Manipulation

    					$SQL1 = $SQL1 . $arrayFieldList[$i] . ' = \'' . mysql_real_escape_string($arrID[$i][$j]). '\' ';

    				} else {

    					$SQL1 = $SQL1 . $arrayFieldList[$i] . ' = \'' . mysql_real_escape_string($arrID[$i][$j]) .'\'  AND ';

    				}
	   		      }
                if($j==(sizeof($arrID[0])-1))
                    {
                      $SQL1 = $SQL1 . ' ';
                    }
                else
                    {
                      $SQL1 = $SQL1 . ' OR ';
                    }
			}

			//$exception_handler = new ExceptionHandler();
	  	 	//$exception_handler->logW($SQL1);

			return $SQL1; //returning the SQL1 which has the SQL Query


		} else {

			$exception_handler = new ExceptionHandler();
	  	 	$exception_handler->dbexInvalidSQL();
			echo "ERROR"; // put Exception Handling
			exit;

		}
	}

	function selectMultipleTab($page,$str,$mode, $sortField = 0, $sortOrder = 'ASC') {
			$arrayFieldList = $this->arr_select;
			$countArrSize = count($arrayFieldList);
			$SQL1 = 'SELECT ';
			for ($i=0;$i<count($arrayFieldList); $i++)
				if ($i == ($countArrSize - 1))   //String Manipulation
					$SQL1 = $SQL1 .'a.'. $arrayFieldList[$i] . ' ';
				else
					$SQL1 = $SQL1 .'a.'. $arrayFieldList[$i] . ', ';

		$SQL1 = $SQL1 . ' FROM ' . strtolower($this->table_name) . ' a, ' .strtolower($this->table2_name) .' b WHERE a.'. $arrayFieldList[0].  ' = b.'. $this->field ;

		if($mode!=0)
				{
                $SQL1 = $SQL1 . ' AND ';

                    switch($mode)
                            {
                              case 1 : $SQL1 = $SQL1 . 'a.'. $arrayFieldList[0] .' LIKE \'%' . trim(mysql_real_escape_string($str)) .'%\'';
                                        break;
                              case 2:  $SQL1 = $SQL1 . 'a.'. $arrayFieldList[1] .' LIKE \'%' . trim(mysql_real_escape_string($str)) .'%\'';
                                        break;
                                    }
                }

		$SQL1 = $SQL1 . ' GROUP BY a.'. $arrayFieldList[0];
            if($page!=0) {
       			$sysConst = new sysConf();

            	$SQL1 = $SQL1 . ' LIMIT ' .(($page-1) * $sysConst->itemsPerPage) . ',' .$sysConst->itemsPerPage;
            }

			//$exception_handler = new ExceptionHandler();
	  	 	//$exception_handler->logW($SQL1);

		return $SQL1; //returning the SQL1 which has the SQL Query
	}

	/*
	 * @author : Mohanjith <mohanjith@beyondm.net> <moha@mohanjith.net>
	 */
	function passResultSetMessageMulti($page=0, $schStr='',$schField=-1, $sortField = 0, $sortOrder = 'ASC') {

		if ($this->flg_select == 'true') { // check whether the flg_select is 'True'

			$arrayFieldList = $this->arr_select;
            $arrayFieldList2 = $this->arr_select2;//assign the sql_format->arr_select instance variable to arrayFieldList

			$countArrSize = count($arrayFieldList); // check the array size
			$SQL1 = 'SELECT ';

			for ($i=0;$i<count($arrayFieldList); $i++) {

				$SQL1 .= 'a.'. $arrayFieldList[$i] . ', ';

			}

            for ($i=0;$i<(count($arrayFieldList2)-1); $i++) {

				$SQL1 .= 'b.'. $arrayFieldList2[$i] . ', ';

			}
            $i=count($arrayFieldList2)-1;

			$SQL1 .= 'b.'. $arrayFieldList2[$i] . ' ';

			$SQL1 .= ' FROM ' . strtolower($this->table_name).' a, '; //Tail of the SQL statement
			$SQL1 .= strtolower($this->table2_name).' b ';

			$SQL1 .= ' WHERE a.'.$this->field;
            $SQL1 .= ' = b.'.$this->field;

			if($schField!=-1)
			{
                if ($schField > (count($arrayFieldList)-1)) {

                	$SQL1 .= ' AND '.$arrayFieldList2[$schField-count($arrayFieldList)] . ' LIKE \'%' . trim(mysql_real_escape_string($schStr)) .'%\'';

                } else {

                	$SQL1 .= ' AND '.$arrayFieldList[$schField] . ' LIKE \'%' . trim(mysql_real_escape_string($schStr)) .'%\'';

                }
            }
			if ($sortField > (count($arrayFieldList)-1)) {

				$SQL1 .= ' ORDER BY b.'. $arrayFieldList2[$sortField-count($arrayFieldList)].' '.$sortOrder;// Sort order is ASC or DESC as passed by the arguement. Default ASC.

			} else {

				$SQL1 .= ' ORDER BY a.'. $arrayFieldList[$sortField].' '.$sortOrder;// Sort order is ASC or DESC as passed by the arguement. Default ASC.

			}

            if($page!=0) {

       			$sysConst = new sysConf();

            	$SQL1 .= ' LIMIT ' .(($page-1) * $sysConst->itemsPerPage) . ',';
            	$SQL1 .= $sysConst->itemsPerPage;

            }
			//$exception_handler = new ExceptionHandler();
	  	 	//$exception_handler->logW($SQL1);

			return $SQL1; //returning the SQL1 which has the SQL Query

		} else {

			$exception_handler = new ExceptionHandler();
	  	 	$exception_handler->dbexInvalidSQL();
			echo "ERROR"; // put Exception Handling
			exit;

		}
	}

	function countMultipleTab($str,$mode) {
			$arrayFieldList = $this->arr_select;
			$countArrSize = count($arrayFieldList);
			$SQL1 = 'SELECT count(DISTINCT a.' .$arrayFieldList[0]. ') FROM ' . strtolower($this->table_name) . ' a, ' .strtolower($this->table2_name) .' b WHERE a.'. $arrayFieldList[0].  ' = b.'. $this->field ;

		if($mode!=0)
				{
                $SQL1 = $SQL1 . ' AND ';

                    switch($mode)
                            {
                              case 1 : $SQL1 = $SQL1 . 'a.'. $arrayFieldList[0] .' LIKE \'%' . trim(mysql_real_escape_string($str)) .'%\'';
                                        break;
                              case 2:  $SQL1 = $SQL1 . 'a.'. $arrayFieldList[1] .' LIKE \'%' . trim(mysql_real_escape_string($str)) .'%\'';
                                        break;
                                    }
                }
			//$exception_handler = new ExceptionHandler();
	  	 	//$exception_handler->logW($SQL1);

		return $SQL1; //returning the SQL1 which has the SQL Query
	}

	function selectEmployee($page,$str,$mode) {

	if($this->flg_select=='true') {

		$SQL1= "SELECT a.EMP_NUMBER, a.EMP_LASTNAME FROM HS_HR_EMPLOYEE a,  " . strtolower($this->table_name) . " b WHERE a.EMP_NUMBER = b." . $this->field;

		if($mode!=0)
				{
                $SQL1 = $SQL1 . ' AND ';

                    switch($mode) {

                              case 1 : $SQL1 = $SQL1 . 'a.EMP_NUMBER LIKE \'%' . trim(mysql_real_escape_string($str)) .'%\'';
                                        break;
                              case 2:  $SQL1 = $SQL1 . 'a.EMP_FULLNAME LIKE \'%' . trim(mysql_real_escape_string($str)) .'%\'';
                                        break;
                   }
                }



		$SQL1 = $SQL1 . " GROUP BY a.EMP_NUMBER";
			//$exception_handler = new ExceptionHandler();
//	  	 	$exception_handler->logW($SQL1);

            if($page!=0) {
       			$sysConst = new sysConf();

            	$SQL1 = $SQL1 . ' LIMIT ' .(($page-1) * $sysConst->itemsPerPage) . ',' .$sysConst->itemsPerPage;
            }
		$SQL1=strtolower($SQL1);
		return $SQL1;
		}
	}

	function countEmployee($str,$mode) {

	if($this->flg_select=='true') {

		$SQL1= "SELECT count(DISTINCT a.EMP_NUMBER) FROM HS_HR_EMPLOYEE a,  " . strtolower($this->table_name) . " b WHERE a.EMP_NUMBER = b." . $this->field;

		if($mode!=0)
				{
                $SQL1 = $SQL1 . ' AND ';

                    switch($mode)
                            {
                              case 1 : $SQL1 = $SQL1 . 'a.EMP_NUMBER LIKE \'%' . trim(mysql_real_escape_string($str)) .'%\'';
                                        break;
                              case 2:  $SQL1 = $SQL1 . 'a.EMP_FULLNAME LIKE \'%' . trim(mysql_real_escape_string($str)) .'%\'';
                                        break;
                                    }
                }

			//$exception_handler = new ExceptionHandler();
//	  	 	$exception_handler->logW($SQL1);
		$SQL1=strtolower($SQL1);
		return $SQL1;
		}
	}

	function countResultset($schStr='',$schField=-1, $schArr = false) {

		if ($this->flg_select == 'true') { // check whether the flg_select is 'True'

			$arrayFieldList = $this->arr_select; //assign the sql_format->arr_select instance variable to arrayFieldList
			$countArrSize = count($arrayFieldList); // check the array size
			$SQL1 = 'SELECT count(*) FROM ' . strtolower($this->table_name); //Tail of the SQL statement

			if($schField!=-1){
                $SQL1 = $SQL1 . ' WHERE ';

				if ($schArr) {

					for ($i = 0; $i < count($schField) ; $i++) {
						if($schField[$i]!=-1) {
                    		$SQL1 = $SQL1 . $arrayFieldList[$schField[$i]] . ' LIKE \'%' . trim(mysql_real_escape_string($schStr[$i])) .'%\' AND ';
						 }
					}
					$SQL1 = substr($SQL1,0,-1-4);
				} else {
					$SQL1 = $SQL1 . $arrayFieldList[$schField] . ' LIKE \'%' . trim(mysql_real_escape_string($schStr)) .'%\'';
				}
             }

			//$exception_handler = new ExceptionHandler();
	  	 	//$exception_handler->logW($SQL1);

			return $SQL1; //returning the SQL1 which has the SQL Query

		} else {

			$exception_handler = new ExceptionHandler();
	  	 	$exception_handler->dbexInvalidSQL();
			echo "ERROR"; // put Exception Handling
			exit;

		}
	}

function listReports($userGroup, $page, $str = '' ,$mode = -1) {

		$SQL1 = "SELECT a.REP_CODE, a.REP_NAME FROM HS_HR_EMPREPORT a, HS_HR_EMPREP_USERGROUP b WHERE a.REP_CODE = b.REP_CODE AND b.USERG_ID = '" .$userGroup . "'";

		if($mode != (-1)) {

                $SQL1 = $SQL1 . " AND ";

                    switch($mode) {

                        case 0 : $SQL1 = $SQL1 . 'a.REP_CODE LIKE \'%' . trim(mysql_real_escape_string($str)) .'%\'';
                                        break;
                        case 1:  $SQL1 = $SQL1 . 'a.REP_NAME LIKE \'%' . trim(mysql_real_escape_string($str)) .'%\'';
                                        break;
                    }
        }

		$SQL1 = $SQL1 . " GROUP BY a.REP_CODE";

            if($page!=0) {
       			$sysConst = new sysConf();

            	$SQL1 = $SQL1 . ' LIMIT ' .(($page-1) * $sysConst->itemsPerPage) . ',' .$sysConst->itemsPerPage;
            }
		$SQL1=strtolower($SQL1);

		//$exception_handler = new ExceptionHandler();
  	 	//$exception_handler->logW($SQL1);

		$SQL1 = strtolower($SQL1);
		return $SQL1;

}

	function countReports($userGroup,$schStr='',$mode=0) {

			$SQL1 = "SELECT count(*) FROM HS_HR_EMPREPORT a, HS_HR_EMPREP_USERGROUP b WHERE a.REP_CODE = b.REP_CODE AND b.USERG_ID = '" .$userGroup . "'"; //Tail of the SQL statement

				if($mode!=0)
				{
                $SQL1 = $SQL1 . ' AND ';

                    switch($mode)
                            {
                              case 1 : $SQL1 = $SQL1 . 'a.REP_CODE LIKE \'%' . trim(mysql_real_escape_string($schStr)) .'%\'';
                                        break;
                              case 2:  $SQL1 = $SQL1 . 'a.REP_NAME LIKE \'%' . trim(mysql_real_escape_string($schStr)) .'%\'';
                                        break;
                            }
                }

			//$exception_handler = new ExceptionHandler();
	  	 	//$exception_handler->logW($SQL1);
		$SQL1 = strtolower($SQL1);

			return $SQL1; //returning the SQL1 which has the SQL Query

	}

////////////special cases

function getJDAssigned($dsg) {

        $sqlQString='SELECT d.JDCAT_CODE,c.JDTYPE_CODE,b.JDKRA_CODE,d.JDCAT_NAME, c.JDTYPE_NAME,b.JDKRA_NAME,a.JDKPI_INDICATORS FROM HS_HR_JD_KPI a, HS_HR_JD_KEY_RESULT_AREA b, HS_HR_JD_TYPE c, HS_HR_JD_CATERY d WHERE a.DSG_CODE = '."'" . $dsg . "'" . ' AND a.JDKRA_CODE = b.JDKRA_CODE AND b.JDTYPE_CODE = c.JDTYPE_CODE AND c.JDCAT_CODE = d.JDCAT_CODE GROUP BY d.JDCAT_CODE,c.JDTYPE_CODE,b.JDKRA_CODE';
		$sqlQString=strtolower($sqlQString);

	return $sqlQString;
	}

function getJDGrouping($dsg) {

        $sqlQString="SELECT d.JDCAT_CODE,c.JDTYPE_CODE,b.JDKRA_CODE,d.JDCAT_NAME, c.JDTYPE_NAME,b.JDKRA_NAME FROM HS_HR_JD_KEY_RESULT_AREA b, HS_HR_JD_TYPE c, HS_HR_JD_CATERY d WHERE b.JDKRA_CODE NOT IN (SELECT JDKRA_CODE FROM HS_HR_JD_KPI WHERE DSG_CODE = '" .$dsg. "' ) AND b.JDTYPE_CODE = c.JDTYPE_CODE AND c.JDCAT_CODE = d.JDCAT_CODE GROUP BY d.JDCAT_CODE,c.JDTYPE_CODE,b.JDKRA_CODE";
		$sqlQString=strtolower($sqlQString);

	return $sqlQString;
	}

function getAssCompStruct($lev,$relat) {

	$sqlQString = "SELECT a.HIE_CODE, b.HIE_NAME  FROM hs_hr_company_hierarchy a, hs_hr_company_struct b WHERE a.DEF_LEVEL='" . $lev . "' AND a.HIE_RELATIONSHIP='" .$relat. "' AND a.HIE_CODE = b.HIE_CODE";
	$sqlQString=strtolower($sqlQString);

	return $sqlQString;
}

function getAssEmpStat($jobtit) {

	//$sqlQString = "SELECT b.ESTAT_CODE, b.ESTAT_NAME FROM HS_HR_JOBTIT_EMPSTAT a, HS_HR_EMPSTAT b WHERE a.ESTAT_CODE = b.ESTAT_CODE AND a.JOBTIT_CODE = '" .$jobtit. "'";
	$sqlQString = "SELECT b.ESTAT_CODE, b.ESTAT_NAME FROM HS_HR_JOBTIT_EMPSTAT a, hs_hr_empstat b WHERE a.ESTAT_CODE = b.ESTAT_CODE AND a.JOBTIT_CODE = '" .$jobtit. "'";
	$sqlQString=strtolower($sqlQString);

	return $sqlQString;
}

function getCurrencyAssigned($salgrd) {

        $sqlQString="SELECT b.CURRENCY_ID, b.CURRENCY_NAME, a.SALCURR_DTL_MINSALARY, a.SALCURR_DTL_MAXSALARY, a.SALCURR_DTL_STEPSALARY FROM HS_HR_CURRENCY_TYPE b, HS_PR_SALARY_CURRENCY_DETAIL a WHERE a.CURRENCY_ID = b.CURRENCY_ID AND a.SAL_GRD_CODE = '" . $salgrd ."'";
		$sqlQString=strtolower($sqlQString);

	return $sqlQString;
	}

	/**
	 * Select from multiple tables
	 */
	function selectFromMultipleTable($arrFields, $arrTables, $joinConditions, $selectConditions = null, $joinTypes = null, $selectOrderBy = null, $selectOrder = null, $selectLimit = null, $groupBy = null, $distinct = false) {

		if (!isset($joinTypes)) {
			$joinTypes = array_fill(1, count($arrTables)-1, "LEFT");
		}

		$query = $this->_buildSelect($arrFields, $distinct);

		$query .= " FROM ";

		$joins = $arrTables[0];

		for ($i=1; $i < count($arrTables); $i++) {
			$joins = "( ".$joins." ".$joinTypes[$i]." JOIN ".$arrTables[$i]." ON ( ".$joinConditions[$i]." ) ) ";
		}

		$query .= $joins;

		if (isset($selectConditions)) {
			$query .= $this->_buildWhere($selectConditions);
		}

		if (isset($groupBy)) {
			$query .= " GROUP BY $groupBy";
		}

		if (isset($selectOrderBy)) {
			$query .= " ORDER BY $selectOrderBy $selectOrder";
		}

		if (isset($selectLimit)) {
			$query .= " LIMIT $selectLimit";
		}

		return $query;
	}

	/**
	 * Return a count of matching rows from one or multiple tables.
     *
     * @param arrTables array of tables to search in
     * @param joinConditions join conditions for the tables. Can be null
     * @param selectConditions conditions for the where clause. Can be null
     * @param joinTypes join types to use. Can be null.
     *
     * @return SQL query string that will return a count with the given conditions.
	 */
	public function countFromMultipleTables($arrTables, $joinConditions, $selectConditions, $joinTypes = null) {

        if (!isset($joinTypes) && count($arrTables) > 1) {
			$joinTypes = array_fill(1, count($arrTables)-1, "LEFT");
		}

		$query = "SELECT COUNT(*) FROM ";

		$joins = $arrTables[0] . " ";

		for ($i=1; $i < count($arrTables); $i++) {
			$joins = "( ".$joins." ".$joinTypes[$i]." JOIN ".$arrTables[$i]." ON ( ".$joinConditions[$i]." ) ) ";
		}

		$query .= $joins;

		if (isset($selectConditions)) {
			$query .= $this->_buildWhere($selectConditions);
		}

		return $query;
	}


	function simpleInsert($insertTable, $insertValues, $insertFields=false, $onDuplicateUpdate=null) {

		if (is_array($insertValues)) {
			$this->flg_insert = true;

			$this->table_name = $insertTable;
		   	$this->arr_insert = $insertValues;

			if ($insertFields) {
				$this->arr_insertfield = $insertFields;
				$query = $this->addNewRecordFeature2('true');
			} else {
				$query = $this->addNewRecordFeature1('true');
			}
		} else {
			/* For Encryption : Begins */
			$encOn = KeyHandler::KeyExists();
			if ($encOn && CryptoQuery::isEncTable($this->table_name)) {
	    		$insertFields = CryptoQuery::prepareEncryptFields($insertFields, $insertValues);
			}
			/* For Encryption : Ends */

			$query = "INSERT INTO $insertTable ";
			if ($insertFields) {
				$query .= "({$this->_buildList($insertFields, " , ")}) ";
			}
			$query .= "$insertValues";
		}

		if (isset($onDuplicateUpdate) && isset($insertFields)) {
			$query .= "ON DUPLICATE KEY UPDATE {$this->_buildFormattedList($insertFields, $this->quoteCorrect($insertValues), " = ", "", ",")}";
		}

		return $query;
	}

	function simpleUpdate($updateTable, $changeFields, $changeValues, $updateConditions, $quoteCorrect=true) {

		if ($quoteCorrect) {
			$changeValues = $this->quoteCorrect($changeValues);
		}

		/* For Encryption : Begins */
		$encOn = KeyHandler::KeyExists();
		if ($encOn && CryptoQuery::isEncTable($updateTable)) {
                    $changeValues = CryptoQuery::prepareEncryptFields($changeFields, $changeValues);
		}
		/* For Encryption : Ends */	

		$query = "UPDATE $updateTable ".$this->_buildSet($changeFields, $changeValues).$this->_buildWhere($updateConditions);

		return $query;
	}

	function simpleSelect($selectTable, $selectFields, $selectConditions=null, $selectOrderBy=null, $selectOrder = null, $selectLimit=null) {

		/* For Encryption : Begins */
		$encOn = KeyHandler::KeyExists();
		if ($encOn && CryptoQuery::isEncTable($this->table_name)) {
	    	$selectFields = CryptoQuery::prepareDecryptFields($selectFields);
		}
		/* For Encryption : Ends */

		$query=$this->_buildSelect($selectFields)." FROM $selectTable ";

		if (isset($selectConditions)) {
			$query .= $this->_buildWhere($selectConditions);
		}

		if (isset($selectOrderBy)) {
			$query .= " ORDER BY $selectOrderBy $selectOrder";
		}

		if (isset($selectLimit)) {
			$query .= " LIMIT $selectLimit";
		}
		
		return $query;
	}

	function simpleDelete($deleteTable, $deleteConditions) {
		$query = "DELETE FROM " . $deleteTable . " ";
		$query .= $this->_buildWhere($deleteConditions);

		return $query;
	}

	function _buildWhere($selectConditions) {

		$query = "WHERE ".$this->_buildList($selectConditions, " AND ");

		return $query;
	}

	function _buildSelect($arrFields, $distinct = false) {

		if ($distinct) {
			$query = "SELECT DISTINCT ".$this->_buildList($arrFields, " , ");
		} else {
			$query = "SELECT ".$this->_buildList($arrFields, " , ");
		}

		return $query;
	}

	function _buildSet($arrFields, $arrValues) {

		$query = "SET ".$this->_buildFormattedList($arrFields, $arrValues, " = ", "", ",");

		return $query;
	}

	function _buildFormattedList($arrFields, $arrValues, $strJoiner, $strPrepend = "", $strAppend = "") {

		$query = "";

		for ($i=0; $i < count($arrFields); $i++) {
			$query .= sprintf(" %s %s %s %s %s ", $strPrepend, $arrFields[$i], $strJoiner, $arrValues[$i], $strAppend);
		}

		$query = $this->_trimLastChar($query, $strAppend);

		return $query;
	}

	/*
	 *	Handy function to build a list out of an array
	 *	Can be used in building all types of SQL statements
	 *
	 *	Arguements
	 *	----------
	 *
	 *	$arrLists 	-	1D array	-	Array of the values (mixed)
	 *	$strJoiner	-	String		-	String to be used in joining
	 *
	 *	Return
	 *	------
	 *
	 *	String that can be used in a SQL statements
	 *	E.x. filed1, filed2, filed3
	 *
	 **/

	function _buildList($arrList, $strJoiner=" , ") {

		$query = implode($strJoiner, $arrList);

		$query = $this->_trimLastChar($query, $strJoiner);

		return $query;

	}


	/*
	 *	Trims a string of the glue word used to join array elements.
	 *	used in function _buildList
	 *
	 *	Arguements
	 *	----------
	 *
	 *	$subject 	-	String	-	String to be trimmed
	 *	$strJoiner	-	String	-	String to used in joining
	 *
	 *	Return
	 *	------
	 *
	 *	String with last glue word removed
	 *
	 *
	 **/

	function _trimLastChar($subject, $strJoiner = " , ") {

		$str = preg_replace("/".$strJoiner."$/", "", trim($subject));

		return $str;
	}
	
}
?>
