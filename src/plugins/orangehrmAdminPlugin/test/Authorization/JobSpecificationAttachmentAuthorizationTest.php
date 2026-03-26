<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Tests\Admin\Authorization;

use OrangeHRM\Admin\Service\UserService;
use OrangeHRM\Authentication\Auth\User as AuthUser;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Authorization\Manager\BasicUserRoleManager;
use OrangeHRM\Core\Helper\ClassHelper;
use OrangeHRM\Core\Service\ConfigService;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\JobSpecificationAttachment;
use OrangeHRM\Entity\JobTitle;
use OrangeHRM\Entity\ReportingMethod;
use OrangeHRM\Entity\ReportTo;
use OrangeHRM\Entity\User;
use OrangeHRM\Entity\UserRole;
use OrangeHRM\Framework\Services;
use OrangeHRM\Pim\Service\EmployeeService;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;
use OrangeHRM\Time\Service\ProjectService;

/**
 * @group Admin
 * @group Authorization
 */
class JobSpecificationAttachmentAuthorizationTest extends KernelTestCase
{
    private string $fixture;

    protected function setUp(): void
    {
        $this->fixture = Config::get(Config::PLUGINS_DIR)
            . '/orangehrmAdminPlugin/test/fixtures/JobSpecificationAttachmentAuthorization.yml';

        TestDataService::truncateSpecificTables([
            JobSpecificationAttachment::class,
            ReportTo::class,
            User::class,
            Employee::class,
            JobTitle::class,
            ReportingMethod::class,
            UserRole::class,
        ]);

        TestDataService::populate($this->fixture);
    }

    private function createManagerWithCoreServices(): BasicUserRoleManager
    {
        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => new EmployeeService(),
                Services::USER_SERVICE => new UserService(),
                Services::CLASS_HELPER => new ClassHelper(),
                Services::PROJECT_SERVICE => new ProjectService(),
                Services::CONFIG_SERVICE => new ConfigService(),
            ]
        );

        return new BasicUserRoleManager();
    }

    public function testAdminCanAccessAllJobSpecificationAttachments(): void
    {
        $manager = $this->createManagerWithCoreServices();

        $authUser = $this->getMockBuilder(AuthUser::class)
            ->onlyMethods(['getEmpNumber'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->method('getEmpNumber')->willReturn(null);
        $this->getContainer()->set(Services::AUTH_USER, $authUser);

        $adminUser = $this->getEntityManager()->getRepository(User::class)->find(1);
        $this->assertNotNull($adminUser);
        $manager->setUser($adminUser);

        $this->assertTrue(
            $manager->isEntityAccessible(JobSpecificationAttachment::class, 1)
        );
        $this->assertTrue(
            $manager->isEntityAccessible(JobSpecificationAttachment::class, 2)
        );
    }

    public function testEssCanAccessOnlyOwnJobTitleJobSpecificationAttachment(): void
    {
        $manager = $this->createManagerWithCoreServices();

        $authUser = $this->getMockBuilder(AuthUser::class)
            ->onlyMethods(['getEmpNumber'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->method('getEmpNumber')->willReturn(1);
        $this->getContainer()->set(Services::AUTH_USER, $authUser);

        $essUser = $this->getEntityManager()->getRepository(User::class)->find(2);
        $this->assertNotNull($essUser);
        $manager->setUser($essUser);

        $this->assertTrue(
            $manager->isEntityAccessible(JobSpecificationAttachment::class, 1)
        );
        $this->assertFalse(
            $manager->isEntityAccessible(JobSpecificationAttachment::class, 2)
        );
    }

    public function testSupervisorCanAccessOwnAndSubordinateJobSpecificationAttachments(): void
    {
        $manager = $this->createManagerWithCoreServices();

        $authUser = $this->getMockBuilder(AuthUser::class)
            ->onlyMethods(['getEmpNumber'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->method('getEmpNumber')->willReturn(3);
        $this->getContainer()->set(Services::AUTH_USER, $authUser);

        $supervisorUser = $this->getEntityManager()->getRepository(User::class)->find(3);
        $this->assertNotNull($supervisorUser);
        $manager->setUser($supervisorUser);

        $this->assertTrue(
            $manager->isEntityAccessible(JobSpecificationAttachment::class, 1)
        );
        $this->assertTrue(
            $manager->isEntityAccessible(JobSpecificationAttachment::class, 2)
        );
    }
}
