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
class viewProjectsAction extends sfAction {

	private $projectService;

	public function getProjectService() {
		if (is_null($this->projectService)) {
			$this->projectService = new ProjectService();
			$this->projectService->setProjectDao(new ProjectDao());
		}
		return $this->projectService;
	}

	/**
	 *
	 * @param <type> $request
	 */
	public function execute($request) {

		$isPaging = $request->getParameter('pageNo');
		$sortField = $request->getParameter('sortField');
		$sortOrder = $request->getParameter('sortOrder');

		$pageNumber = $isPaging;
		$noOfRecords = Project::NO_OF_RECORDS_PER_PAGE;
		$offset = ($pageNumber >= 1) ? (($pageNumber - 1) * $noOfRecords) : ($request->getParameter('pageNo', 1) - 1) * $noOfRecords;
		$customerList = $this->getProjectService()->getProjectList($noOfRecords, $offset, $sortField, $sortOrder);
		$this->_setListComponent($customerList, $noOfRecords, $pageNumber);
		$params = array();
		$this->parmetersForListCompoment = $params;

		if ($this->getUser()->hasFlash('templateMessage')) {
			list($this->messageType, $this->message) = $this->getUser()->getFlash('templateMessage');
		}
	}
	
	/**
	 *
	 * @param <type> $customerList
	 * @param <type> $noOfRecords
	 * @param <type> $pageNumber
	 */
	private function _setListComponent($customerList, $noOfRecords, $pageNumber) {

		$configurationFactory = new ProjectHeaderFactory();
		ohrmListComponent::setPageNumber($pageNumber);
		ohrmListComponent::setConfigurationFactory($configurationFactory);
		ohrmListComponent::setListData($customerList);
		ohrmListComponent::setItemsPerPage($noOfRecords);
		ohrmListComponent::setNumberOfRecords($this->getProjectService()->getProjectCount());
	}

}

?>
