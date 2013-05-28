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
 * @group Admin
 */
class WorkShiftDaoTest extends PHPUnit_Framework_TestCase {

    private $workShiftDao;
    protected $fixture;

    /**
     * Set up method
     */
    protected function setUp() {

        $this->workShiftDao = new WorkShiftDao();
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmAdminPlugin/test/fixtures/WorkShiftDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetWorkShiftList() {
        $result = $this->workShiftDao->getWorkShiftList();
        $this->assertEquals(count($result), 2);
    }

    public function testGetWorkShiftById() {
        $result = $this->workShiftDao->getWorkShiftById(1);
        $this->assertEquals($result->getName(), 'Shift 1');
    }

    public function testGetWorkShiftEmployeeListById() {
        $result = $this->workShiftDao->getWorkShiftEmployeeListById(1);
        $this->assertEquals(count($result), 2);
    }

    public function testGetWorkShiftEmployeeList() {
        $result = $this->workShiftDao->getWorkShiftEmployeeList();
        $this->assertEquals(count($result), 2);
    }

    public function testUpdateWorkShift() {
        $shift = $this->workShiftDao->getWorkShiftById(1);
        $shift->setName("edited shift");
        $this->workShiftDao->updateWorkShift($shift);
        $result = $this->workShiftDao->getWorkShiftById(1);
        $this->assertEquals($result->getName(), "edited shift");
    }

}
