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
class EmployeeDaoTest extends PHPUnit_Framework_TestCase {
    private $testCase;
    private $employeeDao;

    /**
     * Set up method
     */
    protected function setUp() {
        $this->testCase = sfYaml::load(sfConfig::get('sf_plugins_dir') . '/orangehrmPimPlugin/test/fixtures/employee.yml');
        $this->employeeDao = new EmployeeDao();
    }

    /**
     * Testing addEmployee
     */
    public function testAddEmployee() {
        foreach($this->testCase['Employee'] as $k => $v) {
            $employee	=	new Employee();
            $employee->setLastName($v['lastName']);
            $employee->setFirstName($v['firstName']);
            $result		=	$this->employeeDao->addEmployee($employee);
            $this->assertTrue($result);
            $this->testCase['Employee'][$k]['id'] = $employee->getEmpNumber();
        }
        file_put_contents(sfConfig::get('sf_plugins_dir') . '/orangehrmPimPlugin/test/fixtures/employee.yml', sfYaml::dump($this->testCase));
    }

    /**
     * Testing UpdateEmployeeJob
     */
    public function testUpdateEmployeeJob() {
        $jobDao = new JobDao();
        $job = new JobTitle();
        $job->setName("myjob");
        $job->setDescription("desc");
        $jobDao->saveJobTitle($job);

        foreach($this->testCase['Employee'] as $k => $v) {
            $employee = $this->employeeDao->getEmployee($v['id']);
            $employee->job_title_code = $job->getId();
            $result = $this->employeeDao->saveJobDetails($employee);
            $this->assertTrue($result);

            $result = $this->employeeDao->getPastJobTitles($v['id']);
            $this->assertTrue($result instanceof Doctrine_Collection);

            $result = $this->employeeDao->getPastSubdivisions($v['id']);
            $this->assertTrue($result instanceof Doctrine_Collection);
        }
    }

    /**
     * Testing Delete Past SubDivisionHistory
     */
    public function testDeletePastSubDivisionHistory() {
        foreach($this->testCase['Employee'] as $k => $v) {
            $list = $this->employeeDao->getPastLocations($v['id']);
            $deleteSubDivisions = array();
            foreach($list as $c => $obj) {
                $deleteSubDivisions[] = $obj->getId();
            }
            $result = $this->employeeDao->deleteSubDivisionHistory($v['id'], $deleteSubDivisions);
            $this->assertTrue($result);
        }
    }

    /**
     * Testing Delete JobTitleHistory
     */
    public function testDeleteJobTitleHistory() {
        $jobDao  = new JobDao();
        foreach($this->testCase['Employee'] as $k => $v) {
            $employee = $this->employeeDao->getEmployee($v['id']);
            $result = $this->employeeDao->deleteJobTitleHistory($v['id'], array($employee->job_title_code));
            $this->assertTrue($result);
            $jobDao->deleteJobTitle(array($employee->job_title_code));
        }
    }

    /**
     * Testing Add Employee Locations
     */
    public function testEmployeeLocation() {
        $location   = new Location();
        $companyDao = new CompanyDao();
        $location->setLocName("my_loc" . rand(0, 1000));
        $location->setLocCity("own_city" . rand(0, 1000));
        $companyDao->saveCompanyLocation($location);

        foreach($this->testCase['Employee'] as $k => $v) {
            $result = $this->employeeDao->assignLocation($v['id'], $location->getLocCode());
            $this->assertTrue($result);
            $list = $this->employeeDao->getPastLocations($v['id']);
            $this->assertTrue($list instanceof Doctrine_Collection);
            $this->testCase['Employee'][$k]['locCode'] = $location->getLocCode();
        }
        file_put_contents(sfConfig::get('sf_plugins_dir') . '/orangehrmPimPlugin/test/fixtures/employee.yml', sfYaml::dump($this->testCase));
    }

    /**
     * Testing Delete Employee Locations
     */
    public function testDeleteEmployeeLocation() {
        $companyDao = new CompanyDao();
        foreach($this->testCase['Employee'] as $k => $v) {
            $result = $this->employeeDao->removeLocation($v['id'], $v['locCode']);
            $this->assertTrue($result);
            $result = $this->employeeDao->deleteLocationHistory($v['id'], array($v['locCode']));
            $this->assertTrue($result);
            $companyDao->deleteCompanyLocation(array($v['locCode']));
            unset($this->testCase['Employee'][$k]['locCode']);
        }
        file_put_contents(sfConfig::get('sf_plugins_dir') . '/orangehrmPimPlugin/test/fixtures/employee.yml', sfYaml::dump($this->testCase));
    }

