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

namespace OrangeHRM\Tests\Core\Authorization\Manager;

use OrangeHRM\Admin\Service\UserService;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Authorization\Dao\HomePageDao;
use OrangeHRM\Core\Authorization\Dto\DataGroupPermissionFilterParams;
use OrangeHRM\Core\Authorization\Dto\ResourcePermission;
use OrangeHRM\Core\Authorization\Manager\BasicUserRoleManager;
use OrangeHRM\Core\Authorization\Service\ScreenPermissionService;
use OrangeHRM\Core\Helper\ClassHelper;
use OrangeHRM\Core\HomePage\HomePageEnablerInterface;
use OrangeHRM\Core\Service\ConfigService;
use OrangeHRM\Core\Service\TextHelperService;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\HomePage;
use OrangeHRM\Entity\Location;
use OrangeHRM\Entity\Module;
use OrangeHRM\Entity\ModuleDefaultPage;
use OrangeHRM\Entity\Project;
use OrangeHRM\Entity\ProjectAdmin;
use OrangeHRM\Entity\User;
use OrangeHRM\Entity\UserRole;
use OrangeHRM\Framework\Services;
use OrangeHRM\Pim\Service\EmployeeService;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;
use OrangeHRM\Time\Service\ProjectService;

/**
 * @group Core
 * @group Manager
 */
class BasicUserRoleManagerTest extends KernelTestCase
{
    /**
     * @var BasicUserRoleManager
     */
    private BasicUserRoleManager $manager;

    /**
     * @var string
     */
    private string $fixture;

    /**
     * Set up method
     */
    protected function setUp(): void
    {
        $this->fixture = Config::get(
            Config::PLUGINS_DIR
        ) . '/orangehrmCorePlugin/test/fixtures/BasicUserRoleManager.yml';
        TestDataService::truncateSpecificTables(
            [User::class, Project::class, ProjectAdmin::class]
        ); //'JobCandidate', 'JobVacancy', 'JobInterview'
        TestDataService::populate($this->fixture);

        $this->createKernelWithMockServices([Services::CLASS_HELPER => new ClassHelper()]);
        $this->manager = new BasicUserRoleManager();
    }

