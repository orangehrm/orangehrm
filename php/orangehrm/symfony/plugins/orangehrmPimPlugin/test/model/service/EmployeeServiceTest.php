<?php
require_once 'PHPUnit/Framework.php';
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
 * Boston, MA  02110-1301, USA
 */
class EmployeeServiceTest extends PHPUnit_Framework_TestCase {
   private $testCase;
   private $employeeDao;
   private $employeeService;
   private $fixture;

	/**
	 * Set up method
	 */
	protected function setUp() {
            $this->testCase = sfYaml::load(sfConfig::get('sf_plugins_dir') . '/orangehrmPimPlugin/test/fixtures/employee.yml');
            $this->employeeService = new EmployeeService();
	}

	/**
	 * Testing addEmployee
	 */
   public function testAddEmployee() {
      foreach($this->testCase['Employee'] as $k => $v) {
         $employee	=	new Employee();
         $employee->setLastName($v['lastName']);
         $employee->setFirstName($v['firstName']);

         $this->employeeDao  =	$this->getMock('EmployeeDao');
         $this->employeeDao->expects($this->once())
           ->method('addEmployee')
           ->will($this->returnValue(true));
         $this->employeeService->setEmployeeDao($this->employeeDao);
         $result = $this->employeeService->addEmployee($employee);
         $this->assertTrue($result);
      }
   }

	/**
	 * Testing UpdateEmployeeJob
	 */
   public function testUpdateEmployeeJob() {
      foreach($this->testCase['Employee'] as $k => $v) {
         $this->employeeDao  =	$this->getMock('EmployeeDao');
         $this->employeeDao->expects($this->once())
           ->method('saveJobDetails')
           ->will($this->returnValue(true));
         $this->employeeService->setEmployeeDao($this->employeeDao);

         $employee = new Employee();
         $employee->job_title_code = "JOB" . rand(1,100);
         $result = $this->employeeService->saveJobDetails($employee);
         $this->assertTrue($result);
      }
   }

	/**
	 * Testing getPastJobTitles
	 */
   public function testGetPastJobTitles() {
      $empDao = new EmployeeDao();
      foreach($this->testCase['Employee'] as $k => $v) {
         $list = $empDao->getPastJobTitles($v['id']);
         $this->employeeDao  =	$this->getMock('EmployeeDao');
         $this->employeeDao->expects($this->once())
           ->method('getPastJobTitles')
           ->will($this->returnValue($list));
         $this->employeeService->setEmployeeDao($this->employeeDao);
         $result = $this->employeeService->getPastJobTitles($v['id']);
         $this->assertEquals($list, $result);
      }
   }

	/**
	 * Testing getPastSubdivisions
	 */
   public function testGetPastSubdivisions() {
      $empDao = new EmployeeDao();
      foreach($this->testCase['Employee'] as $k => $v) {
         $list = $empDao->getPastSubdivisions($v['id']);
         $this->employeeDao  =	$this->getMock('EmployeeDao');
         $this->employeeDao->expects($this->once())
           ->method('getPastSubdivisions')
           ->will($this->returnValue($list));
         $this->employeeService->setEmployeeDao($this->employeeDao);
         $result = $this->employeeService->getPastSubdivisions($v['id']);
         $this->assertEquals($list, $result);
      }
   }
   
	/**
	 * Testing Delete Past SubDivisionHistory
	 */
   public function testDeletePastSubDivisionHistory() {
      foreach($this->testCase['Employee'] as $k => $v) {
         $this->employeeDao  =	$this->getMock('EmployeeDao');
         $this->employeeDao->expects($this->once())
           ->method('deleteSubDivisionHistory')
           ->will($this->returnValue(true));
         $this->employeeService->setEmployeeDao($this->employeeDao);
         $result = $this->employeeService->deleteSubDivisionHistory($v['id'], array($v['subDivisionId']));
         $this->assertTrue($result);
      }
   }

	/**
	 * Testing Delete JobTitleHistory
	 */
   public function testDeleteJobTitleHistory() {
      foreach($this->testCase['Employee'] as $k => $v) {
         $employee = new Employee();
         $this->employeeDao  =	$this->getMock('EmployeeDao');
         $this->employeeDao->expects($this->once())
           ->method('deleteJobTitleHistory')
           ->will($this->returnValue(true));
         $this->employeeService->setEmployeeDao($this->employeeDao);
         $result = $this->employeeDao->deleteJobTitleHistory($v['id'], array($employee->job_title_code));
         $this->assertTrue($result);
      }
   }

