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
require_once sfConfig::get('sf_test_dir') . '/util/TestDataService.php';

/**
 * @group Pim
 */
class EmployeeDaoTest extends PHPUnit_Framework_TestCase {

    private $testCase;
    private $employeeDao;
    protected $fixture;

    /**
     * Set up method
     */
    protected function setUp() {
        $this->employeeDao = new EmployeeDao();
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmPimPlugin/test/fixtures/EmployeeDao.yml';
        TestDataService::populate($this->fixture);
    }

    /**
     * Testing getEmployeeListAsJson
     */
    public function testGetEmployeeListAsJson() {
        $result = $this->employeeDao->getEmployeeListAsJson();
        $this->assertTrue(!empty($result));
    }

    /**
     * Test saving EmployeePassport without sequence number
     */
    public function testSaveEmployeePassport1() {

        $empPassport = new EmployeeImmigrationRecord();
        $empPassport->setEmpNumber(1);
        $empPassport->setCountryCode('LK');
        $result = $this->employeeDao->saveEmployeeImmigrationRecord($empPassport);
        $this->assertTrue($result instanceof EmployeeImmigrationRecord);
        $this->assertEquals(1, $empPassport->getRecordId());
    }

    /**
     * Test saving EmployeePassport
     */
    public function testSaveEmployeePassport2() {

        $empPassport = TestDataService::fetchLastInsertedRecords('EmployeeImmigrationRecord', 2);
        $empNumbers = array(1 => 1, 2 => 2);

        foreach ($empPassport as $passport) {

            $this->assertTrue($passport instanceof EmployeeImmigrationRecord);
            $this->assertEquals($empNumbers[$passport->getEmpNumber()], $passport->getEmpNumber());
            $comment = "I add more comments";
            $passport->notes = $comment;
            $result = $this->employeeDao->saveEmployeeImmigrationRecord($passport);
            $this->assertTrue($result instanceof EmployeeImmigrationRecord);

            $savedPassport = $this->employeeDao->getEmployeeImmigrationRecords($passport->getEmpNumber(), $passport->getRecordId());
            $this->assertEquals($comment, $savedPassport->notes);
            $this->assertEquals($savedPassport, $passport);
        }
    }

    /**
     * Test saving getEmployeePassport returns object
     */
    public function testGetEmployeePassport1() {

        $empPassports = TestDataService::fetchLastInsertedRecords('EmployeeImmigrationRecord', 2);

        foreach ($empPassports as $passport) {

            $empPassport = $this->employeeDao->getEmployeeImmigrationRecords($passport->getEmpNumber(), $passport->getRecordId());
            $this->assertEquals($passport, $empPassport);
        }
    }

    /**
     * Test saving getEmployeePassport returns Collection
     */
    public function testGetEmployeePassport2() {

        $empPassports = TestDataService::fetchLastInsertedRecords('EmployeeImmigrationRecord', 2);

        foreach ($empPassports as $passport) {

            $collection = $this->employeeDao->getEmployeeImmigrationRecords($passport->getEmpNumber());
            $this->assertTrue($collection instanceof Doctrine_Collection);
        }
    }

    /**
     * Test for getEmployeeTaxExemptions returns Object
     */
    public function testGetEmployeeTaxExemptions() {
        $empTaxExemption = TestDataService::fetchObject('EmpUsTaxExemption', 1);
        $taxObject = $this->employeeDao->getEmployeeTaxExemptions($empTaxExemption->getEmpNumber());
        $this->assertTrue($taxObject instanceof EmpUsTaxExemption);
    }

    /**
     * Test saving Employee Tax Exemptions
     */
    public function testSaveEmployeeTaxExemptions() {

        $empUsTaxExemption = new EmpUsTaxExemption();
        $empUsTaxExemption->setEmpNumber(2);
        $empUsTaxExemption->stateExemptions = 4;
        
        $result = $this->employeeDao->saveEmployeeTaxExemptions($empUsTaxExemption);
        
        $this->assertTrue($result === $empUsTaxExemption);
        $this->assertEquals(2, $result->getEmpNumber());
        $this->assertEquals(4, $result->stateExemptions);
        
    }

    /**
     * Test for getMembershipDetail returns List with one EmployeeMembership object
     */
    public function testGetEmployeeMemberships1() {

        $empNumber = 1;
        $membership = 1;

        $memberDetail = $this->employeeDao->getEmployeeMemberships($empNumber, $membership);
        $this->assertTrue($memberDetail[0] instanceof EmployeeMembership);
        $this->assertEquals('2011-05-20', $memberDetail[0]->getSubscriptionCommenceDate());
        
        $this->assertEquals($empNumber, $memberDetail[0]->getEmpNumber());
        $this->assertEquals($membership, $memberDetail[0]->getMembershipId());
        
        $empNumber = 1;
        $membership = 4;

        $memberDetail = $this->employeeDao->getEmployeeMemberships($empNumber, $membership);        
        $this->assertEquals(0, $memberDetail->count());
   
    }

    /**
     * Test for getMembershipDetail returns List with two EmployeeMembership objects
     */
    public function testGetEmployeeMemberships2() {

        $empNumber = 1;
        $id = 1;

        $memberships = $this->employeeDao->getEmployeeMemberships($empNumber, $id);
        
        /* Checking the type */
        foreach ($memberships as $membership) {
            $this->assertTrue($membership instanceof EmployeeMembership);
        }
        
        /* Checking the count */
        $this->assertEquals(1, count($memberships));
        
        /* Checking the order */
        $this->assertEquals('membership1', $memberships[0]->getMembership()->getName());
        
    }    

    public function testDeleteMembershipDetails1() {

        $empNumber = 1;
        $membershipIds = array(1, 2);
        $result = $this->employeeDao->deleteEmployeeMemberships($empNumber, $membershipIds);
        
        $this->assertEquals(2, $result);
        
    }
    
