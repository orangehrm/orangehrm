<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 *
 * Description of EvaluatePerformanceReviewSearchForm
 *
 * @author nadeera
 */

class EvaluatePerformanceReviewSearchForm extends BasePefromanceSearchForm {

    /**
     * 
     */
    public function configure() {

        $this->setWidgets($this->getFormWidgets());
        $this->setValidators($this->getFormValidators());

        $this->getWidgetSchema()->setNameFormat('evaluatePerformanceReview360SearchForm[%s]');
        $this->getWidgetSchema()->setLabels($this->getFormLabels());
    }

    /**
     *
     * @return array
     */
    public function getStylesheets() {
        $styleSheets = parent::getStylesheets();
        $styleSheets[plugin_web_path('orangehrmPerformancePlugin', 'css/performanceReviewSearchSuccess.css')] = 'all';
        return $styleSheets;
    }

    /**
     *
     * @return array
     */
    protected function getFormWidgets() {
        $widgets = array(
            'employeeName' => new sfWidgetFormInputText(),
            'employeeNumber' => new sfWidgetFormInputHidden(),
            'jobTitleCode' => new sfWidgetFormChoice(array('choices' => $this->getJobTitleListAsArrayWithAllOption()), array('class' => 'formSelect')),
            'department' => new sfWidgetFormChoice(array('choices' => $this->getSubDivisionChoices()), array('class' => 'formSelect')),
            'status' => new sfWidgetFormChoice(array('choices' => $this->getPerformanceReviewStatusAsArray()), array('class' => 'formSelect')),
            'fromDate' => new ohrmWidgetDatePicker(array(), array('id' => 'fromDate'), array('class' => 'formDateInput')),
            'toDate' => new ohrmWidgetDatePicker(array(), array('id' => 'toDate'), array('class' => 'formDateInput')),
        );
        return $widgets;
    }

    /**
     *
     * @return array
     */
    protected function getFormValidators() {

        $validators = array(
            'employeeName' => new sfValidatorString(array('required' => false)),
            'employeeNumber' => new sfValidatorString(array('required' => false)),
            'jobTitleCode' => new sfValidatorString(array('required' => false)),
            'department' => new sfValidatorString(array('required' => false)),
            'status' => new sfValidatorString(array('required' => false)),
            'fromDate' => new sfValidatorString(array('required' => false)),
            'toDate' => new sfValidatorString(array('required' => false)),
        );
        return $validators;
    }

    /**
     *
     * @return array
     */
    protected function getFormLabels() {
        $labels = array(
            'employeeName' => __('Employee Name'),
            'jobTitleCode' => __('Job Title'),
            'department' => __('Department'),
            'status' => __('Status'),
            'fromDate' => __('From Date'),
            'toDate' => __('To Date'),
        );
        return $labels;
    }

    /**
     *
     * @return type 
     */
    public function searchReviews($page) {
        $searchParams = array();
        if ($this->getValues()) {
            if ($this->getValue('employeeName') != __('Type for hints...')) {
                $searchParams ['employeeName'] = $this->getValue('employeeName');
            }
            $searchParams ['jobTitleCode'] = $this->getValue('jobTitleCode');
            $searchParams ['departmentId'] = ($this->getValue('department') > 0 ) ? $this->getValue('department') : "";
            $searchParams ['from'] = (strtotime($this->getValue('fromDate'))) ? $this->getValue('fromDate') : "";
            $searchParams ['to'] = (strtotime($this->getValue('toDate'))) ? $this->getValue('toDate') : "";
            if ($this->getValue('status') > 0) {
                $searchParams['status'] = $this->getValue('status');
            }
        }

        if (!isset($searchParams['status'])) {
            $statusArray [] = $this->getReviewStatusFactory()->getStatus('activated')->getStatusId();
            $statusArray [] = $this->getReviewStatusFactory()->getStatus('inProgress')->getStatusId();
            $statusArray [] = $this->getReviewStatusFactory()->getStatus('approved')->getStatusId();


            $searchParams['status'] = $statusArray;
        }

        $searchParams['reviewerId'] = ($this->getUser()->getEmployeeNumber() > 0) ? $this->getUser()->getEmployeeNumber() : 0;
        $searchParams['employeeNotIn'] = array($this->getUser()->getEmployeeNumber());
        $searchParams['page'] = $page;
        $searchParams['limit'] = sfConfig::get('app_items_per_page');


        return $this->getPerformanceReviewService()->searchReview($searchParams);
    }

    /**
     *
     * @return jsonString 
     */
    public function getReviwerAccessibleEmployeeListAsJson() {

        $jsonArray = array();
        $performanceReviewService = new PerformanceReviewService();

        $performanceReviewList = $performanceReviewService->getReviwerEmployeeList($this->getUser()->getEmployeeNumber());

        $employeeUnique = array();
        foreach ($performanceReviewList as $performanceReview) {
            $employee = $performanceReview->getEmployee();
            $workShiftLength = 0;
            $employeeCountry = null;
            $terminationId = $employee->getTerminationId();
            if (!isset($employeeUnique[$employee->getEmpNumber()]) && empty($terminationId)) {


                $name = $employee->getFullName();

                $employeeUnique[$employee->getEmpNumber()] = $name;
                $jsonArray[] = array('name' => $name, 'id' => $employee->getEmpNumber());
            }
        }
        $jsonString = json_encode($jsonArray);

        return $jsonString;
    }

    public function getCountReviewList() {
        $searchParams = array();
        if ($this->getValues()) {
            if ($this->getValue('employeeName') != __('Type for hints...')) {
                $searchParams ['employeeName'] = $this->getValue('employeeName');
            }
            $searchParams ['jobTitleCode'] = $this->getValue('jobTitleCode');
            $searchParams ['departmentId'] = ($this->getValue('department') > 0 ) ? $this->getValue('department') : "";
            $searchParams ['from'] = (strtotime($this->getValue('fromDate'))) ? $this->getValue('fromDate') : "";
            $searchParams ['to'] = (strtotime($this->getValue('toDate'))) ? $this->getValue('toDate') : "";
            if ($this->getValue('status') > 0) {
                $searchParams['status'] = $this->getValue('status');
            }
        }

        if (!isset($searchParams['status'])) {
            $statusArray [] = $this->getReviewStatusFactory()->getStatus('activated')->getStatusId();
            $statusArray [] = $this->getReviewStatusFactory()->getStatus('inProgress')->getStatusId();
            $statusArray [] = $this->getReviewStatusFactory()->getStatus('approved')->getStatusId();


            $searchParams['status'] = $statusArray;
        }

        $searchParams['reviewerId'] = ($this->getUser()->getEmployeeNumber() > 0) ? $this->getUser()->getEmployeeNumber() : 0;
        $searchParams['employeeNotIn'] = array($this->getUser()->getEmployeeNumber());
        $searchParams['limit'] = null;
        return $this->getPerformanceReviewService()->getCountReviewList($searchParams);
    }

}