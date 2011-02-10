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
        $years = $this->employeeService->getDurationInYears('2008-01-01', '2011-01-01');
        $this->assertEquals($years, 3);
    }

    public function testGetDurationInYears2() {
        $years = $this->employeeService->getDurationInYears('2008-01-01', '2010-12-31');
        $this->assertEquals($years, 3);
    }

    public function testGetDurationInYears3() {
        $years = $this->employeeService->getDurationInYears('2008-01-01', '2010-12-30');
        $this->assertEquals($years, 2);
    }

    public function testGetDurationInYears4() {
        $years = $this->employeeService->getDurationInYears('2008-01-01', '2013-01-01');
        $this->assertEquals($years, 5);
    }

    public function testGetDurationInYears5() {
        $years = $this->employeeService->getDurationInYears('2008-01-01', '2012-12-31');
        $this->assertEquals($years, 5);
    }

    public function testGetDurationInYears6() {
        $years = $this->employeeService->getDurationInYears('2008-01-01', '2012-12-30');
        $this->assertEquals($years, 4);
    }

    public function testGetDurationInYears7() {
        $years = $this->employeeService->getDurationInYears('2008-01-01', '2017-01-01');
        $this->assertEquals($years, 9);
    }

    public function testGetDurationInYears8() {
        $years = $this->employeeService->getDurationInYears('2008-01-01', '2016-12-31');
        $this->assertEquals($years, 9);
    }

    public function testGetDurationInYears9() {
        $years = $this->employeeService->getDurationInYears('2008-01-01', '2016-12-30');
        $this->assertEquals($years, 8);
    }

    public function testGetDurationInYears10() {
        $years = $this->employeeService->getDurationInYears('2008-02-28', '2011-02-28');
        $this->assertEquals($years, 3);
    }

    public function testGetDurationInYears11() {
        $years = $this->employeeService->getDurationInYears('2008-02-28', '2011-02-27');
        $this->assertEquals($years, 3);
    }

    public function testGetDurationInYears12() {
        $years = $this->employeeService->getDurationInYears('2008-02-28', '2011-02-26');
        $this->assertEquals($years, 2);
    }

    public function testGetDurationInYears13() {
        $years = $this->employeeService->getDurationInYears('2008-03-01', '2011-03-01');
        $this->assertEquals($years, 3);
    }

    public function testGetDurationInYears14() {
        $years = $this->employeeService->getDurationInYears('2008-03-01', '2011-02-28');
        $this->assertEquals($years, 3);
    }

    public function testGetDurationInYears15() {
        $years = $this->employeeService->getDurationInYears('2008-03-01', '2011-02-27');
        $this->assertEquals($years, 2);
    }

    public function testGetDurationInYears16() {
        $years = $this->employeeService->getDurationInYears('2008-03-01', '2012-03-01');
        $this->assertEquals($years, 4);
    }

    public function testGetDurationInYears17() {
        $years = $this->employeeService->getDurationInYears('2008-03-01', '2012-02-29');
        $this->assertEquals($years, 4);
    }

    public function testGetDurationInYears18() {
        $years = $this->employeeService->getDurationInYears('2008-03-01', '2012-02-28');
        $this->assertEquals($years, 3);
    }

    public function testGetDurationInYears19() {
        $years = $this->employeeService->getDurationInYears('2008-02-29', '2011-02-28');
        $this->assertEquals($years, 3);
    }

    public function testGetDurationInYears20() {
        $years = $this->employeeService->getDurationInYears('2008-02-29', '2011-03-01');
        $this->assertEquals($years, 3);
    }

    public function testGetDurationInYears21() {
        $years = $this->employeeService->getDurationInYears('2008-02-29', '2012-02-28');
        $this->assertEquals($years, 4);
    }

    public function testGetDurationInYears21A() {
        $years = $this->employeeService->getDurationInYears('2008-02-29', '2012-02-29');
        $this->assertEquals($years, 4);
    }

    public function testGetDurationInYears22() {
        $years = $this->employeeService->getDurationInYears('2008-01-05', '2011-01-04');
        $this->assertEquals($years, 3);
    }

    public function testGetDurationInYears23() {
        $years = $this->employeeService->getDurationInYears('2008-01-05', '2011-01-05');
        $this->assertEquals($years, 3);
    }

    public function testGetDurationInYears24() {
        $years = $this->employeeService->getDurationInYears('2008-01-05', '2011-01-03');
        $this->assertEquals($years, 2);
    }

    public function testGetDurationInYears25() {
        $years = $this->employeeService->getDurationInYears('2008-01-05', '2012-01-04');
        $this->assertEquals($years, 4);
    }

    public function testGetDurationInYears26() {
        $years = $this->employeeService->getDurationInYears('2008-01-05', '2012-01-05');
        $this->assertEquals($years, 4);
    }

    public function testGetDurationInYears27() {
        $years = $this->employeeService->getDurationInYears('2008-01-05', '2016-01-04');
        $this->assertEquals($years, 8);
    }

    public function testGetDurationInYears28() {
        $years = $this->employeeService->getDurationInYears('2008-01-05', '2016-01-05');
        $this->assertEquals($years, 8);
    }

    public function testGetDurationInYears29() {
        $years = $this->employeeService->getDurationInYears('2008-05-01', '2011-04-30');
        $this->assertEquals($years, 3);
    }

    public function testGetDurationInYears30() {
        $years = $this->employeeService->getDurationInYears('2008-05-01', '2011-05-01');
        $this->assertEquals($years, 3);
    }

    public function testGetDurationInYears31() {
        $years = $this->employeeService->getDurationInYears('2008-05-01', '2011-04-29');
        $this->assertEquals($years, 2);
    }

    public function testGetDurationInYears32() {
        $years = $this->employeeService->getDurationInYears('2008-05-01', '2012-04-30');
        $this->assertEquals($years, 4);
    }

    public function testGetDurationInYears33() {
        $years = $this->employeeService->getDurationInYears('2008-05-01', '2012-05-01');
        $this->assertEquals($years, 4);
    }

    public function testGetDurationInYears34() {
        $years = $this->employeeService->getDurationInYears('2008-05-01', '2012-04-29');
        $this->assertEquals($years, 3);
    }

    public function testGetDurationInYears35() {
        $years = $this->employeeService->getDurationInYears('2006-01-01', '2006-06-30');
        $this->assertEquals($years, 0);
    }

    public function testGetDurationInYears36() {
        $years = $this->employeeService->getDurationInYears('2007-02-28', '2007-05-28');
        $this->assertEquals($years, 0);
    }

    public function testGetDurationInYears38() {
        $years = $this->employeeService->getDurationInYears('2012-02-29', '2012-02-29');
        $this->assertEquals($years, 0);
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