    public function testDeleteMembershipDetails2() {

        $membershipIds = array(1);
        $result = $this->employeeDao->deleteEmployeeMemberships($membershipIds);
        
        $this->assertEquals(1, $result);
        
    }
    
    public function testDeleteMembershipDetails3() {

        $empNumber = 1;
        $result = $this->employeeDao->deleteEmployeeMemberships($empNumber);
        
        $this->assertEquals(2, $result);
        
    }

    /**
     *  Test for deleting membership record from Table
     */
    public function testDeleteMemberships() {

        $membershipsToDelete = array(1, 2);

        $result = $this->employeeDao->deleteEmployeeMemberships($membershipsToDelete);
        $this->assertEquals(2, $result);

    }

    /**
     * Test for getSupervisorListForEmployee returns ReportTo doctrine collection
     */
    public function testGetSupervisorListForEmployee() {

        $supervisorReportToList = $this->employeeDao->getImmediateSupervisors(3);
        $this->assertEquals(2, count($supervisorReportToList));
        $this->assertTrue($supervisorReportToList[0] instanceof ReportTo);
        $this->assertEquals($supervisorReportToList[0]->supervisorId, 5);
        $this->assertTrue($supervisorReportToList[0]->getSupervisor() instanceof Employee);
        $this->assertTrue($supervisorReportToList[1] instanceof ReportTo);
        $this->assertEquals($supervisorReportToList[1]->supervisorId, 4);
        $this->assertTrue($supervisorReportToList[1]->getSupervisor() instanceof Employee);
    }

    /**
     * Test for getSubordinateListForEmployee returns ReportTo doctrine collection
     * @group ReportingChain
     */
    public function testGetSubordinateListForEmployee() {

        $subordinateReportToList = $this->employeeDao->getSubordinateListForEmployee(3);
        $this->assertEquals(2, count($subordinateReportToList));
        $this->assertTrue($subordinateReportToList[0] instanceof ReportTo);
        $this->assertEquals($subordinateReportToList[0]->subordinateId, 1);
        $this->assertEquals($subordinateReportToList[1]->subordinateId, 2);
        $this->assertTrue($subordinateReportToList[0]->getSubordinate() instanceof Employee);
    }

    /**
     * Test for getReportToObject returns ReportTo doctrine Object
     */
    public function testGetReportToObject() {

        $subordinateReportTo = $this->employeeDao->getReportToObject(4, 3, 4);
        $this->assertTrue($subordinateReportTo instanceof ReportTo);
    }

    /**
     * Test for deleteReportToObject returns boolean
     */
    public function testDeleteReportToObject() {

        $this->assertTrue($this->employeeDao->deleteReportToObject(3, 1, 3));
    }

    /**
     * Test for get emergency contact returns EmpEmergencyContact doctrine 
     */
    public function testGetEmergencyContacts() {

        $empNumber = 1;

        $emergencyContact = $this->employeeDao->getEmployeeEmergencyContacts($empNumber);
        $this->assertTrue($emergencyContact[0] instanceof EmpEmergencyContact);
    }

    public function testDeleteEmergencyContacts() {

        $empNumber = 1;
        $emergencyContactsToDelete = array(1, 2);

        $result = $this->employeeDao->deleteEmployeeEmergencyContacts($empNumber, $emergencyContactsToDelete);
        $this->assertEquals(1, $result);
        
    }
    
    public function testDeleteNonExistingEmergencyContacts() {

        $empNumber = 1;
        $emergencyContactsToDelete = array(100, 115);

        $result = $this->employeeDao->deleteEmployeeEmergencyContacts($empNumber, $emergencyContactsToDelete);
        $this->assertEquals(0, $result);
        
    }    

    /**
     * Test for get work expierence returns EmpWorkExperience doctrine collection
     */
    public function testGetWorkExperienceWithNullSeqNumber() {

        $empNumber = 1;

        $wrkExp = $this->employeeDao->getEmployeeWorkExperienceRecords($empNumber);
        $this->assertTrue($wrkExp[0] instanceof EmpWorkExperience);
    }

    /**
     * Test for get work expierence returns EmpWorkExperience doctrine object
     */
    public function testGetWorkExperienceWithSeqNumber() {

        $empNumber = 1;
        $sequenceNo = 2;

        $wrkExp = $this->employeeDao->getEmployeeWorkExperienceRecords($empNumber, $sequenceNo);
        $this->assertTrue($wrkExp instanceof EmpWorkExperience);
    }

    /**
     * Test for save work expierence returns boolean
     */
    public function testSaveWorkExperienceWithSeqNum() {

        $empWorkExp = new EmpWorkExperience();

        $empWorkExp->emp_number = 1;
        $empWorkExp->seqno = 3;
        $empWorkExp->jobtitle = "SE";
        $empWorkExp->employer = "OrangeHRM";
        
        $result = $this->employeeDao->saveEmployeeWorkExperience($empWorkExp);

        $this->assertTrue($result === $empWorkExp);
        
    }

    /**
     * Test for save work expierence returns boolean
     */
    public function testSaveWorkExperienceWithoutSeqNum() {

        $empWorkExp = new EmpWorkExperience();

        $empWorkExp->emp_number = 1;
        $empWorkExp->jobtitle = "Architect";
        $empWorkExp->employer = "OrangeHRM";

        $result = $this->employeeDao->saveEmployeeWorkExperience($empWorkExp);

        $this->assertTrue($result === $empWorkExp);
        
    }

    /**
     * Test for deleteWrkExpierence returns boolean
     */
    public function testDeleteWorkExperience() {

        $result = $this->employeeDao->deleteEmployeeWorkExperienceRecords(1, array(1, 2));
        $this->assertEquals(2, $result);
        
    }

