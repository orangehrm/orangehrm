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

namespace OrangeHRM\Tests\Admin\Dao;

use Exception;
use OrangeHRM\Admin\Dao\MembershipDao;
use OrangeHRM\Admin\Dto\MembershipSearchFilterParams;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\Membership;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Admin
 * @group Dao
 */
class MembershipDaoTest extends TestCase
{
    private MembershipDao $membershipDao;
    protected string $fixture;

    /**
     * Set up method
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->membershipDao = new MembershipDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmAdminPlugin/test/fixtures/MembershipDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testAddMembership(): void
    {
        $membership = new Membership();
        $membership->setName('membership one');

        $this->membershipDao->saveMembership($membership);

        $savedMembership = TestDataService::fetchLastInsertedRecord('Membership', 'a.id');

        $this->assertTrue($savedMembership instanceof Membership);
        $this->assertEquals('membership one', $savedMembership->getName());
    }

    public function testEditMembership(): void
    {
        $membership = TestDataService::fetchObject('Membership', 3);
        $membership->setName('membership New');

        $this->membershipDao->saveMembership($membership);

        $savedMembership = TestDataService::fetchLastInsertedRecord('Membership', 'a.id');

        $this->assertTrue($savedMembership instanceof Membership);
        $this->assertEquals('membership New', $savedMembership->getName());
    }

    public function testGetMembershipList(): void
    {
        $membershipFilterParams = new MembershipSearchFilterParams();
        $result = $this->membershipDao->getMembershipList($membershipFilterParams);
        $this->assertCount(3, $result);
        $this->assertTrue($result[0] instanceof Membership);
    }

    public function testGetMembershipById(): void
    {
        $membership = $this->membershipDao->getMembershipById(1);

        $this->assertTrue($membership instanceof Membership);
        $this->assertEquals('membership 1', $membership->getName());
    }

    public function testDeleteMemberships(): void
    {
        $toTobedeletedIds = [1, 2];
        $result = $this->membershipDao->deleteMemberships($toTobedeletedIds);
        $this->assertEquals(2, $result);

        $result = $this->membershipDao->deleteMemberships([]);
        $this->assertEquals(0, $result);
    }

    public function testDeleteWrongRecord(): void
    {
        $result = $this->membershipDao->deleteMemberships([4]);

        $this->assertEquals(0, $result);
    }

    public function testIsExistingMembershipName(): void
    {
        $this->assertTrue($this->membershipDao->isExistingMembershipName('MembershiP 1'));
        $this->assertTrue($this->membershipDao->isExistingMembershipName('MEMBERSHIP 1'));
        $this->assertTrue($this->membershipDao->isExistingMembershipName('membership 1'));
        $this->assertTrue($this->membershipDao->isExistingMembershipName('  membership 1  '));
    }
}
