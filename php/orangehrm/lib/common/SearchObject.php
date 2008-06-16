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

/**
 * Class to represent a search
 */
 class SearchObject {

	const SEARCH_FIELD_NONE = -1;
	const SORT_ORDER_ASC = 'ASC';
	const SORT_ORDER_DESC = 'DESC';

	private $pageNumber = 1;
	private $searchField = self::SEARCH_FIELD_NONE;
	private $searchString = '';
    private $sortField = 0;
    private $sortOrder = self::SORT_ORDER_ASC;

	public function setPageNumber($pageNumber) {
	    $this->pageNumber = $pageNumber;
	}

	public function getPageNumber() {
	    return $this->pageNumber;
	}

	public function setSearchField($searchField) {
	    $this->searchField = $searchField;
	}

	public function getSearchField() {
	    return $this->searchField;
	}

	public function setSearchString($searchString) {
	    $this->searchString = $searchString;
	}

	public function getSearchString() {
	    return $this->searchString;
	}

	public function setSortField($sortField) {
	    $this->sortField = $sortField;
	}

	public function getSortField() {
	    return $this->sortField;
	}

	public function setSortOrder($sortOrder) {
	    $this->sortOrder = $sortOrder;
	}

	public function getSortOrder() {
	    return $this->sortOrder;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
	}
}

?>