    public function testGetEducation() {

        $education = $this->employeeDao->getEducation(1);
        $this->assertTrue($this->employeeDao->getEducation(1) instanceof EmployeeEducation);
    }

    public function testGetEmployeeEducationsWithOnlyEmpNumber() {

        $eduList = $this->employeeDao->getEmployeeEducations(1);

        foreach ($eduList as $item) {
            $this->assertTrue($item instanceof EmployeeEducation);
        }

        $this->assertEquals(2, count($eduList));

        /* Checking the order */
        $this->assertEquals('ENG', $eduList[0]->getMajor());
        $this->assertEquals('ENG1', $eduList[1]->getMajor());
    }

    public function testGetEmployeeEducationsWithEmpNumberAndEduId() {

        $eduList = $this->employeeDao->getEmployeeEducations(1, 2);

        $this->assertTrue($eduList[0] instanceof EmployeeEducation);

        $this->assertEquals(1, count($eduList));

        /* Checking values */
        $this->assertEquals('ENG1', $eduList[0]->getMajor());
    }

    /**
     * Test for save education expierence returns boolean
     */
    public function testSaveEducation() {

        $empEdu = new EmployeeEducation();

        $empEdu->empNumber = 2;
        $empEdu->educationId = 2;
        $empEdu->major = 'major';
        
        $result = $this->employeeDao->saveEmployeeEducation($empEdu);

        $this->assertTrue($result === $empEdu);
        
    }

    /**
     * Test for deleteEducation returns boolean
     */
    public function testDeleteEducation() {

        $empNumber = 1;
        $educationToDelete = array(1, 2);

        $result = $this->employeeDao->deleteEmployeeEducationRecords($empNumber, $educationToDelete);
        $this->assertEquals(2, $result);
        
        // verify records deleted
        $q = Doctrine_Query::create()->from('EmployeeEducation ec')->where('emp_number = ?', $empNumber);
        $this->assertEquals(0, $q->count());
        
        $empNumber = 2;
        $result = $this->employeeDao->deleteEmployeeEducationRecords($empNumber, array(3));
        $this->assertEquals(1, $result);
        $q = Doctrine_Query::create()->from('EmployeeEducation ec')->where('emp_number = ?', $empNumber);
        $this->assertEquals(0, $q->count());
    }

    /**
     * Test for get skill returns EmployeeSkill doctrine collection
     */
    public function testGetSkillWithNullSkillCode() {

        $empNumber = 1;

        $skill = $this->employeeDao->getEmployeeSkills($empNumber);
        $this->assertTrue($skill[0] instanceof EmployeeSkill);
    }

    /**
     * Test for get work expierence returns EmployeeSkill doctrine object
     */
    public function testGetSkillWithSkillCode() {

        $empNumber = 1;
        $skillCode = 2;

        $skill = $this->employeeDao->getEmployeeSkills($empNumber, $skillCode);
        $this->assertTrue($skill instanceof EmployeeSkill);
    }

    /**
     * Test for save Skill returns boolean
     */
    public function testSaveSkill() {

        $empSkill = new EmployeeSkill;
        $empSkill->emp_number = 3;
        $empSkill->skillId = 1;
        
        $result = $this->employeeDao->saveEmployeeSkill($empSkill);
        
        $this->assertTrue($result === $empSkill);
        
    }

    /**
     * Test for deleteSkill returns boolean
     */
    public function testDeleteSkill() {

        $empNumber = 1;
        $skillToDelete = array(1, 2);

        $result = $this->employeeDao->deleteEmployeeSkills($empNumber, $skillToDelete);
        $this->assertEquals(2, $result);
    }

    /**
     * Test for get skill returns EmployeeLanguage doctrine collection
     */
    public function testGetLanguageWithNullLangCodeAndLangType() {

        $empNumber = 1;

        $language = $this->employeeDao->getEmployeeLanguages($empNumber);
        $this->assertTrue($language[0] instanceof EmployeeLanguage);
    }

    /**
     * Test for get work expierence returns EmployeeLanguage doctrine collection
     */
    public function testGetLanguageWithNullLangCode() {

        $empNumber = 1;
        $langType = 2;

        $language = $this->employeeDao->getEmployeeLanguages($empNumber, null, $langType);
        $this->assertTrue($language[0] instanceof EmployeeLanguage);
    }

    /**
     * Test for get work expierence returns EmployeeLanguage doctrine collection
     */
    public function testGetLanguageWithNullLangType() {

        $empNumber = 1;
        $langCode = 'LAN001';

        $language = $this->employeeDao->getEmployeeLanguages($empNumber, $langCode);
        $this->assertTrue($language[0] instanceof EmployeeLanguage);
    }

    /**
     * Test for get work expierence returns EmployeeLanguage doctrine object
     */
    public function testGetLanguage() {

        $empNumber = 1;
        $langCode = 1;
        $langType = 2;

        $language = $this->employeeDao->getEmployeeLanguages($empNumber, $langCode, $langType);
        $this->assertTrue($language instanceof EmployeeLanguage);
    }

    public function testSaveLanguage() {

        $empLang = new EmployeeLanguage();

        $empLang->empNumber = 2;
        $empLang->langId = 1;

        $result = $this->employeeDao->saveEmployeeLanguage($empLang);
        
        $this->assertTrue($result === $empLang);
                
    }

    public function testDeleteLanguage1() {

        $empNumber = 1;
        $languagesToDelete = array(array(1 => 2), array(2 => 1));

        $result = $this->employeeDao->deleteEmployeeLanguages($empNumber, $languagesToDelete);
        $this->assertEquals(2, $result);
        
    }
    
    public function testDeleteLanguage2() {

        $empNumber = 1;
        $languagesToDelete = array(array(2 => 1));

        $result = $this->employeeDao->deleteEmployeeLanguages($empNumber, $languagesToDelete);
        $this->assertEquals(1, $result);
        
    }
    
