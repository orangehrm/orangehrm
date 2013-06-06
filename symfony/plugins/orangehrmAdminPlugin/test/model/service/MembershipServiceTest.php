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
class MembershipServiceTest extends PHPUnit_Framework_TestCase {

    private $membershipService;
    private $fixture;

    /**
     * Set up method
     */
    protected function setUp() {
        $this->membershipService = new MembershipService();
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmAdminPlugin/test/fixtures/MembershipDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetMembershipList() {

        $membershipList = TestDataService::loadObjectList('Membership', $this->fixture, 'Membership');

        $membershipDao = $this->getMock('MembershipDao');
        $membershipDao->expects($this->once())
                ->method('getMembershipList')
                ->will($this->returnValue($membershipList));

        $this->membershipService->setMembershipDao($membershipDao);

        $result = $this->membershipService->getMembershipList();
        $this->assertEquals($result, $membershipList);
    }

    public function testGetMembershipById() {

        $membershipList = TestDataService::loadObjectList('Membership', $this->fixture, 'Membership');

        $membershipDao = $this->getMock('MembershipDao');
        $membershipDao->expects($this->once())
                ->method('getMembershipById')
                ->with(1)
                ->will($this->returnValue($membershipList[0]));

        $this->membershipService->setMembershipDao($membershipDao);

        $result = $this->membershipService->getMembershipById(1);
        $this->assertEquals($result, $membershipList[0]);
    }

    public function testDeleteMemberships() {

        $membershipList = array(1, 2, 3);

        $membershipDao = $this->getMock('MembershipDao');
        $membershipDao->expects($this->once())
                ->method('deleteMemberships')
                ->with($membershipList)
                ->will($this->returnValue(3));

        $this->membershipService->setMembershipDao($membershipDao);

        $result = $this->membershipService->deleteMemberships($membershipList);
        $this->assertEquals($result, 3);
    }

}

