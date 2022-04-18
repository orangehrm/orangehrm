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

namespace OrangeHRM\Tests\Admin\Service;

use Exception;
use OrangeHRM\Admin\Dao\MembershipDao;
use OrangeHRM\Admin\Dto\MembershipSearchFilterParams;
use OrangeHRM\Admin\Service\MembershipService;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\Membership;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Admin
 * @group Service
 */
class MembershipServiceTest extends TestCase
{
    private MembershipService $membershipService;
    private string $fixture;

    /**
     * Set up method
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->membershipService = new MembershipService();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmAdminPlugin/test/fixtures/MembershipDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetMembershipList(): void
    {
        $membershipList = TestDataService::loadObjectList('Membership', $this->fixture, 'Membership');
        $membershipFilterParams = new MembershipSearchFilterParams();
        $membershipDao = $this->getMockBuilder(MembershipDao::class)->getMock();
        $membershipDao->expects($this->once())
                ->method('getMembershipList')
                ->with($membershipFilterParams)
                ->will($this->returnValue($membershipList));
        $this->membershipService->setMembershipDao($membershipDao);
        $result = $this->membershipService->getMembershipList($membershipFilterParams);
        $this->assertCount(3, $result);
        $this->assertTrue($result[0] instanceof Membership);
    }

    public function testGetMembershipById(): void
    {
        $membershipList = TestDataService::loadObjectList('Membership', $this->fixture, 'Membership');
        $membershipDao = $this->getMockBuilder(MembershipDao::class)->getMock();
        $membershipDao->expects($this->once())
                ->method('getMembershipById')
                ->with(1)
                ->will($this->returnValue($membershipList[0]));
        $this->membershipService->setMembershipDao($membershipDao);
        $result = $this->membershipService->getMembershipById(1);
        $this->assertEquals($membershipList[0], $result);
    }

    public function testDeleteMemberships(): void
    {
        $toBeDeletedEducationIds = [1,2];
        $membershipDao = $this->getMockBuilder(MembershipDao::class)->getMock();
        $membershipDao->expects($this->once())
                ->method('deleteMemberships')
                ->with($toBeDeletedEducationIds)
                ->will($this->returnValue(2));
        $this->membershipService->setMembershipDao($membershipDao);
        $result = $this->membershipService->deleteMemberships($toBeDeletedEducationIds);
        $this->assertEquals(2, $result);
    }

    public function testGetEducationByName(): void
    {
        $membershipList = TestDataService::loadObjectList('Membership', $this->fixture, 'Membership');
        $membershipDao = $this->getMockBuilder(MembershipDao::class)->getMock();
        $membershipDao->expects($this->once())
            ->method('getMembershipByName')
            ->with(1)
            ->will($this->returnValue($membershipList[0]));
        $this->membershipService->setMembershipDao($membershipDao);
        $result = $this->membershipService->getMembershipByName(1);
        $this->assertEquals($result, $membershipList[0]);
    }
}
