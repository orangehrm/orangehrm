<?php
/**
 * Created by PhpStorm.
 * User: pola
 * Date: 1/24/17
 * Time: 6:53 AM
 */

namespace Orangehrm\Rest\Api\Pim;


class EmployeeWrapper
{
    private $apiEmployee;

    /**
     * EmployeeWrapper constructor.
     * @param $employee
     */
    public function __construct($apiEmployee)
    {
        $this->apiEmployee = $apiEmployee;
    }

    /**
     * @return mixed
     */
    public function getApiEmployee()
    {
        return $this->apiEmployee;
    }

    /**
     * @param mixed $apiEmployee
     */
    public function setApiEmployee($apiEmployee)
    {
        $this->apiEmployee = $apiEmployee;
    }

    /**
     * set api employee variables 
     *
     * @param $employee
     */
    public  function setEmployee($employee){

        $this->apiEmployee->setFirstName($employee->getFirstName());
        $this->apiEmployee->setMiddleName($employee->getLastName());
        $this->apiEmployee->setLastName($employee->getMiddleName());

     }

     //TODO unwrap method

}