    /**
     * Testing Adding Employee Picture
     */
    public function testAddEmployeePicture() {
        foreach($this->testCase['Employee'] as $k => $v) {
            $pic = new EmpPicture();
            $pic->setEmpNumber($v['id']);
            $pic->setFilename("pic_" . rand(0, 1000));
            $result = $this->employeeDao->saveEmployeePicture($pic);
            $this->assertTrue($result);
        }
    }

    /**
     * Testing Manipulation of Employee Picture
     */
    public function testManipulateEmployeePicture() {
        foreach($this->testCase['Employee'] as $k => $v) {
            $pic = $this->employeeDao->readEmployeePicture($v['id']);
            $this->assertTrue($pic instanceof EmpPicture);

            $pic = $this->employeeDao->getPicture($v['id']);
            $this->assertTrue($pic instanceof EmpPicture);

            $result = $this->employeeDao->deletePhoto($v['id']);
            $this->assertTrue($result);
        }
    }

    /**
     * Testing getEmployeeListAsJson
     */
    public function testGetEmployeeListAsJson() {
        $result = $this->employeeDao->getEmployeeListAsJson();
        $this->assertTrue(!empty($result));
    }

    /**
     * Testing Saving Employee Contract Related
     */
    public function testSaveEmployeeContracts() {
        foreach($this->testCase['Employee'] as $k => $v) {
            $empContract = new EmpContract();
            $empContract->setEmpNumber($v['id']);
            $result = $this->employeeDao->saveEmpContract($empContract);
            $this->assertTrue($result);
            $this->testCase['Employee'][$k]['contractId'] = $empContract->getContractId();
            try {
                $this->employeeDao->saveEmpContract(new EmpContract());
            } catch(Exception $e) {
                $this->assertTrue($e instanceof DaoException);
            }
        }
        file_put_contents(sfConfig::get('sf_plugins_dir') . '/orangehrmPimPlugin/test/fixtures/employee.yml', sfYaml::dump($this->testCase));
    }

    /**
     * Testing Delete Employee Contract Related
     */
    public function testDeleteEmployeeContracts() {
        foreach($this->testCase['Employee'] as $k => $v) {
            $result = $this->employeeDao->deleteContracts($v['id'], array($v['contractId']));
            $this->assertTrue($result);
            try {
                $this->employeeDao->deleteContracts(new EmpContract(), new Employee());
            } catch(Exception $e) {
                $this->assertTrue($e instanceof DaoException);
            }
            unset($this->testCase['Employee'][$k]['contractId']);
        }
        file_put_contents(sfConfig::get('sf_plugins_dir') . '/orangehrmPimPlugin/test/fixtures/employee.yml', sfYaml::dump($this->testCase));
    }

    /**
     * Testing Saving Employee Licenses Related
     */
    public function testEmployeeLicenses() {
        $eduDao = new EducationDao();
        $licenses = new Licenses();
        $licenses->setLicensesDesc('desc_' . rand(0, 1000));
        $eduDao->saveLicenses($licenses);

        foreach($this->testCase['Employee'] as $k => $v) {
            $empLicence = new EmployeeLicense();
            $empLicence->setEmpNumber($v['id']);
            $empLicence->setCode($licenses->getLicensesCode());
            $result = $this->employeeDao->saveEmployeeLicense($empLicence);
            $this->assertTrue($result);

            //testing exceptions
            try {
                $this->employeeDao->saveEmployeeLicense(new EmployeeLicense());
            } catch(Exception $e) {
                $this->assertTrue($e instanceof DaoException);
            }
            $this->testCase['Employee'][$k]['licenseCode'] = $licenses->getLicensesCode();
        }
        file_put_contents(sfConfig::get('sf_plugins_dir') . '/orangehrmPimPlugin/test/fixtures/employee.yml', sfYaml::dump($this->testCase));
    }

