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

/**
 * View employee list action
 */
class viewEmployeeListAction extends sfAction {

    private $employeeService;

    /**
     * Get EmployeeService
     * @returns EmployeeService
     */
    public function getEmployeeService() {
        if (is_null($this->employeeService)) {
            $this->employeeService = new EmployeeService();
            $this->employeeService->setEmployeeDao(new EmployeeDao());
        }
        return $this->employeeService;
    }

    /**
     * Set EmployeeService
     * @param EmployeeService $employeeService
     */
    public function setEmployeeService(EmployeeService $employeeService) {
        $this->employeeService = $employeeService;
    }

    /**
     * Index action. Displays employee list
     *      `
     * @param sfWebRequest $request
     */
    public function execute($request) {

        // Check if admin mode or supervisor mode
        $userType = 'Admin';
        $this->adminMode = $this->getUser()->hasCredential(Auth::ADMIN_ROLE);
        
        if ($this->getUser()->hasFlash('templateMessage')) {
            list($this->messageType, $this->message) = $this->getUser()->getFlash('templateMessage');
        }

        if (!$this->adminMode) {
            $this->supervisorMode = $this->getUser()->hasCredential(Auth::SUPERVISOR_ROLE);
            $userType = 'Supervisor';
        } else {
            $this->supervisorMode = false;
        }

        if (!$this->adminMode && !$this->supervisorMode) {
            return $this->forward("pim", "unauthorized");
        }

        $this->sorter = new ListSorter('emplist.sort', 'pim_module', $this->getUser(), array('lastName', ListSorter::ASCENDING));

        // Sorting
        if ($request->getParameter('sort')) {
            $this->sorter->setSort(array($request->getParameter('sort'), $request->getParameter('order')));
        }

        // Pager
        if ($request->getParameter('page')) {
            $this->setPage($request->getParameter('page'));
        }

        $params = array('userType'=> $userType, 'loggedInUserId'=>$this->getUser()->getEmployeeNumber());
        $this->form = new EmployeeSearchForm($this->getFilters(), $params);
        if ($request->isMethod('post')) {

            $this->form->bind($request->getParameter($this->form->getName()));

            if ($this->form->isValid()) {
                $this->setFilters($this->form->getValues());
            } else {
                $this->setFilters(array());
            }

            $this->setPage(1);
        }

        // Reset filters if requested to
        if ($request->hasParameter('_reset')) {
            $this->setFilters(array());
        }

        $sort = $this->sorter->getSort();
        $filters = $this->getFilters();

        $this->filterApply = !empty($filters);


        if ($this->supervisorMode) {
            $filters['supervisorId'] = $this->getUser()->getEmployeeNumber();
        }

        $table = Doctrine::getTable('Employee');
        $count = $table->getEmployeeCount($filters);

        $this->pager = new SimplePager('Employee', sfConfig::get('app_items_per_page'));

        $this->pager->setPage($this->getPage('page'));
        $this->pager->setNumResults($count);
        $this->pager->init();

        $offset = $this->pager->getOffset();
        $limit = $this->pager->getMaxPerPage();

        $this->employee_list = $table->getEmployeeList($sort[0], $sort[1], $filters, $offset, $limit);

        // Show message if list is empty, and we don't already have a message.
        if (empty($this->message) && (count($this->employee_list) == 0)) {

            // Check to see if we have any employees in system
            $employeeCount = $this->getEmployeeService()->getEmployeeCount();
            $this->messageType = "warning";

            if (empty($employeeCount)) {
                $this->message = __("No Employees Available in the System");
            } else {
                $this->message = __("No Matching Employees Found");
            }

        }
    }

    /**
     * Set's the current page number in the user session.
     * @param $page int Page Number
     * @return None
     */
    protected function setPage($page) {
        $this->getUser()->setAttribute('emplist.page', $page, 'pim_module');
    }

    /**
     * Get the current page number from the user session.
     * @return int Page number
     */
    protected function getPage() {
        return $this->getUser()->getAttribute('emplist.page', 1, 'pim_module');
    }

    /**
     *
     * @param array $filters
     * @return unknown_type
     */
    protected function setFilters(array $filters) {
        return $this->getUser()->setAttribute('emplist.filters', $filters, 'pim_module');
    }

    /**
     *
     * @return unknown_type
     */
    protected function getFilters() {
        return $this->getUser()->getAttribute('emplist.filters', null, 'pim_module');
    }

}
