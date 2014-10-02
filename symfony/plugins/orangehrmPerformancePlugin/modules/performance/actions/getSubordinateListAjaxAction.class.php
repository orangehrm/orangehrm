<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of getSubordinateListAjaxAction
 *
 * @author nadeera
 */

class getSubordinateListAjaxAction extends basePeformanceAction {

    public function execute($request) {
    
        $jsonArray = array();
        $employeeService = new EmployeeService();
        $employeeService->setEmployeeDao(new EmployeeDao());
        
        $employeeList = $employeeService->getSubordinateListForEmployee($request->getGetParameter('id'));   
        
       
      
        foreach ($employeeList as $subordinate) {
            
            $employee = $subordinate->getSubordinate();

            $name = $employee->getFirstName() . " " . $employee->getMiddleName();
            $name = trim(trim($name) . " " . $employee->getLastName());

            $jsonArray[] = array('name' => $name, 'id' => $employee->getEmpNumber());
        }

        $jsonString = json_encode($jsonArray);

        echo $jsonString;
        exit;
    }
}