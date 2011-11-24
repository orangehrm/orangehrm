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
class viewSystemUsersAction extends sfAction {

	
	private $systemUserService;
	
	
        public function getSystemUserService() {
            $this->systemUserService = new SystemUserService();
            return $this->systemUserService;
        }

	
	public function setForm(sfForm $form) {
		if (is_null($this->form)) {
			$this->form = $form;
		}
	}

	/**
	 *
	 * @param <type> $request
	 */
	public function execute($request) {

		
		
		$isPaging = $request->getParameter('pageNo');
		$sortField = $request->getParameter('sortField');
		$sortOrder = $request->getParameter('sortOrder');
		$userId = $request->getParameter('userId');

		$this->setForm(new SearchProjectForm());

		$pageNumber = $isPaging;
		if ($userId > 0 && $this->getUser()->hasAttribute('pageNumber')) {
			$pageNumber = $this->getUser()->getAttribute('pageNumber');
		}
		$limit =1;
		$offset = ($pageNumber >= 1) ? (($pageNumber - 1) * $limit) : ($request->getParameter('pageNo', 1) - 1) * $limit;
		$searchClues = $this->_setSearchClues($sortField, $sortOrder, $offset, $limit);
		if (!empty($sortField) && !empty($sortOrder) || $isPaging > 0 || $userId > 0) {
			if ($this->getUser()->hasAttribute('searchClues')) {
				$searchClues = $this->getUser()->getAttribute('searchClues');
				$searchClues['offset'] = $offset;
				$searchClues['sortField'] = $sortField;
				$searchClues['sortOrder'] = $sortOrder;				
				$this->form->setDefaultDataToWidgets($searchClues);
			}
		} else {
			$this->getUser()->setAttribute('searchClues', $searchClues);
		}
		
		$systemUserList = $this->getSystemUserService()->getSystemUsers();
		$this->_setListComponent($systemUserList, $limit, $pageNumber, 2);
		$this->getUser()->setAttribute('pageNumber', $pageNumber);
		$params = array();
		$this->parmetersForListCompoment = $params;

		if ($this->getUser()->hasFlash('templateMessage')) {
			list($this->messageType, $this->message) = $this->getUser()->getFlash('templateMessage');
		}

	}

	/**
	 *
	 * @param <type> $projectList
	 * @param <type> $noOfRecords
	 * @param <type> $pageNumber
	 */
	private function _setListComponent($systemUserList, $limit, $pageNumber, $recordCount) {

		$configurationFactory = new SystemUserHeaderFactory();
		
                $configurationFactory->setRuntimeDefinitions(array(
			    'hasSelectableRows' => true,
//			    'buttons' => array(),
			));
		
		ohrmListComponent::setPageNumber($pageNumber);
		ohrmListComponent::setConfigurationFactory($configurationFactory);
		ohrmListComponent::setListData($systemUserList);
		ohrmListComponent::setItemsPerPage($limit);
		ohrmListComponent::setNumberOfRecords($recordCount);
	}

	private function _setSearchClues($sortField, $sortOrder, $offset, $limit) {
		return array(
		    'customer' => $this->form->getValue('customer'),
		    'project' => $this->form->getValue('project'),
		    'projectAdmin' => $this->form->getValue('projectAdmin'),
		    'sortField' => $sortField,
		    'sortOrder' => $sortOrder,
		    'offset' => $offset,
		    'limit' => $limit
		);
	}

}