    /**
     * Testing listing Employee Licenses Related
     */
    public function testListEmployeeLicenses() {
        foreach($this->testCase['Employee'] as $k => $v) {
            $list = $this->employeeDao->getAvailableLicenses($v['id']);
            $this->assertTrue($list instanceof Doctrine_Collection);
        }
    }

    /**
     * Testing Delete Employee Licenses Related
     */
    public function testDeleteEmployeeLicence() {
        $eduDao = new EducationDao();
        foreach($this->testCase['Employee'] as $k => $v) {
            $result = $this->employeeDao->deleteLicenses($v['id'], array($v['licenseCode']));
            $eduDao->deleteLicenses(array($v['licenseCode']));
            $this->assertTrue($result);
            try {
                $this->employeeDao->deleteLicenses(new Employee(), new EmployeeLicense());
            } catch(Exception $e) {
                $this->assertTrue($e instanceof DaoException);
            }
            unset($this->testCase['Employee'][$k]['licenseCode']);
        }
        file_put_contents(sfConfig::get('sf_plugins_dir') . '/orangehrmPimPlugin/test/fixtures/employee.yml', sfYaml::dump($this->testCase));
    }

    /**
     * Test Employee Skills Related
     */
    public function testEmployeeSkills() {
        $skillDao = new SkillDao();
        $skill =	 new Skill();
        $skill->setSkillName("name_" . rand(0, 1000));
        $skill->setSkillDescription("desc_" . rand(0, 1000));
        $skillDao->saveSkill($skill);

        foreach($this->testCase['Employee'] as $k => $v) {
            $empSkills = new EmployeeSkill();
            $empSkills->setEmpNumber($v['id']);
            $empSkills->setCode($skill->getSkillCode());
            $result = $this->employeeDao->saveEmployeeSkill($empSkills);
            $this->assertTrue($result);
            $this->testCase['Employee'][$k]['skillCode'] = $skill->getSkillCode();

            //testing exception
            try {
                $this->employeeDao->saveEmployeeSkill(new EmployeeSkill());
            } catch(Exception $e) {
                $this->assertTrue($e instanceof DaoException);
            }
        }
        file_put_contents(sfConfig::get('sf_plugins_dir') . '/orangehrmPimPlugin/test/fixtures/employee.yml', sfYaml::dump($this->testCase));
    }

    /**
     * Test list EmployeeSkills
     */
    public function testListEmployeeSkills() {
        foreach($this->testCase['Employee'] as $k => $v) {
            $list = $this->employeeDao->getAvailableSkills($v['id']);
            $this->assertTrue($list instanceof Doctrine_Collection);
        }
    }

    /**
     * Test Delete EmployeeSkills
     */
    public function testDeleteEmployeeSkills() {
        $skillsDao = new SkillDao();
        foreach($this->testCase['Employee'] as $k => $v) {
            $result = $this->employeeDao->deleteSkills($v['id'], array($v['skillCode']));
            $this->assertTrue($result);
            $skillsDao->deleteSkill(array($v['skillCode']));
            try {
                $this->employeeDao->deleteSkills(new Employee(), new EmployeeSkill());
            } catch(Exception $e) {
                $this->assertTrue($e instanceof DaoException);
            }
            unset($this->testCase['Employee'][$k]['skillCode']);
        }
        file_put_contents(sfConfig::get('sf_plugins_dir') . '/orangehrmPimPlugin/test/fixtures/employee.yml', sfYaml::dump($this->testCase));
    }

    /**
     * Test Employee Education Related
     */
    public function testEmployeeEducation() {
        $eduDao     = new EducationDao();
        $education 	= new Education();
        $education->setEduUni("uni_" . rand(0, 1000));
        $education->setEduDeg("deg_" . rand(0, 1000));
        $eduDao->saveEducation($education);

        foreach($this->testCase['Employee'] as $k => $v) {
            $empEdu = new EmployeeEducation();
            $empEdu->setEmpNumber($v['id']);
            $empEdu->setCode($education->getEduCode());
            $result = $this->employeeDao->saveEmployeeEducation($empEdu);
            $this->assertTrue($result);
            $this->testCase['Employee'][$k]['eduCode'] = $education->getEduCode();

            //testing exception
            try {
                $this->employeeDao->saveEmployeeEducation(new EmployeeEducation());
            } catch(Exception $e) {
                $this->assertTrue($e instanceof DaoException);
            }
        }
        file_put_contents(sfConfig::get('sf_plugins_dir') . '/orangehrmPimPlugin/test/fixtures/employee.yml', sfYaml::dump($this->testCase));
    }

