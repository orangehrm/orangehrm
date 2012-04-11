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

        $empPassport = new EmpPassPort();
        $empPassport->setEmpNumber(1);
        $empPassport->country = 'LK';
        $result = $this->employeeDao->saveEmployeePassport($empPassport);
        $this->assertTrue($result);
        $this->assertEquals(1, $empPassport->seqno);
    }

    /**
     * Test saving EmployeePassport
     */
    public function testSaveEmployeePassport2() {

        $empPassport = TestDataService::fetchLastInsertedRecords('EmpPassport', 2);
        $empNumbers = array(1 => 1, 2 => 2);

        foreach ($empPassport as $passport) {

            $this->assertTrue($passport instanceof EmpPassPort);
            $this->assertEquals($empNumbers[$passport->getEmpNumber()], $passport->getEmpNumber());
            $comment = "I add more comments";
            $passport->comments = $comment;
            $result = $this->employeeDao->saveEmployeePassport($passport);
            $this->assertTrue($result);

            $savedPassport = $this->employeeDao->getEmployeePassport($passport->getEmpNumber(), $passport->getSeqno());
            $this->assertEquals($comment, $savedPassport->comments);
            $this->assertEquals($savedPassport, $passport);
        }
    }

    /**
     * Test saving getEmployeePassport returns object
     */
    public function testGetEmployeePassport1() {

        $empPassports = TestDataService::fetchLastInsertedRecords('EmpPassport', 2);

        foreach ($empPassports as $passport) {

            $empPassport = $this->employeeDao->getEmployeePassport($passport->getEmpNumber(), $passport->getSeqno());
            $this->assertEquals($passport, $empPassport);
        }
    }

    /**
     * Test saving getEmployeePassport returns Collection
     */
    public function testGetEmployeePassport2() {

        $empPassports = TestDataService::fetchLastInsertedRecords('EmpPassport', 2);

        foreach ($empPassports as $passport) {

            $collection = $this->employeeDao->getEmployeePassport($passport->getEmpNumber());
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
        $this->assertTrue($result);
        $this->assertEquals(2, $empUsTaxExemption->getEmpNumber());
        $this->assertEquals(4, $empUsTaxExemption->stateExemptions);
    }

    /**
     * Test for getMembershipDetails returns collection
     */
    public function testGetMembershipDetails() {

        $memberDetailArray = $this->employeeDao->getMembershipDetails(1);
        $this->assertTrue($memberDetailArray[0] instanceof EmployeeMemberDetail);
        $this->assertTrue($memberDetailArray[1] instanceof EmployeeMemberDetail);
    }

    /**
     * Test for getMembershipDetail returns List with one EmployeeMemberDetail object
     */
    public function testGetMembershipDetail() {

        $empNumber = 1;
        $membership = 1;

        $memberDetail = $this->employeeDao->getMembershipDetail($empNumber, $membership);
        $this->assertTrue($memberDetail[0] instanceof EmployeeMemberDetail);
    }

    /**
     * Test for delete EmployeeMemberDetail object returns boolean
     */
    public function testDeleteMembershipDetails() {

        $empNumber = 1;
        $membership = 1;

        $this->assertTrue($this->employeeDao->deleteMembershipDetails($empNumber, $membership));
    }

    /**
     * Test for getSupervisorListForEmployee returns ReportTo doctrine collection
     */
    public function testGetSupervisorListForEmployee() {

        $supervisorReportToList = $this->employeeDao->getSupervisorListForEmployee(3);
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

        $emergencyContact = $this->employeeDao->getEmergencyContacts($empNumber);
        $this->assertTrue($emergencyContact[0] instanceof EmpEmergencyContact);
    }

    /**
     * Test for getReportingMethod returns ReportingMethod doctrine collection
     */
    public function testDeleteEmergencyContacts() {

        $empNumber = 1;
        $emergencyContactsToDelete = array(1, 2);

        $result = $this->employeeDao->deleteEmergencyContacts($empNumber, $emergencyContactsToDelete);
        $this->assertTrue($result);
    }

    /**
     * Test for get work expierence returns EmpWorkExperience doctrine collection
     */
    public function testGetWorkExperienceWithNullSeqNumber() {

        $empNumber = 1;

        $wrkExp = $this->employeeDao->getWorkExperience($empNumber);
        $this->assertTrue($wrkExp[0] instanceof EmpWorkExperience);
    }

    /**
     * Test for get work expierence returns EmpWorkExperience doctrine object
     */
    public function testGetWorkExperienceWithSeqNumber() {

        $empNumber = 1;
        $sequenceNo = 2;

        $wrkExp = $this->employeeDao->getWorkExperience($empNumber, $sequenceNo);
        $this->assertTrue($wrkExp instanceof EmpWorkExperience);
    }

    /**
     * Test for save work expierence returns boolean
     */
    public function testSaveWorkExperienceWithSeqNum() {

        $empWorkExp = new EmpWorkExperience;

        $empWorkExp->emp_number = 1;
        $empWorkExp->seqno = 3;
        $empWorkExp->jobtitle = "SE";
        $empWorkExp->employer = "OrangeHRM";

        $this->assertTrue($this->employeeDao->saveWorkExperience($empWorkExp));
    }

    /**
     * Test for save work expierence returns boolean
     */
    public function testSaveWorkExperienceWithoutSeqNum() {

        $empWorkExp = new EmpWorkExperience;

        $empWorkExp->emp_number = 1;
        $empWorkExp->jobtitle = "Architect";
        $empWorkExp->employer = "IFS";

        $this->assertTrue($this->employeeDao->saveWorkExperience($empWorkExp));
    }

    /**
     * Test for deleteWrkExpierence returns boolean
     */
    public function testDeleteWorkExperience() {

        $empNumber = 1;
        $workExperienceToDelete = array(1, 2);

        $result = $this->employeeDao->deleteWorkExperience($empNumber, $workExperienceToDelete);
        $this->assertTrue($result);
    }

    public function testGetEducation() {

        $education = $this->employeeDao->getEducation(1);
        $this->assertTrue($this->employeeDao->getEducation(1) instanceof EmployeeEducation);
    }

    public function testGetEmployeeEducationListWithOnlyEmpNumber() {

        $eduList = $this->employeeDao->getEmployeeEducationList(1);

        foreach ($eduList as $item) {
            $this->assertTrue($item instanceof EmployeeEducation);
        }

        $this->assertEquals(2, count($eduList));

        /* Checking the order */
        $this->assertEquals('ENG', $eduList[0]->getMajor());
        $this->assertEquals('ENG1', $eduList[1]->getMajor());
    }

    public function testGetEmployeeEducationListWithEmpNumberAndEduId() {

        $eduList = $this->employeeDao->getEmployeeEducationList(1, 2);

        $this->assertTrue($eduList[0] instanceof EmployeeEducation);

        $this->assertEquals(1, count($eduList));

        /* Checking values */
        $this->assertEquals('ENG1', $eduList[0]->getMajor());
    }

    /**
     * Test for save education expierence returns boolean
     */
    public function testSaveEducation() {

        $empEdu = new EmployeeEducation;

        $empEdu->empNumber = 2;
        $empEdu->educationId = 2;
        $empEdu->major = 'major';

        $this->assertTrue($this->employeeDao->saveEducation($empEdu));
    }

    /**
     * Test for deleteEducation returns boolean
     */
    public function testDeleteEducation() {

        $empNumber = 1;
        $educationToDelete = array(1, 2);

        $result = $this->employeeDao->deleteEducation($empNumber, $educationToDelete);
        $this->assertTrue($result);
        
        // verify records deleted
        $q = Doctrine_Query::create()->from('EmployeeEducation ec')->where('emp_number = ?', $empNumber);
        $this->assertEquals(0, $q->count());
        
        $empNumber = 2;
        $result = $this->employeeDao->deleteEducation($empNumber, array(3));
        $this->assertTrue($result);
        $q = Doctrine_Query::create()->from('EmployeeEducation ec')->where('emp_number = ?', $empNumber);
        $this->assertEquals(0, $q->count());
    }

    /**
     * Test for get skill returns EmployeeSkill doctrine collection
     */
    public function testGetSkillWithNullSkillCode() {

        $empNumber = 1;

        $skill = $this->employeeDao->getSkill($empNumber);
        $this->assertTrue($skill[0] instanceof EmployeeSkill);
    }

    /**
     * Test for get work expierence returns EmployeeSkill doctrine object
     */
    public function testGetSkillWithSkillCode() {

        $empNumber = 1;
        $skillCode = 2;

        $skill = $this->employeeDao->getSkill($empNumber, $skillCode);
        $this->assertTrue($skill instanceof EmployeeSkill);
    }

    /**
     * Test for save Skill returns boolean
     */
    public function testSaveSkill() {

        $empSkill = new EmployeeSkill;

        $empSkill->emp_number = 3;
        $empSkill->skillId = 1;

        $this->assertTrue($this->employeeDao->saveSkill($empSkill));
    }

    /**
     * Test for deleteSkill returns boolean
     */
    public function testDeleteSkill() {

        $empNumber = 1;
        $skillToDelete = array(1, 2);

        $result = $this->employeeDao->deleteSkill($empNumber, $skillToDelete);
        $this->assertTrue($result);
    }

    /**
     * Test for get skill returns EmployeeLanguage doctrine collection
     */
    public function testGetLanguageWithNullLangCodeAndLangType() {

        $empNumber = 1;

        $language = $this->employeeDao->getLanguage($empNumber);
        $this->assertTrue($language[0] instanceof EmployeeLanguage);
    }

    /**
     * Test for get work expierence returns EmployeeLanguage doctrine collection
     */
    public function testGetLanguageWithNullLangCode() {

        $empNumber = 1;
        $langType = 2;

        $language = $this->employeeDao->getLanguage($empNumber, null, $langType);
        $this->assertTrue($language[0] instanceof EmployeeLanguage);
    }

    /**
     * Test for get work expierence returns EmployeeLanguage doctrine collection
     */
    public function testGetLanguageWithNullLangType() {

        $empNumber = 1;
        $langCode = 'LAN001';

        $language = $this->employeeDao->getLanguage($empNumber, $langCode);
        $this->assertTrue($language[0] instanceof EmployeeLanguage);
    }

    /**
     * Test for get work expierence returns EmployeeLanguage doctrine object
     */
    public function testGetLanguage() {

        $empNumber = 1;
        $langCode = 1;
        $langType = 2;

        $language = $this->employeeDao->getLanguage($empNumber, $langCode, $langType);
        $this->assertTrue($language instanceof EmployeeLanguage);
    }

    /**
     * Test for save language expierence returns boolean
     */
    public function testSaveLanguage() {

        $empLang = new EmployeeLanguage;

        $empLang->empNumber = 2;
        $empLang->langId = 1;

        $this->assertTrue($this->employeeDao->saveLanguage($empLang));
    }

    /**
     * Test for deleteLanguage returns boolean
     */
    public function testDeleteLanguage() {

        $empNumber = 1;
        $languagesToDelete = array(1 => 2, 2 => 1);

        $result = $this->employeeDao->deleteLanguage($empNumber, $languagesToDelete);
        $this->assertTrue($result > 0);
    }

    /**
     * Test for get skill returns EmployeeLicense doctrine collection
     */
    public function testGetLicenseWithNullLicenseCode() {

        $empNumber = 1;

        $license = $this->employeeDao->getLicense($empNumber);
        $this->assertTrue($license[0] instanceof EmployeeLicense);
    }

    /**
     * Test for get work expierence returns EmployeeLicense doctrine object
     */
    public function testGetLicenseWithLicenseCode() {

        $empNumber = 1;
        $licenseCode = 1;

        $licenseCode = $this->employeeDao->getLicense($empNumber, $licenseCode);
        $this->assertTrue($licenseCode instanceof EmployeeLicense);
    }

    /**
     * Test for save license expierence returns boolean
     */
    public function testSaveLicense() {

        $empLicense = new EmployeeLicense;

        $empLicense->empNumber = 3;
        $empLicense->licenseId = 1;

        $this->assertTrue($this->employeeDao->saveLicense($empLicense));
    }

    /**
     * Test for deleteLicence returns boolean
     */
    public function testDeleteLicense() {

        $empNumber = 1;
        $licenseToDelete = array(1, 2);

        $result = $this->employeeDao->deleteLicense($empNumber, $licenseToDelete);
        $this->assertTrue($result);
    }

    /**
     * Test for get dependents returns EmpDependent doctrine collection
     */
    public function testGetDependents() {

        $empNumber = 1;

        $empDep = $this->employeeDao->getDependents($empNumber);
        $this->assertTrue($empDep[0] instanceof EmpDependent);
        $this->assertTrue($empDep[1] instanceof EmpDependent);
    }

    /**
     * Test for delete dependents returns boolean
     */
    public function testDeleteDependents() {

        $empNumber = 1;
        $entriesToDelete = array(1, 2);

        $result = $this->employeeDao->deleteDependents($empNumber, $entriesToDelete);
        $this->assertTrue($result);
    }

    public function testDeleteChildren() {
        $empNumber = 1;

        // Set 2nd parameter to a non array object
        $result = $this->employeeDao->deleteDependents($empNumber, '');
        $this->assertFalse($result);

        $entriesToDelete = array(1, 2);
        $result = $this->employeeDao->deleteDependents($empNumber, $entriesToDelete);
        $this->assertTrue($result);
    }

    public function testDeletePhoto() {
        $empNumber = 1;

        $result = $this->employeeDao->deletePhoto($empNumber);
        $this->assertTrue($result);
    }

    public function testSaveEmployeePicture() {

        $mockPicture = $this->getMock('EmpPicture', array('save'));
        $mockPicture->expects($this->once())
                ->method('save');
        $result = $this->employeeDao->saveEmployeePicture($mockPicture);

        $mockPicture = $this->getMock('EmpPicture', array('save'));
        $mockPicture->expects($this->once())
                ->method('save')
                ->will($this->throwException(new Exception()));

        try {
            $result = $this->employeeDao->saveEmployeePicture($mockPicture);
            $this->fail("Exception expected");
        } catch (Exception $e) {
            // Expected
        }
    }

    public function testAddEmployee() {
        TestDataService::truncateTables(array('Employee'));

        $employee = new Employee();
        $employee->firstName = 'Tester';
        $employee->lastName = 'Jason';

        $employee = $this->employeeDao->addEmployee($employee);
    }

    public function testGetEmployee() {
        $empNumber = 1;

        $employee = $this->employeeDao->getEmployee($empNumber);
        $this->assertTrue($employee instanceof Employee);
        $this->assertEquals('Kayla', $employee->getFirstName());
    }

    public function testGetPicture() {
        $empNumber = 1;

        $picture = $this->employeeDao->getPicture($empNumber);
        $this->assertTrue($picture instanceof EmpPicture);
        $this->assertEquals('test_file.jpg', $picture->getFileName());
    }

    public function testSavePersonalDetails() {
        $employee = new Employee();
        $employee->firstName = 'Tester';
        $employee->middleName = 'T';
        $employee->lastName = 'Jason';
        $employee->empNumber = 1;

        $employee->nickName = 'TT';
        $employee->otherId = "";
        $employee->emp_marital_status = 0;
        $employee->smoker = 0;
        $employee->emp_gender = 0;
        $employee->militaryService = 0;

        $retVal = $this->employeeDao->savePersonalDetails($employee, false);
        $this->assertTrue($retVal);

        $employee->emp_dri_lice_exp_date = '2011-01-01';
        $employee->nation_code = 1;
        $employee->ethnic_race_code = 'ETH001';
        $employee->emp_birthday = '1975-01-30';

        $retVal = $this->employeeDao->savePersonalDetails($employee, false);
        $this->assertTrue($retVal);

        $retVal = $this->employeeDao->savePersonalDetails($employee, true);
        $this->assertTrue($retVal);
    }

    public function testSaveContactDetails() {
        $employee = new Employee();
        $employee->firstName = 'Tester';
        $employee->lastName = 'Jason';
        $employee->empNumber = 1;
        $employee->street1 = '223 Main Street';
        $employee->street2 = '';
        $employee->city = 'Colombo';
        $employee->province = 'Western';
        $employee->emp_zipcode = '10000';
        $employee->emp_hm_telephone = '99299292';
        $employee->emp_mobile = '9292929';
        $employee->emp_work_telephone = '92999292';
        $employee->emp_work_email = 'adsa@sad.com';
        $employee->emp_oth_email = 'sadfa@dsaf.com';

        $retVal = $this->employeeDao->saveContactDetails($employee);
        $this->assertTrue($retVal);

        $employee->country = 'Sri Lanka';

        $retVal = $this->employeeDao->saveContactDetails($employee);
        $this->assertTrue($retVal);
    }

    public function testDeleteImmigration() {
        $empNumber = 1;
        $retVal = $this->employeeDao->deleteImmigration($empNumber, array(1));
        $this->assertTrue($retVal);
    }

    public function testGetAttachments() {
        $empNumber = 1;
        $retVal = $this->employeeDao->getAttachments($empNumber, 'personal');
        $this->assertEquals(2, count($retVal));
    }

    public function testGetAttachment() {
        $empNumber = 1;
        $retVal = $this->employeeDao->getAttachment($empNumber, 1);
        $this->assertTrue($retVal instanceof EmployeeAttachment);
        $this->assertEquals('test.mdb', $retVal->filename);
    }

    public function testdeleteAttachments() {
        $empNumber = 1;
        $retVal = $this->employeeDao->deleteAttachments($empNumber, array(1, 2));
        $this->assertTrue($retVal);

        $retVal = $this->employeeDao->deleteAttachments($empNumber, array());
        $this->assertFalse($retVal);
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

    public function testGetSupervisorEmployeeList() {
        $repToList = TestDataService::loadObjectList('ReportTo', $this->fixture, 'ReportTo');
        foreach ($repToList as $repTo) {
            $supervisors[] = $repTo->supervisorId;
        }

        $subCounts = array_count_values($supervisors);
        foreach ($subCounts as $supervisor => $count) {
            $list = $this->employeeDao->getSupervisorEmployeeList($supervisor);
            $this->assertEquals($count, count($list));
        }
    }

    public function testGetSupervisorEmployeeChain() {
        $repToList = TestDataService::loadObjectList('ReportTo', $this->fixture, 'ReportTo');
        foreach ($repToList as $repTo) {
            $supervisors[] = $repTo->supervisorId;
        }

        $supervisorId = array_pop($supervisors);

        $chain = $this->employeeDao->getSupervisorEmployeeChain($supervisorId);
        $this->assertTrue(count($chain) > 0);
    }

    public function testDeleteEmployee() {
        $employees = TestDataService::loadObjectList('Employee', $this->fixture, 'Employee');
        foreach ($employees as $emp) {
            $empNumbers[] = $emp->getEmpNumber();
        }

        $retVal = $this->employeeDao->deleteEmployee(array());
        $this->assertEquals(0, $retVal);

        $retVal = $this->employeeDao->deleteEmployee($empNumbers);
        $this->assertEquals(count($empNumbers), $retVal);

        $retVal = $this->employeeDao->deleteEmployee($empNumbers);
        $this->assertEquals(0, $retVal);
    }

    public function testIsEmployeeIdInUse() {
        $employees = TestDataService::loadObjectList('Employee', $this->fixture, 'Employee');

        foreach ($employees as $emp) {
            $empId = $emp->getEmployeeId();
            $this->assertTrue($this->employeeDao->isEmployeeIdInUse($empId));
        }

        $this->assertFalse($this->employeeDao->isEmployeeIdInUse('sdfsd'));
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
        $workShift = $this->employeeDao->getWorkShift($empNumber);
        $this->assertEquals(1, $workShift->getWorkShiftId());
    }

    public function testSaveJobDetails() {

        $employee = $this->getMock('Employee', array('save'));
        $employee->expects($this->once())
                ->method('save');

        $result = $this->employeeDao->saveJobDetails($employee);
        $this->assertTrue($result);

        $employee = $this->getMock('Employee', array('save'));
        $employee->expects($this->once())
                ->method('save')
                ->will($this->throwException(new Exception()));

        try {
            $result = $this->employeeDao->saveJobDetails($employee);
            $this->fail("Exception expected");
        } catch (Exception $e) {
            
        }
    }

    public function testSaveEmpBasicsalary() {
        $salary = $this->getMock('EmpBasicsalary', array('save'));
        $salary->expects($this->once())
                ->method('save');

        $result = $this->employeeDao->saveEmpBasicsalary($salary);
        $this->assertTrue($result);

        $salary = $this->getMock('EmpBasicsalary', array('save'));
        $salary->expects($this->once())
                ->method('save')
                ->will($this->throwException(new Exception()));

        try {
            $result = $this->employeeDao->saveEmpBasicsalary($salary);
            $this->fail("Exception expected");
        } catch (Exception $e) {
            
        }
    }

    public function testDeleteSalary() {
        $empNumber = 1;

        $result = $this->employeeDao->deleteSalary($empNumber, array('SAL001'));
        $this->assertEquals(1, count($result));
    }

    public function testGetSalary() {

        // Get all salaries
        $empNumber = 1;
        $result = $this->employeeDao->getSalary($empNumber);
        $this->assertEquals(2, count($result));
        $this->assertEquals(2, $result[0]->id); // Allowance
        $this->assertEquals('Allowance', $result[0]->getSalaryComponent());
        $this->assertEquals(1, $result[1]->id); // Main Salary
        $this->assertEquals('Main Salary', $result[1]->getSalaryComponent());

        // Get one salary
        $result = $this->employeeDao->getSalary($empNumber, 1);
        $result = $this->employeeDao->getSalary($empNumber, 1);
        $this->assertTrue($result instanceof EmpBasicsalary);
        $this->assertEquals(1, $result->id);
        $this->assertEquals('Main Salary', $result->getSalaryComponent());

        //        
        // None existing salary
        $result = $this->employeeDao->getSalary($empNumber, 12);
        $this->assertFalse($result);

        // employee with no salaries
        $result = $this->employeeDao->getSalary(3);
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
        $this->assertFalse($employee);

        $employee = $this->employeeDao->getEmployeeByEmployeeId('');
        $this->assertFalse($employee);
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
}
