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
 */
 require_once ROOT_PATH . '/lib/common/SearchObject.php';

 class EXTRACTOR_ViewList {

	/**
	 * Parse request data and return a SearchObject
	 *
	 * @param Array $postArr Array of POST variables
	 * @param Array $getArr Array of GET variables
	 *
	 * @return SearchObject Search object with parsed data
	 */
	public function parseSearchData($postArr, $getArr) {

		$searchObject = new SearchObject();

        $pageNo = isset ($postArr['pageNO']) ? (int) $postArr['pageNO'] : 1;
        $searchObject->setPageNumber($pageNo);

        if (isset ($postArr['captureState']) && ($postArr['captureState'] == 'SearchMode')) {

            $searchField = $postArr['loc_code'];
            $searchString = trim($postArr['loc_name']);

            $searchObject->setSearchField($searchField);
            $searchObject->setSearchString($searchString);
        }

        if (!isset ($getArr['sortField'])) {
            $getArr['sortField'] = 0;
        }
        $sortOrderField = 'sortOrder' . $getArr['sortField'];

        if (!isset ($getArr[$sortOrderField])) {
            $getArr[$sortOrderField] = 'ASC';
        }

        $sortField = $getArr['sortField'];
        $sortOrder = $getArr[$sortOrderField];

		$searchObject->setSortField($sortField);
		$searchObject->setSortOrder($sortOrder);

		return $searchObject;
	}

}
?>