	/**
	 * Testing Add Employee Locations
	 */
   public function testEmployeeLocation() {
      foreach($this->testCase['Employee'] as $k => $v) {
         $this->employeeDao  =	$this->getMock('EmployeeDao');
         $this->employeeDao->expects($this->once())
           ->method('assignLocation')
           ->will($this->returnValue(true));
         $this->employeeService->setEmployeeDao($this->employeeDao);
         
         $result = $this->employeeService->assignLocation($v['id'], $v['locCode']);
         $this->assertTrue($result);
      }
   }

	/**
	 * Testing getPastLocations
	 */
   public function testGetPastLocations() {
      $empDao = new EmployeeDao();
      foreach($this->testCase['Employee'] as $k => $v) {
         $list = $empDao->getPastLocations($v['id']);
         $this->employeeDao  =	$this->getMock('EmployeeDao');
         $this->employeeDao->expects($this->once())
           ->method('getPastLocations')
           ->will($this->returnValue($list));
         $this->employeeService->setEmployeeDao($this->employeeDao);
         $result = $this->employeeService->getPastLocations($v['id']);
         $this->assertEquals($result, $list);
      }
   }

	/**
	 * Testing Delete Employee Locations
	 */
   public function testDeleteEmployeeLocation() {
      foreach($this->testCase['Employee'] as $k => $v) {
         $this->employeeDao  =	$this->getMock('EmployeeDao');
         $this->employeeDao->expects($this->once())
           ->method('removeLocation')
           ->will($this->returnValue(true));
         $this->employeeService->setEmployeeDao($this->employeeDao);
         $result = $this->employeeService->removeLocation($v['id'], $v['locCode']);
         $this->assertTrue($result);
      }
   }
   
	/**
	 * Testing Delete Employee Locations
	 */
   public function testDeleteLocationHistory() {
      foreach($this->testCase['Employee'] as $k => $v) {
         $this->employeeDao  =	$this->getMock('EmployeeDao');
         $this->employeeDao->expects($this->once())
           ->method('deleteLocationHistory')
           ->will($this->returnValue(true));
         $this->employeeService->setEmployeeDao($this->employeeDao);
         $result = $this->employeeService->deleteLocationHistory($v['id'], array($v['locCode']));
         $this->assertTrue($result);
      }
   }

	/**
	 * Testing Adding Employee Picture
	 */
   public function testAddEmployeePicture() {
      foreach($this->testCase['Employee'] as $k => $v) {
         $this->employeeDao  =	$this->getMock('EmployeeDao');
         $this->employeeDao->expects($this->once())
           ->method('saveEmployeePicture')
           ->will($this->returnValue(true));
         $this->employeeService->setEmployeeDao($this->employeeDao);

         $pic = new EmpPicture();
         $pic->setEmpNumber($v['id']);
         $pic->setFilename("pic_" . rand(0, 1000));
         $result = $this->employeeService->saveEmployeePicture($pic);
         $this->assertTrue($result);
      }
   }

	/**
	 * Testing readEmployeePicture
	 */
   public function testManipulateEmployeePicture() {
      foreach($this->testCase['Employee'] as $k => $v) {
         $this->employeeDao  =	$this->getMock('EmployeeDao');
         $this->employeeDao->expects($this->once())
           ->method('readEmployeePicture')
           ->will($this->returnValue(new EmpPicture()));
         $this->employeeService->setEmployeeDao($this->employeeDao);
         $pic = $this->employeeService->readEmployeePicture($v['id']);
         $this->assertTrue($pic instanceof EmpPicture);
      }
   }

	/**
	 * Testing deletePhoto
	 */
   public function testDeletePhoto() {
      foreach($this->testCase['Employee'] as $k => $v) {
         $this->employeeDao  =	$this->getMock('EmployeeDao');
         $this->employeeDao->expects($this->once())
           ->method('deletePhoto')
           ->will($this->returnValue(true));
         $this->employeeService->setEmployeeDao($this->employeeDao);
         $result = $this->employeeService->deletePhoto($v['id']);
         $this->assertTrue($result);
      }
   }

	/**
	 * Testing getEmployeeListAsJson
	 */
   public function testGetEmployeeListAsJson() {
      $empDao  = new EmployeeDao();
      $str     = $empDao->getEmployeeListAsJson();
      
      $this->employeeDao  =	$this->getMock('EmployeeDao');
      $this->employeeDao->expects($this->once())
        ->method('getEmployeeListAsJson')
        ->will($this->returnValue($str));
      $this->employeeService->setEmployeeDao($this->employeeDao);
      $result = $this->employeeService->getEmployeeListAsJson();
      $this->assertEquals($str, $result);
   }

