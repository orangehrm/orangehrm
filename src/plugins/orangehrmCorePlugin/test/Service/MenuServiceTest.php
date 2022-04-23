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

namespace OrangeHRM\Tests\Core\Service;

use OrangeHRM\Admin\Service\UserService;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Authorization\Manager\UserRoleManagerFactory;
use OrangeHRM\Core\Dto\AttributeBag;
use OrangeHRM\Core\Helper\ModuleScreenHelper;
use OrangeHRM\Core\Service\ConfigService;
use OrangeHRM\Core\Service\MenuService;
use OrangeHRM\Entity\I18NLangString;
use OrangeHRM\Entity\I18NLanguage;
use OrangeHRM\Entity\I18NTranslation;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Http\RequestStack;
use OrangeHRM\Framework\Services;
use OrangeHRM\I18N\Service\I18NHelper;
use OrangeHRM\Pim\Service\EmployeeService;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\Mock\MockAuthUser;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Core
 * @group Service
 */
class MenuServiceTest extends KernelTestCase
{
    private MenuService $menuService;

    public static function setUpBeforeClass(): void
    {
        TestDataService::populate(Config::get(Config::TEST_DIR) . '/phpunit/fixtures/MenuItem.yaml', true);
        TestDataService::populate(
            Config::get(Config::PLUGINS_DIR) . '/orangehrmCorePlugin/test/fixtures/MenuService.yaml',
            true
        );
        TestDataService::truncateSpecificTables([I18NTranslation::class, I18NLangString::class, I18NLanguage::class]);
    }

    protected function setUp(): void
    {
        $this->menuService = new MenuService();
        Config::set(Config::I18N_ENABLED, false);
    }

    protected function tearDown(): void
    {
        Config::set(Config::I18N_ENABLED, true);
    }