    public function testGetAccessibleEmployeeIdsExcludeIncludeRoles(): void
    {
        $users = TestDataService::loadObjectList(User::class, $this->fixture, 'User');
        $allEmployees = TestDataService::loadObjectList(Employee::class, $this->fixture, 'Employee');

        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => new EmployeeService(),
                Services::USER_SERVICE => new UserService(),
                Services::PROJECT_SERVICE => new ProjectService(),
            ]
        );

        // Default Admin user  (no employee)
        $defaultAdmin = $users[5];
        $this->manager->setUser($defaultAdmin);
        $result = $this->manager->getAccessibleEntityIds(Employee::class);
        $expected = $this->getEmployeeIds($allEmployees);
        $this->assertEquals(count($expected), count($result));
        $this->compareArrays($expected, $result);

        // Exclude Supervisor Role
        $result = $this->manager->getAccessibleEntityIds(Employee::class, null, null, ['Supervisor']);
        $this->assertEquals(count($expected), count($result));
        $this->compareArrays($expected, $result);

        // Include Admin Role
        $result = $this->manager->getAccessibleEntityIds(Employee::class, null, null, [], ['Admin']);
        $this->assertEquals(count($expected), count($result));
        $this->compareArrays($expected, $result);

        // Exclude Admin Role
        $result = $this->manager->getAccessibleEntityIds(Employee::class, null, null, ['Admin']);
        $this->assertEquals(0, count($result));

        $authUser = $this->getMockBuilder(\OrangeHRM\Authentication\Auth\User::class)
            ->onlyMethods(['getEmpNumber'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->expects($this->exactly(2))
            ->method('getEmpNumber')
            ->willReturn(4);
        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => new EmployeeService(),
                Services::USER_SERVICE => new UserService(),
                Services::AUTH_USER => $authUser,
                Services::CONFIG_SERVICE => new ConfigService(),
                Services::PROJECT_SERVICE => new ProjectService(),
            ]
        );
        // Admin + supervisor
        $adminSupervisor = $users[3];
        $this->manager->setUser($adminSupervisor);
        $result = $this->manager->getAccessibleEntityIds(Employee::class);
        $expected = $this->getEmployeeIds($allEmployees);
        $this->assertEquals(count($expected), count($result));
        $this->compareArrays($expected, $result);

        // Exclude supervisor role
        $result = $this->manager->getAccessibleEntityIds(Employee::class, null, null, ['Supervisor']);
        $this->assertEquals(count($expected), count($result));
        $this->compareArrays($expected, $result);

        // Exclude Admin role
        $result = $this->manager->getAccessibleEntityIds(Employee::class, null, null, ['Admin']);
        $expected = [$allEmployees[2]->getEmpNumber()];
        $this->assertEquals(count($expected), count($result));

        $this->compareArrays($expected, $result);
    }

    public function testAreEmployeesAccessibleAdmin(): void
    {
        $users = TestDataService::loadObjectList(User::class, $this->fixture, 'User');
        $allEmployees = TestDataService::loadObjectList(Employee::class, $this->fixture, 'Employee');
        $allIds = $this->getEmployeeIds($allEmployees);

        $authUser = $this->getMockBuilder(\OrangeHRM\Authentication\Auth\User::class)
            ->onlyMethods(['getEmpNumber'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->expects($this->once())
            ->method('getEmpNumber')
            ->willReturn(2);
        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => new EmployeeService(),
                Services::USER_SERVICE => new UserService(),
                Services::AUTH_USER => $authUser,
                Services::CONFIG_SERVICE => new ConfigService(),
                Services::CLASS_HELPER => new ClassHelper(),
                Services::PROJECT_SERVICE => new ProjectService(),
            ]
        );
        // Default Admin user  (no employee)
        $defaultAdmin = $users[5];
        $this->manager->setUser($defaultAdmin);
        $this->assertTrue($this->manager->areEntitiesAccessible(Employee::class, $allIds));

        // test with unavailable emp number
        $empIds = array_merge($allIds, [11]);

        $this->assertFalse($this->manager->areEntitiesAccessible(Employee::class, $empIds));

        // Admin user
        $admin = $users[0];
        $this->manager->setUser($admin);
        $this->assertTrue($this->manager->areEntitiesAccessible(Employee::class, $allIds));

        // Admin + supervisor
        $adminSupervisor = $users[3];
        $this->manager->setUser($adminSupervisor);
        $this->assertTrue($this->manager->areEntitiesAccessible(Employee::class, $allIds));
    }

    public function testAreEmployeesAccessibleSupervisor(): void
    {
        $users = TestDataService::loadObjectList(User::class, $this->fixture, 'User');
        $allEmployees = TestDataService::loadObjectList(Employee::class, $this->fixture, 'Employee');
        $allIds = $this->getEmployeeIds($allEmployees);

        $authUser = $this->getMockBuilder(\OrangeHRM\Authentication\Auth\User::class)
            ->onlyMethods(['getEmpNumber'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->expects($this->exactly(4))
            ->method('getEmpNumber')
            ->willReturn(2);
        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => new EmployeeService(),
                Services::USER_SERVICE => new UserService(),
                Services::AUTH_USER => $authUser,
                Services::CONFIG_SERVICE => new ConfigService(),
                Services::CLASS_HELPER => new ClassHelper(),
                Services::PROJECT_SERVICE => new ProjectService(),
            ]
        );

        // Supervisor with one subordinate
        $supervisor = $users[1];
        $this->manager->setUser($supervisor);
        $expected = [$allEmployees[2]->getEmpNumber()];
        $this->assertTrue($this->manager->areEntitiesAccessible(Employee::class, $expected));
        $this->assertFalse($this->manager->areEntitiesAccessible(Employee::class, $allIds));
        $notAccessible = array_diff($allIds, $expected);
        $this->assertFalse($this->manager->areEntitiesAccessible(Employee::class, $notAccessible));
        $mixed = array_merge($notAccessible, $expected);
        $this->assertFalse($this->manager->areEntitiesAccessible(Employee::class, $mixed));

        $authUser = $this->getMockBuilder(\OrangeHRM\Authentication\Auth\User::class)
            ->onlyMethods(['getEmpNumber'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->expects($this->exactly(4))
            ->method('getEmpNumber')
            ->willReturn(6);
        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => new EmployeeService(),
                Services::USER_SERVICE => new UserService(),
                Services::AUTH_USER => $authUser,
                Services::CONFIG_SERVICE => new ConfigService(),
                Services::CLASS_HELPER => new ClassHelper(),
                Services::PROJECT_SERVICE => new ProjectService(),
            ]
        );
        // Supervisor with multiple subordinates
        $supervisor = $users[6];
        $this->manager->setUser($supervisor);
        $expected = [
            $allEmployees[0]->getEmpNumber(),
            $allEmployees[2]->getEmpNumber(),
            $allEmployees[3]->getEmpNumber(),
            $allEmployees[4]->getEmpNumber()
        ];
        $this->assertTrue($this->manager->areEntitiesAccessible(Employee::class, $expected));
        $this->assertFalse($this->manager->areEntitiesAccessible(Employee::class, $allIds));
        $notAccessible = array_diff($allIds, $expected);
        $this->assertFalse($this->manager->areEntitiesAccessible(Employee::class, $notAccessible));
        $mixed = array_merge($notAccessible, $expected);
        $this->assertFalse($this->manager->areEntitiesAccessible(Employee::class, $mixed));
    }

    public function testAreEmployeesAccessibleESS(): void
    {
        $users = TestDataService::loadObjectList(User::class, $this->fixture, 'User');
        $allEmployees = TestDataService::loadObjectList(Employee::class, $this->fixture, 'Employee');
        $allIds = $this->getEmployeeIds($allEmployees);

        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => new EmployeeService(),
                Services::CLASS_HELPER => new ClassHelper(),
                Services::PROJECT_SERVICE => new ProjectService(),
                Services::USER_SERVICE => new UserService(),
            ]
        );
        // ESS user
        $essUser = $users[4];
        $this->manager->setUser($essUser);
        $this->assertFalse($this->manager->areEntitiesAccessible(Employee::class, $allIds));
        foreach ($allIds as $id) {
            $this->assertFalse($this->manager->areEntitiesAccessible(Employee::class, [$id]));
        }
    }

    public function testIsEmployeeAccessibleAdmin(): void
    {
        $users = TestDataService::loadObjectList(User::class, $this->fixture, 'User');
        $allEmployees = TestDataService::loadObjectList(Employee::class, $this->fixture, 'Employee');
        $allIds = $this->getEmployeeIds($allEmployees);

        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => new EmployeeService(),
                Services::USER_SERVICE => new UserService(),
                Services::CLASS_HELPER => new ClassHelper(),
                Services::PROJECT_SERVICE => new ProjectService(),
            ]
        );
        // Default Admin user  (no employee)
        $defaultAdmin = $users[5];
        $this->manager->setUser($defaultAdmin);
        foreach ($allIds as $id) {
            $this->assertTrue($this->manager->isEntityAccessible(Employee::class, $id));
        }

        // Admin user
        $admin = $users[0];
        $this->manager->setUser($admin);
        foreach ($allIds as $id) {
            $this->assertTrue($this->manager->isEntityAccessible(Employee::class, $id));
        }

        $authUser = $this->getMockBuilder(\OrangeHRM\Authentication\Auth\User::class)
            ->onlyMethods(['getEmpNumber'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->expects($this->exactly(6))
            ->method('getEmpNumber')
            ->willReturn(4);
        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => new EmployeeService(),
                Services::USER_SERVICE => new UserService(),
                Services::AUTH_USER => $authUser,
                Services::CONFIG_SERVICE => new ConfigService(),
                Services::CLASS_HELPER => new ClassHelper(),
                Services::PROJECT_SERVICE => new ProjectService(),
            ]
        );
        // Admin + supervisor
        $adminSupervisor = $users[3];
        $this->manager->setUser($adminSupervisor);
        foreach ($allIds as $id) {
            $this->assertTrue($this->manager->isEntityAccessible(Employee::class, $id));
        }
    }

    public function testIsEmployeeAccessibleSupervisor(): void
    {
        $users = TestDataService::loadObjectList(User::class, $this->fixture, 'User');
        $allEmployees = TestDataService::loadObjectList(Employee::class, $this->fixture, 'Employee');
        $allIds = $this->getEmployeeIds($allEmployees);

        $authUser = $this->getMockBuilder(\OrangeHRM\Authentication\Auth\User::class)
            ->onlyMethods(['getEmpNumber'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->expects($this->exactly(6))
            ->method('getEmpNumber')
            ->willReturn(2);
        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => new EmployeeService(),
                Services::USER_SERVICE => new UserService(),
                Services::AUTH_USER => $authUser,
                Services::CONFIG_SERVICE => new ConfigService(),
                Services::CLASS_HELPER => new ClassHelper(),
                Services::PROJECT_SERVICE => new ProjectService(),
            ]
        );
        // Supervisor with one subordinate
        $supervisor = $users[1];
        $this->manager->setUser($supervisor);
        $expected = [$allEmployees[2]->getEmpNumber()];
        foreach ($allIds as $id) {
            if (in_array($id, $expected)) {
                $this->assertTrue($this->manager->isEntityAccessible(Employee::class, $id));
            } else {
                $this->assertFalse($this->manager->isEntityAccessible(Employee::class, $id));
            }
        }

        $authUser = $this->getMockBuilder(\OrangeHRM\Authentication\Auth\User::class)
            ->onlyMethods(['getEmpNumber'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->expects($this->exactly(6))
            ->method('getEmpNumber')
            ->willReturn(6);
        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => new EmployeeService(),
                Services::USER_SERVICE => new UserService(),
                Services::AUTH_USER => $authUser,
                Services::CONFIG_SERVICE => new ConfigService(),
                Services::CLASS_HELPER => new ClassHelper(),
                Services::PROJECT_SERVICE => new ProjectService(),
            ]
        );
        // Supervisor with multiple subordinates
        $supervisor = $users[6];
        $this->manager->setUser($supervisor);
        $expected = [
            $allEmployees[0]->getEmpNumber(),
            $allEmployees[2]->getEmpNumber(),
            $allEmployees[3]->getEmpNumber(),
            $allEmployees[4]->getEmpNumber()
        ];
        foreach ($allIds as $id) {
            if (in_array($id, $expected)) {
                $this->assertTrue($this->manager->isEntityAccessible(Employee::class, $id));
            } else {
                $this->assertFalse($this->manager->isEntityAccessible(Employee::class, $id));
            }
        }
    }

    public function testIsEmployeeAccessibleESS(): void
    {
        $users = TestDataService::loadObjectList(User::class, $this->fixture, 'User');
        $allEmployees = TestDataService::loadObjectList(Employee::class, $this->fixture, 'Employee');
        $allIds = $this->getEmployeeIds($allEmployees);

        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => new EmployeeService(),
                Services::CLASS_HELPER => new ClassHelper(),
                Services::PROJECT_SERVICE => new ProjectService(),
                Services::USER_SERVICE => new UserService(),
            ]
        );
        // ESS user
        $essUser = $users[4];
        $this->manager->setUser($essUser);
        foreach ($allIds as $id) {
            $this->assertFalse($this->manager->isEntityAccessible(Employee::class, $id));
        }
    }

    public function xtestGetAccessibleEmployeesAdmin(): void
    {
        $users = TestDataService::loadObjectList(User::class, $this->fixture, 'User');
        $allEmployees = TestDataService::loadObjectList(Employee::class, $this->fixture, 'Employee');

        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => new EmployeeService(),
                Services::USER_SERVICE => new UserService(),
                Services::CLASS_HELPER => new ClassHelper(),
                Services::TEXT_HELPER_SERVICE => new TextHelperService(),
                Services::PROJECT_SERVICE => new ProjectService(),
            ]
        );

        // Default Admin user  (no employee)
        $defaultAdmin = $users[5];
        $this->manager->setUser($defaultAdmin);
        $result = $this->manager->getAccessibleEntities(Employee::class);
        $expected = $this->getEmployeeIds($allEmployees);
        $this->assertEquals(count($expected), count($result));

        $this->checkEmployees($expected, $result);

        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => new EmployeeService(),
                Services::USER_SERVICE => new UserService(),
                Services::CLASS_HELPER => new ClassHelper(),
                Services::TEXT_HELPER_SERVICE => new TextHelperService(),
                Services::PROJECT_SERVICE => new ProjectService(),
            ]
        );
        // Admin user
        $admin = $users[0];
        $this->manager->setUser($admin);
        $result = $this->manager->getAccessibleEntities(Employee::class);
        $expected = $this->getEmployeeIds($allEmployees);
        $this->assertEquals(count($expected), count($result));
        $this->checkEmployees($expected, $result);

        $authUser = $this->getMockBuilder(\OrangeHRM\Authentication\Auth\User::class)
            ->onlyMethods(['getEmpNumber'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->expects($this->once())
            ->method('getEmpNumber')
            ->willReturn(4);
        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => new EmployeeService(),
                Services::USER_SERVICE => new UserService(),
                Services::AUTH_USER => $authUser,
                Services::CONFIG_SERVICE => new ConfigService(),
                Services::CLASS_HELPER => new ClassHelper(),
                Services::TEXT_HELPER_SERVICE => new TextHelperService(),
                Services::PROJECT_SERVICE => new ProjectService(),
            ]
        );
        // Admin + supervisor
        $adminSupervisor = $users[3];
        $this->manager->setUser($adminSupervisor);
        $result = $this->manager->getAccessibleEntities(Employee::class);
        $expected = $this->getEmployeeIds($allEmployees);
        $this->assertEquals(count($expected), count($result));
        $this->checkEmployees($expected, $result);
    }

    public function xtestGetAccessibleEmployeesSupervisor(): void
    {
        $users = TestDataService::loadObjectList(User::class, $this->fixture, 'User');
        $allEmployees = TestDataService::loadObjectList(Employee::class, $this->fixture, 'Employee');

        $authUser = $this->getMockBuilder(\OrangeHRM\Authentication\Auth\User::class)
            ->onlyMethods(['getEmpNumber'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->expects($this->once())
            ->method('getEmpNumber')
            ->willReturn(2);
        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => new EmployeeService(),
                Services::USER_SERVICE => new UserService(),
                Services::AUTH_USER => $authUser,
                Services::CONFIG_SERVICE => new ConfigService(),
                Services::CLASS_HELPER => new ClassHelper(),
                Services::PROJECT_SERVICE => new ProjectService(),
            ]
        );

        // Supervisor with one subordinate
        $supervisor = $users[1];
        $this->manager->setUser($supervisor);
        $expected = [$allEmployees[2]->getEmpNumber()];

        $result = $this->manager->getAccessibleEntities(Employee::class);
        $this->assertEquals(count($expected), count($result));
        $this->checkEmployees($expected, $result);

        $authUser = $this->getMockBuilder(\OrangeHRM\Authentication\Auth\User::class)
            ->onlyMethods(['getEmpNumber'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->expects($this->once())
            ->method('getEmpNumber')
            ->willReturn(6);
        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => new EmployeeService(),
                Services::USER_SERVICE => new UserService(),
                Services::AUTH_USER => $authUser,
                Services::CONFIG_SERVICE => new ConfigService(),
                Services::CLASS_HELPER => new ClassHelper(),
                Services::PROJECT_SERVICE => new ProjectService(),
            ]
        );
        // Supervisor with multiple subordinates
        $supervisor = $users[6];
        $this->manager->setUser($supervisor);
        $expected = [
            $allEmployees[0]->getEmpNumber(),
            $allEmployees[2]->getEmpNumber(),
            $allEmployees[3]->getEmpNumber(),
            $allEmployees[4]->getEmpNumber()
        ];

        $result = $this->manager->getAccessibleEntities(Employee::class);
        $this->assertEquals(count($expected), count($result));
        $this->checkEmployees($expected, $result);
    }

    public function xtestGetAccessibleEmployeesESS(): void
    {
        $users = TestDataService::loadObjectList(User::class, $this->fixture, 'User');

        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => new EmployeeService(),
                Services::CLASS_HELPER => new ClassHelper(),
                Services::PROJECT_SERVICE => new ProjectService(),
            ]
        );
        // Supervisor with one subordinate
        $essUser = $users[4];
        $this->manager->setUser($essUser);

        $result = $this->manager->getAccessibleEntities(Employee::class);
        $this->assertEquals(0, count($result));
    }

    public function testGetAccessibleEmployeeIdsAdmin(): void
    {
        $users = TestDataService::loadObjectList(User::class, $this->fixture, 'User');
        $allEmployees = TestDataService::loadObjectList(Employee::class, $this->fixture, 'Employee');

        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => new EmployeeService(),
                Services::CLASS_HELPER => new ClassHelper(),
                Services::PROJECT_SERVICE => new ProjectService(),
            ]
        );
        // Default Admin user  (no employee)
        $defaultAdmin = $users[5];
        $this->manager->setUser($defaultAdmin);
        $result = $this->manager->getAccessibleEntityIds(Employee::class);
        $expected = $this->getEmployeeIds($allEmployees);
        $this->assertEquals(count($expected), count($result));
        $this->compareArrays($expected, $result);

        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => new EmployeeService(),
                Services::USER_SERVICE => new UserService(),
                Services::CLASS_HELPER => new ClassHelper(),
                Services::PROJECT_SERVICE => new ProjectService(),
            ]
        );
        // Admin user
        $admin = $users[0];
        $this->manager->setUser($admin);
        $result = $this->manager->getAccessibleEntityIds(Employee::class);
        $expected = $this->getEmployeeIds($allEmployees);
        $this->assertEquals(count($expected), count($result));
        $this->compareArrays($expected, $result);

        $authUser = $this->getMockBuilder(\OrangeHRM\Authentication\Auth\User::class)
            ->onlyMethods(['getEmpNumber'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->expects($this->once())
            ->method('getEmpNumber')
            ->willReturn(4);
        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => new EmployeeService(),
                Services::USER_SERVICE => new UserService(),
                Services::AUTH_USER => $authUser,
                Services::CONFIG_SERVICE => new ConfigService(),
                Services::PROJECT_SERVICE => new ProjectService(),
            ]
        );
        // Admin + supervisor
        $adminSupervisor = $users[3];
        $this->manager->setUser($adminSupervisor);
        $result = $this->manager->getAccessibleEntityIds(Employee::class);
        $expected = $this->getEmployeeIds($allEmployees);
        $this->assertEquals(count($expected), count($result));
        $this->compareArrays($expected, $result);
    }

    public function testGetAccessibleEmployeeIdsSupervisor(): void
    {
        $users = TestDataService::loadObjectList(User::class, $this->fixture, 'User');
        $allEmployees = TestDataService::loadObjectList(Employee::class, $this->fixture, 'Employee');

        $authUser = $this->getMockBuilder(\OrangeHRM\Authentication\Auth\User::class)
            ->onlyMethods(['getEmpNumber'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->expects($this->once())
            ->method('getEmpNumber')
            ->willReturn(2);
        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => new EmployeeService(),
                Services::USER_SERVICE => new UserService(),
                Services::AUTH_USER => $authUser,
                Services::CONFIG_SERVICE => new ConfigService(),
                Services::CLASS_HELPER => new ClassHelper(),
                Services::PROJECT_SERVICE => new ProjectService(),
            ]
        );
        // Supervisor with one subordinate
        $supervisor = $users[1];
        $this->manager->setUser($supervisor);
        $expected = [$allEmployees[2]->getEmpNumber()];

        $result = $this->manager->getAccessibleEntityIds(Employee::class);
        $this->assertEquals(count($expected), count($result));
        $this->compareArrays($expected, $result);

        $authUser = $this->getMockBuilder(\OrangeHRM\Authentication\Auth\User::class)
            ->onlyMethods(['getEmpNumber'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->expects($this->once())
            ->method('getEmpNumber')
            ->willReturn(6);
        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => new EmployeeService(),
                Services::USER_SERVICE => new UserService(),
                Services::AUTH_USER => $authUser,
                Services::CONFIG_SERVICE => new ConfigService(),
                Services::CLASS_HELPER => new ClassHelper(),
                Services::PROJECT_SERVICE => new ProjectService(),
            ]
        );
        // Supervisor with multiple subordinates
        $supervisor = $users[6];
        $this->manager->setUser($supervisor);
        $expected = [
            $allEmployees[0]->getEmpNumber(),
            $allEmployees[2]->getEmpNumber(),
            $allEmployees[3]->getEmpNumber(),
            $allEmployees[4]->getEmpNumber()
        ];

        $result = $this->manager->getAccessibleEntityIds(Employee::class);
        $this->assertEquals(count($expected), count($result));
        $this->compareArrays($expected, $result);
    }

    public function testGetAccessibleEmployeeIdsESS(): void
    {
        $users = TestDataService::loadObjectList(User::class, $this->fixture, 'User');
        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => new EmployeeService(),
                Services::CLASS_HELPER => new ClassHelper(),
                Services::PROJECT_SERVICE => new ProjectService(),
                Services::USER_SERVICE => new UserService(),
            ]
        );

        // Supervisor with one subordinate
        $essUser = $users[4];
        $this->manager->setUser($essUser);

        $result = $this->manager->getAccessibleEntityIds(Employee::class);
        $this->assertEquals(0, count($result));
    }

    public function testGetAccessibleSystemUsersAdmin(): void
    {
        $users = TestDataService::loadObjectList(User::class, $this->fixture, 'User');
        $expected = $this->getObjectIds($users);
        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => new EmployeeService(),
                Services::USER_SERVICE => new UserService(),
                Services::CLASS_HELPER => new ClassHelper(),
                Services::PROJECT_SERVICE => new ProjectService(),
            ]
        );

        // Default Admin user  (no employee)
        $defaultAdmin = $users[5];
        $this->manager->setUser($defaultAdmin);
        $result = $this->manager->getAccessibleEntityIds(User::class);

        $this->assertEquals(count($expected), count($result));
        $this->compareArrays($expected, $result);

        // Admin user
        $admin = $users[0];
        $this->manager->setUser($admin);
        $result = $this->manager->getAccessibleEntityIds(User::class);

        $this->assertEquals(count($expected), count($result));
        $this->compareArrays($expected, $result);

        // Admin + supervisor
        $adminSupervisor = $users[3];
        $this->manager->setUser($adminSupervisor);
        $result = $this->manager->getAccessibleEntityIds(User::class);

        $this->assertEquals(count($expected), count($result));
        $this->compareArrays($expected, $result);
    }

    public function testGetAccessibleSystemUsersESSSupervisor(): void
    {
        $users = TestDataService::loadObjectList(User::class, $this->fixture, 'User');
        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => new EmployeeService(),
                Services::USER_SERVICE => new UserService(),
                Services::CLASS_HELPER => new ClassHelper(),
                Services::PROJECT_SERVICE => new ProjectService(),
            ]
        );

        // Supervisor with one subordinate
        $supervisor = $users[1];
        $this->manager->setUser($supervisor);

        $result = $this->manager->getAccessibleEntityIds(User::class);
        $this->assertEquals(0, count($result));

        // Supervisor with multiple subordinates
        $supervisor = $users[6];
        $this->manager->setUser($supervisor);

        $result = $this->manager->getAccessibleEntityIds(User::class);
        $this->assertEquals(0, count($result));

        // ESS user
        $essUser = $users[4];
        $this->manager->setUser($essUser);

        $result = $this->manager->getAccessibleEntityIds(User::class);
        $this->assertEquals(0, count($result));
    }

    public function testGetAccessibleOperationalCountriesESSSupervisor(): void
    {
        $users = TestDataService::loadObjectList(User::class, $this->fixture, 'User');
        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => new EmployeeService(),
                Services::USER_SERVICE => new UserService(),
                Services::CLASS_HELPER => new ClassHelper(),
                Services::PROJECT_SERVICE => new ProjectService(),
            ]
        );

        // Supervisor with one subordinate
        $supervisor = $users[1];
        $this->manager->setUser($supervisor);

        $result = $this->manager->getAccessibleEntityIds(User::class);
        $this->assertEquals(0, count($result));

        // Supervisor with multiple subordinates
        $supervisor = $users[6];
        $this->manager->setUser($supervisor);

        $result = $this->manager->getAccessibleEntityIds(User::class);
        $this->assertEquals(0, count($result));

        // ESS user
        $essUser = $users[4];
        $this->manager->setUser($essUser);

        $result = $this->manager->getAccessibleEntityIds(User::class);
        $this->assertEquals(0, count($result));
    }

    public function testGetAccessibleUserRolesAdmin(): void
    {
        $users = TestDataService::loadObjectList(User::class, $this->fixture, 'User');
        $userRoles = TestDataService::loadObjectList(UserRole::class, $this->fixture, 'UserRole');

        $expected = [];
        foreach ($userRoles as $role) {
            if ($role->isAssignable() == 1) {
                $expected[] = $role->getId();
            }
        }
        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => new EmployeeService(),
                Services::USER_SERVICE => new UserService(),
                Services::CLASS_HELPER => new ClassHelper(),
                Services::PROJECT_SERVICE => new ProjectService(),
            ]
        );

        // Default Admin user  (no employee)
        $defaultAdmin = $users[5];
        $this->manager->setUser($defaultAdmin);
        $result = $this->manager->getAccessibleEntityIds(UserRole::class);

        $this->assertEquals(count($expected), count($result));
        $this->compareArrays($expected, $result);

        // Admin user
        $admin = $users[0];
        $this->manager->setUser($admin);
        $result = $this->manager->getAccessibleEntityIds(UserRole::class);

        $this->assertEquals(count($expected), count($result));
        $this->compareArrays($expected, $result);

        // Admin + supervisor
        $adminSupervisor = $users[3];
        $this->manager->setUser($adminSupervisor);
        $result = $this->manager->getAccessibleEntityIds(UserRole::class);

        $this->assertEquals(count($expected), count($result));
        $this->compareArrays($expected, $result);
    }

    public function testGetAccessibleUserRolesESSSupervisor(): void
    {
        $users = TestDataService::loadObjectList(User::class, $this->fixture, 'User');
        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => new EmployeeService(),
                Services::USER_SERVICE => new UserService(),
                Services::CLASS_HELPER => new ClassHelper(),
                Services::PROJECT_SERVICE => new ProjectService(),
            ]
        );

        // Supervisor with one subordinate
        $supervisor = $users[1];
        $this->manager->setUser($supervisor);

        $result = $this->manager->getAccessibleEntityIds(UserRole::class);
        $this->assertEquals(0, count($result));

        // Supervisor with multiple subordinates
        $supervisor = $users[6];
        $this->manager->setUser($supervisor);

        $result = $this->manager->getAccessibleEntityIds(UserRole::class);
        $this->assertEquals(0, count($result));

        // ESS user
        $essUser = $users[4];
        $this->manager->setUser($essUser);

        $result = $this->manager->getAccessibleEntityIds(UserRole::class);
        $this->assertEquals(0, count($result));
    }

    public function testGetAccessibleLocationIdsAdmin(): void
    {
        $users = TestDataService::loadObjectList(User::class, $this->fixture, 'User');
        $locations = TestDataService::loadObjectList('Location', $this->fixture, 'Location');
        $expected = $this->getObjectIds($locations);

        $authUser = $this->getMockBuilder(\OrangeHRM\Authentication\Auth\User::class)
            ->onlyMethods(['getEmpNumber'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->expects($this->once())
            ->method('getEmpNumber')
            ->willReturn(4);
        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => new EmployeeService(),
                Services::USER_SERVICE => new UserService(),
                Services::CLASS_HELPER => new ClassHelper(),
                Services::PROJECT_SERVICE => new ProjectService(),
                Services::CONFIG_SERVICE => new ConfigService(),
                Services::AUTH_USER => $authUser,
            ]
        );
        // Default Admin user  (no employee)
        $defaultAdmin = $users[5];
        $this->manager->setUser($defaultAdmin);
        $result = $this->manager->getAccessibleEntityIds(Location::class);

        $this->assertEquals(count($expected), count($result));
        $this->compareArrays($expected, $result);

        // Admin user
        $admin = $users[0];
        $this->manager->setUser($admin);
        $result = $this->manager->getAccessibleEntityIds(Location::class);

        $this->assertEquals(count($expected), count($result));
        $this->compareArrays($expected, $result);

        // Admin + supervisor
        $adminSupervisor = $users[3];
        $this->manager->setUser($adminSupervisor);
        $result = $this->manager->getAccessibleEntityIds(Location::class);

        $this->assertEquals(count($expected), count($result));
        $this->compareArrays($expected, $result);
    }

    public function testGetAccessibleLocationIdsSupervisor(): void
    {
        $users = TestDataService::loadObjectList(User::class, $this->fixture, 'User');

        $authUser = $this->getMockBuilder(\OrangeHRM\Authentication\Auth\User::class)
            ->onlyMethods(['getEmpNumber'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->expects($this->exactly(2))
            ->method('getEmpNumber')
            ->willReturn(1);
        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => new EmployeeService(),
                Services::USER_SERVICE => new UserService(),
                Services::CLASS_HELPER => new ClassHelper(),
                Services::PROJECT_SERVICE => new ProjectService(),
                Services::CONFIG_SERVICE => new ConfigService(),
                Services::AUTH_USER => $authUser,
            ]
        );
        // Supervisor with one subordinate
        $supervisor = $users[1];
        $this->manager->setUser($supervisor);

        $result = $this->manager->getAccessibleEntityIds(Location::class);
        $this->assertEquals(0, count($result));


        // Supervisor with multiple subordinates
        $supervisor = $users[6];
        $this->manager->setUser($supervisor);

        $result = $this->manager->getAccessibleEntityIds(Location::class);
        $this->assertEquals(0, count($result));
    }

    public function testGetAccessibleLocationIdsESS(): void
    {
        $users = TestDataService::loadObjectList(User::class, $this->fixture, 'User');

        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => new EmployeeService(),
                Services::CLASS_HELPER => new ClassHelper(),
                Services::PROJECT_SERVICE => new ProjectService(),
                Services::USER_SERVICE => new UserService(),
            ]
        );
        $essUser = $users[4];
        $this->manager->setUser($essUser);

        $result = $this->manager->getAccessibleEntityIds(Location::class);
        $this->assertEquals(0, count($result));
    }

    public function testGetUserRoles(): void
    {
        $this->manager = new TestBasicUserRoleManager();
        $users = TestDataService::loadObjectList(User::class, $this->fixture, 'User');

        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => new EmployeeService(),
                Services::USER_SERVICE => new UserService(),
                Services::CLASS_HELPER => new ClassHelper(),
                Services::PROJECT_SERVICE => new ProjectService(),
            ]
        );

        // id is not preserved in loadObjectList()
        $users[0]->setId(11);

        // 0 - Admin (also ESS?)
        $roles = $this->manager->getUserRolesPublic($users[0]);
        $this->compareUserRoles(['Admin', 'ESS'], $roles);

        // 1 - ESS, Supervisor
        $users[1]->setId(12);
        $roles = $this->manager->getUserRolesPublic($users[1]);
        $this->compareUserRoles(['ESS', 'Supervisor'], $roles);

        // 2 - ESS
        $users[2]->setId(13);
        $roles = $this->manager->getUserRolesPublic($users[2]);
        $this->compareUserRoles(['ESS'], $roles);

        // 3 - Admin, Supervisor
        $users[0]->setId(14);
        $roles = $this->manager->getUserRolesPublic($users[3]);
        $this->compareUserRoles(['Admin', 'Supervisor', 'ESS'], $roles);

        // 4 - ESS
        $users[3]->setId(15);
        $roles = $this->manager->getUserRolesPublic($users[4]);
        $this->compareUserRoles(['ESS'], $roles);

        // 5 - Admin (Default admin) - does not have ESS role
        $users[4]->setId(16);
        $roles = $this->manager->getUserRolesPublic($users[5]);
        $this->compareUserRoles(['Admin'], $roles);
    }

    public function testGetScreenPermissions(): void
    {
        $userRole = new UserRole();
        $userRole->setId(1);
        $userRole->setName('Admin');

        $user = new User();
        $user->setId(1);
        $user->setEmployee(null);
        $user->setUserRole($userRole);

        $this->manager->setUser($user);

        $mockScreenPermissionService = $this->getMockBuilder(ScreenPermissionService::class)
            ->onlyMethods(['getScreenPermissions'])
            ->getMock();
        $permission = new ResourcePermission(true, false, true, false);

        $module = 'admin';
        $action = 'testAction';
        $roles = [$userRole];

        $mockScreenPermissionService->expects($this->once())
            ->method('getScreenPermissions')
            ->with($module, $action, $roles)
            ->will($this->returnValue($permission));

        $this->manager->setScreenPermissionService($mockScreenPermissionService);

        $this->createKernelWithMockServices([Services::CLASS_HELPER => new ClassHelper()]);
        $result = $this->manager->getScreenPermissions($module, $action);

        $this->assertEquals($permission, $result);
    }

    public function testFilterRoles()
    {
        $testManager = new TestBasicUserRoleManager();

        $userRoles = $this->__convertRoleNamesToObjects(['Supervisor', 'Admin', 'RegionalAdmin']);

        $rolesToExclude = [];
        $rolesToInclude = [];

        $this->createKernelWithMockServices([Services::CLASS_HELPER => new ClassHelper()]);
        $roles = $testManager->filterUserRolesPublic($userRoles, $rolesToExclude, $rolesToInclude);
        $this->assertEquals($userRoles, $roles);

        $rolesToExclude = ['Admin'];
        $rolesToInclude = [];

        $roles = $testManager->filterUserRolesPublic($userRoles, $rolesToExclude, $rolesToInclude);
        $this->assertEquals([$userRoles[0], $userRoles[2]], $roles);

        $rolesToExclude = [];
        $rolesToInclude = ['Supervisor', 'RegionalAdmin'];

        $roles = $testManager->filterUserRolesPublic($userRoles, $rolesToExclude, $rolesToInclude);
        $this->assertEquals([$userRoles[0], $userRoles[2]], $roles);

        $rolesToExclude = ['Admin', 'Supervisor', 'RegionalAdmin'];
        $rolesToInclude = [];

        $roles = $testManager->filterUserRolesPublic($userRoles, $rolesToExclude, $rolesToInclude);
        $this->assertEquals(0, count($roles));

        $rolesToExclude = ['NewRole'];
        $rolesToInclude = [];

        $roles = $testManager->filterUserRolesPublic($userRoles, $rolesToExclude, $rolesToInclude);
        $this->assertEquals($userRoles, $roles);
    }

    public function testFilterRolesSupervisorForEmployee()
    {
        $testManager = new TestBasicUserRoleManager();

        $rolesToExclude = [];
        $rolesToInclude = [];

        $userRoles = $this->__convertRoleNamesToObjects(['Supervisor', 'Admin', 'RegionalAdmin']);
        $roles = $testManager->filterUserRolesPublic($userRoles, $rolesToExclude, $rolesToInclude);
        $this->assertEquals($userRoles, $roles);

        $rolesToExclude = [];
        $rolesToInclude = [];

        $employee = new Employee();
        $employee->setEmpNumber(9);
        $userRole = new UserRole();
        $userRole->setId(2);
        $userRole->setName('ESS');
        $user = new User();
        $user->setId(11);
        $user->setEmployee($employee);
        $user->setUserRole($userRole);

        $employeeIds = [1, 2, 3];

        $employeeService = $this->getMockBuilder(EmployeeService::class)
            ->onlyMethods(['getSubordinateIdListBySupervisorId'])
            ->getMock();
        $employeeService->expects($this->once())
            ->method('getSubordinateIdListBySupervisorId')
            ->with($user->getEmpNumber())
            ->will($this->returnValue($employeeIds));

        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => $employeeService,
                Services::CLASS_HELPER => new ClassHelper(),
                Services::PROJECT_SERVICE => new ProjectService(),
            ]
        );
        $testManager->setUser($user);

        // Test that supervisor role is returned for Employee who is a subordinate
        $roles = $testManager->filterUserRolesPublic(
            $userRoles,
            $rolesToExclude,
            $rolesToInclude,
            [Employee::class => 3]
        );
        $this->assertEquals($userRoles, $roles);

        // Test that supervisor role is not returned for Employee who is not a subordinate
        $roles = $testManager->filterUserRolesPublic(
            $userRoles,
            $rolesToExclude,
            $rolesToInclude,
            [Employee::class => 13]
        );
        $this->assertEquals([$userRoles[1], $userRoles[2]], $roles);
    }

    public function testGetHomePage(): void
    {
        $userRoleIds = [1, 2, 3];

        $this->createKernelWithMockServices(
            [
                Services::USER_SERVICE => new UserService(),
                Services::EMPLOYEE_SERVICE => new EmployeeService(),
                Services::CLASS_HELPER => new ClassHelper(),
                Services::PROJECT_SERVICE => new ProjectService(),
            ]
        );

        $users = TestDataService::loadObjectList(User::class, $this->fixture, 'User');
        $adminUserSupervisor = $users[3];
        $this->manager->setUser($adminUserSupervisor);

        $userRole = new UserRole();
        $userRole->setId(1);
        $homePage1 = new HomePage();
        $homePage1->setId(4);
        $homePage1->setUserRole($userRole);
        $homePage1->setAction('pim/viewEmployeeTimesheets');
        $homePage1->setEnableClass(TestEnableClass::class);
        $homePage1->setPriority(50);

        $homePage2 = new HomePage();
        $homePage2->setId(5);
        $homePage2->setUserRole($userRole);
        $homePage2->setAction('pim/viewEmployeeList');
        $homePage2->setPriority(30);

        $homePage3 = new HomePage();
        $homePage3->setId(3);
        $homePage3->setUserRole($userRole);
        $homePage3->setAction('pim/viewSystemUsers');
        $homePage3->setPriority(30);

        $homePage4 = new HomePage();
        $homePage4->setId(1);
        $homePage4->setUserRole($userRole);
        $homePage4->setAction('pim/viewEmployeeList2');
        $homePage4->setPriority(10);

        $homePage5 = new HomePage();
        $homePage5->setId(2);
        $homePage5->setUserRole($userRole);
        $homePage5->setAction('pim/viewMyDetails');
        $homePage5->setPriority(0);

        $homePages = [
            $homePage1,
            $homePage2,
            $homePage3,
            $homePage4,
            $homePage5
        ];
        $mockDao = $this->getMockBuilder(HomePageDao::class)
            ->onlyMethods(['getHomePagesInPriorityOrder'])
            ->getMock();
        $mockDao->expects($this->once())
            ->method('getHomePagesInPriorityOrder')
            ->with($userRoleIds)
            ->will($this->returnValue($homePages));

        $this->manager->setHomePageDao($mockDao);
        $homePage = $this->manager->getHomePage();

        $this->assertEquals('pim/viewEmployeeList', $homePage);
    }

    public function testGetModuleDefaultPage(): void
    {
        $userRoleIds = [1, 2, 3];

        $this->createKernelWithMockServices(
            [
                Services::USER_SERVICE => new UserService(),
                Services::EMPLOYEE_SERVICE => new EmployeeService(),
                Services::CLASS_HELPER => new ClassHelper(),
                Services::PROJECT_SERVICE => new ProjectService(),
            ]
        );

        $users = TestDataService::loadObjectList(User::class, $this->fixture, 'User');
        $adminUserSupervisor = $users[3];
        $this->manager->setUser($adminUserSupervisor);

        $userRole = new UserRole();
        $userRole->setId(1);
        $module = new Module();
        $module->setId(5);
        $defaultPage1 = new ModuleDefaultPage();
        $defaultPage1->setId(4);
        $defaultPage1->setModule($module);
        $defaultPage1->setUserRole($userRole);
        $defaultPage1->setAction('pim/viewEmployeeTimesheets');
        $defaultPage1->setEnableClass(TestEnableClass::class);
        $defaultPage1->setPriority(50);

        $defaultPage2 = new ModuleDefaultPage();
        $defaultPage2->setId(5);
        $defaultPage2->setModule($module);
        $defaultPage2->setUserRole($userRole);
        $defaultPage2->setAction('pim/viewEmployeeList');
        $defaultPage2->setPriority(30);

        $defaultPage3 = new ModuleDefaultPage();
        $defaultPage3->setId(3);
        $defaultPage3->setModule($module);
        $defaultPage3->setUserRole($userRole);
        $defaultPage3->setAction('pim/viewSystemUsers');
        $defaultPage3->setPriority(30);

        $defaultPage4 = new ModuleDefaultPage();
        $defaultPage4->setId(1);
        $defaultPage4->setModule($module);
        $defaultPage4->setUserRole($userRole);
        $defaultPage4->setAction('pim/viewEmployeeList2');
        $defaultPage4->setPriority(10);

        $defaultPage5 = new ModuleDefaultPage();
        $defaultPage4->setId(2);
        $defaultPage4->setModule($module);
        $defaultPage4->setUserRole($userRole);
        $defaultPage4->setAction('pim/viewMyDetails');
        $defaultPage4->setPriority(0);

        $defaultPages = [
            $defaultPage1,
            $defaultPage2,
            $defaultPage3,
            $defaultPage4,
            $defaultPage5
        ];
        $mockDao = $this->getMockBuilder(HomePageDao::class)
            ->onlyMethods(['getModuleDefaultPagesInPriorityOrder'])
            ->getMock();
        $mockDao->expects($this->once())
            ->method('getModuleDefaultPagesInPriorityOrder')
            ->with('time', $userRoleIds)
            ->will($this->returnValue($defaultPages));

        $this->manager->setHomePageDao($mockDao);
        $homePage = $this->manager->getModuleDefaultPage('time');

        $this->assertEquals('pim/viewEmployeeList', $homePage);
    }

    /**
     * @param string[] $roleNames
     * @return UserRole[]
     */
    private function __convertRoleNamesToObjects(array $roleNames): array
    {
        $roles = [];

        foreach ($roleNames as $name) {
            $userRole = new UserRole();
            $userRole->setName($name);

            $roles[] = $userRole;
        }

        return $roles;
    }

    protected function compareUserRoles($expected, $actual): void
    {
        $this->assertEquals(count($expected), count($actual));
        foreach ($expected as $role) {
            $found = false;

            foreach ($actual as $roleObject) {
                if ($roleObject->getName() == $role) {
                    $found = true;
                    break;
                }
            }

            $this->assertTrue($found, 'Expected Role ' . $role . ' not found');
        }
    }

    protected function compareEmployees($expected, $actual): void
    {
        $this->assertEquals(count($expected), count($actual));

        foreach ($expected as $expectedEmployee) {
            $found = false;

            foreach ($actual as $employee) {
                if ($employee->getEmpNumber() == $expectedEmployee->getEmpNumber()) {
                    $found = true;
                    break;
                }
            }

            $this->assertTrue($found, 'Expected Employee (id = ' . $expectedEmployee->getEmpNumber() . ' not found');
        }
    }

    protected function compareArrays($expected, $actual)
    {
        $this->assertEquals(count($expected), count($actual));

        $diff = array_diff($expected, $actual);
        $this->assertEquals(0, count($diff), json_encode($diff));
    }

    protected function checkEmployees($expected, $actual)
    {
        $this->assertEquals(count($expected), count($actual));
        foreach ($expected as $id) {
            $this->assertTrue(isset($actual[$id]));
        }
    }

    protected function getEmployeeIds($employees)
    {
        $ids = [];

        foreach ($employees as $employee) {
            $ids[] = $employee->getEmpNumber();
        }

        return $ids;
    }

    protected function getEmployeePropertyList($employees, $properties)
    {
        $propertyList = [];

        foreach ($employees as $employee) {
            $propertyValueArray = [];
            foreach ($properties as $property) {
                $propertyValueArray[$property] = $employee["$property"];
            }
            $propertyList[$propertyValueArray['empNumber']] = $propertyValueArray;
        }

        return $propertyList;
    }

    protected function getObjectIds($users)
    {
        $ids = [];

        foreach ($users as $user) {
            $ids[] = $user->getId();
        }

        return $ids;
    }

    public function testGetAllowedActionsForAdminUserRole(): void
    {
        $users = TestDataService::loadObjectList(User::class, $this->fixture, 'User');
        $expected = [14, 15];

        $defaultAdmin = $users[5];
        $this->manager->setUser($defaultAdmin);

        $this->createKernelWithMockServices([Services::CLASS_HELPER => new ClassHelper()]);
        $workflow = 3;
        $state = 'ACTIVE';
        $result = $this->manager->getAllowedActions($workflow, $state);

        $this->assertEquals(2, count($result));
        foreach ($expected as $expectedId) {
            $found = false;
            foreach ($result as $workflowItem) {
                if ($workflowItem->getId() == $expectedId) {
                    $found = true;
                    break;
                }
            }
            $this->assertTrue($found);
        }
    }

    public function testIsActionAllowedForAdminAddEmployee(): void
    {
        $users = TestDataService::loadObjectList(User::class, $this->fixture, 'User');

        $defaultAdmin = $users[5];
        $this->manager->setUser($defaultAdmin);

        $this->createKernelWithMockServices([Services::CLASS_HELPER => new ClassHelper()]);
        $workflow = 3;
        $state = 'NOT EXIST';
        $action = '1';
        $isAllowed = $this->manager->isActionAllowed($workflow, $state, $action);

        $this->assertTrue($isAllowed);
    }

    public function testIsActionAllowedForAdminAddActiveEmployee(): void
    {
        $users = TestDataService::loadObjectList(User::class, $this->fixture, 'User');

        $defaultAdmin = $users[5];
        $this->manager->setUser($defaultAdmin);

        $this->createKernelWithMockServices([Services::CLASS_HELPER => new ClassHelper()]);
        $workflow = 3;
        $state = 'ACTIVE';
        $action = '1';
        $isAllowed = $this->manager->isActionAllowed($workflow, $state, $action);

        $this->assertTrue(!$isAllowed);
    }

    public function testGetEmployeesWithRole(): void
    {
        $this->createKernelWithMockServices(
            [
                Services::USER_SERVICE => new UserService(),
                Services::CLASS_HELPER => new ClassHelper(),
                Services::PROJECT_SERVICE => new ProjectService(),
            ]
        );
        $employees = $this->manager->getEmployeesWithRole('Admin');
        $this->assertEquals(2, count($employees));

        $expected = [1, 4];
        foreach ($employees as $employee) {
            $id = array_search($employee->getEmpNumber(), $expected);
            $this->assertTrue($id !== false);
            unset($expected[$id]);
        }
        $this->assertEquals(0, count($expected));
    }

    public function testGetActionableStatesValidActionsAndRole(): void
    {
        $users = TestDataService::loadObjectList(User::class, $this->fixture, 'User');

        $defaultAdmin = $users[5];
        $this->manager->setUser($defaultAdmin);

        $this->createKernelWithMockServices([Services::CLASS_HELPER => new ClassHelper()]);
        $workflow = 3;
        $actions = ['1', '2', '3'];
        $expected = ['ACTIVE', 'NOT EXIST'];

        $states = $this->manager->getActionableStates($workflow, $actions);
        $this->assertTrue(is_array($states));

        sort($states);
        $this->assertEquals($expected, $states);
    }

    public function testGetActionableStatesInvalidAction(): void
    {
        $users = TestDataService::loadObjectList(User::class, $this->fixture, 'User');

        $defaultAdmin = $users[5];
        $this->manager->setUser($defaultAdmin);

        $this->createKernelWithMockServices([Services::CLASS_HELPER => new ClassHelper()]);
        $workflow = 3;
        $actions = ['11'];
        $expected = [];

        $states = $this->manager->getActionableStates($workflow, $actions);
        $this->assertTrue(is_array($states));

        $this->assertEquals(0, count($states));
    }

    public function testGetActionableStatesUserRoleWithNoWorkflowAccess(): void
    {
        $this->createKernelWithMockServices(
            [
                Services::USER_SERVICE => new UserService(),
                Services::EMPLOYEE_SERVICE => new EmployeeService(),
                Services::CLASS_HELPER => new ClassHelper(),
                Services::PROJECT_SERVICE => new ProjectService(),
            ]
        );
        $users = TestDataService::loadObjectList(User::class, $this->fixture, 'User');

        $ess = $users[1];
        $this->manager->setUser($ess);

        $workflow = 3;
        $actions = ['1', '2', '3'];
        $expected = ['ACTIVE', 'NOT EXIST'];

        $states = $this->manager->getActionableStates($workflow, $actions);
        $this->assertTrue(is_array($states));

        $this->assertEquals(0, count($states));
    }

    public function testGetDataGroupPermissionCollectionForEss(): void
    {
        $users = TestDataService::loadObjectList(User::class, $this->fixture, 'User');

        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => new EmployeeService(),
                Services::USER_SERVICE => new UserService(),
                Services::CLASS_HELPER => new ClassHelper(),
                Services::PROJECT_SERVICE => new ProjectService(),
            ]
        );
        $ess = $users[1];
        $this->manager->setUser($ess);

        $dataGroupPermissionFilterParams = new DataGroupPermissionFilterParams();
        $permissionCollection = $this->manager->getDataGroupPermissionCollection($dataGroupPermissionFilterParams);
        $this->assertEquals(
            [
                'personal_information' => [
                    'canRead' => true,
                    'canCreate' => false,
                    'canUpdate' => false,
                    'canDelete' => false,
                ],
                'emergency_contacts' => [
                    'canRead' => true,
                    'canCreate' => false,
                    'canUpdate' => false,
                    'canDelete' => false,
                ],
            ],
            $permissionCollection->toArray()
        );
    }

    public function testGetDataGroupPermissionCollectionForAdmin(): void
    {
        $users = TestDataService::loadObjectList(User::class, $this->fixture, 'User');

        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => new EmployeeService(),
                Services::USER_SERVICE => new UserService(),
                Services::CLASS_HELPER => new ClassHelper(),
                Services::PROJECT_SERVICE => new ProjectService(),
            ]
        );
        $admin = $users[0];
        $this->manager->setUser($admin);

        $dataGroupPermissionFilterParams = new DataGroupPermissionFilterParams();
        $permissionCollection = $this->manager->getDataGroupPermissionCollection($dataGroupPermissionFilterParams);
        $this->assertEquals(
            [
                'personal_information' => [
                    'canRead' => true,
                    'canCreate' => true,
                    'canUpdate' => false,
                    'canDelete' => false,
                ],
            ],
            $permissionCollection->toArray()
        );
    }

    public function testGetDataGroupPermissionCollectionSelf(): void
    {
        $users = TestDataService::loadObjectList(User::class, $this->fixture, 'User');

        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => new EmployeeService(),
                Services::USER_SERVICE => new UserService(),
                Services::CLASS_HELPER => new ClassHelper(),
                Services::PROJECT_SERVICE => new ProjectService(),
            ]
        );
        $essUser = $users[0];
        $this->manager->setUser($essUser);

        $dataGroupPermissionFilterParams = new DataGroupPermissionFilterParams();
        $dataGroupPermissionFilterParams->setSelfPermissions(true);
        $permissionCollection = $this->manager->getDataGroupPermissionCollection($dataGroupPermissionFilterParams);
        $this->assertEquals(
            [
                'contact_details' => [
                    'canRead' => false,
                    'canCreate' => false,
                    'canUpdate' => true,
                    'canDelete' => false,
                ],
            ],
            $permissionCollection->toArray()
        );
    }

    public function testGetDataGroupPermissionCollectionForEssExcludeSupervisor(): void
    {
        $users = TestDataService::loadObjectList(User::class, $this->fixture, 'User');

        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => new EmployeeService(),
                Services::USER_SERVICE => new UserService(),
                Services::CLASS_HELPER => new ClassHelper(),
                Services::PROJECT_SERVICE => new ProjectService(),
            ]
        );
        $ess = $users[1];
        $this->manager->setUser($ess);

        $dataGroupPermissionFilterParams = new DataGroupPermissionFilterParams();
        $dataGroupPermissionFilterParams->setRolesToExclude(['Supervisor']);
        $permissionCollection = $this->manager->getDataGroupPermissionCollection($dataGroupPermissionFilterParams);
        $this->assertEquals(
            [
                'personal_information' => [
                    'canRead' => true,
                    'canCreate' => false,
                    'canUpdate' => false,
                    'canDelete' => false,
                ],
            ],
            $permissionCollection->toArray()
        );
    }

    public function testGetDataGroupPermissionCollectionForEssIncludeSupervisor(): void
    {
        $users = TestDataService::loadObjectList(User::class, $this->fixture, 'User');

        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => new EmployeeService(),
                Services::USER_SERVICE => new UserService(),
                Services::CLASS_HELPER => new ClassHelper(),
                Services::PROJECT_SERVICE => new ProjectService(),
            ]
        );
        $ess = $users[1];
        $this->manager->setUser($ess);

        $dataGroupPermissionFilterParams = new DataGroupPermissionFilterParams();
        $dataGroupPermissionFilterParams->setRolesToInclude(['Supervisor']);
        $permissionCollection = $this->manager->getDataGroupPermissionCollection($dataGroupPermissionFilterParams);
        $this->assertEquals(
            [
                'emergency_contacts' => [
                    'canRead' => true,
                    'canCreate' => false,
                    'canUpdate' => false,
                    'canDelete' => false,
                ]
            ],
            $permissionCollection->toArray()
        );
    }

    public function testGetDataGroupPermissionCollectionFilterDataGroups(): void
    {
        $users = TestDataService::loadObjectList(User::class, $this->fixture, 'User');

        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => new EmployeeService(),
                Services::USER_SERVICE => new UserService(),
                Services::CLASS_HELPER => new ClassHelper(),
                Services::PROJECT_SERVICE => new ProjectService(),
            ]
        );
        $ess = $users[1];
        $this->manager->setUser($ess);

        $dataGroupPermissionFilterParams = new DataGroupPermissionFilterParams();
        $dataGroupPermissionFilterParams->setDataGroups(['personal_information']);
        $permissionCollection = $this->manager->getDataGroupPermissionCollection($dataGroupPermissionFilterParams);
        $this->assertEquals(
            [
                'personal_information' => [
                    'canRead' => true,
                    'canCreate' => false,
                    'canUpdate' => false,
                    'canDelete' => false,
                ],
            ],
            $permissionCollection->toArray()
        );
    }

    public function testGetDataGroupPermissionCollectionWithEntities(): void
    {
        $users = TestDataService::loadObjectList(User::class, $this->fixture, 'User');

        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => new EmployeeService(),
                Services::USER_SERVICE => new UserService(),
                Services::CONFIG_SERVICE => new ConfigService(),
                Services::CLASS_HELPER => new ClassHelper(),
                Services::PROJECT_SERVICE => new ProjectService(),
            ]
        );
        $ess = $users[1];
        $this->manager->setUser($ess);

        $dataGroupPermissionFilterParams = new DataGroupPermissionFilterParams();
        $dataGroupPermissionFilterParams->setEntities([Employee::class => 3]);
        $permissionCollection = $this->manager->getDataGroupPermissionCollection($dataGroupPermissionFilterParams);
        $this->assertEquals(
            [
                'emergency_contacts' => [
                    'canRead' => true,
                    'canCreate' => false,
                    'canUpdate' => false,
                    'canDelete' => false,
                ],
            ],
            $permissionCollection->toArray()
        );
    }

    public function testGetDataGroupPermissionCollectionWithEntitiesSelf(): void
    {
        $users = TestDataService::loadObjectList(User::class, $this->fixture, 'User');

        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => new EmployeeService(),
                Services::USER_SERVICE => new UserService(),
                Services::CONFIG_SERVICE => new ConfigService(),
                Services::CLASS_HELPER => new ClassHelper(),
                Services::PROJECT_SERVICE => new ProjectService(),
            ]
        );
        $ess = $users[1];
        $this->manager->setUser($ess);

        $dataGroupPermissionFilterParams = new DataGroupPermissionFilterParams();
        $dataGroupPermissionFilterParams->setEntities([Employee::class => 2]);
        $permissionCollection = $this->manager->getDataGroupPermissionCollection($dataGroupPermissionFilterParams);
        $this->assertEquals(
            [
                'personal_information' => [
                    'canRead' => true,
                    'canCreate' => false,
                    'canUpdate' => false,
                    'canDelete' => false,
                ],
            ],
            $permissionCollection->toArray()
        );
    }
}

/* Extend class to get access to protected method */

class TestBasicUserRoleManager extends BasicUserRoleManager
{
    public function getUserRolesPublic($user)
    {
        return $this->computeUserRoles($user);
    }

    public function filterUserRolesPublic($userRoles, $rolesToExclude, $rolesToInclude, $entities = [])
    {
        return $this->filterRoles($userRoles, $rolesToExclude, $rolesToInclude, $entities);
    }
}

class TestEnableClass implements HomePageEnablerInterface
{
    public function isEnabled(User $user): bool
    {
        return false;
    }
}
