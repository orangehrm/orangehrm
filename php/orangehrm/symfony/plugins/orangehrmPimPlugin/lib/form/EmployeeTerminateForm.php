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
    private $terminationReasonService;

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
    
    public function getTerminationReasonService() {

        if (is_null($this->terminationReasonService)) {
            $this->terminationReasonService = new TerminationReasonService();
        }
        
        return $this->terminationReasonService;
        
    }

    public function setTerminationReasonService(TerminationReasonService $terminationReasonService) {
        $this->terminationReasonService = $terminationReasonService;
    }    

    public function configure() {

        $empNumber = $this->getOption('empNumber');
        $employee = $this->getOption('employee');

        $empTerminatedId = $employee->termination_id;

        $terminateReasons = $this->__getTerminationReasons();

        //creating widgets
        $this->setWidgets(array(
            'date' => new ohrmWidgetDatePickerNew(array(), array('id' => 'terminate_date')),
            'reason' => new sfWidgetFormSelect(array('choices' => $terminateReasons)),
            'note' => new sfWidgetFormTextArea()
        ));
        
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

        if(!empty($empTerminatedId)){
            $empTermination = $employee->getEmpTermination();
            $this->setDefault('date', set_datepicker_date_format($empTermination->getDate()));
            $this->setDefault('reason', $empTermination->getReasonId());
            $this->setDefault('note', $empTermination->getNote());
        }

        $this->widgetSchema->setNameFormat('terminate[%s]');
    }

    public function terminateEmployement($empNumber, $terminatedId) {
        $date = $this->getValue('date');
        $reasonId = $this->getValue('reason');
        $note = $this->getValue('note');

        if(!empty($terminatedId)){
            $empTermination = $this->getEmployeeService()->getEmpTerminationById($terminatedId);
        }else{
            $empTermination = new EmpTermination();
        }
        $empTermination->setDate($date);
        $empTermination->setReasonId($reasonId);
        $empTermination->setEmpNumber($empNumber);
        $empTermination->setNote($note);

        $empTermination->save();
        $this->getEmployeeService()->terminateEmployment($empNumber, $empTermination->getId());
    }

    public function __getTerminationReasons() {
        $list = array();
        $terminateReasons = $this->getTerminationReasonService()->getTerminationReasonList();
        foreach ($terminateReasons as $terminateReason) {
            $list[$terminateReason->getId()] = $terminateReason->getName();
        }
        return $list;
    }

}