    public function testDeleteLanguage3() {

        $empNumber = 1;

        $result = $this->employeeDao->deleteEmployeeLanguages($empNumber);
        $this->assertEquals(2, $result);
        
    }    
    
    /**
     * Test for get skill returns EmployeeLicense doctrine collection
     */
    public function testGetLicenseWithNullLicenseCode() {

        $empNumber = 1;

        $license = $this->employeeDao->getEmployeeLicences($empNumber);
        $this->assertTrue($license[0] instanceof EmployeeLicense);
    }

    /**
     * Test for get work expierence returns EmployeeLicense doctrine object
     */
    public function testGetLicenseWithLicenseCode() {

        $empNumber = 1;
        $licenseCode = 1;

        $licenseCode = $this->employeeDao->getEmployeeLicences($empNumber, $licenseCode);
        $this->assertTrue($licenseCode instanceof EmployeeLicense);
        //testing with not included 
        $licenseCodeNull = $this->employeeDao->getEmployeeLicences(2, 1);
        $this->assertTrue(!($licenseCodeNull instanceof EmployeeLicense));
    }

    /**
     * Test for save license expierence returns boolean
     */
    public function testSaveLicense() {

        $empLicense = new EmployeeLicense();

        $empLicense->empNumber = 3;
        $empLicense->licenseId = 1;
        
        $result = $this->employeeDao->saveEmployeeLicense($empLicense);

        $this->assertTrue($result === $empLicense);
        
    }

    public function testDeleteLicense1() {

        $empNumber = 1;
        $licenseToDelete = array(1, 2);

        $result = $this->employeeDao->deleteEmployeeLicenses($empNumber, $licenseToDelete);
        $this->assertEquals(2, $result);
        
    }
    
    public function testDeleteLicense2() {

        $empNumber = 1;
        $licenseToDelete = array(1);

        $result = $this->employeeDao->deleteEmployeeLicenses($empNumber, $licenseToDelete);
        $this->assertEquals(1, $result);
        
    }
    
    public function testDeleteLicense3() {

        $empNumber = 1;

        $result = $this->employeeDao->deleteEmployeeLicenses($empNumber);
        $this->assertEquals(2, $result);
        
    } 
    
    public function testDeleteLicense4() {

        $empNumber = 1;
        $licenseToDelete = array(3, 5);

        $result = $this->employeeDao->deleteEmployeeLicenses($empNumber, $licenseToDelete);
        $this->assertEquals(0, $result);
        
    }    

    public function testDeleteLicense5() {

        $empNumber = 5;
        $licenseToDelete = array(1, 2);

        $result = $this->employeeDao->deleteEmployeeLicenses($empNumber, $licenseToDelete);
        $this->assertEquals(0, $result);
        
    }    
    
    public function testDeleteLicense6() {

        $empNumber = 1;
        $licenseToDelete = array(1, 7);

        $result = $this->employeeDao->deleteEmployeeLicenses($empNumber, $licenseToDelete);
        $this->assertEquals(1, $result);
        
    }     
    
    /**
     * Test for get dependents returns EmpDependent doctrine collection
     */
    public function testGetDependents() {

        $empNumber = 1;

        $empDep = $this->employeeDao->getEmployeeDependents($empNumber);
        $this->assertTrue($empDep[0] instanceof EmpDependent);
        $this->assertTrue($empDep[1] instanceof EmpDependent);
    }

    /**
     * Test for delete dependents returns boolean
     */
    public function testDeleteDependentsAllWithRecordIds() {

        $result = $this->employeeDao->deleteEmployeeDependents(1, array(1, 2));
        $this->assertEquals(2, $result);
        
    }
    
    public function testDeleteDependentsWithOneRecordId() {

        $result = $this->employeeDao->deleteEmployeeDependents(1, array(1));
        $this->assertEquals(1, $result);        
        
    } 
    
    public function testDeleteDependentsAllWithoutRecordIds() {

        $result = $this->employeeDao->deleteEmployeeDependents(1);
        $this->assertEquals(2, $result);        
        
    }    

    public function testDeletePhoto() {
        $empNumber = 1;

        $result = $this->employeeDao->deleteEmployeePicture($empNumber);
        $this->assertEquals(1, $result);
    }

    public function xtestSaveEmployeePicture() {

        $mockPicture = $this->getMockBuilder('EmpPicture')
			->setMethods( array('save'))
			->getMock();
        $mockPicture->expects($this->once())
                ->method('save');
        $result = $this->employeeDao->saveEmployeePicture($mockPicture);
        
        $this->assertTrue($result instanceof EmpPicture);        

    }
    
    /**
     * @expectedException DaoException
     */
    public function xtestSaveEmployeePictureException() {

        $mockPicture = $this->getMockBuilder('EmpPicture')
			->setMethods( array('save'))
			->getMock();
        $mockPicture->expects($this->once())
                ->method('save')
                ->will($this->throwException(new DaoException()));
        
        $this->employeeDao->saveEmployeePicture($mockPicture);

    }

    public function testAddEmployee() {
        TestDataService::truncateTables(array('Employee'));

        $employee = new Employee();
        $employee->firstName = 'Tester';
        $employee->lastName = 'Jason';

        $employee = $this->employeeDao->saveEmployee($employee);
    }

    public function testGetEmployee() {
        $empNumber = 1;

        $employee = $this->employeeDao->getEmployee($empNumber);
        $this->assertTrue($employee instanceof Employee);
        $this->assertEquals('Kayla', $employee->getFirstName());
    }

    public function testGetPicture() {
        $empNumber = 1;

        $picture = $this->employeeDao->getEmployeePicture($empNumber);
        $this->assertTrue($picture instanceof EmpPicture);
        $this->assertEquals('test_file.jpg', $picture->getFileName());
    }