    /**
     * Test list Employee Education
     */
    public function testListEmployeeEducation() {
        foreach($this->testCase['Employee'] as $k => $v) {
            $list = $this->employeeDao->getAvailableEducationList($v['id']);
            $this->assertTrue($list instanceof Doctrine_Collection);
        }
    }

    /**
     * Test Delete Employee Education
     */
    public function testDeleteEmployeeEducation() {
        $eduDao     = new EducationDao();
        foreach($this->testCase['Employee'] as $k => $v) {
            $result = $this->employeeDao->deleteEducation($v['id'], array($v['eduCode']));
            $this->assertTrue($result);
            $eduDao->deleteEducation(array($v['eduCode']));
            try {
                $this->employeeDao->deleteEducation(new Employee(), new EmployeeEducation());
            } catch(Exception $e) {
                $this->assertTrue($e instanceof DaoException);
            }
            unset($this->testCase['Employee'][$k]['eduCode']);
        }
        file_put_contents(sfConfig::get('sf_plugins_dir') . '/orangehrmPimPlugin/test/fixtures/employee.yml', sfYaml::dump($this->testCase));
    }

    /**
     * Test Employee Language Related
     */
    public function testEmployeeLanguage() {
        $skillDao = new SkillDao();
        $lang = new Language();
        $lang->setLangName("lang_" . rand(0, 1000));
        $skillDao->saveLanguage($lang);

        foreach($this->testCase['Employee'] as $k => $v) {
            $empLang = new EmployeeLanguage();
            $empLang->setEmpNumber($v['id']);
            $empLang->setLangType(1);
            $empLang->setCode($lang->getLangCode());
            $result = $this->employeeDao->saveEmployeeLanguage($empLang);
            $this->assertTrue($result);
            $this->testCase['Employee'][$k]['langCode'] = $lang->getLangCode();

            //testing exception
            try {
                $this->employeeDao->saveEmployeeLanguage(new EmployeeLanguage());
            } catch(Exception $e) {
                $this->assertTrue($e instanceof DaoException);
            }
        }
        file_put_contents(sfConfig::get('sf_plugins_dir') . '/orangehrmPimPlugin/test/fixtures/employee.yml', sfYaml::dump($this->testCase));
    }

    /**
     * Test Delete Employee Language
     */
    public function testDeleteEmployeeLanguage() {
        $skillsDao = new SkillDao();
        foreach($this->testCase['Employee'] as $k => $v) {
            $result = $this->employeeDao->deleteLanguages($v['id'], array(array($v['langCode'], 1)));
            $this->assertTrue($result);
            $skillsDao->deleteLanguage(array($v['langCode']));
            try {
                $this->employeeDao->deleteLanguages(new Employee(), new EmployeeLanguage());
            } catch(Exception $e) {
                $this->assertTrue($e instanceof DaoException);
            }
            unset($this->testCase['Employee'][$k]['langCode']);
        }
        file_put_contents(sfConfig::get('sf_plugins_dir') . '/orangehrmPimPlugin/test/fixtures/employee.yml', sfYaml::dump($this->testCase));
    }

    /**
     * Test Saving Employee Membership Detail
     */
    public function testEmployeeMemberDetail() {
        $membershipDao = new MembershipDao();
        $type = new MembershipType();
        $type->setMembtypeName("name_" . rand(0, 1000));
        $membershipDao->saveMembershipType($type);

        $mem = new Membership();
        $mem->setMembtypeCode($type->getMembtypeCode());
        $mem->setMembshipName("name_". rand(0, 1000));
        $membershipDao->saveMembership($mem);

        foreach($this->testCase['Employee'] as $k => $v) {
            $empMemDet = new EmployeeMemberDetail();
            $empMemDet->setEmpNumber($v['id']);
            $empMemDet->setMembshipCode($mem->getMembshipCode());
            $empMemDet->setMembtypeCode($type->getMembtypeCode());
            $result = $this->employeeDao->saveEmployeeMemberDetail($empMemDet);
            $this->assertTrue($result);

            $this->testCase['Employee'][$k]['memTypeCode']  = $type->getMembtypeCode();
            $this->testCase['Employee'][$k]['memCode']      = $mem->getMembshipCode();
        }
        file_put_contents(sfConfig::get('sf_plugins_dir') . '/orangehrmPimPlugin/test/fixtures/employee.yml', sfYaml::dump($this->testCase));
    }

