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
 */

require_once ROOT_PATH . '/lib/dao/DMLFunctions.php';
require_once ROOT_PATH . '/lib/dao/SQLQBuilder.php';
require_once ROOT_PATH . '/lib/confs/sysConf.php';
require_once ROOT_PATH . '/lib/common/CommonFunctions.php';

class Customer{


/**
 * Customer status constants ..
 */

	const CUSTOMER_DELETED = 1;
	const CUSTOMER_NOT_DELETED = 0;

	/**
	 * Table Name
	 */

	const TABLE_NAME = 'hs_hr_customer';

	//Table field names

	const CUSTOMER_DB_FIELDS_ID = 'customer_id';
	const CUSTOMER_DB_FIELDS_NAME = 'name';
	const CUSTOMER_DB_FIELDS_DESCRIPTION = 'description';
	const CUSTOMER_DB_FIELDS_DELETED = 'deleted';


	/**
	 * Class Attributes
	 */

	private $customerId ;
	private $customerName;
	private $customerDescrption;

	/**
	 * Automatic id genaration
	 */

	private  $singleField;
	private  $maxidLength = '4';

	/**
	 *	Setter method followed by getter method for each
	 *	attribute
	 */
	public function setCustomerId($customerId) {
			$this->customerId = $customerId;
	}

	public function getCustomerId () {
		return $this->customerId;
	}

	public function setCustomerName($customerName){
		$this->customerName  = 	$customerName ;
	}

	public function getCustomerName(){
		return $this->customerName;
	}

	public function setCustomerDescription ($customerDescrption) {
		$this->customerDescrption = $customerDescrption ;
	}

	public function getCustomerDescription () {
		return $this->customerDescrption;
	}

	/**
	 *
	 */
	public function addCustomer() {

		$this->getCustomerId();

		$arrRecord[0] = "'". $this->getCustomerId() . "'";
		$arrRecord[1] = "'". $this->getCustomerName() . "'";
		$arrRecord[2] = "'". $this->getCustomerDescription() . "'";
		$arrRecord[3] = self::CUSTOMER_NOT_DELETED;

		$tableName = self::TABLE_NAME;

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_insert = 'true';
		$sql_builder->arr_insert = $arrRecord;


		$sqlQString = $sql_builder->addNewRecordFeature1();

		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		//echo $sqlQString;

		return $message2;
	}

	/**
	 *
	 */
	public	function updateCustomer() {

		$arrRecord[0] = "'". $this->getCustomerId() . "'";
		$arrRecord[1] = "'". $this->getCustomerName() . "'";
		$arrRecord[2] = "'". $this->getCustomerDescription() . "'";
		$arrRecord[3] = self::CUSTOMER_NOT_DELETED;


		$tableName = self::TABLE_NAME;

		$arrFieldList[0] = self::CUSTOMER_DB_FIELDS_ID;
		$arrFieldList[1] = self::CUSTOMER_DB_FIELDS_NAME;
		$arrFieldList[2] = self::CUSTOMER_DB_FIELDS_DESCRIPTION;
		$arrFieldList[3] = self::CUSTOMER_DB_FIELDS_DELETED;

		return $this->updateRecord($tableName,$arrFieldList,$arrRecord);
	}

	/**
	 *
	 *
	 *
	 */

 public function deletewrapperCustomer ($arrList) {


	$i=0;
	$array_count = count($arrList,COUNT_RECURSIVE)- 1;
	  for ($i=0; $i <  $array_count;$i++){

	 		 $this->setCustomerId( $arrList[0][$i]);
	 	return	 $this->deleteCustomer();

	 	 }

	 }
	public function deleteCustomer() {


		$arrRecordsList[0] = "'". $this->getCustomerId() ."'";
		$arrRecordsList[1] = "'". self::CUSTOMER_DELETED ."'";


		$tableName = self::TABLE_NAME;

		$arrFieldList[0] = self::CUSTOMER_DB_FIELDS_ID;
		$arrFieldList[1] = self::CUSTOMER_DB_FIELDS_DELETED ;

		return $this->updateRecord($tableName,$arrFieldList,$arrRecordsList);
	}


	/**
	 * To update the records reuse this function
	 */
	private function updateRecord($tableName,$arrFieldList,$arrRecordsList){
		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_update = 'true';
		$sql_builder->arr_update = $arrFieldList;
		$sql_builder->arr_updateRecList = $arrRecordsList;

		$sqlQString = $sql_builder->addUpdateRecord1(0);

		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		return $message2;

	}

	/**
	 *
	 */
	public function getListofCustomers($pageNO,$schStr,$mode,$sortField = 0, $sortOrder = 'ASC') {
print $schStr;
		$customerArr = $this->fetchCustomers($pageNO,$schStr,$mode, $sortField, $sortOrder);

		$arrDispArr = null;
		for($i=0; count($customerArr) > $i; $i++) {

			$arrDispArr[$i][0] = $customerArr[$i]->getCustomerId();
			$arrDispArr[$i][1] = $customerArr[$i]->getCustomerName();
			$arrDispArr[$i][2] = $customerArr[$i]->getCustomerDescription();

		}

		return $arrDispArr;
	}

