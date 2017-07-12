<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CorporateDirectoryWebServiceWrapperTest
 *
 * @author emma
 */
require_once ROOT_PATH . "/lib/confs/Conf.php";

class CorporateDirectoryWebServiceWrapperTest extends PHPUnit_Framework_TestCase {

    protected $fixture;
    protected $manager;
    protected $corporateDirectoryWebServiceWrapper;

    public static function setupBeforeClass() {
        WSManager::resetConfiguration();
    }

    /**
     * Set up method
     */
    protected function setUp() {
        $this->corporateDirectoryWebServiceWrapper = new CorporateDirectoryWebServiceWrapper();
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmCorporateDirectoryPlugin/test/fixtures/EmployeeDirectoryWrapperData.yml';
        $this->manager = new WSManager();
        $this->helper = new WSHelper();
    }

    public function getExpectedEmployeeList($base64TestStringForEmp1, $base64TestStringForEmp2) {
        $array = array(
            array(
                'emp_number' => 2,
                'employee_id' => 'Z0002',
                'emp_firstname' => 'Saman',
                'emp_lastname' => 'Fernando',
                'home_telephone' => null,
                'work_telephone' => null,
                'mobile' => null,
                'work_email' => null,
                'other_email' => null,
                'profile_image_url' => 'directory/viewDirectoryPhoto/empNumber/2',
                'terminated' => null,
                'profile_picture' => array(
                    'image_string' => $base64TestStringForEmp1,
                    'image_type' => 'image/gif'),
                'location_id' => 1,
                'job_title_id' => 2,
                'subunit_id' => null,
            ),
            array(
                'emp_number' => 4,
                'employee_id' => null,
                'emp_firstname' => 'Chuck',
                'emp_lastname' => 'Fernando',
                'home_telephone' => null,
                'work_telephone' => null,
                'mobile' => null,
                'work_email' => null,
                'other_email' => null,
                'profile_image_url' => 'directory/viewDirectoryPhoto/empNumber/4',
                'terminated' => null,
                'profile_picture' => null,
                'location_id' => 2,
                'job_title_id' => 3,
                'subunit_id' => null,
            ),
            array(
                'emp_number' => 1,
                'employee_id' => 'Z0001',
                'emp_firstname' => 'Saman',
                'emp_lastname' => 'Herath',
                'home_telephone' => null,
                'work_telephone' => null,
                'mobile' => null,
                'work_email' => null,
                'other_email' => null,
                'profile_image_url' => 'directory/viewDirectoryPhoto/empNumber/1',
                'terminated' => null,
                'profile_picture' => null,
                'location_id' => 1,
                'job_title_id' => 1,
                'subunit_id' => null,
            ),
            array(
                'emp_number' => 5,
                'employee_id' => null,
                'emp_firstname' => 'Pasindu',
                'emp_lastname' => 'Jayasekara',
                'home_telephone' => null,
                'work_telephone' => null,
                'mobile' => null,
                'work_email' => null,
                'other_email' => null,
                'profile_image_url' => 'directory/viewDirectoryPhoto/empNumber/5',
                'terminated' => null,
                'profile_picture' => null,
                'location_id' => null,
                'job_title_id' => 4,
                'subunit_id' => null,
            ),
            array(
                'emp_number' => 6,
                'employee_id' => null,
                'emp_firstname' => 'kamal',
                'emp_lastname' => 'jayasinghe',
                'home_telephone' => '3333',
                'work_telephone' => '5555',
                'mobile' => '4444',
                'work_email' => 'bbb@bbb.com',
                'other_email' => 'aaa@aaa.com',
                'profile_image_url' => 'directory/viewDirectoryPhoto/empNumber/6',
                'terminated' => null,
                'profile_picture' => null,
                'location_id' => null,
                'job_title_id' => 4,
                'subunit_id' => null,
            ),
            array(
                'emp_number' => 3,
                'employee_id' => null,
                'emp_firstname' => 'Ashan',
                'emp_lastname' => 'Perera',
                'home_telephone' => null,
                'work_telephone' => null,
                'mobile' => null,
                'work_email' => null,
                'other_email' => null,
                'profile_image_url' => 'directory/viewDirectoryPhoto/empNumber/3',
                'terminated' => null,
                'profile_picture' => array(
                    'image_string' => $base64TestStringForEmp2,
                    'image_type' => 'image/jpeg'),
                'location_id' => 1,
                'job_title_id' => 2,
                'subunit_id' => null,
            ),
        );
        return $array;
    }

    public function testCallGetCorporateDirectoryEmployeeDetailsMethod() {
        TestDataService::populate($this->fixture);

        $employeeService = new EmployeeService();
        $empPicture = $employeeService->getEmployeePicture(2);
        $base64TestStringForEmp1 = 'R0lGODlhCgAKALMAAAAAAIAAAACAAICAAAAAgIAAgACAgMDAwICAgP8AAAD/AP//AAAA//8A/wD//////ywAAAAACgAKAAAEClDJSau9OOvNe44AOw==';
        $decodedBase64String = base64_decode($base64TestStringForEmp1);
        $empPicture->setPicture($decodedBase64String);
        $empPicture->save();

        $empPicture = $employeeService->getEmployeePicture(3);
        $base64TestStringForEmp2 = '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';
        $decodedBase64String = base64_decode($base64TestStringForEmp2);
        $empPicture->setPicture($decodedBase64String);
        $empPicture->save();

        $paramObj = new WSRequestParameters();
        $paramObj->setAppId(1);
        $paramObj->setAppToken('1234567890');
        $paramObj->setMethod('getCorporateDirectoryEmployeeDetails');
        $paramObj->setSessionToken(uniqid('ohrm_ws_session_'));
        $paramObj->setParameters(array('includeTerminate' => 0));
        $paramObj->setRequestMethod('GET');

        $result = $this->manager->callMethod($paramObj);
        $this->assertNotNull($result);
        $this->assertEquals(6, count($result));
        $expectedEmployeeList = $this->getExpectedEmployeeList($base64TestStringForEmp1, $base64TestStringForEmp2);
        foreach ($expectedEmployeeList as $key => $testCase) {

            /* Fix profile image url (remove absolute path to phpunit
             * /usr/local/bin/phpunit/phpunit/directory/viewDirectoryPhoto/empNumber/2
             * => directory/viewDirectoryPhoto/empNumber/2
             */
            if (isset($expectedEmployeeList[$key]['profile_image_url'])) {
                $result[$key]['profile_image_url'] =
                    strstr($result[$key]['profile_image_url'], 'directory');
            }

            $this->assertEquals($expectedEmployeeList[$key], $result[$key]);
        }
    }

}
