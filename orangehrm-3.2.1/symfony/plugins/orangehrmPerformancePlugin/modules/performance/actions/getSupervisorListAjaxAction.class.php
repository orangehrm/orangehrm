<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of getSupervisorListAjaxAction
 *
 * @author nadeera
 */

class getSupervisorListAjaxAction extends basePeformanceAction {

    public function execute($request) {
    
        $jsonArray = array();
        $employeeService = new EmployeeService();
        $employeeService->setEmployeeDao(new EmployeeDao());
        
        $supervisorList = $employeeService->getSupervisorIdListBySubordinateId($request->getGetParameter('id'));
        foreach ($supervisorList as $supervisorId) {
            $employee = $employeeService->getEmployee($supervisorId);
            $name = $employee->getFirstName() . " " . $employee->getMiddleName();
            $name = trim(trim($name) . " " . $employee->getLastName());

            $jsonArray[] = array('name' => $name, 'id' => $employee->getEmpNumber());
        }

        $jsonString = json_encode($jsonArray);

        echo $jsonString;
        exit;
    }
}