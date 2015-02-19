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
class viewDirectoryAction extends basePimAction {

    public $empList;

    const DIRECTORY_USER_ATTRIBUTE = 'directoryArray';

    public function execute($request) {

        $request->setParameter('initialActionName', 'viewDirectory');
        $isPaging = $request->getParameter('hdnAction') == 'search' ? 1 : $request->getParameter('pageNo', 1);
        $pageNumber = $isPaging;

        if (!empty($empNumber) && $this->getUser()->hasAttribute('pageNumber')) {
            $pageNumber = $this->getUser()->getAttribute('pageNumber');
        }

        $noOfRecords = sfConfig::get('app_items_per_page');
        $offset = ($pageNumber >= 1) ? (($pageNumber - 1) * $noOfRecords) : ($request->getParameter('pageNo', 1) - 1) * $noOfRecords;
        
         // Reset filters if requested to
        if ($request->hasParameter('reset')) {
            $this->setFilters(array());
            $this->setSortParameter(array("field"=> NULL, "order"=> NULL));
            $this->setPage(1);
        }
        
        $this->setForm(new EmployeeDirectorySearchForm($this->getFilters()));

        //handles post method
        if ($request->isMethod('post')) {
            $this->form->bind($request->getParameter($this->form->getName()));

            if ($this->form->isValid()) {
                if ($this->form->getValue('isSubmitted') == 'yes') {
                    $this->setSortParameter(array("field" => NULL, "order" => NULL));
                }
                $this->setFilters($this->form->getValues());
            } else {
                $this->setFilters(array());
            }
            $this->setPage(1);
        }

        //handles get method
        if ($request->isMethod('get')) {
            $sortParam = array("field" => $request->getParameter('sortField'),
                "order" => $request->getParameter('sortOrder'));
            $this->setSortParameter($sortParam);
            $this->setPage(1);
        }

        $filters = $this->getFilters();

        if (isset($filters['emp_name']) && ($filters['emp_name']['empName'] != __('Type for hints...'))) {
            if(strpos($filters['emp_name']['empName'], __('Past Employee')) !== false){
                $filters['termination'] = EmployeeSearchForm::WITH_TERMINATED;
            }
            $filters['employee_name'] = str_replace(' (' . __('Past Employee') . ')', '', $filters['emp_name']['empName']);
        }

        $this->filterApply = !empty($filters);

        $empList = $this->getEmployeeService()->getEmployeeIdList();

        if (count($empList) > 0) {
            $filters['employee_id_list'] = $empList;
            $count = $this->getDirectoryService()->getSearchEmployeeCount($filters);
            $parameterHolder = new EmployeeSearchParameterHolder();
            $parameterHolder->setOrderField($sortField);
            $parameterHolder->setOrderBy($sortOrder);
            $parameterHolder->setLimit($noOfRecords);
            $parameterHolder->setOffset($offset);
            $parameterHolder->setFilters($filters);
            $this->list = $this->getDirectoryService()->searchEmployees($parameterHolder);
        } else {
            $count = 0;
            $list = array();
        }
        $recordsLimit = sfConfig::get('app_items_per_page'); //self::$itemsPerPage;//
        $pageNo = $request->getParameter('pageNo', 1);
        /*         * if (self::$pageNumber) {
          $pageNo = self::$pageNumber;
          } else { */
        //$pageNo = 1; //$request->getParameter('pageNo', 1);
        //}

        $numberOfRecords = $count; //replace with the count of all the records(self::$listData instanceof Doctrine_Collection) ? self::$listData->count() : count(self::$listData); // TODO: Remove the dependancy of ORM here; Use a Countable interface and a Iterator interface

        if($recordsLimit < $numberOfRecords ){
            $this->isPaginated = TRUE;
        }
        $pager = new SimplePager($this->className, $recordsLimit);
        $pager->setPage($pageNo);
        $pager->setNumResults($numberOfRecords);
        $pager->init();

        $offset = $pager->getOffset();
        $offset = empty($offset) ? 0 : $offset;

        $this->offset = $offset;
        $this->pager = $pager;
        $this->recordLimit = $recordsLimit;

        //$this->currentSortField = $request->getParameter('sortField', '');
        //$this->currentSortOrder = $request->getParameter('sortOrder', '');
    }

    public function setForm(sfForm $form) {
        if (is_null($this->form)) {
            $this->form = $form;
        }
    }

    protected function getSearchForm() {
        return new EmployeeDirectorySearchForm(array(), array(), true);
    }

    protected function getFilters() {
        return $this->getUser()->getAttribute('empdirlist.filters', null, 'directory_module');
    }

    protected function setFilters(array $filters) {
        return $this->getUser()->setAttribute('empdirlist.filters', $filters, 'directory_module');
    }

    public function getEmployeeService() {
        if (is_null($this->employeeService)) {
            $this->employeeService = new EmployeeService();
            $this->employeeService->setEmployeeDao(new EmployeeDao());
        }
        return $this->employeeService;
    }

    public function getDirectoryService() {
        if (is_null($this->directoryService)) {
            $this->directoryService = new EmployeeDirectoryService();
            $this->directoryService->setEmployeeDirectoryDao(new EmployeeDirectoryDao());
        }
        return $this->directoryService;
    }

    protected function setPage($page) {
        $this->getUser()->setAttribute('empdirlist.page', $page, 'directory_module');
    }

    protected function setSortParameter($sort) {
        $this->getUser()->setAttribute('empdirlist.sort', $sort, 'directory_module');
    }

}