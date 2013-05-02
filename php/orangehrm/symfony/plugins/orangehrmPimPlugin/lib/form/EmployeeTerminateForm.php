<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EmployeeTerminateForm
 *
 * @author orangehrm
 */
class EmployeeTerminateForm extends BaseForm {

    private $employeeService;
    private $terminationReasonConfigurationService;
    
    private $allowActivate;
    private $allowTerminate;

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
    
    public function getTerminationReasonConfigurationService() {

        if (is_null($this->terminationReasonConfigurationService)) {
            $this->terminationReasonConfigurationService = new TerminationReasonConfigurationService();
        }
        
        return $this->terminationReasonConfigurationService;
        
    }

    public function setTerminationReasonConfigurationService(TerminationReasonConfigurationService $terminationReasonConfigurationService) {
        $this->terminationReasonConfigurationService = $terminationReasonConfigurationService;
    }    

    public function configure() {

        $employee = $this->getOption('employee');

        $this->allowActivate = $this->getOption('allowActivate');
        $this->allowTerminate = $this->getOption('allowTerminate');
        
        $empTerminatedId = $employee->termination_id;

        $terminateReasons = $this->__getTerminationReasons();

        //creating widgets
        $widgets = array(
            'date' => new ohrmWidgetDatePicker(array(), array('id' => 'terminate_date')),
            'reason' => new sfWidgetFormSelect(array('choices' => $terminateReasons)),
            'note' => new sfWidgetFormTextArea()
        );

        if ((!$this->allowTerminate) && (!$this->allowActivate)) {
            foreach ($widgets as $widget) {
                $widget->setAttribute('disabled', 'disabled');
            }
        }
        
        $this->setWidgets($widgets);
        
        $inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();

        //Setting validators
        $this->setValidators(array(
            'date' => new ohrmDateValidator(array('date_format' => $inputDatePattern, 'required' => true),
                    array('invalid' => 'Date format should be ' . $inputDatePattern)),
            'reason' => new sfValidatorChoice(array('required' => true, 'choices' => array_keys($terminateReasons))),
            'note' => new sfValidatorString(array('required' => false, 'max_length' => 250, 'trim' => true))
        ));

        $this->setDefault('date', set_datepicker_date_format(date('Y-m-d')));
        $this->setDefault('reason', 1);

        if (!empty($empTerminatedId)){
            $employeeTerminationRecord = $employee->getEmployeeTerminationRecord();
            $this->setDefault('date', set_datepicker_date_format($employeeTerminationRecord->getDate()));
            $this->setDefault('reason', $employeeTerminationRecord->getReasonId());
            $this->setDefault('note', $employeeTerminationRecord->getNote());
        }
        
        $this->widgetSchema->setNameFormat('terminate[%s]');
    }

    public function terminateEmployement($empNumber, $terminatedId) {
        $date = $this->getValue('date');
        $reasonId = $this->getValue('reason');
        $note = $this->getValue('note');

        if(!empty($terminatedId)){
            $employeeTerminationRecord = $this->getEmployeeService()->getEmployeeTerminationRecord($terminatedId);
        }else{
            $employeeTerminationRecord = new EmployeeTerminationRecord();
        }
        $employeeTerminationRecord->setDate($date);
        $employeeTerminationRecord->setReasonId($reasonId);
        $employeeTerminationRecord->setEmpNumber($empNumber);
        $employeeTerminationRecord->setNote($note);

        $this->getEmployeeService()->terminateEmployment($employeeTerminationRecord);
        
    }

    public function __getTerminationReasons() {
        $list = array();
        $terminateReasons = $this->getTerminationReasonConfigurationService()->getTerminationReasonList();
        foreach ($terminateReasons as $terminateReason) {
            $list[$terminateReason->getId()] = $terminateReason->getName();
        }
        return $list;
    }

}
