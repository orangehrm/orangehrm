<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 *
 * Description of PerformanceReviewSearchForm
 *
 * @author nadeera
 */

class PerformanceReviewSearchForm extends BasePefromanceSearchForm {

    /**
     * 
     */
    public function configure() {

        $this->setWidgets($this->getFormWidgets());
        $this->setValidators($this->getFormValidators());

        $this->getWidgetSchema()->setNameFormat('performanceReview360SearchForm[%s]');
        $this->getWidgetSchema()->setLabels($this->getFormLabels());
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
            'status' => new sfWidgetFormChoice(array('choices' => $this->getPerformanceReviewStatusAsArray(true)), array('class' => 'formSelect')),
            'fromDate' => new ohrmWidgetDatePicker(array(), array('id' => 'fromDate')),
            'toDate' => new ohrmWidgetDatePicker(array(), array('id' => 'toDate')),
            'reviwerName' => new sfWidgetFormInputText(),
            'reviwerNumber' => new sfWidgetFormInputHidden()   
            
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
            'fromDate' => new sfValidatorString(array('required' => false)),
            'toDate' => new sfValidatorString(array('required' => false)),
            'status' =>  new sfValidatorString(array('required' => false)),
            'reviwerName' => new sfValidatorString(array('required' => false)),
            'reviwerNumber' => new sfValidatorString(array('required' => false))
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
            'fromDate' =>  __('From Date'),
            'toDate' =>  __('To Date'),
            'status' =>  __('Status'),
            'reviwerName' =>  __('Reviewer')
        );
        return $labels;
    }

    /**
     *
     * @return type 
     */
    public function searchReviews($limit, $page = 1, $sortOrder = null, $sortFeild = null) {        
        
        $serachParams ['employeeName'] =  $this->getValue('employeeName');
        $serachParams ['jobTitleCode'] =  $this->getValue('jobTitleCode');
        $serachParams ['from'] =  (strtotime($this->getValue('fromDate')))? $this->getValue('fromDate') :"";
        $serachParams ['to'] = (strtotime( $this->getValue('toDate')))?  $this->getValue('toDate') :"";
        $serachParams ['reviewerId'] =  ($this->getValue('reviwerNumber') > 0)?$this->getValue('reviwerNumber'):"";
        $serachParams['status']         =   ($this->getValue('status') > 0 )? $this->getValue('status') :"" ;
        $serachParams['limit']         =   $limit;
        $serachParams['page']         =   $page;
        
        $orderBy['orderBy'] = $sortFeild;
        $orderBy['sortOrder'] = $sortOrder;
        
       return $this->getPerformanceReviewService()->searchReview($serachParams, $orderBy);
       
    }
    
    /**
     * Returns the set of action buttons associated with each mode of the leave list
     *
     * @return array Array of action buttons as instances of ohrmWidegetButton class
     */
    public function getSearchActionButtons() {
        return array(
            'btnSearch' => new ohrmWidgetButton('btnSearch', 'Search', array()),
            'btnReset' => new ohrmWidgetButton('btnReset', 'Reset', array('class' => 'reset')),
        );
    }
    
    public function getCountReviewList(){
        $serachParams ['employeeName'] =  $this->getValue('employeeName');
        $serachParams ['jobTitleCode'] =  $this->getValue('jobTitleCode');
        $serachParams ['from'] = (strtotime($this->getValue('fromDate')))? $this->getValue('fromDate') :"";
        $serachParams ['to'] = (strtotime( $this->getValue('toDate')))?  $this->getValue('toDate') :"";
        $serachParams ['reviewerId'] = ($this->getValue('reviwerNumber') > 0)?$this->getValue('reviwerNumber'):"";
        $serachParams['status'] = ($this->getValue('status') > 0 )? $this->getValue('status') :"" ;
        $serachParams['limit'] = null;
        return $this->getPerformanceReviewService()->getCountReviewList($serachParams);
    }
    
    public function getStylesheets() {
        $stylesheets = parent::getStylesheets();
        $stylesheets[plugin_web_path('orangehrmPerformancePlugin','css/searchReviewSuccess.css')] = 'all';
        return $stylesheets;
        
    }

}