    /**
     * Test Saving Employee Membership Detail
     */
    public function testListMembershipDetail() {
        foreach($this->testCase['Employee'] as $k => $v) {
            $list = $this->employeeDao->getAvailableMemberships($v['id'], $v['memTypeCode']);
            $this->assertTrue($list instanceof Doctrine_Collection);
        }
    }

    /**
     * Test Delete Employee Membership Detail
     */
    public function testDeleteEmployeeMemberDetail() {
        $membershipDao = new MembershipDao();
        foreach($this->testCase['Employee'] as $k => $v) {
            $result = $this->employeeDao->deleteMemberships($v['id'], array(array($v['memCode'], $v['memTypeCode'])));
            $this->assertTrue($result);
            $membershipDao->deleteMembership(array($v['memCode']));
            $membershipDao->deleteMembershipType(array($v['memTypeCode']));
            try {
                $this->employeeDao->deleteMemberships(new Employee(), new EmployeeMemberDetail());
            } catch(Exception $e) {
                $this->assertTrue($e instanceof DaoException);
            }

            unset($this->testCase['Employee'][$k]['memCode']);
            unset($this->testCase['Employee'][$k]['memTypeCode']);
        }
        file_put_contents(sfConfig::get('sf_plugins_dir') . '/orangehrmPimPlugin/test/fixtures/employee.yml', sfYaml::dump($this->testCase));
    }

    /**
     * Test Add Employee Salary
     */
    public function testEmployeeSalary() {
        $jobDao = new JobDao();
        $salaryGrade = new SalaryGrade();
        $salaryGrade->setSalGrdName('name_' . rand(0, 1000));
        $jobDao->saveSalaryGrade($salaryGrade);

        foreach($this->testCase['Employee'] as $k => $v) {
            $empSal = new EmpBasicsalary();
            $empSal->setEmpNumber($v['id']);
            $empSal->setCurrencyId("USD");
            $empSal->setSalGrdCode($salaryGrade->getSalGrdCode());
            $result = $this->employeeDao->saveEmpBasicsalary($empSal);
            $this->assertTrue($result);

            $this->testCase['Employee'][$k]['salGrdCode'] = $salaryGrade->getSalGrdCode();
            $this->testCase['Employee'][$k]['currencyId'] = "USD";
        }
        file_put_contents(sfConfig::get('sf_plugins_dir') . '/orangehrmPimPlugin/test/fixtures/employee.yml', sfYaml::dump($this->testCase));
    }

    /**
     * Test Delete Employee Salary
     */
    public function testDeleteEmployeeSalary() {
        $jobDao = new JobDao();
        foreach($this->testCase['Employee'] as $k => $v) {
            $result = $this->employeeDao->deleteSalary($v['id'], array(array($v['salGrdCode'], $v['currencyId'])));
            $this->assertTrue($result);
            try {
                $this->employeeDao->deleteSalary(new Employee(), new EmpBasicsalary());
            } catch(Exception $e) {
                $this->assertTrue($e instanceof DaoException);
            }
            $jobDao->deleteSalaryGrade(array($v['salGrdCode']));
            unset($this->testCase['Employee'][$k]['salGrdCode']);
            unset($this->testCase['Employee'][$k]['currencyId']);
        }
        file_put_contents(sfConfig::get('sf_plugins_dir') . '/orangehrmPimPlugin/test/fixtures/employee.yml', sfYaml::dump($this->testCase));
    }

    /**
     * Testing deleteEmployee and all associated domain classes
     */
    public function testDeleteEmployee() {
        foreach($this->testCase['Employee'] as $k => $v) {
            $employee = $this->employeeDao->getEmployee($v['id']);
            $this->assertTrue($employee instanceof Employee);

            $result = $this->employeeDao->deleteEmployee(array($v['id']));
            $this->assertEquals($result, 1);
            unset($this->testCase['Employee'][$k]['id']);
        }
        file_put_contents(sfConfig::get('sf_plugins_dir') . '/orangehrmPimPlugin/test/fixtures/employee.yml', sfYaml::dump($this->testCase));
    }
}
?>