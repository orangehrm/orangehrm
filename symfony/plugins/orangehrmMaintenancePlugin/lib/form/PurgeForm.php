<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 27/8/18
 * Time: 6:04 PM
 */
class PurgeForm extends sfForm {
    public function configure() {
        $this->setWidgets($this->getWidgetList());
        $this->setValidators($this->getValidatorList());
        $this->getWidgetSchema()->setLabels($this->getLabelList());
    }
    public function getWidgetList() {
        $widgets = array();
        $widgets['employee'] = new ohrmWidgetEmployeeNameAutoFill(array('jsonList' => $this->getEmployeeListAsJson()));
        return $widgets;
    }
    public function getValidatorList() {
        $validators = array();
        $validators['employee'] = new ohrmValidatorEmployeeNameAutoFill(array('required' => true));
        return $validators;
    }
    public function getLabelList() {
        $requiredMarker = ' <em>*</em>';
        $lableList = array();
        $lableList['employee'] = __('Select Terminated Employee') . $requiredMarker;
        return $lableList;
    }



    protected function getEmployeeListAsJson() {
        $jsonArray = array();
        $properties = array("empNumber", "firstName", "middleName", "lastName", 'termination_id','purged_at');
        $goalPermissions = $this->getOption('goalPermissions');
        $requiredPermissions = array(
            BasicUserRoleManager::PERMISSION_TYPE_DATA_GROUP => $goalPermissions);
        $employeeList = UserRoleManagerFactory::getUserRoleManager()
            ->getAccessibleEntityProperties('Employee', $properties, null, null, array(), array(), $requiredPermissions);
        $empNo = sfContext::getInstance()->getUser()->getAttribute('user')->getEmployeeNumber();

        $employeeService = new EmployeeService();

        if(!is_null($empNo)) {
            $currentUser = $employeeService->getEmployee($empNo);
            $currentEmployee = array(
                'termination_id'=> $currentUser->getTerminationId(),
                'empNumber'=>$currentUser->getEmpNumber(),
                'firstName'=>$currentUser->getFirstName(),
                'middleName'=>$currentUser->getMiddleName(),
                'lastName'=>$currentUser->getLastName(),
                'purged_at'=>$currentUser->getPurgedAt()
            );
            $employeeList[] = $currentEmployee;
        }
        $employeeUnique = array();
        foreach ($employeeList as $employee) {

            $terminationId = $employee['termination_id'];
            $empNumber = $employee['empNumber'];
            $purge = $employee['purged_at'];

//            if (!isset($employeeUnique[$empNumber]) && !empty($terminationId) && empty($purge)) {
            if (!isset($employeeUnique[$empNumber]) && !empty($terminationId)) {
                $name = trim(trim($employee['firstName'] . ' ' . $employee['middleName'], ' ') . ' ' . $employee['lastName'].'(Past Employee)');
                $employeeUnique[$empNumber] = $name;
                $jsonArray[] = array('name' => $name, 'id' => $empNumber);
            }
        }
        $jsonString = json_encode($jsonArray);
        return $jsonString;
    }
}