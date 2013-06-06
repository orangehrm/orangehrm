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
    
        $userEmployeeNumber = null;
        $timesheetService = new TimesheetService();

        $timesheets = $timesheetService->getTimesheetByEmployeeId($employeeId);
        $dateOptions = array();
        $dateOptionsToDrpDwn = array();

        $userObj = sfContext::getInstance()->getUser()->getAttribute('user');
        $userId = $userObj->getUserId();
        $userEmployeeNumber = $userObj->getEmployeeNumber();

        if ($userEmployeeNumber == $employeeId) {
            $user = new User();
            $decoratedUser = new EssUserRoleDecorator($user);
        } else {
            $userRoleFactory = new UserRoleFactory();
            $decoratedUser = $userRoleFactory->decorateUserRole($userId, $employeeId, $userEmployeeNumber);
        }

        $i = 0;
	if($timesheets != null){
        foreach ($timesheets as $timesheet) {

            $allowedActions = $decoratedUser->getAllowedActions(WorkflowStateMachine::FLOW_TIME_TIMESHEET, $timesheet->getState());

            if (in_array(WorkflowStateMachine::TIMESHEET_ACTION_VIEW, $allowedActions)) {

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

