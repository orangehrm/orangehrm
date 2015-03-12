<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EmployeeServiceWrapper
 *
 * @author orangehrm
 */
class EmployeeServiceWrapper implements WebServiceWrapper {
    
    private $instance;
    
    public function getServiceInstance() {
        if(!$this->instance instanceof EmployeeService){
            $this->instance = new EmployeeService();
        }
        return $this->instance;
    }
    
    /**
     * 
     * @return Doctrine_Collection
     */
    public function getEmployeeList(){
        $instance = $this->getServiceInstance();
        return $instance->getEmployeeList();
    }
    
    /**
     * 
     * @param array $employee
     * @return Employee
     * @throws Exception
     */
    public function addEmployee($employee){
        $fileds = array_keys($employee);
        foreach ($this->getRequiredFields() as $val) {
            if(!in_array($val, $fileds)){
                throw new Exception("required filed " . $val . " is not provided");
            }
        }
        $empObject = new Employee();
        foreach ($employee as $key => $val) {
            $empObject->set($key, $val);
        }
        $instance = $this->getServiceInstance();
        return $instance->saveEmployee($empObject);
    }
    
    /**
     * 
     * @param string $empNumber
     * @return integer
     */
    public function deleteEmployee($empNumber){
        return $this->deleteEmployee(array(intval($empNumber)));
    }
    
    /**
     * 
     * @param array $empNumbers
     * @return integer
     */
    public function deleteEmployees(array $empNumbers){
        $instance = $this->getServiceInstance();
        return $instance->deleteEmployees($empNumbers);
    }
    
    /**
     * This will return an array of required fields of the given entity.
     * @param string $entityName
     * @return array
     */
    protected function getRequiredFields($entityName = 'Employee'){
        $reqiredFields = array();
        switch ($entityName){
            case 'Employee':
                array_push($reqiredFields, 'firstName');
                array_push($reqiredFields, 'lastName');
                break;
            default :
                // do nothing
        }
        return $reqiredFields;
    }
    
}

?>