	/**
	 * Testing Saving Employee Contract Related
	 */
   public function testSaveEmployeeContracts() {
      foreach($this->testCase['Employee'] as $k => $v) {
         $empContract = new EmpContract();
         $empContract->setEmpNumber($v['id']);
         $this->employeeDao  =	$this->getMock('EmployeeDao');
         $this->employeeDao->expects($this->once())
           ->method('saveEmpContract')
           ->will($this->returnValue(true));
         $this->employeeService->setEmployeeDao($this->employeeDao);
         $result = $this->employeeService->saveEmpContract($empContract);
         $this->assertTrue($result);
      }
   }

	/**
	 * Testing Delete Employee Contract Related
	 */
   public function testDeleteEmployeeContracts() {
      foreach($this->testCase['Employee'] as $k => $v) {
         $this->employeeDao  =	$this->getMock('EmployeeDao');
         $this->employeeDao->expects($this->once())
           ->method('deleteContracts')
           ->will($this->returnValue(true));
         $this->employeeService->setEmployeeDao($this->employeeDao);
         $result = $this->employeeService->deleteContracts($v['id'], array($v['contractId']));
         $this->assertTrue($result);
      }
   }

	/**
	 * Testing Saving Employee Licenses Related
	 */
   public function testEmployeeLicenses() {
      foreach($this->testCase['Employee'] as $k => $v) {
         $this->employeeDao  =	$this->getMock('EmployeeDao');
         $this->employeeDao->expects($this->once())
           ->method('saveEmployeeLicense')
           ->will($this->returnValue(true));
         $this->employeeService->setEmployeeDao($this->employeeDao);
         
         $empLicence = new EmployeeLicense();
         $empLicence->setEmpNumber($v['id']);
         $empLicence->setCode($v['licenseCode']);
         $result = $this->employeeService->saveEmployeeLicense($empLicence);
         $this->assertTrue($result);
      }
   }

	/**
	 * Testing listing Employee Licenses Related
	 */
   public function testListEmployeeLicenses() {
      $empDao = new EmployeeDao();
      foreach($this->testCase['Employee'] as $k => $v) {
         $list = $empDao->getAvailableLicenses($v['id']);

         $this->employeeDao  =	$this->getMock('EmployeeDao');
         $this->employeeDao->expects($this->once())
           ->method('getAvailableLicenses')
           ->will($this->returnValue($list));
         $this->employeeService->setEmployeeDao($this->employeeDao);
         $result = $this->employeeService->getAvailableLicenses($v['id']);
         $this->assertEquals($result, $list);
      }
   }

	/**
	 * Testing Delete Employee Licenses Related
	 */
   public function testDeleteEmployeeLicence() {
      foreach($this->testCase['Employee'] as $k => $v) {
         $this->employeeDao  =	$this->getMock('EmployeeDao');
         $this->employeeDao->expects($this->once())
           ->method('deleteLicenses')
           ->will($this->returnValue(true));
         $this->employeeService->setEmployeeDao($this->employeeDao);
         $result = $this->employeeService->deleteLicenses($v['id'], array($v['licenseCode']));
         $this->assertTrue($result);
      }
   }

   /**
    * Test Employee Skills Related
    */
   public function testEmployeeSkills() {
      foreach($this->testCase['Employee'] as $k => $v) {
         $this->employeeDao  =	$this->getMock('EmployeeDao');
         $this->employeeDao->expects($this->once())
           ->method('saveEmployeeSkill')
           ->will($this->returnValue(true));
         $this->employeeService->setEmployeeDao($this->employeeDao);
         
         $empSkills = new EmployeeSkill();
         $empSkills->setEmpNumber($v['id']);
         $empSkills->setCode($v['skillCode']);
         $result = $this->employeeService->saveEmployeeSkill($empSkills);
         $this->assertTrue($result);
      }
   }

   /**
    * Test list EmployeeSkills
    */
   public function testListEmployeeSkills() {
      $empDao = new EmployeeDao();
      foreach($this->testCase['Employee'] as $k => $v) {
         $list = $empDao->getAvailableSkills($v['id']);
         $empDao->getAvailableSkills($v['id']);

         $this->employeeDao  =	$this->getMock('EmployeeDao');
         $this->employeeDao->expects($this->once())
           ->method('getAvailableSkills')
           ->will($this->returnValue($list));
         $this->employeeService->setEmployeeDao($this->employeeDao);
         $result = $this->employeeService->getAvailableSkills($v['id']);
      }
   }


