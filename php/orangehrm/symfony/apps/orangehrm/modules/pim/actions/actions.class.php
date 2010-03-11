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
 * Actions class for PIM module
 */
class pimActions extends sfActions {

    /**
     * Index action. Displays employee list
     *
     * @param sfWebRequest $request
     */
    public function executeIndex(sfWebRequest $request) {

        $oc = OrangeConfig::getInstance();
        $aa = $oc->getConf();

        $this->sorter = new ListSorter('emplist.sort', 'pim_module', $this->getUser(), array('employeeId', ListSorter::ASCENDING));

        // Sorting
        if ($request->getParameter('sort')) {
            $this->sorter->setSort(array($request->getParameter('sort'), $request->getParameter('order')));
        }

        // Pager
        if ($request->getParameter('page')) {
            $this->setPage($request->getParameter('page'));
        }

        // Reset filters if requested to
        if ($request->hasParameter('_reset')) {
            $this->setFilters(array());
        }

        $this->form = new EmployeeSearchForm($this->getFilters());
        if ($request->isMethod('post')) {

            $this->form->bind($request->getParameter($this->form->getName()));

            if ($this->form->isValid()) {
                $this->setFilters($this->form->getValues());
            }

            $this->setPage(1);
        }

        $sort = $this->sorter->getSort();
        $filters = $this->getFilters();
        $search = array();
        if (isset($filters['search_by']) && isset($filters['search_for'])) {
            $search = array($filters['search_by'] => $filters['search_for']);
        }

        $table = Doctrine::getTable('Employee');
        $count = $table->getEmployeeCount($search);

        $this->pager = new SimplePager('Employee', sfConfig::get('app_items_per_page'));

        //$this->pager->setQuery($query);
        $this->pager->setPage($this->getPage('page'));
        $this->pager->setNumResults($count);
        $this->pager->init();

        $offset = $this->pager->getOffset();
        $limit = $this->pager->getMaxPerPage();

        $this->employee_list = $table->getEmployeeList($sort[0], $sort[1], $search, $offset, $limit);
    }

    /**
     * Delete action. Deletes the employees with the given ids
     */
    public function executeDelete(sfWebRequest $request) {

        $ids = $request->getParameter('ids[]');

        $count = Doctrine::getTable('Employee')->delete($ids);

        if ($count == count($ids)) {
            $this->getUser()->setFlash('success', 'The selected items have been deleted successfully.');
        } else {
            $this->getUser()->setFlash('error', 'A problem occured when deleting the selected items.');
        }

        $this->setPage(1);
        $this->redirect('pim/index');
    }

    /**
     * Add a new employee
     */
    public function executeAddEmployee(sfWebRequest $request) {

        $this->form = new EmployeeAddForm();

        if ($this->getRequest()->isMethod('post')) {

            // Handle the form submission
           $this->form->bind($request->getPostParameters(), $request->getFiles());

           if ( $this->form->isValid() ) {

               // save data
               $employee = $this->form->getEmployee();
               $service = new EmployeeService();
               $service->addEmployee($employee);

               // change to full pim edit view
               $this->redirect('pim/viewEmployee/' . $employee->empNumber);
           }

        }
    }

    /**
     * View employee details
     * @param int $empNumber Employee number
     */
    public function executeViewEmployee(sfWebRequest $request) {
        $empNumber = $request->getParameter('empNumber');
        $service = new EmployeeService();
        $this->employee = $service->getEmployee($empNumber);
        $countryService = new CountryService();
        $this->countries = $countryService->getCountryList();
        $this->provinces = $countryService->getProvinceList();

        $nationalityService = new NationalityService();

        $this->nationalities = $nationalityService->getNationalityList();
        $this->races = $nationalityService->getEthnicRaceList();


        $this->locRights = array();
        $this->locRights['add'] = true;
        $this->reqcode = "ESS";
        $this->getArr = array();
        $this->getArr['reqcode'] = "ESS";
        $this->getArr['capturemode'] = 'updatemode';
        $this->getArr['id'] = $empNumber;
        $this->postArr = array();
        $this->postArr['pane'] = 1;
        $this->postArr['txtShowAddPane'] = false;
        $this->postArr['EditMode'] = false;

        $this->popArr = array();
    }

    public function executeViewPhoto(sfWebRequest $request) {
        $empNumber = $request->getParameter('id');

		$tmpName = ROOT_PATH . '/themes/beyondT/pictures/default_employee_image.gif';
		$fp = fopen($tmpName,'r');
		$contents = fread($fp,filesize($tmpName));
		fclose($fp);

		$response = $this->getResponse();
		$response->setContentType("image/gif");
		$response->setContent($contents);
		$response->send();
    }
    
    public function executeGetSupervisorListAsJson(sfWebRequest $request) {
    	
    	$employeeService = new EmployeeService();
    	
    	echo $employeeService->getSupervisorListAsJson($request->getParameter('empId'));
    	return sfView::NONE;
        
    }
    
    public function executeGetSupervisorListAsString(sfWebRequest $request) {
    	
    	$employeeService = new EmployeeService();
    	
    	echo $employeeService->getSupervisorListAsString($request->getParameter('empId'));
    	return sfView::NONE;
        
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
