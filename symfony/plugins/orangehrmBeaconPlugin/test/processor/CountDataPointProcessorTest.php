<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CountDataPointProcessorTest
 *
 * @group beacon
 */
class CountDataPointProcessorTest extends PHPUnit_Framework_TestCase {

    private $datapoints;

    protected function setUp() {
        $this->datapoints = sfYaml::load(sfConfig::get('sf_plugins_dir') . '/orangehrmBeaconPlugin/test/fixtures/BeaconDatapointDao.yml');
    }

    public function testProcessCountDatapoint() {

        $countDatapointProcessor = $this->getMock('countDataPointProcessor', array('executeQuery', 'sanitize'));

        $countDatapointProcessor->expects($this->any())
                ->method('executeQuery')
                ->with($this->anything())
                ->will($this->returnCallback(function() {

                            $args = func_get_args();
                            return array($args[0]);
                        }));
                        
        $countDatapointProcessor->expects($this->any())
                ->method('sanitize')
                ->with($this->anything())
                ->will($this->returnValue(true));

        $this->assertEquals("SELECT COUNT(*) FROM hs_hr_employee  WHERE termination_id IS NULL  ", $countDatapointProcessor->process($this->datapoints['Datapoint']['2']['definition']));

        $this->assertEquals("SELECT COUNT(*) FROM hs_hr_employee e left JOIN hs_hr_emp_locations el on e.emp_number ="
                . " el.emp_number left JOIN ohrm_location l on el.location_id = l.id WHERE e.termination_id is null   "
                . "AND  l.country_code = 'LK' group by l.country_code", $countDatapointProcessor->process($this->datapoints['Datapoint']['5']['definition']));
    }

}

function returnQuery() {
    
}