   /**
    * Test Delete EmployeeSkills
    */
   public function testDeleteEmployeeSkills() {
      foreach($this->testCase['Employee'] as $k => $v) {
         $this->employeeDao  =	$this->getMock('EmployeeDao');
         $this->employeeDao->expects($this->once())
           ->method('deleteSkills')
           ->will($this->returnValue(true));
         $this->employeeService->setEmployeeDao($this->employeeDao);
         $result = $this->employeeService->deleteSkills($v['id'], array($v['skillCode']));
         $this->assertTrue($result);
      }
   }

   /**
    * Test Employee Education Related
    */
   public function testEmployeeEducation() {
      foreach($this->testCase['Employee'] as $k => $v) {
         $empEdu = new EmployeeEducation();
         $empEdu->setEmpNumber($v['id']);
         $empEdu->setCode($v['eduCode']);

         $this->employeeDao  =	$this->getMock('EmployeeDao');
         $this->employeeDao->expects($this->once())
           ->method('saveEmployeeEducation')
           ->will($this->returnValue(true));
         $this->employeeService->setEmployeeDao($this->employeeDao);

         $result = $this->employeeService->saveEmployeeEducation($empEdu);
         $this->assertTrue($result);
      }
   }

   /**
    * Test list Employee Education
    */
   public function testListEmployeeEducation() {
      $empDao = new EmployeeDao();
      foreach($this->testCase['Employee'] as $k => $v) {
         $list = $empDao->getAvailableEducationList($v['id']);

         $this->employeeDao  =	$this->getMock('EmployeeDao');
         $this->employeeDao->expects($this->once())
           ->method('getAvailableEducationList')
           ->will($this->returnValue($list));
         $this->employeeService->setEmployeeDao($this->employeeDao);
         $result = $this->employeeService->getAvailableEducationList($v['id']);
         $this->assertEquals($result, $list);
      }
   }

   /**
    * Test Delete Employee Education
    */
   public function testDeleteEmployeeEducation() {
      foreach($this->testCase['Employee'] as $k => $v) {
         $this->employeeDao  =	$this->getMock('EmployeeDao');
         $this->employeeDao->expects($this->once())
           ->method('deleteEducation')
           ->will($this->returnValue(true));
         $this->employeeService->setEmployeeDao($this->employeeDao);
         $result = $this->employeeService->deleteEducation($v['id'], array($v['eduCode']));
         $this->assertTrue($result);
      }
   }

   /**
    * Test Employee Language Related
    */
   public function testEmployeeLanguage() {
      foreach($this->testCase['Employee'] as $k => $v) {
         $empLang = new EmployeeLanguage();
         $empLang->setEmpNumber($v['id']);
         $empLang->setLangType(1);
         $empLang->setCode($v['langCode']);

         $this->employeeDao  =	$this->getMock('EmployeeDao');
         $this->employeeDao->expects($this->once())
           ->method('saveEmployeeLanguage')
           ->will($this->returnValue(true));
         $this->employeeService->setEmployeeDao($this->employeeDao);
         $result = $this->employeeService->saveEmployeeLanguage($empLang);
         $this->assertTrue($result);
      }
   }

   /**
    * Test Delete Employee Language
    */
   public function testDeleteEmployeeLanguage() {
      foreach($this->testCase['Employee'] as $k => $v) {
         $this->employeeDao  =	$this->getMock('EmployeeDao');
         $this->employeeDao->expects($this->once())
           ->method('deleteLanguages')
           ->will($this->returnValue(true));
         $this->employeeService->setEmployeeDao($this->employeeDao);

         $result = $this->employeeDao->deleteLanguages($v['id'], array(array($v['langCode'], 1)));
         $this->assertTrue($result);
      }
   }

   /**
    * Test Saving Employee Membership Detail
    */
   public function testEmployeeMemberDetail() {
      foreach($this->testCase['Employee'] as $k => $v) {
         $this->employeeDao  =	$this->getMock('EmployeeDao');
         $this->employeeDao->expects($this->once())
           ->method('saveEmployeeMemberDetail')
           ->will($this->returnValue(true));
         $this->employeeService->setEmployeeDao($this->employeeDao);
         
         $empMemDet = new EmployeeMemberDetail();
         $empMemDet->setEmpNumber($v['id']);
         $empMemDet->setMembshipCode($v['memCode']);
         $empMemDet->setMembtypeCode($v['memTypeCode']);
         $result = $this->employeeService->saveEmployeeMemberDetail($empMemDet);
         $this->assertTrue($result);
      }
   }

