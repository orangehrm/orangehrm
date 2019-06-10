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
 * Boston, MA  02110-1301, USA
 */

/**
 * @group Pim
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
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmPimPlugin/test/fixtures/EmployeeDao.yml';
        $this->employeeService = new EmployeeService();
    }
    
    public function testGetSetEmployeeDao() {
        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
                
        $this->employeeService->setEmployeeDao($mockDao);
        $this->assertEquals($mockDao, $this->employeeService->getEmployeeDao());        
    }

    /**
     * Testing addEmployee
     */
    public function testAddEmployee() {
        foreach ($this->testCase['Employee'] as $k => $v) {
            $employee = new Employee();
            $employee->setLastName($v['lastName']);
            $employee->setFirstName($v['firstName']);

            $employeeDao = $this->getMockBuilder('EmployeeDao')->getMock();
            $employeeDao->expects($this->once())
                    ->method('saveEmployee')
                    ->will($this->returnValue($employee));
            $this->employeeService->setEmployeeDao($employeeDao);
            $result = $this->employeeService->saveEmployee($employee);
            $this->assertTrue($result instanceof Employee);
        }
    }

    /**
     * Testing GetEmployee
     */
    public function testGetEmployee() {
        
        $empNumber = 12;
        $employee = new Employee();
        $employee->setLastName('Last Name');
        $employee->setFirstName('First Name');
        $employee->setEmpNumber($empNumber);
        
        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
             ->method('getEmployee')
             ->with($empNumber)
             ->will($this->returnValue($employee));
        
        $this->employeeService->setEmployeeDao($mockDao);
        
        $retVal = $this->employeeService->getEmployee($empNumber);
        
        $this->assertEquals($employee, $retVal);
        
    }
    
    
    /**
     * Testing Adding Employee Picture
     */
    public function testSaveEmployeePicture() {
        
        $this->employeeDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $this->employeeDao->expects($this->once())
                ->method('saveEmployeePicture')
                ->will($this->returnValue(new EmpPicture()));
        $this->employeeService->setEmployeeDao($this->employeeDao);
        
        $result = $this->employeeService->saveEmployeePicture(new EmpPicture());
        $this->assertTrue($result instanceof EmpPicture);        
        
    }

    /**
     * Testing Adding Employee Picture
     */
    public function testAddEmployeePictureException() {
        
        $empNumber = 102;
        
        $pic = new EmpPicture();
        $pic->setEmpNumber($empNumber);
        $pic->setFilename("pic_" . rand(0, 1000));        
        
    }
    
    /**
     * Testing readEmployeePicture
     */
    public function testReadEmployeePicture() {
        foreach ($this->testCase['Employee'] as $k => $v) {
            $this->employeeDao = $this->getMockBuilder('EmployeeDao')->getMock();
            $this->employeeDao->expects($this->once())
                    ->method('readEmployeePicture')
                    ->will($this->returnValue(new EmpPicture()));
            $this->employeeService->setEmployeeDao($this->employeeDao);
            $pic = $this->employeeService->readEmployeePicture($v['id']);
            $this->assertTrue($pic instanceof EmpPicture);
        }
    }
    
    /**
     * Testing getPicture
     */
    public function testGetPicture() {
        $picture = 'askd;sadjf';
        $empNumber = 121;
        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
                 ->method('getEmployeePicture')
                 ->with($empNumber)
                 ->will($this->returnValue($picture));
        
        $this->employeeService->setEmployeeDao($mockDao);
        
        $result = $this->employeeService->getEmployeePicture($empNumber);
        $this->assertEquals($picture, $result);
        
    }
    
    /**
     * Testing deletePhoto
     */
    public function testDeletePhoto() {
        foreach ($this->testCase['Employee'] as $k => $v) {
            $this->employeeDao = $this->getMockBuilder('EmployeeDao')->getMock();
            $this->employeeDao->expects($this->once())
                    ->method('deleteEmployeePicture')
                    ->will($this->returnValue(1));
            $this->employeeService->setEmployeeDao($this->employeeDao);
            $result = $this->employeeService->deleteEmployeePicture($v['id']);
            $this->assertEquals(1, $result);
        }
    }    
    
    /**
     * Testing deleteEmergencyContacts
     */
    public function testDeleteEmergencyContacts() {
        $empNumber = 111;
        $contactsToDelete = array('1', '2', '4');
        
        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
                 ->method('deleteEmployeeEmergencyContacts')
                 ->with($empNumber, $contactsToDelete)
                 ->will($this->returnValue(true));
        
        $this->employeeService->setEmployeeDao($mockDao);
        
        $result = $this->employeeService->deleteEmployeeEmergencyContacts($empNumber, $contactsToDelete);
        $this->assertTrue($result);
        
    }

    /**
     * Testing deleteImmigration
     */
    public function testDeleteImmigration() {
        $empNumber = 111;
        $immigrationToDelete = array('1', '2', '4');
        
        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
                 ->method('deleteEmployeeImmigrationRecords')
                 ->with($empNumber, $immigrationToDelete)
                 ->will($this->returnValue(2));
        
        $this->employeeService->setEmployeeDao($mockDao);
        
        $result = $this->employeeService->deleteEmployeeImmigrationRecords($empNumber, $immigrationToDelete);
        $this->assertEquals(2, $result);
        
    }
     
    /**
     * Testing getDependents
     */
    public function testGetDependents() {
        $empNumber = 111;
        $dependents = array();
        $dependent = new EmpDependent();
        $dependent->setEmpNumber(111);
        $dependent->setSeqno(1);
        $dependent->setName('Anthony Perera');
        $dependent->setRelationshipType('child');
        
        $dependents[0] = $dependent;

        $dependent = new EmpDependent();
        $dependent->setEmpNumber(111);
        $dependent->setSeqno(2);
        $dependent->setName('Anton Perera');
        $dependent->setRelationshipType('child');
        
        $dependents[1] = $dependent;
        
        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
                 ->method('getEmployeeDependents')
                 ->with($empNumber)
                 ->will($this->returnValue($dependents));
        
        $this->employeeService->setEmployeeDao($mockDao);
        
        $result = $this->employeeService->getEmployeeDependents($empNumber);
        $this->assertEquals($dependents, $result);
        
    }
    
    /**
     * Testing deleteDependents
     */
    public function testDeleteDependents() {
        $empNumber = 111;
        $entriesToDelete = array('1', '2', '4');
        
        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
                 ->method('deleteEmployeeDependents')
                 ->with($empNumber, $entriesToDelete)
                 ->will($this->returnValue(2));
        
        $this->employeeService->setEmployeeDao($mockDao);
        
        $result = $this->employeeService->deleteEmployeeDependents($empNumber, $entriesToDelete);
        $this->assertEquals(2, $result);
        
    }
    
    /**
     * Testing isSupervisor
     */
    public function testIsSupervisor() {
        $empNumber = 111;
        
        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
                 ->method('isSupervisor')
                 ->with($empNumber)
                 ->will($this->returnValue(true));
        
        $this->employeeService->setEmployeeDao($mockDao);
        
        $result = $this->employeeService->isSupervisor($empNumber);
        $this->assertTrue($result);
        
    }
    
    /**
     * Testing saveWorkExperience
     */
    public function testSaveWorkExperience() {
        
        $empNumber = 121;
        $experience = new EmpWorkExperience();
        $experience->setEmpNumber($empNumber);
        $experience->setSeqno(1);
        $experience->setEmployer('ACME Inc');
        $experience->setJobtitle('Manager');
        
        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
                 ->method('saveEmployeeWorkExperience')
                 ->with($experience)
                 ->will($this->returnValue($experience));
        
        $this->employeeService->setEmployeeDao($mockDao);        
        $result = $this->employeeService->saveEmployeeWorkExperience($experience);
        
        $this->assertTrue($result === $experience);
        
    }    
    
    /**
     * Testing getWorkExperience
     */
    public function testGetWorkExperience() {
        $empNumber = 121;
        $sequence = 1;
        
        $experience = new EmpWorkExperience();
        $experience->setEmpNumber($empNumber);
        $experience->setSeqno(1);
        $experience->setEmployer('ACME Inc');
        $experience->setJobtitle('Manager');
        
        $isEss = true;
        
        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
                 ->method('getEmployeeWorkExperienceRecords')
                 ->with($empNumber, $sequence)
                 ->will($this->returnValue($experience));
        
        $this->employeeService->setEmployeeDao($mockDao);
        
        $result = $this->employeeService->getEmployeeWorkExperienceRecords($empNumber, $sequence);
        $this->assertEquals($experience, $result);              
    } 
    
    /**
     * Testing deleteWorkExperience
     */
    public function testDeleteWorkExperience() {
        $empNumber = 111;
        $entriesToDelete = array('1', '2', '4');
        
        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
                 ->method('deleteEmployeeWorkExperienceRecords')
                 ->with($empNumber, $entriesToDelete)
                 ->will($this->returnValue(2));
        
        $this->employeeService->setEmployeeDao($mockDao);
        
        $result = $this->employeeService->deleteEmployeeWorkExperienceRecords($empNumber, $entriesToDelete);
        $this->assertEquals(2, $result);              
    }
    
    /**
     * Testing saveEducation
     */
    public function testSaveEducation() {
        
        $empNumber = 121;
        $education = new EmployeeEducation();
        $education->setEmpNumber($empNumber);
        $education->setEducationId(1);
        $education->setMajor('Engineering');
        $education->setYear('2000');
        $education->setScore('3.2');       
       
        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
                 ->method('saveEmployeeEducation')
                 ->with($education)
                 ->will($this->returnValue($education));
        
        $this->employeeService->setEmployeeDao($mockDao);        
        $result = $this->employeeService->saveEmployeeEducation($education);
        
        $this->assertTrue($result === $education); 
        
    }    
    
    /**
     * Testing getEducation
     */
    public function testGetEducation() {
        
        $education = new EmployeeEducation();
        $education->setId(1);
        $education->setEmpNumber(121);
        $education->setEducationId(1);
        $education->setMajor('Engineering');
        $education->setYear('2000');
        $education->setScore('3.2');  
        
        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
                 ->method('getEducation')
                 ->with(1)
                 ->will($this->returnValue($education));
        
        $this->employeeService->setEmployeeDao($mockDao);
        
        $result = $this->employeeService->getEducation(1);
        $this->assertEquals($education, $result);              
    } 
    
    /**
     * Testing deleteEducation
     */
    public function testDeleteEducation() {
        $empNumber = 111;
        $entriesToDelete = array('1', '2', '4');
        
        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
                 ->method('deleteEmployeeEducationRecords')
                 ->with($empNumber, $entriesToDelete)
                 ->will($this->returnValue(2));
        
        $this->employeeService->setEmployeeDao($mockDao);
        
        $result = $this->employeeService->deleteEmployeeEducationRecords($empNumber, $entriesToDelete);
        $this->assertEquals(2, $result);              
    }
    
    /**
     * Testing saveSkill
     */
    public function testSaveSkill() {
        
        $empNumber = 121;
        $skill = new EmployeeSkill();
        $skill->setEmpNumber($empNumber);
        $skill->setSkillId(2);
        $skill->setYearsOfExp(2);
        
        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
                 ->method('saveEmployeeSkill')
                 ->with($skill)
                 ->will($this->returnValue($skill));
        
        $this->employeeService->setEmployeeDao($mockDao);
        
        $result = $this->employeeService->saveEmployeeSkill($skill);
        $this->assertTrue($result === $skill);
        
    }    
    
    /**
     * Testing getSkill
     */
    public function testGetSkill() {
        $empNumber = 121;
        $skillCode = 'SKI002';
        
        $skill = new EmployeeSkill();
        $skill->setEmpNumber($empNumber);
        $skill->setSkillId(2);
        $skill->setYearsOfExp(2);
        
        $isEss = true;
        
        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
                 ->method('getEmployeeSkills')
                 ->with($empNumber, $skillCode)
                 ->will($this->returnValue($skill));
        
        $this->employeeService->setEmployeeDao($mockDao);
        
        $result = $this->employeeService->getEmployeeSkills($empNumber, $skillCode);
        $this->assertEquals($skill, $result);              
    } 
    
    /**
     * Testing deleteSkill
     */
    public function testDeleteSkill() {
        $empNumber = 111;
        $entriesToDelete = array('1', '2', '4');
        
        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
                 ->method('deleteEmployeeSkills')
                 ->with($empNumber, $entriesToDelete)
                 ->will($this->returnValue(2));
        
        $this->employeeService->setEmployeeDao($mockDao);
        
        $result = $this->employeeService->deleteEmployeeSkills($empNumber, $entriesToDelete);
        $this->assertEquals(2, $result);              
    }
    
    /**
     * Testing getSalary
     */
    public function testGetSalary() {
        $empNumber = 121;
        $id = 1;
        
        $salary = new EmployeeSalary();
        $salary->setEmpNumber($empNumber);
        $salary->setId($id);
        $salary->setSalaryName('Travel Expenses');
        
        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
                 ->method('getEmployeeSalaries')
                 ->with($empNumber, $id)
                 ->will($this->returnValue($salary));
        
        $this->employeeService->setEmployeeDao($mockDao);
        
        $result = $this->employeeService->getEmployeeSalaries($empNumber, $id);
        $this->assertEquals($salary, $result);              
    }
    
    public function testDeleteEmployeeSalaries() {
        
        $empNumber = 1;
        $entriesToDelete = array(1, 2);
        
        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
                 ->method('deleteEmployeeSalaryComponents')
                 ->with($empNumber, $entriesToDelete)
                 ->will($this->returnValue(2));
        
        $this->employeeService->setEmployeeDao($mockDao);
        
        $result = $this->employeeService->deleteEmployeeSalaryComponents($empNumber, $entriesToDelete);
        $this->assertEquals(2, $result); 
        
    }  
    
    /**
     * Testing saveLanguage
     */
    public function testSaveLanguage() {
        
        $empNumber = 121;
        $language = new EmployeeLanguage();
        $language->setEmpNumber($empNumber);
        $language->setLangId(2);
        $language->setFluency(1);
        $language->setCompetency(1);
        $language->setComments('no comments'); 
        
        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
                 ->method('saveEmployeeLanguage')
                 ->with($language)
                 ->will($this->returnValue($language));
        
        $this->employeeService->setEmployeeDao($mockDao);        
        $result = $this->employeeService->saveEmployeeLanguage($language);
        
        $this->assertTrue($result === $language);
        
    }    
    
    /**
     * Testing getLanguage
     */
    public function testGetLanguage() {
        $empNumber = 121;
        
        $language = new EmployeeLanguage();
        $language->setEmpNumber($empNumber);
        $language->setLangId(2);
        $language->setFluency(1);
        $language->setCompetency(1);
        $language->setComments('no comments');        
        
        $languageCode = 2;
        $langType = 1;
        
        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
                 ->method('getEmployeeLanguages')
                 ->with($empNumber, $languageCode, $langType)
                 ->will($this->returnValue($language));
        
        $this->employeeService->setEmployeeDao($mockDao);
        
        $result = $this->employeeService->getEmployeeLanguages($empNumber, $languageCode, $langType);
        $this->assertEquals($language, $result);              
    } 
    
    /**
     * Testing deleteLanguage
     */
    public function testDeleteLanguage() {
        $empNumber = 111;
        $entriesToDelete = array(array(1 => 1), array(1 => 2));
        
        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
                 ->method('deleteEmployeeLanguages')
                 ->with($empNumber, $entriesToDelete)
                 ->will($this->returnValue(2));
        
        $this->employeeService->setEmployeeDao($mockDao);
        
        $result = $this->employeeService->deleteEmployeeLanguages($empNumber, $entriesToDelete);
        $this->assertEquals(2, $result);              
    }
    
    /**
     * Testing saveLicense
     */
    public function testSaveLicense() {
        
        $empNumber = 121;
        $license = new EmployeeLicense();
        $license->setEmpNumber($empNumber);
        $license->setLicenseId(2);
        $license->setLicenseNo('199919');
        
        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
                 ->method('saveEmployeeLicense')
                 ->with($license)
                 ->will($this->returnValue($license));
        
        $this->employeeService->setEmployeeDao($mockDao);        
        $result = $this->employeeService->saveEmployeeLicense($license);
        
        $this->assertTrue($result === $license);  
        
    }    
    
    /**
     * Testing getLicense
     */
    public function testGetLicense() {
        $empNumber = 121;
        $licenseCode = 2;
        
        $license = new EmployeeLicense();
        $license->setEmpNumber($empNumber);
        $license->setLicenseId($licenseCode);
        $license->setLicenseNo('199919');        
        
        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
                 ->method('getEmployeeLicences')
                 ->with($empNumber, $licenseCode)
                 ->will($this->returnValue($license));
        
        $this->employeeService->setEmployeeDao($mockDao);
        
        $result = $this->employeeService->getEmployeeLicences($empNumber, $licenseCode);
        $this->assertEquals($license, $result);              
    } 
    
    /**
     * Testing deleteLicense
     */
    public function testDeleteLicense() {
        $empNumber = 111;
        $entriesToDelete = array('1', '2', '4');
        
        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
                 ->method('deleteEmployeeLicenses')
                 ->with($empNumber, $entriesToDelete)
                 ->will($this->returnValue(2));
        
        $this->employeeService->setEmployeeDao($mockDao);
        
        $result = $this->employeeService->deleteEmployeeLicenses($empNumber, $entriesToDelete);
        $this->assertEquals(2, $result);              
    } 
    
    /**
     * Testing getAttachments
     */
    public function testGetAttachments() {
        $empNumber = 121;
        $screen = 'personal';
        
        $attachments = array();
        foreach ($this->testCase['EmployeeAttachment'] as $values ) {
            $attachment = new EmployeeAttachment();
            $attachment->fromArray($values);
            $attachments[] = $attachment;
        }             
        
        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
                 ->method('getEmployeeAttachments')
                 ->with($empNumber, $screen)
                 ->will($this->returnValue($attachments));
        
        $this->employeeService->setEmployeeDao($mockDao);
        
        $result = $this->employeeService->getEmployeeAttachments($empNumber, $screen);
        $this->assertEquals($attachments, $result);              
        
        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
                 ->method('getEmployeeAttachments')
                 ->with($empNumber, $screen)
                 ->will($this->throwException(new DaoException()));
               
        $this->employeeService->setEmployeeDao($mockDao);
        
        try {
            $result = $this->employeeService->getEmployeeAttachments($empNumber, $screen);
            $this->fail("Exception expected");
        } catch (Exception $e) {
        }  
        
    } 
    
    /**
     * Testing deleteAttachments
     */
    public function testDeleteAttachments() {
        $empNumber = 111;
        $entriesToDelete = array('1', '2', '4');
        
        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
                 ->method('deleteEmployeeAttachments')
                 ->with($empNumber, $entriesToDelete)
                 ->will($this->returnValue(2));
        
        $this->employeeService->setEmployeeDao($mockDao);
        
        $result = $this->employeeService->deleteEmployeeAttachments($empNumber, $entriesToDelete);
        $this->assertEquals(2, $result);              
        
        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
                 ->method('deleteEmployeeAttachments')
                 ->with($empNumber, $entriesToDelete)
                 ->will($this->throwException(new DaoException()));
               
        $this->employeeService->setEmployeeDao($mockDao);
        
        try {
            $result = $this->employeeService->deleteEmployeeAttachments($empNumber, $entriesToDelete);
            $this->fail("Exception expected");
        } catch (Exception $e) {
        }         
    }
    
    /**
     * Testing getAttachment
     */
    public function testGetAttachment() {
        $empNumber = 121;
        
        $attachments = array();
        foreach ($this->testCase['EmployeeAttachment'] as $values ) {
            $attachment = new EmployeeAttachment();
            $attachment->fromArray($values);
            $attachments[] = $attachment;
        }             
        
        $attachment = $attachments[0];
        $attachmentId = $attachment->getAttachId();
        
        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
                 ->method('getEmployeeAttachment')
                 ->with($empNumber, $attachmentId)
                 ->will($this->returnValue($attachment));
        
        $this->employeeService->setEmployeeDao($mockDao);
        
        $result = $this->employeeService->getEmployeeAttachment($empNumber, $attachmentId);
        $this->assertEquals($attachment, $result);              
        
        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
                 ->method('getEmployeeAttachment')
                 ->with($empNumber, $attachmentId)
                 ->will($this->throwException(new DaoException()));
               
        $this->employeeService->setEmployeeDao($mockDao);
        
        try {
            $result = $this->employeeService->getEmployeeAttachment($empNumber, $attachmentId);
            $this->fail("Exception expected");
        } catch (Exception $e) {
        }  
        
    }
    
    /**
     * Testing getEmployeeList
     */
    public function testGetEmployeeList() {
        $sortField = 'empNumber';
        $orderBy = 'DESC';
        
        $employees = array();
        foreach ($this->testCase['Employee'] as $values ) {
            $employee = new Employee();
            $employee->fromArray($values);
            $employees[] = $employee;
        }             
        
        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
                 ->method('getEmployeeList')
                 ->with($sortField, $orderBy)
                 ->will($this->returnValue($employees));
        
        $this->employeeService->setEmployeeDao($mockDao);
        
        $result = $this->employeeService->getEmployeeList($sortField, $orderBy);
        $this->assertEquals($employees, $result);              
        
    }
    
    /**
     * Testing getSupervisorList
     */
    public function testGetSupervisorList() {
        $sortField = 'empNumber';
        $orderBy = 'DESC';
        
        $supervisors = array();
        foreach ($this->testCase['Employee'] as $values ) {
            $employee = new Employee();
            $employee->fromArray($values);
            $supervisors[] = $employee;
        }             
        
        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
                 ->method('getSupervisorList')
                 ->will($this->returnValue($supervisors));
        
        $this->employeeService->setEmployeeDao($mockDao);
        
        $result = $this->employeeService->getSupervisorList();
        $this->assertEquals($supervisors, $result);              
        
    }
    
    /**
     * Testing searchEmployee
     */
    public function testSearchEmployee() {
        $field = 'empNumber';
        $value = '2';
        
        $employees = array();
        foreach ($this->testCase['Employee'] as $values ) {
            $employee = new Employee();
            $employee->fromArray($values);
            $employees[] = $employee;
        }             
        
        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
                 ->method('searchEmployee')
                 ->with($field, $value)
                 ->will($this->returnValue($employees));
        
        $this->employeeService->setEmployeeDao($mockDao);
        
        $result = $this->employeeService->searchEmployee($field, $value);
        $this->assertEquals($employees, $result);              
        
    }    
    
    /**
     * Testing getEmployeeCount
     */
    public function testGetEmployeeCount() {

        $count = 212;         
        
        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
                 ->method('getEmployeeCount')
                 ->will($this->returnValue($count));
        
        $this->employeeService->setEmployeeDao($mockDao);
        
        $result = $this->employeeService->getEmployeeCount();
        $this->assertEquals($count, $result);              
        
    }    
    
    /**
     * Testing getEmployeeListAsJson
     */
    public function testGetEmployeeListAsJson() {

        $employees = array();
        foreach ($this->testCase['EmployeeJson'] as $values ) {
            $employees[] = $values;
        } 
        
        $workShift = true;
        
        $jsonStr = json_encode($employees);

        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
                ->method('getEmployeeListAsJson')
                ->with($workShift)
                ->will($this->returnValue($jsonStr));
        $this->employeeService->setEmployeeDao($mockDao);
        $result = $this->employeeService->getEmployeeListAsJson($workShift);
        $this->assertEquals($jsonStr, $result);
        
    }

    /**
     * Testing getSubordinateList
     */
    public function testGetSubordinateList() {
        $supervisorId = '11';
        
        $employees = array();
        foreach ($this->testCase['Employee'] as $values ) {
            $employee = new Employee();
            $employee->fromArray($values);
            $employees[] = $employee;
        }             
        
        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
                 ->method('getSubordinateList')
                 ->with($supervisorId, true, true)
                 ->will($this->returnValue($employees));
                 
        $configServiceMock = $this->getMockBuilder('ConfigService')->getMock();
        $configServiceMock->expects($this->once())
             ->method('isSupervisorChainSuported')
             ->will($this->returnValue(true));
        
        $this->employeeService->setEmployeeDao($mockDao);
        $this->employeeService->setConfigurationService($configServiceMock);
        
        $result = $this->employeeService->getSubordinateList($supervisorId,  true);
        $this->assertEquals($employees, $result);
        
    }
    
   /**
     * Testing filterEmployeeListBySubUnit
     */
    public function testFilterEmployeeListBySubUnit() {
        $subUnitId = 12;    
        
        $employees = array();
        foreach ($this->testCase['Employee'] as $values ) {
            $employee = new Employee();
            $employee->fromArray($values);
            $employees[] = $employee;
        }             
        
        $employees[0]->setWorkStation($subUnitId);
        
        // Search by specific sub unit
        $result = $this->employeeService->filterEmployeeListBySubUnit($employees, $subUnitId);
        $this->assertTrue(is_array($result));
        $this->assertEquals(1, count($result));
        $this->assertEquals($employees[0], $result[0]);
        
        // Search by Root - Should return all employees in list
        $result = $this->employeeService->filterEmployeeListBySubUnit($employees, 1);
        $this->assertEquals($employees, $result);
        
        // Empty subunit Id should be the same as root 
        $result = $this->employeeService->filterEmployeeListBySubUnit($employees, NULL);
        $this->assertEquals($employees, $result);
        
        // If no employee list passed, will get all employees from dao adn filter        
        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
                 ->method('getEmployeeList')
                 ->will($this->returnValue($employees));
        
        $this->employeeService->setEmployeeDao($mockDao);
        
        $result = $this->employeeService->filterEmployeeListBySubUnit(NULL, $subUnitId);
        $this->assertTrue(is_array($result));
        $this->assertEquals(1, count($result));
        $this->assertEquals($employees[0], $result[0]);           
        
    }
    
    
    /**
     * Testing deleteEmployee
     */
    public function testDeleteEmployees() {
                
        $employeesToDelete = array('1', '2', '4');
        $numEmployees = count($employeesToDelete);
        
        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
                 ->method('deleteEmployees')
                 ->with($employeesToDelete)
                 ->will($this->returnValue($numEmployees));
        
        $this->employeeService->setEmployeeDao($mockDao);
        
        $result = $this->employeeService->deleteEmployees($employeesToDelete);
        $this->assertEquals($numEmployees, $result);              
        
    }

    /**
     * Testing isEmployeeIdInUse
     */
    public function testIsEmployeeIdInUse() {

        $employeeId = 'E199';
        
        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
                ->method('isExistingEmployeeId')
                ->with($employeeId)
                ->will($this->returnValue(true));
        
        $this->employeeService->setEmployeeDao($mockDao);
        $result = $this->employeeService->isExistingEmployeeId($employeeId);
        $this->assertTrue($result);               
        
    }
    
    
    /**
     * Testing checkForEmployeeWithSameName
     */
    public function testCheckForEmployeeWithSameName() {

        $first = 'John';
        $middle = 'A';
        $last = 'Kennedy';
        
        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
                ->method('checkForEmployeeWithSameName')
                ->with($first, $middle, $last)
                ->will($this->returnValue(true));
        
        $this->employeeService->setEmployeeDao($mockDao);
        $result = $this->employeeService->checkForEmployeeWithSameName($first, $middle, $last);
        $this->assertTrue($result);               
        
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

        $employeeDao = $this->getMockBuilder('EmployeeDao')->getMock();

        $employeeDao->expects($this->once())
                ->method('getEmployeeEmergencyContacts')
                ->with($empNumber)
                ->will($this->returnValue($contacts));

        $this->employeeService->setEmployeeDao($employeeDao);

        $emgContacts = $this->employeeService->getEmployeeEmergencyContacts($empNumber);
        $this->assertEquals(count($contacts), count($emgContacts));
        $this->assertEquals($emgContacts, $contacts);

    }

    /**
     * Test SaveEmployeePassport
     */
    public function testSaveEmployeePassport() {
        
        $empPassport = new EmployeeImmigrationRecord();
        $empPassport->setEmpNumber(1);        

        $employeeDao = $this->getMockBuilder('EmployeeDao')->getMock();

        $employeeDao->expects($this->once())
                ->method('saveEmployeeImmigrationRecord')
                ->with($empPassport)
                ->will($this->returnValue($empPassport));

        $this->employeeService->setEmployeeDao($employeeDao);

        $result = $this->employeeService->saveEmployeeImmigrationRecord($empPassport);
        $this->assertTrue($result === $empPassport);
        
    }

    /**
     * Test saving getEmployeePassport returns object
     */
    public function testGetEmployeePassport() {

        $employeeDao = $this->getMockBuilder('EmployeeDao')->getMock();

        $employeeDao->expects($this->once())
                ->method('getEmployeeImmigrationRecords')
                ->will($this->returnValue(new EmployeeImmigrationRecord()));

        $this->employeeService->setEmployeeDao($employeeDao);

        $readEmpPassport = $this->employeeService->getEmployeeImmigrationRecords(1);
        $this->assertTrue($readEmpPassport instanceof EmployeeImmigrationRecord);
    }

    /**
     * Test getEmployeeTax returns object
     */
    public function testGetEmployeeTaxExemptions() {

        $employeeDao = $this->getMockBuilder('EmployeeDao')->getMock();

        $employeeDao->expects($this->once())
                ->method('getEmployeeTaxExemptions')
                ->will($this->returnValue(new EmpUsTaxExemption()));

        $this->employeeService->setEmployeeDao($employeeDao);

        $readEmpTaxExemption = $this->employeeService->getEmployeeTaxExemptions(1);
        $this->assertTrue($readEmpTaxExemption instanceof EmpUsTaxExemption);
    }

    /**
     * Test SaveEmployeeTaxExemptions
     */
    public function testSaveEmployeeTaxExemptions() {

        $empUsTaxExemption = new EmpUsTaxExemption();
        $empUsTaxExemption->setEmpNumber(3);
        
        $employeeDao = $this->getMockBuilder('EmployeeDao')->getMock();

        $employeeDao->expects($this->once())
                ->method('saveEmployeeTaxExemptions')
                ->will($this->returnValue($empUsTaxExemption));

        $this->employeeService->setEmployeeDao($employeeDao);
        $result = $this->employeeService->saveEmployeeTaxExemptions($empUsTaxExemption);
        
        $this->assertTrue($result === $empUsTaxExemption);
        
    }

    /**
     * Test Supervisor Report-To list for a given employee
     */
    public function testGetSupervisorListForEmployee() {

        $empNumber = 3;

        $reportToSupervisorList = TestDataService::loadObjectList('ReportTo', $this->fixture, 'ReportTo');
        $reportToSupervisorList1 = array($reportToSupervisorList[0], $reportToSupervisorList[1]);
        $employeeDao = $this->getMockBuilder('EmployeeDao')->getMock();

        $employeeDao->expects($this->once())
                ->method('getImmediateSupervisors')
                ->with($empNumber)
                ->will($this->returnValue($reportToSupervisorList1));

        $this->employeeService->setEmployeeDao($employeeDao);

        $readReportToSupervisorList1 = $this->employeeService->getImmediateSupervisors($empNumber);
        $this->assertTrue($readReportToSupervisorList1[0] instanceof ReportTo);
    }

    /**
     * Test Subordiate Report-To list for a given employee
     */
    public function testGetSubordinateListForEmployee() {

        $empNumber = 3;

        $reportToSubordinateList = TestDataService::loadObjectList('ReportTo', $this->fixture, 'ReportTo');
        $reportToSubordinateListList1 = array($reportToSubordinateList[2], $reportToSubordinateList[3]);
        $employeeDao = $this->getMockBuilder('EmployeeDao')->getMock();

        $employeeDao->expects($this->once())
                ->method('getSubordinateListForEmployee')
                ->with($empNumber)
                ->will($this->returnValue($reportToSubordinateListList1));

        $this->employeeService->setEmployeeDao($employeeDao);

        $readReportToSubordinateList1 = $this->employeeService->getSubordinateListForEmployee($empNumber);
        $this->assertTrue($readReportToSubordinateList1[0] instanceof ReportTo);
        $this->assertTrue($readReportToSubordinateList1[1] instanceof ReportTo);
    }

    /**
     * Test get Report-To object for a given supervisor subordinate and reporting method
     */
    public function testGetReportToObject() {

        $reportingMode = 4;
        $supNumber = 4;
        $subNumber = 3;

        $reportToObjectList = TestDataService::loadObjectList('ReportTo', $this->fixture, 'ReportTo');

        $employeeDao = $this->getMockBuilder('EmployeeDao')->getMock();

        $employeeDao->expects($this->once())
                ->method('getReportToObject')
                ->with($supNumber, $subNumber)
                ->will($this->returnValue($reportToObjectList[0]));

        $this->employeeService->setEmployeeDao($employeeDao);

        $readReportToObject = $this->employeeService->getReportToObject($supNumber, $subNumber);
        $this->assertTrue($readReportToObject instanceof ReportTo);
    }

    /**
     * Test delete Report-To object list for a given string array containing supervisor subordinate and reporting method
     */
    public function testDeleteReportToObject() {

        $supNumber = 4;
        $subNumber = 3;
        $reportingMode = 4;

        $supOrSubListToDelete = array("4 3 4");

        $employeeDao = $this->getMockBuilder('EmployeeDao')->getMock();

        $employeeDao->expects($this->once())
                ->method('deleteReportToObject')
                ->with($supNumber, $subNumber, $reportingMode)
                ->will($this->returnValue(true));

        $this->employeeService->setEmployeeDao($employeeDao);

        $result = $this->employeeService->deleteReportToObject($supOrSubListToDelete);
        $this->assertTrue($result);
    }

    /**
     * Test get membership details list for a given employee
     */
    public function testGetEmployeeMembershipsWithoutMembershipId() {

        $empNumber = 1;

        $membershipDetailList = TestDataService::loadObjectList('EmployeeMembership', $this->fixture, 'EmployeeMembership');

        $employeeDao = $this->getMockBuilder('EmployeeDao')->getMock();

        $employeeDao->expects($this->once())
                ->method('getEmployeeMemberships')
                ->with($empNumber)
                ->will($this->returnValue($membershipDetailList));

        $this->employeeService->setEmployeeDao($employeeDao);

        $readMembershipDetailList = $this->employeeService->getEmployeeMemberships($empNumber);
        $this->assertTrue($readMembershipDetailList[0] instanceof EmployeeMembership);
        $this->assertTrue($readMembershipDetailList[1] instanceof EmployeeMembership);
    }

    /**
     * Test get membership detail object for a given employee membershipType and membership
     */
    public function testGetEmployeeMemberships() {

        $empNumber = 1;
        $membership = 1;

        $membershipDetailList = TestDataService::loadObjectList('EmployeeMembership', $this->fixture, 'EmployeeMembership');

        $employeeDao = $this->getMockBuilder('EmployeeDao')->getMock();

        $employeeDao->expects($this->once())
                ->method('getEmployeeMemberships')
                ->with($empNumber)
                ->will($this->returnValue($membershipDetailList[0]));

        $this->employeeService->setEmployeeDao($employeeDao);

        $readMembershipDetail = $this->employeeService->getEmployeeMemberships($empNumber, $membership);
        $this->assertTrue($readMembershipDetail instanceof EmployeeMembership);
        
    }

    public function testDeleteMembershipDetails() {

        $membershipIds = array(1, 2);

        $employeeDao = $this->getMockBuilder('EmployeeDao')->getMock();

        $employeeDao->expects($this->once())
                ->method('deleteEmployeeMemberships')
                ->with($membershipIds)
                ->will($this->returnValue(2));

        $this->employeeService->setEmployeeDao($employeeDao);
        $result = $this->employeeService->deleteEmployeeMemberships($membershipIds);
        
        $this->assertEquals(2, $result);
        
    }

    /**
     * Test get emergency contacts for a given employee
     */
    public function testGetEmergencyContactsusingFixture() {

        $empNumber = 1;

        $emergencyContactList = TestDataService::loadObjectList('EmpEmergencyContact', $this->fixture, 'EmpEmergencyContact');

        $employeeDao = $this->getMockBuilder('EmployeeDao')->getMock();

        $employeeDao->expects($this->once())
                ->method('getEmployeeEmergencyContacts')
                ->with($empNumber)
                ->will($this->returnValue($emergencyContactList));

        $this->employeeService->setEmployeeDao($employeeDao);
        $readEmergencyContactlList = $this->employeeService->getEmployeeEmergencyContacts($empNumber);
        $this->assertTrue($readEmergencyContactlList[0] instanceof EmpEmergencyContact);
        $this->assertTrue($readEmergencyContactlList[1] instanceof EmpEmergencyContact);
    }
    
    /**
     * Test getEmployeeYearsOfService 
     */
    public function testGetEmployeeYearsOfService() {

        $empNumber = 12;
        $joinedDate = '2001-02-01';
        $currentDate = '2010-03-04';
        $employee = new Employee();
        $employee->setLastName('Last Name');
        $employee->setFirstName('First Name');
        $employee->setEmpNumber($empNumber);
        $employee->setJoinedDate($joinedDate);
        
        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
             ->method('getEmployee')
             ->with($empNumber)
             ->will($this->returnValue($employee));
        
        $this->employeeService->setEmployeeDao($mockDao);
        
        $yearsOfService = $this->employeeService->getEmployeeYearsOfService($empNumber, $currentDate);
        
        //$this->assertEquals(9, $yearsOfService);
        
        // Test with non-existant employee
        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
             ->method('getEmployee')
             ->with($empNumber)
             ->will($this->returnValue(null));
        
        $this->employeeService->setEmployeeDao($mockDao);
        
        try {
            $yearsOfService = $this->employeeService->getEmployeeYearsOfService($empNumber, $currentDate);        
            $this->fail("PIMServiceException expected");
        } catch (PIMServiceException $e) {
            // Expected
        }
        
    }
    
    public function testGetEmailList() {
        
        $list = array(0 => array( 'empNo' => 1, 'workEmail' => 'kayla@xample.com', 'othEmail' => 'kayla2@xample.com' ));
        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
             ->method('getEmailList')
             ->will($this->returnValue($list));
        
        $this->employeeService->setEmployeeDao($mockDao);
        $result = $this->employeeService->getEmailList();
        $this->assertEquals($list, $result);
    }
    
    public function testGetEmployeesBySubUnit() {
        
        $employees = TestDataService::loadObjectList('Employee', $this->fixture, 'Employee');
        $subUnit = 2;
        $includeTerminatedEmployees = true;

        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
             ->method('getEmployeesBySubUnit')
             ->with($subUnit, $includeTerminatedEmployees)
             ->will($this->returnValue($employees));
        
        $this->employeeService->setEmployeeDao($mockDao);
        $result = $this->employeeService->getEmployeesBySubUnit($subUnit, $includeTerminatedEmployees);
        $this->assertEquals($employees, $result);
    }    
    
    public function testSearchEmployeeList(){
        
        $parameterHolder = new EmployeeSearchParameterHolder();
        
        $employee1 = new Employee();
        $employee1->setLastName('Last Name');
        $employee1->setFirstName('First Name');
        
        $employee2 = new Employee();
        $employee2->setLastName('Last Name');
        $employee2->setFirstName('First Name');
                
        $list   =   array( $employee1,$employee2);
        
        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
             ->method('searchEmployees')
             ->with($parameterHolder)   
             ->will($this->returnValue($list));
        
        $this->employeeService->setEmployeeDao($mockDao);
        $result = $this->employeeService->searchEmployees($parameterHolder);
        $this->assertEquals($list, $result);
        
    }
    
    public function testGetEmployeeIdList(){
        $employeeIdList = array(1, 2, 3);
        
        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
             ->method('getEmployeeIdList')
             ->will($this->returnValue($employeeIdList));
        
        $this->employeeService->setEmployeeDao($mockDao);
        $result = $this->employeeService->getEmployeeIdList(true);
        $this->compareArrays($employeeIdList, $result);
        
    }
    
    public function testGetEmployeePropertyList(){
        $properties = array('empNumber', 'firstName', 'lastName', 'middleName', 'employeeId' );
        $emplyees = TestDataService::loadObjectList('Employee', $this->fixture, 'Employee');
        $employeePropertyArray = array();
        foreach ($emplyees as $employee) {
            $employeePropertyArray[$employee['empNumber']] = array('empNumber' => $employee['empNumber'], 'firstName' => $employee['firstName'], 
            	'lastName' => $employee['lastName'], 'middleName' => $employee['middleName'], 'employeeId' => $employee['employeeId'] );
        }
        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
             ->method('getEmployeePropertyList')
             ->with($properties, 'empNumber', 'ASC')
             ->will($this->returnValue($employeePropertyArray));
        
        $this->employeeService->setEmployeeDao($mockDao);
        $result = $this->employeeService->getEmployeePropertyList($properties, 'empNumber', 'ASC', true);
        $this->compareArrays($employeePropertyArray, $result);
        
    }
    
    public function testGetSubordinateIdListBySupervisorId(){
        $subordinateIdList = array(1, 2, 3);
        
        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
             ->method('getSubordinateIdListBySupervisorId')
             ->with(1, true)
             ->will($this->returnValue($subordinateIdList));
        
        $this->employeeService->setEmployeeDao($mockDao);
        $result = $this->employeeService->getSubordinateIdListBySupervisorId(1, true);
        $this->compareArrays($subordinateIdList, $result);
        
    }
    
    public function testGetSupervisorIdListBySubordinateId(){
        $subordinateIdList = array(4, 5);
        
        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
             ->method('getSupervisorIdListBySubordinateId')
             ->with(3, true)
             ->will($this->returnValue($subordinateIdList));
        
        $this->employeeService->setEmployeeDao($mockDao);
        $result = $this->employeeService->getSupervisorIdListBySubordinateId(3, true);
        $this->compareArrays($subordinateIdList, $result);
        
    }
    
    public function testGetSubordinatePropertyListBySupervisorId(){
        $properties = array('empNumber', 'firstName', 'lastName', 'middleName', 'employeeId' );
        $emplyee1 = TestDataService::fetchObject('Employee', 1);
        $emplyee2 = TestDataService::fetchObject('Employee', 2);
        $employees = array($emplyee1, $emplyee2);
        $subordinatePropertyArray = array();
        foreach ($employees as $employee) {
            $subordinatePropertyArray[$employee['empNumber']] = array('empNumber' => $employee['empNumber'], 'firstName' => $employee['firstName'], 
            	'lastName' => $employee['lastName'], 'middleName' => $employee['middleName'], 'employeeId' => $employee['employeeId'] );
        }
        
        $configServiceMock = $this->getMockBuilder('ConfigService')->getMock();
        $configServiceMock->expects($this->once())
             ->method('isSupervisorChainSuported')
             ->will($this->returnValue(true));
             
        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
             ->method('getSubordinatePropertyListBySupervisorId')
             ->with(3, $properties, true, 'empNumber', 'ASC')
             ->will($this->returnValue($subordinatePropertyArray));
        
        $this->employeeService->setEmployeeDao($mockDao);
        $this->employeeService->setConfigurationService($configServiceMock);
        $result = $this->employeeService->getSubordinatePropertyListBySupervisorId(3, $properties, 'empNumber', 'ASC', true);
        $this->compareArrays($subordinatePropertyArray, $result);
        
    }
    
    protected function compareArrays($expected, $actual) {
        $this->assertEquals(count($expected), count($actual));
        
        $diff = array_diff($expected, $actual);
        $this->assertEquals(0, count($diff), $diff);       
    } 
    
    
    public function testGetDurationInYearsForOneYear(){
        $startDate = '2012-01-01';
        $endDate = '2013-01-01';
        $expected = 1;
        
        $result = $this->employeeService->getDurationInYears($startDate, $endDate);
        $this->assertEquals($expected, $result);
        
    }
    
    public function testGetDurationInYearsForDecimalValues1(){
        $startDate = '2012-01-01';
        $endDate = '2013-06-30';
        $expected = 1.5;
        
        $result = $this->employeeService->getDurationInYears($startDate, $endDate);
        $this->assertEquals($expected, $result);
    }
    
   public function testGetDurationInYearsForDecimalValues2() {

       $fromDate = '2012-05-01';
       $toDate = '2013-08-01';
       $expected = 1.25;
       
       $timeStampDiff = $this->employeeService->getDurationInYears($fromDate, $toDate);
       $this->assertEquals($expected, $timeStampDiff);
   }
   
   public function testGetDurationInYearsForDecimalValues3() {

        $fromDate = '2012-02-01';
        $toDate = '2013-11-01';
        $expected = 1.75;

        $timeStampDiff = $this->employeeService->getDurationInYears($fromDate, $toDate);
        $this->assertEquals($expected, $timeStampDiff);
    }
   
   public function ttestGetDurationInYearsForDecimalValues4(){
        $startDate = '2012-01-01';
        $endDate = '2013-06-01';
        $expected = 1.4166666666667;
        
        $result = $this->employeeService->getDurationInYears($startDate, $endDate);
        $this->assertEquals($expected, $result);
        
    }
   
   public function testGetDurationInYearsForLeapYears(){
        $startDate = '2011-02-28';
        $endDate = '2012-02-29';
        $expected = 1;
        
        $result = $this->employeeService->getDurationInYears($startDate, $endDate);
        $this->assertEquals($expected, $result);
    }
    
    public function testGetDurationInYearsFornonLeapYears(){
        $startDate = '2010-02-28';
        $endDate = '2011-02-28';
        $expected = 1;
        
        $result = $this->employeeService->getDurationInYears($startDate, $endDate);
        $this->assertEquals($expected, $result);
    }
    
    public function testGetDurationInYearsForTwoYears(){
        $startDate = '2012-06-01';
        $endDate = '2014-05-31';
        $expected = 2;
  
        $result = $this->employeeService->getDurationInYears($startDate, $endDate);
        $this->assertEquals($expected, $result);
    }

    /**
     *@todo This test shows wrong behavior of the method 
     */
    public function testGetDurationInYearsForLeapYearBorder1(){
        $startDate = '2012-03-01';
        $endDate = '2013-02-28';
        $expected = 0;
        
        $result = $this->employeeService->getDurationInYears($startDate, $endDate);
        $this->assertEquals($expected, $result);
    }
    
    public function testGetDurationInYearsForLeapYearBorder2(){
        $startDate = '2012-02-29';
        $endDate = '2013-02-27';
        $expected = 0;
        
        $result = $this->employeeService->getDurationInYears($startDate, $endDate);
        $this->assertEquals($expected, $result);
    }
    
    public function testGetDurationInYearsForLeapYearBorder3() {
        $fromDate = '2012-07-05';
        $toDate = '2015-06-30';
        $expected = 2;
        
        $years = $this->employeeService->getDurationInYears($fromDate, $toDate);
        $this->assertEquals($expected, $years);
    }
    
}
