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
 * @copyright 2006 hSenid Software International Pvt. Ltd, http://www.hsenid.com
 */

class ReportXml {

	private $tables=null;
	private $join=null;
	private $fields=null;

	private $simpleXmlObj;

	public function __construct($xml) {
		$this->simpleXmlObj = new SimpleXMLElement($xml);
	}

	public function parseXml() {
		$this->_extractTables();
	}

	private function _extractTables() {
		$tableElements = $this->simpleXmlObj->xpath("//table");
		foreach ($tableElements as $tableElement) {
			if (isset($tableElement['id'])) {
				$this->tables[$tableElement['id']] = $tableElement['name'];
			} else {
				$this->tables[] = $tableElement['name'];
			}
			$this->_extractFields($tableElement);
		}
	}

	private function _extractFields($tableElement) {
		$fieldElements = $tableElement->xpath("//field");
		foreach ($fieldElements as $fieldElement) {
			if (isset($fieldElement['id'])) {
				$this->fields[$fieldElement['id']] = $fieldElement['name'];
			} else {
				$this->fields[] = $fieldElement['name'];
			}
		}
	}

	private function _extractJoin($tableElement) {
		$tableElements = $this->simpleXmlObj->xpath("//table");
		foreach ($tableElements as $tableElement) {
			if (isset($tableElement) && isset($tableElement)) {
				$pairElements = $tableElement->xpath("joins/pair");
				$joinArr = null;
				foreach ($pairElements as $pairElement) {
					$qStr = "{$this->fields[$pairElement['field1']]} {$pairElement['compare']} {$this->fields[$pairElement['field2']]}";
					if (isset($pairElement['id'])) {
						$joinArr[$pairElement['id']] = $qStr;
					} else {
						$joinArr[] =  $qStr;
					}
				}
				if (isset($tableElement['id'])) {
					$this->join[$tableElement['id']] = $joinArr;
				} else {
					$this->join[] = $joinArr;
				}
			}
		}
	}
}

?>