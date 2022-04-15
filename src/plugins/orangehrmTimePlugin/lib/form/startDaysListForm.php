<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of startDaysList
 *
 * @author orangehrm
 */
class startDaysListForm extends sfFormSymfony {

    private $dateOptions;

    public function configure() {
        $employeeId = $this->getOption('employeeId');

        $this->setWidgets(array(
            'startDates' => new sfWidgetFormSelect(array('choices' => $this->getStartAndEndDates($employeeId))),
        ));
    }

    public function getStartAndEndDates($employeeId) {
    
        $timesheetService = new TimesheetService();

        $timesheets = $timesheetService->getTimesheetByEmployeeId($employeeId);
        $dateOptions = array();
        $dateOptionsToDrpDwn = array();

        $userRoleManager = sfContext::getInstance()->getUserRoleManager();
        $user = $userRoleManager->getUser();
        
        $excludeRoles = array();
        $includeRoles = $employeeId == $user->getEmpNumber() ? array('ESS') : array();
        $entities = array('Employee' => $employeeId);

        $i = 0;
	if($timesheets != null){
            
        $dataGroupPermissions = $userRoleManager->getDataGroupPermissions(array('time_employee_timesheets'), array(), array(), $employeeId == $user->getEmpNumber(), $entities);
            
        foreach ($timesheets as $timesheet) {

            $allowedActions = $userRoleManager->getAllowedActions(WorkflowStateMachine::FLOW_TIME_TIMESHEET, 
                    $timesheet->getState(), $excludeRoles, $includeRoles, $entities);

            if (isset($allowedActions[WorkflowStateMachine::TIMESHEET_ACTION_VIEW]) || $dataGroupPermissions->canRead()) {

                $dateOptions[$i] = $timesheet->getStartDate(). " ".__("to")." " . $timesheet->getEndDate();
                $dateOptionsToDrpDwn[$i] = set_datepicker_date_format($timesheet->getStartDate() ). " ".__("to")." "  . set_datepicker_date_format($timesheet->getEndDate());
                $i++;
            }
        }
	}
        $this->dateOptions = array_reverse($dateOptions);
	
        return  array_reverse($dateOptionsToDrpDwn);
    }

    public function returnSelectedIndex($enteredStartDate, $employeeId) {

        $datesArray = $this->dateOptions;
        $i = 0;

        foreach ($datesArray as $startDate) {
	 $tempArray = explode(" ", $startDate);
            if ($tempArray[0] == $enteredStartDate) {
                return $i;
            }
            $i++;
        }
    }

    public function getDateOptions() {


        return $this->dateOptions;
    }

}