    public function testDeleteImmigrationWithRecordIds() {
        
        $result = $this->employeeDao->deleteEmployeeImmigrationRecords(2, array(1));
        $this->assertEquals(1, $result);
        
    }
    
    public function testDeleteImmigrationWithoutRecordIds() {
        
        $retVal = $this->employeeDao->deleteEmployeeImmigrationRecords(2);
        $this->assertEquals(1, $retVal);
        
    }    

    public function testGetAttachments() {
        $empNumber = 1;
        $retVal = $this->employeeDao->getEmployeeAttachments($empNumber, 'personal');
        $this->assertEquals(2, count($retVal));
    }

    public function testGetAttachment() {
        $empNumber = 1;
        $retVal = $this->employeeDao->getEmployeeAttachment($empNumber, 1);
        $this->assertTrue($retVal instanceof EmployeeAttachment);
        $this->assertEquals('test.mdb', $retVal->filename);
    }

    public function testdeleteAttachments1() {
        
        $empNumber = 1;
        $retVal = $this->employeeDao->deleteEmployeeAttachments($empNumber, array(1, 2));
        $this->assertEquals(2, $retVal);

    }
    
    public function testdeleteAttachments2() {
        
        $empNumber = 1;
        $retVal = $this->employeeDao->deleteEmployeeAttachments($empNumber);
        $this->assertEquals(2, $retVal);
        
    }    

    public function testReadEmployeePicture() {
        $empNumber = 1;
        $picture = $this->employeeDao->readEmployeePicture($empNumber);
        $this->assertTrue($picture instanceof EmpPicture);
        $this->assertEquals('test_file.jpg', $picture->getFileName());
    }

    public function testGetEmployeeList() {
        $empList = TestDataService::loadObjectList('Employee', $this->fixture, 'Employee');
        $list = $this->employeeDao->getEmployeeList();
        $this->assertEquals(count($empList), count($list));
        $employee = $list[0];
        $employee->purged_at = date('Y-m-d H:i:s');
        $employee->save();
        $list = $this->employeeDao->getEmployeeList();
        $this->assertEquals(count($empList), count($list) + 1);
        $list = $this->employeeDao->getEmployeeList($orderField = 'lastName', $orderBy = 'ASC', $includeTerminatedEmployees = false, $includePurgeEmployee = true);
        $this->assertEquals(count($empList), count($list));
    }

    public function testGetSupervisorList() {
        $supervisorCount = 0;

        $repToList = TestDataService::loadObjectList('ReportTo', $this->fixture, 'ReportTo');
        $supervisors = array();

        foreach ($repToList as $repTo) {
            $supervisors[] = $repTo->supervisorId;
        }

        $supervisors = array_unique($supervisors);

        $list = $this->employeeDao->getSupervisorList();

        $this->assertEquals(count($supervisors), count($list));
    }
    
    public function testGetSupervisorListCount() {

        $list = $this->employeeDao->getSupervisorList();

        $this->assertEquals(3, count($list));
        
    }    
    
    public function testGetSupervisorListType() {

        $list = $this->employeeDao->getSupervisorList();

        foreach ($list as $item) {
            $this->assertTrue($item instanceof Employee);
        }
       
    }  
    
    public function testGetSupervisorListOrder() {

        $list = $this->employeeDao->getSupervisorList();

        $this->assertEquals('Nadarajah', $list[0]->getLastName());
        $this->assertEquals('Saputhanthri', $list[2]->getLastName());
        
    }      

    public function testIsSupervisor() {
        $supervisorCount = 0;

        $repToList = TestDataService::loadObjectList('ReportTo', $this->fixture, 'ReportTo');
        $supervisors = array();

        foreach ($repToList as $repTo) {
            $supervisors[] = $repTo->supervisorId;
        }

        $supervisors = array_unique($supervisors);
        $supervisorId = array_pop($supervisors);

        $retVal = $this->employeeDao->isSupervisor($supervisorId);

        $this->assertTrue($retVal);
    }

    public function testSearchEmployee() {
        $result = $this->employeeDao->searchEmployee('firstName', 'Kayla');
        $this->assertEquals(1, count($result));

        $result = $this->employeeDao->searchEmployee('firstName', 'Not Valid');
        $this->assertEquals(0, count($result));

        try {
            $result = $this->employeeDao->searchEmployee('firstNameX', 'Kayla');
            $this->fail("Exception expected");
        } catch (Exception $e) {
            
        }
    }

    public function testGetEmployeeCount() {
        $empList = TestDataService::loadObjectList('Employee', $this->fixture, 'Employee');
        $result = $this->employeeDao->getEmployeeCount();
        $this->assertEquals(count($empList), $result);
    }

    /**
     * @group ReportingChain 
     */
    public function testGetSubordinateList() {
        $repToList = TestDataService::loadObjectList('ReportTo', $this->fixture, 'ReportTo');
        foreach ($repToList as $repTo) {
            $supervisors[] = $repTo->supervisorId;
        }

        $supervisorId = array_pop($supervisors);

        $chain = $this->employeeDao->getSubordinateList($supervisorId, false, true);
        $this->assertTrue(count($chain) > 0);
    }

    /**
     * @group ReportingChain 
     */
    public function testGetSubordinateList_ReportingChain_Simple2LevelHierarchy() {
        $chain = $this->employeeDao->getSubordinateList(3, false, true);
        $this->assertTrue(is_array($chain));
        $this->assertEquals(2, count($chain));

        list($subordinate1, $subordinate2) = $chain;

        $this->assertTrue($subordinate1 instanceof Employee);
        $this->assertEquals(1, $subordinate1->getEmpNumber());

        $this->assertTrue($subordinate2 instanceof Employee);
        $this->assertEquals(2, $subordinate2->getEmpNumber());
    }

