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

require_once ROOT_PATH . '/lib/confs/Conf.php';
require_once ROOT_PATH . '/lib/dao/DMLFunctions.php';
require_once ROOT_PATH . '/lib/dao/SQLQBuilder.php';
require_once ROOT_PATH . '/lib/common/CommonFunctions.php';
require_once ROOT_PATH . '/lib/common/UniqueIDGenerator.php';

class PayPeriod {

	const TABLE_NAME = "hs_hr_payperiod";
	const DB_FIELD_PAYPERIOD_CODE = "payperiod_code";
	const DB_FIELD_PAYPERIOD_NAME = "payperiod_name";

	private $code;
	private $name;

	public function __construct() {
	}

	public function setCode($code) {
		$this->code = $code;
	}

	public function getCode() {
		return $this->code;
	}

	public function setName($name) {
		$this->name = $name;
	}

	public function getName() {
		return $this->name;
	}

	/**
	 * Get list of pay periods defined in the system
	 * @return array Array of all pay periods defined in the system
	 */
	public static function getPayPeriodList() {

		$fields[0] = self::DB_FIELD_PAYPERIOD_NAME;
		$fields[1] = self::DB_FIELD_PAYPERIOD_CODE;

		$sql_builder = new SQLQBuilder();

		$sql_builder->table_name = self::TABLE_NAME;
		$sql_builder->flg_select = 'true';
		$sql_builder->arr_select = $fields;

		$sql = $sql_builder->queryAllInformation();

		$dbConnection = new DMLFunctions();
		$result = $dbConnection->executeQuery($sql);

		$periods = array();

		if ($result && mysql_num_rows($result) > 0) {
			while($line = mysql_fetch_assoc($result)) {;
				$period = new PayPeriod();
				$period->setCode($line[self::DB_FIELD_PAYPERIOD_CODE]);
				$period->setName($line[self::DB_FIELD_PAYPERIOD_NAME]);
				$periods[$period->getCode()] = $period;
			}
		}

		return $periods;
	}
}

?>