	/**
	 *
	 */
	public function fetchCustomers($pageNO=0,$schStr='',$schField=-1, $sortField=0, $sortOrder='ASC') {

		$arrFieldList[0] = self::CUSTOMER_DB_FIELDS_ID;
		$arrFieldList[1] = self::CUSTOMER_DB_FIELDS_NAME;
		$arrFieldList[2] = self::CUSTOMER_DB_FIELDS_DESCRIPTION;
		$arrFieldList[3] = self::CUSTOMER_DB_FIELDS_DELETED;

		$tableName = "`".self::TABLE_NAME."`";

		$sql_builder = new SQLQBuilder();

		$arrSelectConditions[0] = "`".self::CUSTOMER_DB_FIELDS_DELETED."`= ".self::CUSTOMER_NOT_DELETED."";

		if ($schField != -1) {
			$arrSelectConditions[1] = "`".$arrFieldList[$schField]."` LIKE '%".$schStr."%'";
		}

		$limitStr = null;

		if ($pageNO > 0) {
			$sysConfObj = new sysConf();
			$page = ($pageNO-1)*$sysConfObj->itemsPerPage;
			$limit = $sysConfObj->itemsPerPage;
			$limitStr = "$page,$limit";
			//echo $limitStr;
		}
		$sqlQString = $sql_builder->simpleSelect($tableName, $arrFieldList, $arrSelectConditions, $arrFieldList[0], 'ASC', $limitStr);



		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		return $this->customerObjArr($message2) ;
	}

	/**
	 *
	 */
	public function fetchCustomer($cusId) {

		$selectTable = "`".self::TABLE_NAME."`";

		$arrFieldList[0] = self::CUSTOMER_DB_FIELDS_ID;
		$arrFieldList[1] = self::CUSTOMER_DB_FIELDS_NAME;
		$arrFieldList[2] = self::CUSTOMER_DB_FIELDS_DESCRIPTION;

		$arrSelectConditions[0] = "`".self::CUSTOMER_DB_FIELDS_ID."` = $cusId";
		$arrSelectConditions[1] = "`".self::CUSTOMER_DB_FIELDS_DELETED."`= ".self::CUSTOMER_NOT_DELETED."";

		$sqlBuilder = new SQLQBuilder();
		$query = $sqlBuilder->simpleSelect($selectTable, $arrFieldList, $arrSelectConditions, null, null, 1);
		$dbConnection = new DMLFunctions();
		$result = $dbConnection -> executeQuery($query);

		$tempArr =  $this->customerObjArr($result) ;
		return $tempArr[0];
	}

	/**
	 *
	 */
	function getLastRecord() {

		$sql_builder = new SQLQBuilder();
		$tableName = self::TABLE_NAME;
		$arrFieldList[0] = self::CUSTOMER_DB_FIELDS_ID;

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->selectOneRecordOnly();

		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$common_func = new CommonFunctions();

		if (isset($message2)) {

			$i=0;

		while ($line = mysql_fetch_array($message2, MYSQL_ASSOC)) {
			foreach ($line as $col_value) {
			$this->singleField = $col_value;
			}
		}

		return str_pad(((int) $this->singleField)+1, $this->maxidLength, "0", STR_PAD_LEFT);
		}

	}

	/**
	 *
	 */
	public function countcustomerID($schStr,$schField) {

		$tableName = self::TABLE_NAME;
		$arrFieldList[0] = self::CUSTOMER_DB_FIELDS_ID;
		$arrFieldList[1] = self::CUSTOMER_DB_FIELDS_NAME;
		$arrFieldList[2] = self::CUSTOMER_DB_FIELDS_DELETED;

		$schField   = 2;
		$schStr		= 0;

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = $tableName;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $arrFieldList;

		$sqlQString = $sql_builder->countResultset($schStr,$schField);

		//echo $sqlQString;
		$dbConnection = new DMLFunctions();
		$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function

		$line = mysql_fetch_array($message2, MYSQL_NUM);

	    return $line[0];
	}


	/**
	 *
	 */
	public function customerObjArr($result) {

		$objArr = null;
		$tableName = self::TABLE_NAME;


		while ($row = mysql_fetch_assoc($result)) {

			$tmpcusArr = new Customer();

			$tmpcusArr->setCustomerId($row[self::CUSTOMER_DB_FIELDS_ID]);
			$tmpcusArr->setCustomerName($row[self::CUSTOMER_DB_FIELDS_NAME]);
			$tmpcusArr->setCustomerDescription($row[self::CUSTOMER_DB_FIELDS_DESCRIPTION]);

			$objArr[] = $tmpcusArr;
		}

		return $objArr;
	}

}




?>
