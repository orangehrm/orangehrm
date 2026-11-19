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

namespace OrangeHRM\Tests\Pim\Controller;

use OrangeHRM\Admin\Service\UserService;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Authorization\Helper\UserRoleManagerHelper;
use OrangeHRM\Core\Authorization\Manager\UserRoleManagerFactory;
use OrangeHRM\Core\Service\ConfigService;
use OrangeHRM\Framework\Services;
use OrangeHRM\Pim\Controller\File\EmployeeAttachmentController;
use OrangeHRM\Pim\Service\EmployeeService;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\Mock\MockAuthUser;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * The attachment file is served only when the user can read the attachments of the PIM screen
 * it belongs to ({screen}_attachment) for that employee.
 *
 * @group Pim
 * @group Controller
 */
class EmployeeAttachmentControllerTest extends KernelTestCase
{
    // userId => [empNumber, user role]
    private const USERS = [
        1 => [1, 'Admin'],
        2 => [2, 'ESS'],
        3 => [3, 'ESS'],
    ];

    protected function setUp(): void
    {
        parent::setUp();
        TestDataService::populate(
            Config::get(Config::PLUGINS_DIR) . '/orangehrmPimPlugin/test/fixtures/EmployeeScreenPermissionAPI.yml',
            true
        );
    }

    /**
     * @dataProvider dataProviderForTestHandle
     */
    public function testHandle(int $userId, int $empNumber, int $attachId, ?string $expectedContent): void
    {
        $this->createKernelForUser($userId);
        $request = $this->getHttpRequest([], [], ['empNumber' => $empNumber, 'attachId' => $attachId]);
        $response = (new EmployeeAttachmentController())->handle($request);

        if (is_null($expectedContent)) {
            $this->assertEquals(400, $response->getStatusCode());
            $this->assertEmpty($response->getContent());
        } else {
            $this->assertEquals(200, $response->getStatusCode());
            $this->assertEquals($expectedContent, $response->getContent());
        }
    }

    public function dataProviderForTestHandle(): array
    {
        return [
            'Admin downloads a subordinate\'s salary attachment' => [1, 3, 1, 'offer'],
            'Supervisor downloads a subordinate\'s personal attachment' => [2, 3, 2, 'cv'],
            'Supervisor cannot download a subordinate\'s salary attachment' => [2, 3, 1, null],
            'Supervisor downloads own salary attachment' => [2, 2, 1, 'offer'],
            'ESS downloads own salary attachment' => [3, 3, 1, 'offer'],
            'ESS cannot download the supervisor\'s salary attachment' => [3, 2, 1, null],
            // Job screen: the employment contract is stored with screen `contract` and read through job_details
            'Admin downloads a subordinate\'s employment contract' => [1, 3, 3, 'contract'],
            'Supervisor downloads a subordinate\'s employment contract' => [2, 3, 3, 'contract'],
            'ESS downloads own employment contract' => [3, 3, 3, 'contract'],
        ];
    }

    private function createKernelForUser(int $userId): void
    {
        [$empNumber, $userRoleName] = self::USERS[$userId];
        $authUser = $this->getMockBuilder(MockAuthUser::class)
            ->onlyMethods(['getUserId', 'getEmpNumber', 'getUserRoleName'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->method('getUserId')->willReturn($userId);
        $authUser->method('getEmpNumber')->willReturn($empNumber);
        $authUser->method('getUserRoleName')->willReturn($userRoleName);

        $this->createKernelWithMockServices([
            Services::AUTH_USER => $authUser,
            Services::EMPLOYEE_SERVICE => new EmployeeService(),
            Services::USER_SERVICE => new UserService(),
            Services::CONFIG_SERVICE => new ConfigService(),
            Services::USER_ROLE_MANAGER_HELPER => new UserRoleManagerHelper(),
        ]);
        $this->getContainer()->register(Services::USER_ROLE_MANAGER)
            ->setFactory([UserRoleManagerFactory::class, 'getNewUserRoleManager']);
    }
}
