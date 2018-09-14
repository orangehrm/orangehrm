<?php

/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 3/9/18
 * Time: 12:07 PM
 */
class PurgeDaoTest extends PHPUnit_Framework_TestCase
{

    protected $fixture;
    private $employeeDao;

    /**
     * Set up method
     */
    protected function setUp()
    {
        $this->employeeDao = new PurgeDao();
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmMaintenancePlugin/test/fixtures/EmployeeDaoWithDeletedEmployee.yml';
        TestDataService::populate($this->fixture);
    }


    public function testGetSoftDeletedEmployee()
    {
        $empNumber = 6;
        $employee = $this->employeeDao->getSoftDeletedEmployee($empNumber);

        $this->assertTrue($employee instanceof Employee);
        $this->assertEquals("Shalitha", $employee->getFirstName());
        $this->assertEquals("Vikum", $employee->getLastName());
        $empNumber = 4;
        $employee = $this->employeeDao->getSoftDeletedEmployee($empNumber);
        $this->assertTrue($employee instanceof Employee);
        $this->assertEquals("Chaturanga", $employee->getFirstName());
        $this->assertEquals("Namal", $employee->getLastName());

    }

    public function testIsEmployeeIdExists()
    {
        $employeeId = "E001";
        $this->assertTrue($this->employeeDao->isEmployeeIdExists($employeeId));
        $employeeId = "FAKEID";
        $this->assertFalse($this->employeeDao->isEmployeeIdExists($employeeId));
    }

    public function testSaveEntity()
    {
        $employeeNumber = 6;
        $employee = $this->getEmployeeService()->getEmployee($employeeNumber);
        $this->assertEquals("Shalitha", $employee->getFirstName());

        $employee->setFirstName("test");
        $savedEmployee = $this->employeeDao->saveEntity($employee);
        $this->assertTrue($savedEmployee instanceof Employee);
        $employee = $this->getEmployeeService()->getEmployee($employeeNumber);
        $this->assertEquals("test", $employee->getFirstName());
    }

    public function getEmployeeService()
    {
        if (!isset($this->employeeService)) {
            $this->employeeService = new EmployeeService();
        }
        return $this->employeeService;
    }
////////////////////////////////// testReplaceEntityValues
    public function testReplaceEntityValuesWithValuesAndSingleMatcher()
    {
        $employeeNumber = 1;
        $entityClassName = "Employee";
        $fieldValueArray = array(
            "nickName" => "Shali",
            "emp_mobile" => "0714582524"
        );
        $matchByValuesArray = array("empNumber" => $employeeNumber);

        $employee = $this->getEmployeeService()->getEmployee($employeeNumber);

        $this->assertEquals("viki", $employee->getNickName());
        $this->assertEquals("111111", $employee->getEmpMobile());

        $recordsCount = $this->employeeDao->replaceEntityValues($entityClassName, $fieldValueArray, $matchByValuesArray);
//
        $this->assertEquals(1, $recordsCount);

        $employee = $this->getEmployeeService()->getEmployee($employeeNumber);

        $this->assertEquals("Shali", $employee->getNickName());
        $this->assertEquals("0714582524", $employee->getEmpMobile());
    }
    public function testReplaceEntityValuesWithValuesAndNullValuesSingleMatcher() {

        $employeeNumber = 1;

        $entityClassName = "Employee";
        $fieldValueArray = array(
            "nickName" => "Shali",
            "emp_mobile" => null
        );
        $matchByValuesArray = array("empNumber" => $employeeNumber);

        $employee = $this->getEmployeeService()->getEmployee($employeeNumber);

        $this->assertEquals("viki", $employee->getNickName());
        $this->assertEquals("111111", $employee->getEmpMobile());

        $recordsCount = $this->employeeDao->replaceEntityValues($entityClassName, $fieldValueArray, $matchByValuesArray);

        $this->assertEquals(1,$recordsCount);
        $employee = $this->getEmployeeService()->getEmployee($employeeNumber);

        $this->assertEquals("Shali", $employee->getNickName());
        $this->assertNull($employee->getEmpMobile());
    }
    public function testReplaceEntityValuesWithValuesAndNullValuesMultipleMatcher() {

        $employeeNumber = 1;

        $entityClassName = "Employee";
        $fieldValueArray = array(
            "nickName" => "Shali",
            "emp_mobile" => null
        );
        $matchByValuesArray = array(
            "firstName" => "Shalitha",
            "lastName" => "Vikum"
        );

        $employee = $this->getEmployeeService()->getEmployee($employeeNumber);

        $this->assertEquals("viki", $employee->getNickName());
        $this->assertEquals("111111", $employee->getEmpMobile());

        $recordsCount = $this->employeeDao->replaceEntityValues($entityClassName, $fieldValueArray, $matchByValuesArray);
        $this->assertEquals(1,$recordsCount);

        $employee = $this->getEmployeeService()->getEmployee($employeeNumber);

        $this->assertEquals("Shali", $employee->getNickName());
        $this->assertNull($employee->getEmpMobile());
    }
    public function testReplaceEntityValuesWithValuesMatcherMatchingMultipleEntities() {

        $entityClassName = "Employee";
        $fieldValueArray = array(
            "firstName" => "John",
        );
        $matchByValuesArray = array(
            "middleName" => "ST",
        );

        $employee1 = $this->getEmployeeService()->getEmployee(2);
        $employee2 = $this->getEmployeeService()->getEmployee(4);

        $this->assertEquals("Ashley", $employee1->getFirstName());

        $this->assertEquals("Chaturanga", $employee2->getFirstName());

        $recordsCount = $this->employeeDao->replaceEntityValues($entityClassName, $fieldValueArray, $matchByValuesArray);
        $this->assertEquals(2,$recordsCount);

        $employee1 = $this->getEmployeeService()->getEmployee(2);
        $employee2 = $this->getEmployeeService()->getEmployee(4);

        $this->assertEquals("John", $employee1->getFirstName());

        $this->assertEquals("John", $employee2->getFirstName());
    }


