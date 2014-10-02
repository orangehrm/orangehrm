<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CreateFeedbackForm
 *
 * @author indiran
 */
class AddPerformanceTrackerForm extends sfForm {

    public $employeeService;
    public $performanceTrackerService;
    public $reviewersList = array();
    private $reviewerIdList = array();
    private $performanceTrackId;

    /**
     *
     * @return EmployeeService
     */
    public function getEmployeeService() {
        if (!($this->employeeService instanceof EmployeeService)) {
            $this->employeeService = new EmployeeService();
        }
        return $this->employeeService;
    }

    /**
     *
     * @param EmployeeService $service 
     */
    public function setEmployeeService(EmployeeService $service) {
        $this->employeeService = $service;
    }

    public function getPerformanceTrackerService() {
        if (is_null($this->performanceTrackerService)) {
            $this->performanceTrackerService = new PerformanceTrackerService();
        }
        return $this->performanceTrackerService;
    }

    public function configure() {
        $trackId = $this->getOption('trackId');
        $availableReviewersList = $this->getEmployeeList($trackId);
        $assignedReviewersList = $this->getReviwersList($trackId);

        $this->setWidgets(array(
           
            'tracker_name' => new sfWidgetFormInput(),
            'employeeName' => new ohrmWidgetEmployeeNameAutoFill(array('loadingMethod' => 'ajax')),
            'availableEmp' => new sfWidgetFormSelectMany(array('choices' => $availableReviewersList)),
            'assignedEmp' => new sfWidgetFormSelectMany(array('choices' => $assignedReviewersList)),
            'hdnTrckId' => new sfWidgetFormInputHidden(),
            'hdnMode' => new sfWidgetFormInputHidden(),
        ));

        $this->widgetSchema->setNameFormat('addPerformanceTracker[%s]');
        $this->setValidators(array(
            'tracker_name' => new sfValidatorString(array('required' => true, 'max_length'=> 200)),
            'employeeName' => new ohrmValidatorEmployeeNameAutoFill(array('required' => true)),
            'availableEmp' => new sfValidatorPass(),
            'assignedEmp' => new sfValidatorPass(array('required' => true)),
            'hdnTrckId' => new sfValidatorString(array('required' => false)),
            'hdnMode' => new sfValidatorString(array('required' => false))
        ));
        
        $this->setDefaultValues($trackId);
    }

    /*
     * 
     */

    private function getReviwersList($performanceTrackId) {
        $reviewersList = array();
        if (isset($performanceTrackId)) {
            $this->performanceTrackId = $performanceTrackId;
            $performanceTrack = $this->getPerformanceTrackerService()->getPerformanceTrack($performanceTrackId);
            if ($performanceTrack instanceof PerformanceTrack) {
                //existing reviewers
                $reviwers = $performanceTrack->getPerformanceTrackerReviewer();
                foreach ($reviwers as $reviewer) {
                    if ($reviewer instanceof PerformanceTrackerReviewer) {
                        $employee = $reviewer->getEmployee();
                        $empNumber = $employee->getEmpNumber();
                        $name = trim(trim($employee->getFirstName() . ' ' . $employee->getMiddleName(), ' ') . ' ' . $employee->getLastName());
                        $reviewersList[$empNumber] = $name;
                    }
                }
            }
        }
        return $reviewersList;
    }
        
    /**
     *This method is used to set defau 
     * @param type $performanceTrackId
     */
    public function setDefaultValues($performanceTrackId) {
        
        $this->performanceTrackId = $performanceTrackId;
        $performanceTrack = $this->getPerformanceTrackerService()->getPerformanceTrack($performanceTrackId);
        if ($performanceTrack instanceof PerformanceTrack) {
            $this->setDefault('tracker_name', $performanceTrack->getTrackerName());
            $this->setDefault('hdnTrckId', $performanceTrack->getId());
            $this->setDefault('hdnMode', 'edit');
            $this->setDefault('employeeName', array('empName' => $performanceTrack->getEmployee()->getFirstAndLastNames(), 'empId' => $performanceTrack->getEmployee()->getEmpNumber()));
        }
    }

