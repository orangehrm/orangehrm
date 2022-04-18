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

namespace OrangeHRM\Tests\Pim\Service;

use DateTime;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\EmployeeMembership;
use OrangeHRM\Pim\Dao\EmployeeMembershipDao;
use OrangeHRM\Pim\Dto\EmployeeMembershipSearchFilterParams;
use OrangeHRM\Pim\Service\EmployeeMembershipService;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Pim
 * @group Service
 */
class EmployeeMembershipServiceTest extends TestCase
{
    private EmployeeMembershipService $employeeMembershipService;
    protected string $fixture;

    protected function setUp(): void
    {
        $this->employeeMembershipService = new EmployeeMembershipService();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmPimPlugin/test/fixtures/EmployeeMembershipDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetEmployeeMembershipById(): void
    {
        $employeeMembership1 = new EmployeeMembership();
        $employeeMembership1->getDecorator()->setMembershipByMembershipId(1);
        $employeeMembership1->getDecorator()->setEmployeeByEmpNumber(1);
        $employeeMembership1->setSubscriptionFee('4');
        $employeeMembership1->setSubscriptionPaidBy('Individual');
        $employeeMembership1->setSubscriptionCurrency('LKR');
        $employeeMembership1->setSubscriptionCommenceDate(new DateTime('2011-05-20'));
        $employeeMembership1->setSubscriptionRenewalDate(new DateTime('2011-05-22'));

        $employeeMembershipDao = $this->getMockBuilder(EmployeeMembershipDao::class)->getMock();
        $employeeMembershipDao->expects($this->once())
            ->method('getEmployeeMembershipById')
            ->with(1, 1)
            ->will($this->returnValue($employeeMembership1));

        $this->employeeMembershipService->setEmployeeMembershipDao($employeeMembershipDao);
        $employeeMembership = $this->employeeMembershipService->getEmployeeMembershipById(1, 1);
        $this->assertEquals('4', $employeeMembership->getSubscriptionFee());
    }

    public function testSaveEmployeeMembership(): void
    {
        $employee = $this->getEntityReference(Employee::class, 1);
        $employeeMembership = new EmployeeMembership();
        $employeeMembership->setEmployee($employee);
        $employeeMembership->getDecorator()->setMembershipByMembershipId(1);
        $employeeMembership->setSubscriptionFee('4');
        $employeeMembership->setSubscriptionPaidBy('Individual');
        $employeeMembership->setSubscriptionCurrency('LKR');
        $employeeMembership->setSubscriptionCommenceDate(new DateTime('2011-05-20'));
        $employeeMembership->setSubscriptionRenewalDate(new DateTime('2011-05-22'));

        $employeeMembershipDao = $this->getMockBuilder(EmployeeMembershipDao::class)->getMock();

        $employeeMembershipDao->expects($this->once())
            ->method('saveEmployeeMembership')
            ->with($employeeMembership)
            ->will($this->returnValue($employeeMembership));

        $this->employeeMembershipService->setEmployeeMembershipDao($employeeMembershipDao);

        $employeeMembershipObj = $this->employeeMembershipService->saveEmployeeMembership($employeeMembership);
        $this->assertEquals("4", $employeeMembershipObj->getSubscriptionFee());
        $this->assertEquals("Individual", $employeeMembershipObj->getSubscriptionPaidBy());
        $this->assertEquals("LKR", $employeeMembershipObj->getSubscriptionCurrency());
        $this->assertEquals(new DateTime('2011-05-20'), $employeeMembershipObj->getSubscriptionCommenceDate());
        $this->assertEquals(new DateTime('2011-05-22'), $employeeMembershipObj->getSubscriptionRenewalDate());
    }

    public function testDeleteEmployeeMemberships(): void
    {
        $employeeMembershipDao = $this->getMockBuilder(EmployeeMembershipDao::class)->getMock();
        $employeeMembershipDao->expects($this->once())
            ->method('deleteEmployeeMemberships')
            ->with(1, [1, 2])
            ->will($this->returnValue(2));

        $this->employeeMembershipService->setEmployeeMembershipDao($employeeMembershipDao);
        $rows = $this->employeeMembershipService->deleteEmployeeMemberships(1, [1, 2]);
        $this->assertEquals(2, $rows);
    }

    public function testSearchEmployeeMembership(): void
    {
        $employeeMembership1 = new EmployeeMembership();
        $employeeMembership1->getDecorator()->setEmployeeByEmpNumber(1);
        $employeeMembership1->setSubscriptionFee('4');
        $employeeMembership1->setSubscriptionPaidBy('Individual');

        $employeeMembership2 = new EmployeeMembership();
        $employeeMembership2->getDecorator()->setEmployeeByEmpNumber(1);
        $employeeMembership2->setSubscriptionPaidBy('Company');

        $employeeMembershipList = [$employeeMembership1, $employeeMembership2];
        $employeeMembershipSearchParams = new EmployeeMembershipSearchFilterParams();
        $employeeMembershipSearchParams->setEmpNumber(1);
        $employeeMembershipDao = $this->getMockBuilder(EmployeeMembershipDao::class)->getMock();

        $employeeMembershipDao->expects($this->once())
            ->method('searchEmployeeMembership')
            ->with($employeeMembershipSearchParams)
            ->will($this->returnValue($employeeMembershipList));

        $this->employeeMembershipService->setEmployeeMembershipDao($employeeMembershipDao);
        $result = $this->employeeMembershipService->searchEmployeeMembership($employeeMembershipSearchParams);
        $this->assertCount(2, $result);
        $this->assertTrue($result[0] instanceof EmployeeMembership);
    }

    public function testGetSearchEmployeeMembershipsCount(): void
    {
        $employeeMembershipSearchParams = new EmployeeMembershipSearchFilterParams();
        $employeeMembershipSearchParams->setEmpNumber(1);
        $employeeMembershipDao = $this->getMockBuilder(EmployeeMembershipDao::class)->getMock();

        $employeeMembershipDao->expects($this->once())
            ->method('getSearchEmployeeMembershipsCount')
            ->with($employeeMembershipSearchParams)
            ->will($this->returnValue(2));
        $this->employeeMembershipService->setEmployeeMembershipDao($employeeMembershipDao);
        $result = $this->employeeMembershipService->getSearchEmployeeMembershipsCount($employeeMembershipSearchParams);
        $this->assertEquals(2, $result);
    }
}
