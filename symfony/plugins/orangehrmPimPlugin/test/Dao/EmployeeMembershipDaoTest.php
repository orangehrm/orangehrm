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

namespace OrangeHRM\Tests\Pim\Dao;

use DateTime;
use Exception;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\EmployeeMembership;
use OrangeHRM\Pim\Dao\EmployeeMembershipDao;
use OrangeHRM\Pim\Dto\EmployeeMembershipSearchFilterParams;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Pim
 * @group Dao
 */
class EmployeeMembershipDaoTest extends TestCase
{
    private EmployeeMembershipDao $employeeMembershipDao;
    protected string $fixture;

    /**
     * Set up method
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->employeeMembershipDao = new EmployeeMembershipDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmPimPlugin/test/fixtures/EmployeeMembershipDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetEmployeeMembershipById(): void
    {
        $result = $this->employeeMembershipDao->getEmployeeMembershipById(1, 1);
        $this->assertEquals('4.00', $result->getSubscriptionFee());
        $this->assertEquals('individual', $result->getSubscriptionPaidBy());
        $this->assertEquals('Rs', $result->getSubscriptionCurrency());
        $this->assertEquals(new DateTime('2011-05-20'), $result->getSubscriptionCommenceDate());
        $this->assertEquals(new DateTime('2011-05-22'), $result->getSubscriptionRenewalDate());
    }

    public function testDeleteEmployeeMembership(): void
    {
        $toTobedeletedIds = [1, 2];
        $result = $this->employeeMembershipDao->deleteEmployeeMemberships(1, $toTobedeletedIds);
        $this->assertEquals(2, $result);
    }

    public function testSearchEmployeeMembership(): void
    {
        $employeeMembershipSearchParams = new EmployeeMembershipSearchFilterParams();
        $employeeMembershipSearchParams->setEmpNumber(1);
        $result = $this->employeeMembershipDao->searchEmployeeMembership($employeeMembershipSearchParams);
        $this->assertCount(2, $result);
        $this->assertTrue($result[0] instanceof EmployeeMembership);
    }

    public function testSearchEmployeeMembershipWithLimit(): void
    {
        $employeeMembershipSearchParams = new EmployeeMembershipSearchFilterParams();
        $employeeMembershipSearchParams->setEmpNumber(1);
        $employeeMembershipSearchParams->setLimit(1);

        $result = $this->employeeMembershipDao->searchEmployeeMembership($employeeMembershipSearchParams);
        $this->assertCount(1, $result);
    }

    public function testSaveEmployeeMembership(): void
    {
        $employeeMembership = new EmployeeMembership();
        $employeeMembership->getDecorator()->setMembershipByMembershipId(1);
        $employeeMembership->getDecorator()->setEmployeeByEmpNumber(3);
        $employeeMembership->setSubscriptionFee('4');
        $employeeMembership->setSubscriptionPaidBy('individual');
        $employeeMembership->setSubscriptionCurrency('Rs');
        $employeeMembership->setSubscriptionCommenceDate(new DateTime('2011-05-20'));
        $employeeMembership->setSubscriptionRenewalDate(new DateTime('2011-05-22'));
        $result = $this->employeeMembershipDao->saveEmployeeMembership($employeeMembership);
        $this->assertTrue($result instanceof EmployeeMembership);
        $this->assertEquals("4", $result->getSubscriptionFee());
        $this->assertEquals("individual", $result->getSubscriptionPaidBy());
        $this->assertEquals("Rs", $result->getSubscriptionCurrency());
        $this->assertEquals(new DateTime('2011-05-20'), $result->getSubscriptionCommenceDate());
        $this->assertEquals(new DateTime('2011-05-22'), $result->getSubscriptionRenewalDate());
    }

    public function testEditEmployeeMembership(): void
    {
        $employeeMembership = $this->employeeMembershipDao->getEmployeeMembershipById(1, 1);
        $employeeMembership->setSubscriptionFee('5');
        $employeeMembership->setSubscriptionPaidBy('company');
        $employeeMembership->setSubscriptionCurrency('Rb');
        $employeeMembership->setSubscriptionCommenceDate(new DateTime('2011-05-21'));
        $employeeMembership->setSubscriptionRenewalDate(new DateTime('2011-05-24'));
        $result = $this->employeeMembershipDao->saveEmployeeMembership($employeeMembership);
        $this->assertTrue($result instanceof EmployeeMembership);
        $this->assertEquals("5", $result->getSubscriptionFee());
        $this->assertEquals("company", $result->getSubscriptionPaidBy());
        $this->assertEquals("Rb", $result->getSubscriptionCurrency());
        $this->assertEquals(new DateTime('2011-05-21'), $result->getSubscriptionCommenceDate());
        $this->assertEquals(new DateTime('2011-05-24'), $result->getSubscriptionRenewalDate());
    }

    public function testGetSearchEmployeeMembershipsCount(): void
    {
        $employeeMembershipSearchParams = new EmployeeMembershipSearchFilterParams();
        $employeeMembershipSearchParams->setEmpNumber(1);
        $result = $this->employeeMembershipDao->getSearchEmployeeMembershipsCount($employeeMembershipSearchParams);
        $this->assertEquals(2, $result);
    }
}
