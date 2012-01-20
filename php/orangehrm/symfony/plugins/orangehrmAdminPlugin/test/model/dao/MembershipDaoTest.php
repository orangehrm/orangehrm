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
 *  @group Admin
 */
class MembershipDaoTest extends PHPUnit_Framework_TestCase {

    private $membershipDao;
    protected $fixture;

    /**
     * Set up method
     */
    protected function setUp() {

        $this->membershipDao = new MembershipDao();
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmAdminPlugin/test/fixtures/MembershipDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetMembershipList() {
        $result = $this->membershipDao->getMembershipList();
        $this->assertEquals(count($result), 3);
    }

    public function testGetMembershipById() {
        $result = $this->membershipDao->getMembershipById(1);
        $this->assertEquals($result->getName(), 'membership 1');
    }

    public function testDeleteMemberships() {
        $result = $this->membershipDao->deleteMemberships(array(1, 2, 3));
        $this->assertEquals($result, 3);
    }

}