    /**
     * @group ReportingChain 
     */
    public function testGetSubordinateList_ReportingChain_3LevelHierarchy() {
        $chain = $this->employeeDao->getSubordinateList(5, false, true);
        $this->assertTrue(is_array($chain));
        $this->assertEquals(3, count($chain));

        list($subordinate1, $subordinate2, $subordinate3) = $chain;

        $this->assertTrue($subordinate1 instanceof Employee);
        $this->assertEquals(3, $subordinate1->getEmpNumber());

        $this->assertTrue($subordinate2 instanceof Employee);
        $this->assertEquals(1, $subordinate2->getEmpNumber());

        $this->assertTrue($subordinate3 instanceof Employee);
        $this->assertEquals(2, $subordinate3->getEmpNumber());

        $chain = $this->employeeDao->getSubordinateList(4, false, true);
        $this->assertTrue(is_array($chain));
        $this->assertEquals(3, count($chain));

        list($subordinate1, $subordinate2, $subordinate3) = $chain;

        $this->assertTrue($subordinate1 instanceof Employee);
        $this->assertEquals(3, $subordinate1->getEmpNumber());

        $this->assertTrue($subordinate2 instanceof Employee);
        $this->assertEquals(1, $subordinate2->getEmpNumber());

        $this->assertTrue($subordinate3 instanceof Employee);
        $this->assertEquals(2, $subordinate3->getEmpNumber());
    }

    public function testDeleteEmployees() {
        
        $employees = TestDataService::loadObjectList('Employee', $this->fixture, 'Employee');
        foreach ($employees as $emp) {
            $empNumbers[] = $emp->getEmpNumber();
        }

        $retVal = $this->employeeDao->deleteEmployees($empNumbers);
        $this->assertEquals(count($empNumbers), $retVal);

        $retVal = $this->employeeDao->deleteEmployees($empNumbers);
        $this->assertEquals(0, $retVal);
        
    }
    
    /**
     * @expectedException DaoException
     */
    public function testDeleteEmployeeException() {
        
        $this->employeeDao->deleteEmployees(array());
        
    }

    public function testIsEmployeeIdInUse() {
        $employees = TestDataService::loadObjectList('Employee', $this->fixture, 'Employee');

        foreach ($employees as $emp) {
            $empId = $emp->getEmployeeId();
            $this->assertTrue($this->employeeDao->isExistingEmployeeId($empId));
        }

        $this->assertFalse($this->employeeDao->isExistingEmployeeId('sdfsd'));
    }

    public function testCheckForEmployeeWithSameName() {
        $employees = TestDataService::loadObjectList('Employee', $this->fixture, 'Employee');

        foreach ($employees as $emp) {
            $empId = $emp->getEmployeeId();
            $this->assertTrue($this->employeeDao->checkForEmployeeWithSameName($emp->getFirstName(), $emp->getMiddleName(), $emp->getLastName()));
        }
        $this->assertFalse($this->employeeDao->checkForEmployeeWithSameName('sdfsd', 'sadf', 'sf'));
    }

    public function testGetWorkShift() {
        $empNumber = 1;
        $workShift = $this->employeeDao->getEmployeeWorkShift($empNumber);
        $this->assertEquals(1, $workShift->getWorkShiftId());
    }

    public function testSaveEmployeeSalary() {
        
        $salary = new EmployeeSalary();
        $salary->setEmpNumber(1);
        $salary->setCurrencyCode('LKR');
        $salary->setAmount(2121212);
        
        $result = $this->employeeDao->saveEmployeeSalary($salary);

        $this->assertTrue($result === $salary);

    }

    /**
     * @expectedException DaoException
     */
    public function testSaveEmployeeSalaryException() {
        
        $salary = $this->getMockBuilder('EmployeeSalary')->setMethods(array('save'))->getMock();
        $salary->expects($this->once())
                ->method('save')
                ->will($this->throwException(new Exception()));

        $result = $this->employeeDao->saveEmployeeSalary($salary);

    }
    
    public function testDeleteEmployeeSalaries1() {
        
        $empNumber = 1;
        $entriesToDelete = array(1, 2);

        $result = $this->employeeDao->deleteEmployeeSalaryComponents($empNumber, $entriesToDelete);
        $this->assertEquals(2, $result);
        
    }
    
    public function testDeleteEmployeeSalaries2() {
        
        $empNumber = 1;
        $entriesToDelete = array(1);

        $result = $this->employeeDao->deleteEmployeeSalaryComponents($empNumber, $entriesToDelete);
        $this->assertEquals(1, $result);
        
    }
    
    public function testDeleteEmployeeSalaries3() {
        
        $empNumber = 1;

        $result = $this->employeeDao->deleteEmployeeSalaryComponents($empNumber);
        $this->assertEquals(2, $result);
        
    }    

    public function testGetSalary() {

        // Get all salaries
        $empNumber = 1;
        $result = $this->employeeDao->getEmployeeSalaries($empNumber);
        $this->assertEquals(2, count($result));
        $this->assertEquals(2, $result[0]->id); // Allowance
        $this->assertEquals('Allowance', $result[0]->getSalaryName());
        $this->assertEquals(1, $result[1]->id); // Main Salary
        $this->assertEquals('Main Salary', $result[1]->getSalaryName());

        // Get one salary
        $result = $this->employeeDao->getEmployeeSalaries($empNumber, 1);
        $result = $this->employeeDao->getEmployeeSalaries($empNumber, 1);
        $this->assertTrue($result instanceof EmployeeSalary);
        $this->assertEquals(1, $result->id);
        $this->assertEquals('Main Salary', $result->getSalaryName());

        //        
        // None existing salary
        $result = $this->employeeDao->getEmployeeSalaries($empNumber, 12);
        $this->assertFalse($result);

        // employee with no salaries
        $result = $this->employeeDao->getEmployeeSalaries(3);
        $this->assertEquals(0, count($result));
    }

