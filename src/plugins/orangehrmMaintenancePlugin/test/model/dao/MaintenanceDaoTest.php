<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA 02110-1301, USA
 */

use OrangeHRM\Maintenance\Dao\MaintenanceDao;

/**
 * Class MaintenanceDaoTest
 * @group maintenance
 */
class MaintenanceDaoTest extends PHPUnit_Framework_TestCase
{

    protected $fixture = null;
    private $maintenanceDao = null;
    private $candidateService = null;

    /**
     * Set up method
     */
    protected function setUp()
    {
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmMaintenancePlugin/test/fixtures/EmployeeDaoWithDeletedEmployee.yml';
        TestDataService::populate($this->fixture);
    }

    /**
     * @return EmployeeService
     */
    public function getEmployeeService()
    {
        if (!isset($this->employeeService)) {
            $this->employeeService = new EmployeeService();
        }
        return $this->employeeService;
    }

    /**
     * @return MaintenanceDao
     */
    public function getMaintenanceDao()
    {
        if (!isset($this->maintenanceDao)) {
            $this->maintenanceDao = new MaintenanceDao();
        }
        return $this->maintenanceDao;
    }

    /**
     * @return CandidateService
     */
    public function getCandidateService()
    {
        if (is_null($this->candidateService)) {
            $this->candidateService = new CandidateService();
            $this->candidateService->setCandidateDao(new CandidateDao());
        }
        return $this->candidateService;
    }


    /**
     *
     */
    public function testGetEmployeePurgingList()
    {
        $data = $this->getMaintenanceDao()->getEmployeePurgingList();
        $this->assertEquals(gettype($data), 'object');
        $this->assertEquals(sizeof($data), 1);

        $data = $this->getMaintenanceDao()->getEmployeePurgingList()->toArray();
        $this->assertEquals(sizeof($data), 1);

        $employeeId = $data[0]['empNumber'];
        $employee = $this->getEmployeeService()->getEmployee($employeeId);

        $this->assertEquals($employee->getFirstName(), 'Kayla');
        $this->assertEquals($employee->getLastName(), 'Abbey');
        $this->assertEquals($employee->getMiddleName(), 'T');
        $this->assertEquals($employee->getNickName(), 'viki');
        $this->assertEquals($employee->getJobTitleName(), 'Software Engineer');
        $this->assertEquals($employee->getEmpMobile(), '111111');
        $this->assertEquals($employee->getEmpOthEmail(), 'kayla2@xample.com');
        $this->assertEquals($employee->getEmpWorkEmail(), 'kayla@xample.com');
    }

    /**
     * @throws DaoException
     */
    public function testExtractDataFromEmpNumber()
    {
        $table = 'Employee';
        $employeeId = 1;
        $matchByValues = ['empNumber' => '1'];

        $employee = $this->getEmployeeService()->getEmployee($employeeId);
        $this->assertEquals($employee->getFirstName(), 'Kayla');
        $this->assertEquals($employee->getLastName(), 'Abbey');
        $this->assertEquals($employee->getMiddleName(), 'T');
        $this->assertEquals($employee->getNickName(), 'viki');

        $data = $this->getMaintenanceDao()->extractDataFromEmpNumber($matchByValues, $table);
        $this->assertTrue(sizeof($data) > 0);
        $this->assertEquals($data[0]->getFirstName(), 'Kayla');
        $this->assertEquals($data[0]->getLastName(), 'Abbey');
        $this->assertEquals($data[0]->getMiddleName(), 'T');
        $this->assertEquals($data[0]->getNickName(), 'viki');
    }

    /**
     * @throws DaoException
     */
    public function testSaveEntity()
    {
        $employeeId = 1;
        $employee = $this->getEmployeeService()->getEmployee($employeeId);
        $this->assertEquals($employee->getFirstName(), 'Kayla');
        $this->assertEquals($employee->getLastName(), 'Abbey');
        $this->assertEquals($employee->getMiddleName(), 'T');
        $this->assertEquals($employee->getNickName(), 'viki');

        $employee->firstName = 'ashan1';
        $employee->lastName = 'ashan2';
        $employee->middleName = 'ashan3';
        $employee->nickName = 'ashan4';

        $this->getMaintenanceDao()->saveEntity($employee);
        $employee = $this->getEmployeeService()->getEmployee($employeeId);
        $this->assertEquals($employee->getFirstName(), 'ashan1');
        $this->assertEquals($employee->getLastName(), 'ashan2');
        $this->assertEquals($employee->getMiddleName(), 'ashan3');
        $this->assertEquals($employee->getNickName(), 'ashan4');
    }

    /**
     * @throws DaoException
     */
    public function testGetVacancyListToPurge()
    {
        $vacancyList = $this->getMaintenanceDao()->getVacancyListToPurge();
        $this->assertEquals(gettype($vacancyList), 'object');
    }

    /**
     * @throws DaoException
     */
    public function testGetDeniedCandidatesToKeepDataByVacnacyId()
    {
        $candidates = $this->getMaintenanceDao()->getDeniedCandidatesToKeepDataByVacnacyId(1)->toArray();
        $this->assertEquals($candidates[0]['firstName'], 'Ashley');
        $this->assertEquals($candidates[0]['middleName'], 'sT');
        $this->assertEquals($candidates[0]['lastName'], 'Abel');
        $this->assertEquals($candidates[0]['email'], 'ssssla@xample.com');
        $this->assertEquals($candidates[0]['contactNumber'], '112221111');
        $this->assertEquals($candidates[0]['status'], '1');
        $this->assertEquals($candidates[0]['comment'], 'comment2');
        $this->assertEquals($candidates[0]['modeOfApplication'], '2');
    }
}
