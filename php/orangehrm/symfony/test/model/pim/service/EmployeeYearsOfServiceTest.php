<?php

/**
 * EmployeeYearsOfService
 *
 * @author sujith
 */
class EmployeeYearsOfServiceTest extends PHPUnit_Framework_TestCase {
    private $employeeService;

    /**
     * Setup Function
     */
    public function setup() {
        $this->employeeService = new EmployeeService();
    }

    public function testGetDurationInYears1() {
        $years = $this->employeeService->getDurationInYears('2006-01-01', '2011-01-01');
        $this->assertEquals($years, 5);
    }

    public function testGetDurationInYears2() {
        $years = $this->employeeService->getDurationInYears('2006-01-01', '2006-06-30');
        $this->assertEquals($years, 0.5);
    }

    public function testGetDurationInYears3() {
        $years = $this->employeeService->getDurationInYears('2007-02-28', '2007-05-28');
        $this->assertEquals($years, 0.25);
    }

    public function testGetDurationInYears4() {
        $years = $this->employeeService->getDurationInYears('2009-06-30', '2012-02-29');
        $this->assertEquals(round($years, 2), 2.67);
    }

    public function testGetDurationInYears5() {
        $years = $this->employeeService->getDurationInYears('2012-02-29', '2009-06-30');
        $this->assertEquals($years, 0);
    }

    public function testGetDurationInYears6() {
        $years = $this->employeeService->getDurationInYears('2010-03-15', '2012-09-05');
        $this->assertEquals($years, 2.5);
    }

    public function testGetDurationInYears7() {
        $years = $this->employeeService->getDurationInYears('2010-01-02', '2011-01-01');
        $this->assertTrue($years < 1);
    }

    public function testGetDurationInYears8() {
        $years = $this->employeeService->getDurationInYears('2010-01-10', '2011-01-01');
        $this->assertTrue($years < 1);
    }

    public function testGetDurationInYears9() {
        $years = $this->employeeService->getDurationInYears('2010-01-20', '2011-01-01');
        $this->assertTrue($years < 1);
    }

    public function testGetDurationInYears10() {
        $years = $this->employeeService->getDurationInYears('2009-12-01', '2011-01-01');
        $this->assertTrue($years > 1);
    }

    public function testGetEmployeeYearsOfService() {
        $employee = new Employee();
        $employee->setJoinedDate('2007-06-22');
        $employeeDao    = $this->getMock('EmployeeDao', array('getEmployee'));
        $employeeDao->expects($this->once())
                    ->method('getEmployee')
                    ->will($this->returnValue($employee));

        $this->employeeService->setEmployeeDao($employeeDao);
        $years = $this->employeeService->getDurationInYears('2007-06-22', date("Y-m-d"));

        $employeeYearsOfService = $this->employeeService->getEmployeeYearsOfService(1, date("Y-m-d"));
        $this->assertEquals($employeeYearsOfService, $years);
    }
}
?>