   /**
    * Test Saving Employee Membership Detail
    */
   public function testListMembershipDetail() {
      $empDao = new EmployeeDao();
      foreach($this->testCase['Employee'] as $k => $v) {
         $list = $empDao->getAvailableMemberships($v['id'], $v['memTypeCode']);
         $this->employeeDao  =	$this->getMock('EmployeeDao');
         $this->employeeDao->expects($this->once())
           ->method('getAvailableMemberships')
           ->will($this->returnValue($list));
         $this->employeeService->setEmployeeDao($this->employeeDao);
         $result = $this->employeeService->getAvailableMemberships($v['id'], $v['memTypeCode']);
         $this->assertEquals($list, $result);
      }
   }

   /**
    * Test Delete Employee Membership Detail
    */
   public function testDeleteEmployeeMemberDetail() {
      foreach($this->testCase['Employee'] as $k => $v) {
         $this->employeeDao  =	$this->getMock('EmployeeDao');
         $this->employeeDao->expects($this->once())
           ->method('deleteMemberships')
           ->will($this->returnValue(true));
         $this->employeeService->setEmployeeDao($this->employeeDao);
         $result = $this->employeeService->deleteMemberships($v['id'], array(array($v['memCode'], $v['memTypeCode'])));
         $this->assertTrue($result);
      }
   }

   /**
    * Test Add Employee Salary
    */
   public function testEmployeeSalary() {
      foreach($this->testCase['Employee'] as $k => $v) {
         $empSal = new EmpBasicsalary();
         $empSal->setEmpNumber($v['id']);
         $empSal->setCurrencyId("USD");
         $empSal->setSalGrdCode($v['salGrdCode']);

         $this->employeeDao  =	$this->getMock('EmployeeDao');
         $this->employeeDao->expects($this->once())
           ->method('saveEmpBasicsalary')
           ->will($this->returnValue(true));
         $this->employeeService->setEmployeeDao($this->employeeDao);
         $result = $this->employeeService->saveEmpBasicsalary($empSal);
         $this->assertTrue($result);
      }
   }

   /**
    * Test Delete Employee Salary
    */
   public function testDeleteEmployeeSalary() {
      foreach($this->testCase['Employee'] as $k => $v) {
         $this->employeeDao  =	$this->getMock('EmployeeDao');
         $this->employeeDao->expects($this->once())
           ->method('deleteSalary')
           ->will($this->returnValue(true));
         $this->employeeService->setEmployeeDao($this->employeeDao);
         $result = $this->employeeService->deleteSalary($v['id'], array(array($v['salGrdCode'], $v['currencyId'])));
         $this->assertTrue($result);
      }
   }

	/**
	 * Testing deleteEmployee
	 */
   public function testDeleteEmployee() {
      foreach($this->testCase['Employee'] as $k => $v) {
         $this->employeeDao  =	$this->getMock('EmployeeDao');
         $this->employeeDao->expects($this->once())
           ->method('deleteEmployee')
           ->will($this->returnValue(1));
         $this->employeeService->setEmployeeDao($this->employeeDao);
         $result = $this->employeeService->deleteEmployee(array($v['id']));
         $this->assertEquals($result, 1);
      }
   }

   /**
     * Test GetEmergencyContacts
     */
    public function testGetEmergencyContacts() {

        // TODO: Load from fixture
        $contacts = array();
        $contacts[0] = new EmpEmergencyContact();
        $contacts[1] = new EmpEmergencyContact();

        $empNumber = 2;
        
        $emergencyContactList = array();

        $employeeDao = $this->getMock('EmployeeDao');

        $employeeDao->expects($this->once())
                    ->method('getEmergencyContacts')
                    ->with($empNumber)
                    ->will($this->returnValue($contacts));

        $this->employeeService->setEmployeeDao($employeeDao);

        $emgContacts = $this->employeeService->getEmergencyContacts($empNumber);
        $this->assertEquals(count($contacts), count($emgContacts));
        $this->assertEquals($emgContacts, $contacts);


        // Test exception
        $employeeDao = $this->getMock('EmployeeDao');

        $employeeDao->expects($this->once())
                    ->method('getEmergencyContacts')
                    ->with($empNumber)
                    ->will($this->throwException(new DaoException('test')));

        $this->employeeService->setEmployeeDao($employeeDao);

        try {
            $emgContacts = $this->employeeService->getEmergencyContacts($empNumber);
            $this->fail('DaoException expected');
        } catch (PIMServiceException $e) {
            // expected
        }
    }
}
?>