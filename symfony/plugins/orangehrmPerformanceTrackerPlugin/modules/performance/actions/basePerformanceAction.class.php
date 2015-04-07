<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of basePerformanceAction
 *
 * @author indiran
 */
abstract class basePerformanceAction  extends sfAction{
    
    public $employeeService;
    public $performanceTrackerService;
    
    public function preExecute() {
        return;
    }
       
    
    /**
     * Get EmployeeService
     * @returns EmployeeService
     */
    public function getEmployeeService() {
        if(is_null($this->employeeService)) {
            $this->employeeService = new EmployeeService();
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
    
    /**
     * Get PerformanceTracker Service
     * @returns PerformanceTrackerService
     */
    public function getPerformanceTrackerService() {
        if(is_null($this->performanceTrackerService)) {
            $this->performanceTrackerService = new PerformanceTrackerService();
        }
        return $this->performanceTrackerService;
    }

    /**
     * Set EmployeeService
     * @param EmployeeService $employeeService
     */
    public function setPerformanceTrackerService(PerformanceTrackerService $performanceTrackerService) {
        $this->performanceTrackerService = $performanceTrackerService;        
    }

}

?>
