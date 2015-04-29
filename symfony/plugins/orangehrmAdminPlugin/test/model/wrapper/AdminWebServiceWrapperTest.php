<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AdminWebServiceWrapperTest
 *
 * @author emma
 */
require_once ROOT_PATH . "/lib/confs/Conf.php";

class AdminWebServiceWrapperTest extends PHPUnit_Framework_TestCase {

    protected $jobTitlefixture;
    protected $manager;
    protected $adminWebServiceWrapper;
    protected $locationFixture;
    protected $locationTestCases;
    public static function setupBeforeClass() {
        WSManager::resetConfiguration();
    }

    /**
     * Set up method
     */
    protected function setUp() {
        $this->adminWebServiceWrapper = new AdminWebServiceWrapper();
        $this->jobTitlefixture = sfConfig::get('sf_plugins_dir') . '/orangehrmAdminPlugin/test/fixtures/JobTitleDao.yml';
        $this->locationFixture = sfConfig::get('sf_plugins_dir') . '/orangehrmAdminPlugin/test/fixtures/LocationDao.yml';
        $this->locationTestCases = sfYaml::load(sfConfig::get('sf_plugins_dir') . '/orangehrmAdminPlugin/test/fixtures/LocationDao.yml');
        $this->manager = new WSManager();
        $this->helper = new WSHelper();

        PHPUnit_Framework_Error_Warning::$enabled = FALSE;

        PHPUnit_Framework_Error_Notice::$enabled = FALSE;
    }

    public function testCallGetJobTitleListMethod() {
        TestDataService::populate($this->jobTitlefixture);
        $paramObj = new WSRequestParameters();
        $paramObj->setAppId(1);
        $paramObj->setAppToken('1234567890');
        $paramObj->setMethod('getJobTitleList');
        $paramObj->setSessionToken(uniqid('ohrm_ws_session_'));
        $paramObj->setParameters(array(NULL));
        $paramObj->setRequestMethod('GET');

        $result = $this->manager->callMethod($paramObj);
        $this->assertNotNull($result);
        $this->assertEquals(3, count($result));

        $this->assertEquals($result[0]['id'], 3);
        $this->assertEquals($result[0]['jobTitleName'], 'Quality Assuarance');
        $this->assertEquals($result[0]['isDeleted'], 0);

        $this->assertEquals($result[1]['id'], 1);
        $this->assertEquals($result[1]['jobTitleName'], 'Software Architect');
        $this->assertEquals($result[1]['isDeleted'], 0);

        $this->assertEquals($result[2]['id'], 2);
        $this->assertEquals($result[2]['jobTitleName'], 'Software Engineer');
        $this->assertEquals($result[2]['isDeleted'], 0);
    }

    public function testCallGetLocationListMethod() {
        TestDataService::populate($this->locationFixture);
        $paramObj = new WSRequestParameters();
        $paramObj->setAppId(1);
        $paramObj->setAppToken('1234567890');
        $paramObj->setMethod('getLocationList');
        $paramObj->setSessionToken(uniqid('ohrm_ws_session_'));
        $paramObj->setParameters(array(0));
        $paramObj->setRequestMethod('GET');
        $mock = $this->getMock('AdminWebServiceHelper', array('getAccessibleLocations'));
        $mock->method('getAccessibleLocations')
                ->will($this->returnValue(array()));

        $this->adminWebServiceWrapper->setServiceInstance($mock);
        $paramObj->setWrapperObject($this->adminWebServiceWrapper);
        $result = $this->manager->callMethod($paramObj);
        $this->assertNotNull($result);
        foreach ($this->locationTestCases['Location'] as $key => $testCase) {
            $this->assertEquals($result[$key]['id'], $testCase['id']);
            $this->assertEquals($result[$key]['locationName'], $testCase['name']);
            $this->assertEquals($result[$key]['country_code'], $testCase['country_code']);
            $this->assertEquals($result[$key]['province'], $testCase['province']);
            $this->assertEquals($result[$key]['city'], $testCase['city']);
            $this->assertEquals($result[$key]['address'], $testCase['address']);
        }
    }

}