    public function testGetUnassignedCurrencyList() {
        $empNumber = 1;

        $unassignedCurrencies = $this->employeeDao->getUnAssignedCurrencyList($empNumber, $salaryGrade);
        $this->assertFalse(array_search('LKR', $unassignedCurrencies->toArray()));
    }

    public function testGetEmailList() {

        $result = $this->employeeDao->getEmailList();
        $list = array();
        foreach ($result as $k => $email) {
            $list[] = array('empNo' => $email['empNumber'], 'workEmail' => $email['emp_work_email'], 'othEmail' => $email['emp_oth_email']);
        }
        $this->assertEquals('kayla@xample.com', $list[0]['workEmail']);
    }

    public function testGetAssignedCurrencyList() {

        $salaryGrade = 1;
        $assignedCurrencies = $this->employeeDao->getAssignedCurrencyList($salaryGrade, true);

        $this->assertTrue(in_array('USD', $assignedCurrencies[0]));
        $this->assertTrue(in_array('LKR', $assignedCurrencies[1]));
    }

    public function testGetEmployeeByEmployeeIdWithCorrectId() {

        $employee = $this->employeeDao->getEmployeeByEmployeeId('E001');
        $this->assertTrue($employee instanceof Employee);
        $this->assertEquals('Kayla', $employee->getFirstName());

        $employee = $this->employeeDao->getEmployeeByEmployeeId('e001');
        $this->assertTrue($employee instanceof Employee);
        $this->assertEquals('Kayla', $employee->getFirstName());

        $employee = $this->employeeDao->getEmployeeByEmployeeId('E001');
        $this->assertTrue($employee instanceof Employee);
        $this->assertEquals('Kayla', $employee->getFirstName());
    }

    public function testGetEmployeeByEmployeeIdWithWrongId() {

        $employee = $this->employeeDao->getEmployeeByEmployeeId('abcd');
        $this->assertNull($employee);

        $employee = $this->employeeDao->getEmployeeByEmployeeId('');
        $this->assertNull($employee);
    }
    
    public function testGetEmployeesBySubUnit() {
        
        // subunit with no employees        
        $employees = $this->employeeDao->getEmployeesBySubUnit('6');
        $this->assertEquals(0, count($employees));

        $employees = $this->employeeDao->getEmployeesBySubUnit(array('6'));
        $this->assertEquals(0, count($employees));
        
        // subunit with 1 employee
        $employees = $this->employeeDao->getEmployeesBySubUnit('2');
        $this->assertEquals(1, count($employees));
        $this->compareArrays(array(1), $this->getEmployeeIds($employees));
        
        $employees = $this->employeeDao->getEmployeesBySubUnit(array('2'));
        $this->assertEquals(1, count($employees));
        $this->compareArrays(array(1), $this->getEmployeeIds($employees));
        
        // subunit with 2 employees
        $employees = $this->employeeDao->getEmployeesBySubUnit('4');
        $this->assertEquals(2, count($employees));
        $this->compareArrays(array(2, 4), $this->getEmployeeIds($employees));
        
        $employees = $this->employeeDao->getEmployeesBySubUnit(array('4'));
        $this->assertEquals(2, count($employees));
        $this->compareArrays(array(2, 4), $this->getEmployeeIds($employees));
        
        // subunit with no employees + subunit with no employees
        $employees = $this->employeeDao->getEmployeesBySubUnit(array('6', '5'));
        $this->assertEquals(0, count($employees));
        
        // subunit with no employees + subunit with 2 employees
        $employees = $this->employeeDao->getEmployeesBySubUnit(array('4', '5'));
        $this->assertEquals(2, count($employees));
        $this->compareArrays(array(2, 4), $this->getEmployeeIds($employees));
        
        // subunit with 1 employee + subunit with 2 employees
        $employees = $this->employeeDao->getEmployeesBySubUnit(array('4', '2'));
        $this->assertEquals(3, count($employees));        
        $this->compareArrays(array(1, 2, 4), $this->getEmployeeIds($employees));        
    }
    
    protected function getEmployeeIds($employees) {
        $ids = array();
        
        foreach ($employees as $employee) {
            $ids[] = $employee->getEmpNumber();
        }
        
        return $ids;
    }
    
    protected function compareArrays($expected, $actual) {
        $this->assertEquals(count($expected), count($actual));
        
        $diff = array_diff($expected, $actual);
        $this->assertEquals(0, count($diff), $diff);       
    }
    
    public function testGetEmployeeIdList() {
        
        $employeeIdList = $this->employeeDao->getEmployeeIdList();
        $this->assertEquals(5, count($employeeIdList));

        $employeeIdList = $this->employeeDao->getEmployeeIdList();
        $employees = TestDataService::loadObjectList('Employee', $this->fixture, 'Employee');
        $employeeIdArray = $this->getEmployeeIds($employees);
        $this->compareArrays($employeeIdArray, $employeeIdList);
    }
    
    public function testGetEmployeeIdListOneEmployee() {
        
        Doctrine_Query::create()->delete()->from('Employee e')->where('e.emp_number > 1')->execute();
        
        $employeeIdList = $this->employeeDao->getEmployeeIdList();
        $this->assertTrue(is_array($employeeIdList));
        $this->assertEquals(1, count($employeeIdList));
        $this->assertEquals(1, $employeeIdList[0]);
    }
    