    public function save() {
        $performanceTrack = $this->getPerformanceTracker();         
        $this->getPerformanceTrackerService()->savePerformanceTrack($performanceTrack);        
    }

    //get the values from form and set it to performanceTracker 
    public function getPerformanceTracker() {
        $trackerName = $this->getValue('tracker_name');
        $trackId = $this->getValue('hdnTrckId');        
        $currentDate = date(Performancetrack::DATE_FORMAT);
        $employeeName = $this->getValue('employeeName');
        $empId = $employeeName['empId'];
        $assignedEmp = $this->getValue('assignedEmp');

        $performanceTracker = new PerformanceTrack();
        
        
        //modify existing performance tracker
        if (!empty($trackId)) {
            $performanceTracker = $this->getPerformanceTrackerService()->getPerformanceTrack($trackId);
            $performanceTracker->setModifiedDate($currentDate);
        }
        //add performance tracker
        else {

            $performanceTracker->setEmpNumber($empId);
            $performanceTracker->setStatus(PerformanceTrack::STATUS_ACTIVE);            
            $performanceTracker->setAddedDate($currentDate);
        }
       $performanceTracker->setTrackerName($trackerName);
        //setting reviewers.
        $newReviewers = $performanceTracker->getPerformanceTrackerReviewer();        
        $newReviewers->clear();
        
        foreach ($assignedEmp as $reviewerId) {
            $reviewer = new PerformanceTrackerReviewer();
            $reviewer->setAddedDate($currentDate);
            $reviewer->setReviewerId($reviewerId);
            $newReviewers->add($reviewer);
        }  
        $performanceTracker->setPerformanceTrackerReviewer($newReviewers);
        
        return $performanceTracker;
    }

    protected function getAddedPerformanceTrackReviwers() {
        $assignedEmp = $this->getValue('assignedEmp');
        echo "=>" . $assignedEmp[0] . "##";
    }

    // get the employees list as json.
    public function getEmployeeListAsJson() {
        $jsonArray = array();
        $employeeService = $this->getEmployeeService();

        $locationService = new LocationService();

        $properties = array("empNumber", "firstName", "middleName", "lastName", 'termination_id');

        $employeeList = UserRoleManagerFactory::getUserRoleManager()
                ->getAccessibleEntityProperties('Employee', $properties, null, null, array(), array(), array());

        $employeeUnique = array();
        foreach ($employeeList as $employee) {
            $terminationId = $employee['termination_id'];
            $empNumber = $employee['empNumber'];
            if (!isset($employeeUnique[$empNumber]) && empty($terminationId)) {
                $name = trim(trim($employee['firstName'] . ' ' . $employee['middleName'], ' ') . ' ' . $employee['lastName']);

                $employeeUnique[$empNumber] = $name;
                $jsonArray[] = array('name' => $name, 'id' => $empNumber);
            }
        }
        $jsonString = json_encode($jsonArray);
        return $jsonString;
    }

    public function getEmployeeList($trackId) {
        $empNameList = array(); 
        $existReviewersList = $this->getReviwerIdList($trackId);
                
        $employeeService = new EmployeeService();
        $employeeService->setEmployeeDao(new EmployeeDao());

        $properties = array("empNumber", "firstName", "middleName", "lastName");
        $employeeList = $employeeService->getEmployeePropertyList($properties, 'lastName', 'ASC', true);
        
        foreach ($employeeList as $employee) {
            $empNumber = $employee['empNumber'];
            if (!in_array($empNumber, $existReviewersList)) {
                $name = trim(trim($employee['firstName'] . ' ' . $employee['middleName'], ' ') . ' ' . $employee['lastName']);
                $empNameList[$empNumber] = $name;
            }
        }
        $this->employeeList = $empNameList;
        return $empNameList;
    }

    public function getReviwerIdList($trackId) {
        $this->reviewerIdList = $this->getPerformanceTrackerService()->getPerformanceReviewersIdListByTrackId($trackId);
        return $this->reviewerIdList;
    }

}

?>