    public function testGetMenuItemsForAdmin(): void
    {
        ModuleScreenHelper::resetCurrentModuleAndScreen();
        $requestStack = new RequestStack();
        $request = Request::create('http://example.com/admin/viewSystemUsers');
        $requestStack->push($request);

        $attributeBag = new AttributeBag();
        $authUser = $this->getMockBuilder(MockAuthUser::class)
            ->onlyMethods(['getUserId', 'setAttribute', 'getAttribute', 'hasAttribute'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->expects($this->once())
            ->method('getUserId')
            ->willReturn(1);
        $authUser->expects($this->exactly(3))
            ->method('setAttribute')
            ->willReturnCallback(function (string $name, $value) use ($attributeBag) {
                $attributeBag->set($name, $value);
            });
        $authUser->expects($this->exactly(5))
            ->method('getAttribute')
            ->willReturnCallback(function (string $name, $default = null) use ($attributeBag) {
                return $attributeBag->get($name, $default);
            });
        $authUser->expects($this->exactly(4))
            ->method('hasAttribute')
            ->willReturnCallback(function (string $name) use ($attributeBag) {
                return $attributeBag->has($name);
            });

        $this->createKernelWithMockServices([
            Services::REQUEST_STACK => $requestStack,
            Services::AUTH_USER => $authUser,
            Services::CONFIG_SERVICE => new ConfigService(),
            Services::USER_SERVICE => new UserService(),
            Services::EMPLOYEE_SERVICE => new EmployeeService(),
            Services::I18N_HELPER => new I18NHelper(),
        ]);
        $this->getContainer()->register(Services::USER_ROLE_MANAGER)
            ->setFactory([UserRoleManagerFactory::class, 'getNewUserRoleManager']);

        // Calling twice to check menu caching
        list($sidePanelMenuItems, $topMenuItems) = $this->menuService->getMenuItems('');
        list($sidePanelMenuItems, $topMenuItems) = $this->menuService->getMenuItems('');
        $this->assertEquals([
            [
                'id' => 1,
                'name' => 'Admin',
                'url' => '/admin/viewAdminModule',
                'icon' => 'admin',
                'active' => true,
            ],
            [
                'id' => 30,
                'name' => 'PIM',
                'url' => '/pim/viewPimModule',
                'icon' => 'pim',
            ],
            [
                'id' => 41,
                'name' => 'Leave',
                'url' => '/leave/viewLeaveModule',
                'icon' => 'leave',
            ],
            [
                'id' => 52,
                'name' => 'Time',
                'url' => '/time/viewTimeModule',
                'icon' => 'time',
            ],
            [
                'id' => 65,
                'name' => 'Recruitment',
                'url' => '/recruitment/viewRecruitmentModule',
                'icon' => 'recruitment',
            ],
            [
                'id' => 40,
                'name' => 'My Info',
                'url' => '/pim/viewMyDetails',
                'icon' => 'myinfo',
            ],
            [
                'id' => 83,
                'name' => 'Performance',
                'url' => '/performance/viewPerformanceModule',
                'icon' => 'performance',
            ],
            [
                'id' => 82,
                'name' => 'Dashboard',
                'url' => '/dashboard/index',
                'icon' => 'dashboard',
            ],
            [
                'id' => 93,
                'name' => 'Directory',
                'url' => '/directory/viewDirectory',
                'icon' => 'directory',
            ],
            [
                'id' => 96,
                'name' => 'Maintenance',
                'url' => '/maintenance/purgeEmployee',
                'icon' => 'maintenance',
            ],
            [
                'id' => 101,
                'name' => 'Buzz',
                'url' => '/buzz/viewBuzz',
                'icon' => 'buzz',
            ]
        ], $sidePanelMenuItems);
        $this->assertEquals([
            [
                'id' => 2,
                'name' => 'User Management',
                'url' => '#',
                'children' => [
                    [
                        'id' => 81,
                        'name' => 'Users',
                        'url' => '/admin/viewSystemUsers',
                        'active' => true,
                    ]
                ],
                'active' => true,
            ],
            [
                'id' => 6,
                'name' => 'Job',
                'url' => '#',
                'children' => [
                    [
                        'id' => 7,
                        'name' => 'Job Titles',
                        'url' => '/admin/viewJobTitleList',
                    ],
                    [
                        'id' => 8,
                        'name' => 'Pay Grades',
                        'url' => '/admin/viewPayGrades',
                    ],
                    [
                        'id' => 9,
                        'name' => 'Employment Status',
                        'url' => '/admin/employmentStatus',
                    ],
                    [
                        'id' => 10,
                        'name' => 'Job Categories',
                        'url' => '/admin/jobCategory',
                    ],
                    [
                        'id' => 11,
                        'name' => 'Work Shifts',
                        'url' => '/admin/workShift',
                    ]
                ],
            ],
            [
                'id' => 12,
                'name' => 'Organization',
                'url' => '#',
                'children' => [
                    [
                        'id' => 13,
                        'name' => 'General Information',
                        'url' => '/admin/viewOrganizationGeneralInformation',
                    ],
                    [
                        'id' => 14,
                        'name' => 'Locations',
                        'url' => '/admin/viewLocations',
                    ],
                    [
                        'id' => 15,
                        'name' => 'Structure',
                        'url' => '/admin/viewCompanyStructure',
                    ]
                ],
            ],
            [
                'id' => 16,
                'name' => 'Qualifications',
                'url' => '#',
                'children' => [
                    [
                        'id' => 17,
                        'name' => 'Skills',
                        'url' => '/admin/viewSkills',
                    ],
                    [
                        'id' => 18,
                        'name' => 'Education',
                        'url' => '/admin/viewEducation',
                    ],
                    [
                        'id' => 19,
                        'name' => 'Licenses',
                        'url' => '/admin/viewLicenses',
                    ],
                    [
                        'id' => 20,
                        'name' => 'Languages',
                        'url' => '/admin/viewLanguages',
                    ],
                    [
                        'id' => 21,
                        'name' => 'Memberships',
                        'url' => '/admin/membership',
                    ]
                ],
            ],
            [
                'id' => 22,
                'name' => 'Nationalities',
                'url' => '/admin/nationality',
                'children' => [],
            ],
            [
                'id' => 103,
                'name' => 'Corporate Branding',
                'url' => '/admin/addTheme',
                'children' => []
            ],
            [
                'id' => 23,
                'name' => 'Configuration',
                'url' => '#',
                'children' => [
                    [
                        'id' => 24,
                        'name' => 'Email Configuration',
                        'url' => '/admin/listMailConfiguration',
                    ],
                    [
                        'id' => 25,
                        'name' => 'Email Subscriptions',
                        'url' => '/admin/viewEmailNotification',
                    ],
                    [
                        'id' => 27,
                        'name' => 'Localization',
                        'url' => '/admin/localization',
                    ],
                    [
                        'id' => 102,
                        'name' => 'Language Packages',
                        'url' => '/admin/languagePackage',
                    ],
                    [
                        'id' => 28,
                        'name' => 'Modules',
                        'url' => '/admin/viewModules',
                    ],
                    [
                        'id' => 94,
                        'name' => 'Social Media Authentication',
                        'url' => '/admin/openIdProvider',
                    ],
                    [
                        'id' => 95,
                        'name' => 'Register OAuth Client',
                        'url' => '/admin/registerOAuthClient',
                    ],
                ],
            ],
        ], $topMenuItems);
    }

    public function testGetMenuItemsForSupervisor(): void
    {
        ModuleScreenHelper::resetCurrentModuleAndScreen();
        $requestStack = new RequestStack();
        $request = Request::create('http://example.com/pim/viewEmployeeList');
        $requestStack->push($request);

        $attributeBag = new AttributeBag();
        $authUser = $this->getMockBuilder(MockAuthUser::class)
            ->onlyMethods(['getUserId', 'setAttribute', 'getAttribute', 'hasAttribute'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->expects($this->once())
            ->method('getUserId')
            ->willReturn(4);
        $authUser->expects($this->exactly(3))
            ->method('setAttribute')
            ->willReturnCallback(function (string $name, $value) use ($attributeBag) {
                $attributeBag->set($name, $value);
            });
        $authUser->expects($this->exactly(5))
            ->method('getAttribute')
            ->willReturnCallback(function (string $name, $default = null) use ($attributeBag) {
                return $attributeBag->get($name, $default);
            });
        $authUser->expects($this->exactly(4))
            ->method('hasAttribute')
            ->willReturnCallback(function (string $name) use ($attributeBag) {
                return $attributeBag->has($name);
            });

        $this->createKernelWithMockServices([
            Services::REQUEST_STACK => $requestStack,
            Services::AUTH_USER => $authUser,
            Services::CONFIG_SERVICE => new ConfigService(),
            Services::USER_SERVICE => new UserService(),
            Services::EMPLOYEE_SERVICE => new EmployeeService(),
            Services::I18N_HELPER => new I18NHelper(),
        ]);
        $this->getContainer()->register(Services::USER_ROLE_MANAGER)
            ->setFactory([UserRoleManagerFactory::class, 'getNewUserRoleManager']);

        // Calling twice to check menu caching
        list($sidePanelMenuItems, $topMenuItems) = $this->menuService->getMenuItems('');
        list($sidePanelMenuItems, $topMenuItems) = $this->menuService->getMenuItems('');
        $this->assertEquals([
            [
                'id' => 30,
                'name' => 'PIM',
                'url' => '/pim/viewPimModule',
                'icon' => 'pim',
                'active' => true,
            ],
            [
                'id' => 41,
                'name' => 'Leave',
                'url' => '/leave/viewLeaveModule',
                'icon' => 'leave',
            ],
            [
                'id' => 52,
                'name' => 'Time',
                'url' => '/time/viewTimeModule',
                'icon' => 'time',
            ],
            [
                'id' => 40,
                'name' => 'My Info',
                'url' => '/pim/viewMyDetails',
                'icon' => 'myinfo',
            ],
            [
                'id' => 83,
                'name' => 'Performance',
                'url' => '/performance/viewPerformanceModule',
                'icon' => 'performance',
            ],
            [
                'id' => 82,
                'name' => 'Dashboard',
                'url' => '/dashboard/index',
                'icon' => 'dashboard',
            ],
            [
                'id' => 93,
                'name' => 'Directory',
                'url' => '/directory/viewDirectory',
                'icon' => 'directory',
            ],
            [
                'id' => 101,
                'name' => 'Buzz',
                'url' => '/buzz/viewBuzz',
                'icon' => 'buzz',
            ]
        ], $sidePanelMenuItems);
        $this->assertEquals([
            [
                'id' => 37,
                'name' => 'Employee List',
                'url' => '/pim/viewEmployeeList',
                'children' => [],
                'active' => true,
            ],
        ], $topMenuItems);
    }
}
