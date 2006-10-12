<?
/*
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
*/

require_once ROOT_PATH . '/lib/confs/Conf.php';
require_once ROOT_PATH . '/lib/dao/DMLFunctions.php';
require_once ROOT_PATH . '/lib/exception/ExceptionHandler.php';
require_once ROOT_PATH . '/lib/confs/sysConf.php';

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
	
	}
	
/*	function quoteCorrect($arr) {
		
		foreach ($arr as $value) {
			if ($value != 'null') {
			
				//$temp = substr($value,1,strlen($value)-2);				
				if (preg_match('//', $value) == 0) {
					$temp = preg_replace(array("/^'/", "/'$/"), array("", ""), trim($value));					
					$temp = mysql_real_escape_string($temp)/*str_replace("'","\'",$temp);
					$tempArr[] = "'" . $temp . "'";
				} else {
					$tempArr[] = $value;
				}				
								
			} else {
				$tempArr[] = $value;
			}
		}
		
		return $arr;
	}

	Function passresultSetMessage Will 
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
    					$SQL1 = $SQL1 . $arrComp[$c][0] . ' = \'' . $arrComp[$c][1] . '\' ';
    				 else 
    					$SQL1 = $SQL1 . $arrComp[$c][0] . ' = \'' . $arrComp[$c][1] .'\'  AND ';
			}

		if(is_array($arrFlt)) {
			$SQL1 = $SQL1 . ') AND ';
			for($c=0;count($arrFlt)>$c;$c++) 
    				if ($c == (count($arrFlt) - 1))   //String Manipulation
    					$SQL1 = $SQL1 . $arrFlt[$c][0] . ' = \'' . $arrFlt[$c][1] . '\' ';
    				 else 
    					$SQL1 = $SQL1 . $arrFlt[$c][0] . ' = \'' . $arrFlt[$c][1] .'\'  AND ';

			$SQL1 = $SQL1 . ' ORDER BY '. $arrayFieldList[0];
		} else
			$SQL1 = $SQL1 . ') ORDER BY '. $arrayFieldList[$sortField];
			
			//$exception_handler = new ExceptionHandler();
	  	 	//$exception_handler->logW($SQL1);

		return $SQL1; //returning the SQL1 which has the SQL Query
	}
	
	function passResultSetMessage($page=0, $schStr='',$schField=-1, $sortField = 0, $sortOrder = 'ASC', $schArr = false) {
	
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
                    		$SQL1 = $SQL1 . $arrayFieldList[$schField[$i]] . ' LIKE \'%' . trim($schStr[$i]) .'%\' AND ';
						 }
						}
						$SQL1 = substr($SQL1,0,-1-4);
					} else {
						$SQL1 = $SQL1 . $arrayFieldList[$schField] . ' LIKE \'%' . trim($schStr) .'%\'';
					}	
					
                    
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
			$SQL1 = 'SELECT ';
			for ($i=0;$i<count($arrayFieldList); $i++) 
				if ($i == ($countArrSize - 1))   //String Manipulation
					$SQL1 = $SQL1 . $arrayFieldList[$i] . ' ';		
				else 
				
					$SQL1 = $SQL1 . $arrayFieldList[$i] . ', ';
			
		$SQL1 = $SQL1 . ' FROM ' . strtolower($this->table_name) . ' WHERE ' . $this->field . ' NOT IN (SELECT ' . $this->field . ' FROM ' . strtolower($this->table2_name) . ' ) '; 

				if($mode!=0)
				{
                $SQL1 = $SQL1 . ' AND ';
                    switch($mode)
                            {
                              case 1 : $SQL1 = $SQL1 . $arrayFieldList[0] . ' LIKE \'%' . trim($str) .'%\'';
                                        break;
                              case 2:  $SQL1 = $SQL1 . $arrayFieldList[1] . ' LIKE \'%' . trim($str) .'%\'';
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
                              case 1 : $SQL1 = $SQL1 . $arrayFieldList[0] . ' LIKE \'%' . trim($str) .'%\'';
                                        break;
                              case 2:  $SQL1 = $SQL1 . $arrayFieldList[1] . ' LIKE \'%' . trim($str) .'%\'';
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
	
	function addNewRecordFeature1() { 
		
		if ($this->flg_insert == 'true') { // check whether the flg_insert is 'True'
						
			$arrayFieldList = $this->arr_insert; //assign the sql_format->arr_select instance variable to arrayFieldList
			$countArrSize = count($arrayFieldList); // check the array size
			
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

	function addNewRecordFeature2() { 
		
		if ($this->flg_insert == 'true') { // check whether the flg_insert is 'True'

			$arrayFieldList = $this->arr_insertfield;		
			$arrayRecordList = $this->arr_insert; //assign the sql_format->arr_select instance variable to arrayFieldList
			$countArrSize = count($arrayFieldList); // check the array size
			
			$SQL1 = 'INSERT INTO ' . strtolower($this->table_name) . ' ( ';

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
		
		
		if ($this->flg_select == 'true') { // check whether the flg_select is 'True'
						
			$arrayFieldList = $this->arr_select; //assign the sql_format->arr_select instance variable to arrayFieldList
			$SQL1 = 'SELECT MAX(' . $arrayFieldList[0] . ') FROM ' . strtolower($this->table_name); //Tail of the SQL statement
				
			if($n>0) {
				$SQL1 = $SQL1 . ' WHERE ' . $arrayFieldList[1] . '=' . "'" . $str[0] . "'";
                  for($c = 1 ; $c < $n ; $c++)
                    $SQL1 = $SQL1 . ' AND '. $arrayFieldList[($c+1)] . '=' . "'" . $str[$c] . "'";
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
			
				$SQL1 = $SQL1 . ' FROM ' . strtolower($this->table_name) . ' WHERE ' . $field . '=' . "'" . $filID . "'";  //Tail of the SQL statement
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
	
///////////////////
	
	function selectOneRecordFiltered($filID, $num=0) {
				
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
			
			if($num==0)
				$SQL1 = $SQL1 . ' FROM ' . strtolower($this->table_name) . ' WHERE ' . $arrayFieldList[0] . '=' . "'" . $filID . "'";  //Tail of the SQL statement
            else
                {
                  $SQL1 = $SQL1 . ' FROM ' . strtolower($this->table_name) . ' WHERE ' . $arrayFieldList[0] . '=' . "'" . $filID[0] . "'";
                  for($c = 1 ; $c <= $num ; $c++)
                    $SQL1 = $SQL1 . ' AND '. $arrayFieldList[$c] . "=" . "'" . $filID[$c] . "'";
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
			
				$SQL1 = $SQL1 . ' FROM ' . strtolower($this->table_name) . ' WHERE ' . $arrayFieldList[0] . '<>' . "'" . $filID . "'";  //Tail of the SQL statement
								
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

	function addUpdateRecord1($num=0) {
		
		if ($this->flg_update == 'true') { // check whether the flg_insert is 'True'
						
			$arrayFieldList = $this->arr_update; //assign the sql_format->arr_select instance variable to arrayFieldList
			$arrayRecordSet = $this->arr_updateRecList;
			$countArrSize = count($arrayFieldList); // check the array size
			
			$SQL1 = 'UPDATE ' . strtolower($this->table_name) . ' SET ';
			
			for ($i = $num + 1; $i<count($arrayFieldList); $i++) {
				
				if ($i == ($countArrSize - 1))  { //String Manipulation
				
					$SQL1 = $SQL1 . $arrayFieldList[$i] . '=' . $arrayRecordSet[$i];		
				
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

/////////////////////////
	function deleteRecord($arrID) {

		if ($this->flg_delete == 'true') { // check whether the flg_select is 'True'

			$arrayFieldList = $this->arr_delete; //assign the sql_format->arr_select instance variable to arrayFieldList
			$countArrSize = count($arrayFieldList); // check the array size

			$SQL1 = 'DELETE FROM ' . strtolower($this->table_name) . ' WHERE ';

            for($j=0;$j<sizeof($arrID[0]);$j++)
            {
    			for ($i=0;$i<count($arrayFieldList); $i++) {

    				if ($i == ($countArrSize - 1))  { //String Manipulation

    					$SQL1 = $SQL1 . $arrayFieldList[$i] . ' = \'' . $arrID[$i][$j] . '\' ';

    				} else {

    					$SQL1 = $SQL1 . $arrayFieldList[$i] . ' = \'' . $arrID[$i][$j] .'\'  AND ';

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
                              case 1 : $SQL1 = $SQL1 . 'a.'. $arrayFieldList[0] .' LIKE \'%' . trim($str) .'%\'';
                                        break;
                              case 2:  $SQL1 = $SQL1 . 'a.'. $arrayFieldList[1] .' LIKE \'%' . trim($str) .'%\'';
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
                	
                	$SQL1 .= ' AND '.$arrayFieldList2[$schField-count($arrayFieldList)] . ' LIKE \'%' . trim($schStr) .'%\'';
                	
                } else {
                	
                	$SQL1 .= ' AND '.$arrayFieldList[$schField] . ' LIKE \'%' . trim($schStr) .'%\'';
                
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
                              case 1 : $SQL1 = $SQL1 . 'a.'. $arrayFieldList[0] .' LIKE \'%' . trim($str) .'%\'';
                                        break;
                              case 2:  $SQL1 = $SQL1 . 'a.'. $arrayFieldList[1] .' LIKE \'%' . trim($str) .'%\'';
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
                
                    switch($mode)
                            {
                              case 1 : $SQL1 = $SQL1 . 'a.EMP_NUMBER LIKE \'%' . trim($str) .'%\'';
                                        break;
                              case 2:  $SQL1 = $SQL1 . 'a.EMP_FULLNAME LIKE \'%' . trim($str) .'%\'';
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
                              case 1 : $SQL1 = $SQL1 . 'a.EMP_NUMBER LIKE \'%' . trim($str) .'%\'';
                                        break;
                              case 2:  $SQL1 = $SQL1 . 'a.EMP_FULLNAME LIKE \'%' . trim($str) .'%\'';
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
                    		$SQL1 = $SQL1 . $arrayFieldList[$schField[$i]] . ' LIKE \'%' . trim($schStr[$i]) .'%\' AND ';
						 }
					}
					$SQL1 = substr($SQL1,0,-1-4);
				} else {
					$SQL1 = $SQL1 . $arrayFieldList[$schField] . ' LIKE \'%' . trim($schStr) .'%\'';
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

function listReports($userGroup, $page, $str = '' ,$mode = 0) {
	
		$SQL1 = "SELECT a.REP_CODE, a.REP_NAME FROM HS_HR_EMPREPORT a, HS_HR_EMPREP_USERGROUP b WHERE a.REP_CODE = b.REP_CODE AND b.USERG_ID = '" .$userGroup . "'";

		if($mode!=0)
				{
                $SQL1 = $SQL1 . " AND ";
                
                    switch($mode)
                            {
                              case 1 : $SQL1 = $SQL1 . 'a.REP_CODE LIKE \'%' . trim($str) .'%\'';
                                        break;
                              case 2:  $SQL1 = $SQL1 . 'a.REP_NAME LIKE \'%' . trim($str) .'%\'';
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
                              case 1 : $SQL1 = $SQL1 . 'a.REP_CODE LIKE \'%' . trim($schStr) .'%\'';
                                        break;
                              case 2:  $SQL1 = $SQL1 . 'a.REP_NAME LIKE \'%' . trim($schStr) .'%\'';
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
	
	$sqlQString = "SELECT b.ESTAT_CODE, b.ESTAT_NAME FROM HS_HR_JOBTIT_EMPSTAT a, HS_HR_EMPSTAT b WHERE a.ESTAT_CODE = b.ESTAT_CODE AND a.JOBTIT_CODE = '" .$jobtit. "'";
	$sqlQString=strtolower($sqlQString);
	
	return $sqlQString;
}

function getCurrencyAssigned($salgrd) {

        $sqlQString="SELECT b.CURRENCY_ID, b.CURRENCY_NAME, a.SALCURR_DTL_MINSALARY, a.SALCURR_DTL_MAXSALARY, a.SALCURR_DTL_STEPSALARY FROM HS_HR_CURRENCY_TYPE b, HS_PR_SALARY_CURRENCY_DETAIL a WHERE a.CURRENCY_ID = b.CURRENCY_ID AND a.SAL_GRD_CODE = '" . $salgrd ."'";
		$sqlQString=strtolower($sqlQString);
	
	return $sqlQString;
	}
	
	
	function selectFromMultipleTable($arrFields, $arrTables, $joinConditions, $selectConditions, $joinType = "LEFT") {
		$query = "SELECT ";
		
		foreach ($arrFields as $arrField) {
			$query .= $arrField." , ";
		}
		
		$query = $this->_trimLastChar($query);
		
		$query .= "FROM ";
		
		$joins = $arrTables[0];
		
		for ($i=1; $i < count($arrTables); $i++) {
			$joins = "( ".$joins." ".$joinType." JOIN ".$arrTables[$i]." ON ( ".$joinConditions[$i]." ) )";
		}
		
		$query .= $joins." WHERE ";
		
		foreach ($selectConditions as $selectCondition) {
			$query .= $selectCondition." AND ";
		}
		
		$query = $this->_trimLastChar($query, "AND");
		
		return $query;
	}
	
	function _trimLastChar($str, $char = ",") {
		
		$str = preg_replace("/".$char."$/", "", trim($str));

		return $str;	
	}

}
?>