    public function testGetEmployeePropertyList() {
        $properties = array();
        $employeePropertyList = $this->employeeDao->getEmployeePropertyList($properties, 'empNumber', 'ASC');
        $this->assertEquals(5, count($employeePropertyList));
        
        $properties = array('empNumber', 'firstName', 'lastName', 'middleName', 'employeeId' );
        $employeePropertyList = $this->employeeDao->getEmployeePropertyList($properties, 'empNumber', 'ASC');
        $this->assertEquals(5, count($employeePropertyList));
        
        $properties = array('empNumber', 'firstName', 'lastName', 'middleName', 'employeeId' );
        $employeePropertyList = $this->employeeDao->getEmployeePropertyList($properties, 'empNumber', 'ASC');
        foreach ($employeePropertyList as $employeeProperties) {
            $this->assertEquals(5, count($employeeProperties));
        }

        $properties = array('empNumber', 'firstName', 'lastName', 'middleName', 'employeeId' );
        $employeePropertyList = $this->employeeDao->getEmployeePropertyList($properties, 'empNumber', 'ASC');
        $employee = TestDataService::fetchObject('Employee', 1);
        $this->assertEquals($employee->getFirstName(), $employeePropertyList[0]['firstName']);
        
        $propertyArray = array();
        $propertyArray['empNumber'] = $employee->getEmpNumber();
        $propertyArray['firstName'] = $employee->getFirstName();
        $propertyArray['lastName'] = $employee->getLastName();
        $propertyArray['middleName'] = $employee->getMiddleName();
        $propertyArray['employeeId'] = $employee->getEmployeeId();
        $this->compareArrays($propertyArray, $employeePropertyList[0]);
    }

    /**
     * @group ReportingChain 
     */
    public function testGetSubordinateIdListBySupervisorId() {

        $subordinateIdList = $this->employeeDao->getSubordinateIdListBySupervisorId(3, true);
        $this->assertEquals(2, count($subordinateIdList));
        
        $subordinateIdList = $this->employeeDao->getSubordinateIdListBySupervisorId(4, true);
        $this->assertEquals(3, count($subordinateIdList));
        
        $subordinateIdList = $this->employeeDao->getSubordinateIdListBySupervisorId(4, false);
        $this->assertEquals(1, count($subordinateIdList));
        
        $subordinateIdList = $this->employeeDao->getSubordinateIdListBySupervisorId(10, true);
        $this->assertEquals(0, count($subordinateIdList));
        
        $subordinateIdList = $this->employeeDao->getSubordinateIdListBySupervisorId(1, true);
        $this->assertEquals(0, count($subordinateIdList));
        
        $subordinateIdList = $this->employeeDao->getSubordinateIdListBySupervisorId(3, true);
        $subordinateIdArray = array(1, 2);
        $this->compareArrays($subordinateIdArray, $subordinateIdList);

    }
    
    public function testGetSupervisorIdListBySubordinateId() {

        $supervisorIdList = $this->employeeDao->getSupervisorIdListBySubordinateId(2, true);
        $this->assertEquals(3, count($supervisorIdList));
        
        $supervisorIdList = $this->employeeDao->getSupervisorIdListBySubordinateId(10, true);
        $this->assertEquals(0, count($supervisorIdList));
        
        $supervisorIdList = $this->employeeDao->getSupervisorIdListBySubordinateId(1, true);
        $this->assertEquals(3, count($supervisorIdList));
        
        $supervisorIdList = $this->employeeDao->getSupervisorIdListBySubordinateId(1, false);
        $this->assertEquals(1, count($supervisorIdList));
        
        $supervisorIdList = $this->employeeDao->getSupervisorIdListBySubordinateId(2, true);
        $supervisorIdArray = array(3, 4, 5);
        $this->compareArrays($supervisorIdArray, $supervisorIdList);

    }

    /**
     * @group ReportingChain 
     */
    public function testGetSubordinatePropertyListBySupervisorId() {
        
        $properties = array();
        $subordinatePropertyList = $this->employeeDao->getSubordinatePropertyListBySupervisorId(3, $properties, true, 'empNumber', 'ASC');
        $this->assertEquals(2, count($subordinatePropertyList));
        
        $properties = array('empNumber', 'firstName', 'lastName', 'middleName', 'employeeId' );
        $subordinatePropertyList = $this->employeeDao->getSubordinatePropertyListBySupervisorId(3, $properties, true, 'empNumber', 'ASC');
        $this->assertEquals(2, count($subordinatePropertyList));
        
        
        $properties = array('empNumber', 'firstName', 'lastName', 'middleName', 'employeeId' );
        $subordinatePropertyList = $this->employeeDao->getSubordinatePropertyListBySupervisorId(10, $properties, true, 'empNumber', 'ASC');
        $this->assertEquals(0,count($subordinatePropertyList));
        
        
        $properties = array('empNumber', 'firstName', 'lastName', 'middleName', 'employeeId' );
        $subordinatePropertyList = $this->employeeDao->getSubordinatePropertyListBySupervisorId(3, $properties, true, 'empNumber', 'ASC');
        foreach ($subordinatePropertyList as $subbordinateProperties) {
            $this->assertEquals(6, count($subbordinateProperties));
        }

        $properties = array('empNumber', 'firstName', 'lastName', 'middleName', 'employeeId' );
        $subordinatePropertyList = $this->employeeDao->getSubordinatePropertyListBySupervisorId(3, $properties, true, 'empNumber', 'ASC');
        $employee = TestDataService::fetchObject('Employee', 1);
        $this->assertEquals($employee->getFirstName(), $subordinatePropertyList[1]['firstName']);
        
        $propertyArray = array();
        $propertyArray['empNumber'] = $employee->getEmpNumber();
        $propertyArray['firstName'] = $employee->getFirstName();
        $propertyArray['lastName'] = $employee->getLastName();
        $propertyArray['middleName'] = $employee->getMiddleName();
        $propertyArray['employeeId'] = $employee->getEmployeeId();
        $propertyArray['ReportTo'] = array();
        
        $this->compareArrays($propertyArray, $subordinatePropertyList[1]);

    }
    
}
