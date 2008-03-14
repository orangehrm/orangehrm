<?php
/**
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
 *
 */

class HspAllotment {

	const HSP_ALLOTMENT_DB_TABLE = 'hs_hr_hsp_allotment';
	const HSP_ALLOTMENT_DB_FIELD_ID = 'id';
	const HSP_ALLOTMENT_DB_FIELD_NAME = 'name';
	const HSP_ALLOTMENT_DB_FIELD_DESCRIPTION = 'description';

	private $id;
	private $name;
	private $description;

	public function __construct() {
		// nothing to do
	}

	public function setId($id) {
		$this->id=$id;
	}

	public function getId() {
		return $this->id;
	}

	public function setName($name) {
		$this->name=$name;
	}

	public function getName() {
		return $this->name;
	}

	public function setDescription($description) {
		$this->description=$description;
	}

	public function getDescription() {
		return $this->description;
	}

	public static function fetchHspAllotments() {
		$sqlBuilder = new SQLQBuilder();

		$selectTable = "`".self::HSP_ALLOTMENT_DB_TABLE."`";

		$selectFields[] = "`".self::HSP_ALLOTMENT_DB_FIELD_ID."`";
		$selectFields[] = "`".self::HSP_ALLOTMENT_DB_FIELD_NAME."`";
		$selectFields[] = "`".self::HSP_ALLOTMENT_DB_FIELD_DESCRIPTION."`";

		$selectOrder = "ASC";
		$selectOrderBy = "`".self::HSP_ALLOTMENT_DB_FIELD_ID."`";

		$query = $sqlBuilder->simpleSelect($selectTable, $selectFields, null, $selectOrderBy, $selectOrder);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection->executeQuery($query);

		return self::_buildObjArr($result);
	}

	private static function _buildObjArr($result) {

		$objArr = array();

		while ($row = mysql_fetch_assoc($result)) {
			$tmpArr = new HspAllotment();
			$tmpArr->setId($row[self::HSP_ALLOTMENT_DB_FIELD_ID]);
			$tmpArr->setName($row[self::HSP_ALLOTMENT_DB_FIELD_NAME]);
			$tmpArr->setDescription($row[self::HSP_ALLOTMENT_DB_FIELD_DESCRIPTION]);

			$objArr[] = $tmpArr;
		}

		return $objArr;
	}
}
?>