    public function testReplaceEntityValuesWithValuesAndMatcherMatchingMultipleEntitiesUsingArray() {

        $entityClassName = "Employee";
        $fieldValueArray = array(
            "firstName" => "John",
        );
        $matchByValuesArray = array(
            "empNumber" => array(2,4),
        );

        $employee1 = $this->getEmployeeService()->getEmployee(2);
        $employee2 = $this->getEmployeeService()->getEmployee(4);

        $this->assertEquals("Ashley", $employee1->getFirstName());

        $this->assertEquals("Chaturanga", $employee2->getFirstName());

        $recordsCount = $this->employeeDao->replaceEntityValues($entityClassName, $fieldValueArray, $matchByValuesArray);
        $this->assertEquals(2,$recordsCount);

        $employee1 = $this->getEmployeeService()->getEmployee(2);
        $employee2 = $this->getEmployeeService()->getEmployee(4);

        $this->assertEquals("John", $employee1->getFirstName());
        $this->assertEquals("John", $employee2->getFirstName());
    }
    /////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////
    /// /////////////////////////////////////////////////////////// testReplaceEntityValues
    ///
    public function testRemoveEntitiesSingleMatcher() {

        $employeeNumber = 1;

        $entityClassName = "Employee";

        $matchByValuesArray = array("empNumber" => $employeeNumber);

        $employee = $this->getEmployeeService()->getEmployee($employeeNumber);

        $this->assertEquals("viki", $employee->getNickName());
        $this->assertEquals("111111", $employee->getEmpMobile());

        $recordsCount = $this->employeeDao->removeEntities($entityClassName, $matchByValuesArray);
        $this->assertEquals(1,$recordsCount);

        $employee = $this->getEmployeeService()->getEmployee($employeeNumber);

        $this->assertFalse($employee);
    }

    public function testRemoveEntitiesMultipleMatcher() {

        $employeeNumber = 1;

        $entityClassName = "Employee";

        $matchByValuesArray = array(
            "firstName" => "Shalitha",
            "lastName" => "Vikum"
        );
        $employee = $this->getEmployeeService()->getEmployee($employeeNumber);

        $this->assertEquals("viki", $employee->getNickName());
        $this->assertEquals("111111", $employee->getEmpMobile());

        $recordsCount = $this->employeeDao->removeEntities($entityClassName, $matchByValuesArray);
        $this->assertEquals(1,$recordsCount);

        $employee = $this->getEmployeeService()->getEmployee($employeeNumber);

        $this->assertFalse($employee instanceof Employee);
    }

    public function testRemoveEntitiesSingleMatcherUsingArray() {

        $entityClassName = "Employee";

        $matchByValuesArray = array(
            "empNumber" => array(2,4),
        );

        $employee1 = $this->getEmployeeService()->getEmployee(2);
        $employee2 = $this->getEmployeeService()->getEmployee(4);


        $this->assertEquals("Ashley", $employee1->getFirstName());

        $this->assertEquals("Chaturanga", $employee2->getFirstName());

        $recordsCount = $this->employeeDao->removeEntities($entityClassName, $matchByValuesArray);
        $this->assertEquals(2,$recordsCount);

        $employee1 = $this->getEmployeeService()->getEmployee(2);
        $employee2 = $this->getEmployeeService()->getEmployee(4);

        $this->assertFalse($employee1);
        $this->assertFalse($employee2);
    }
}