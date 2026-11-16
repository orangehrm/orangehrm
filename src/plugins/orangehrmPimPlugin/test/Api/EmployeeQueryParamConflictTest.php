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

namespace OrangeHRM\Tests\Pim\Api;

use OrangeHRM\Admin\Service\UserService;
use OrangeHRM\Core\Authorization\Helper\UserRoleManagerHelper;
use OrangeHRM\Core\Service\ConfigService;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\EmployeeSalary;
use OrangeHRM\Entity\InterviewInterviewer;
use OrangeHRM\Entity\ProjectAdmin;
use OrangeHRM\Entity\Vacancy;
use OrangeHRM\Framework\Services;
use OrangeHRM\Pim\Api\EmployeePersonalDetailAPI;
use OrangeHRM\Pim\Api\EmployeeSalaryComponentAPI;
use OrangeHRM\Pim\Service\EmployeeSalaryService;
use OrangeHRM\Pim\Service\EmployeeService;
use OrangeHRM\Tests\Util\EndpointIntegrationTestCase;
use OrangeHRM\Tests\Util\Integration\RestControllerTestTrait;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * Employee endpoints read `empNumber` from the route. A query value that differs from it is refused (422), so the
 * accessible-employee and permission rules always check the employee the handler works on.
 *
 * @group Pim
 * @group APIv2
 */
class EmployeeQueryParamConflictTest extends EndpointIntegrationTestCase
{
    use RestControllerTestTrait;

    // Users and employees in EmployeeSalaryComponentAPI.yml
    private const USER_SAM = 2; // ESS, supervises Sue
    private const USER_SUE = 3; // ESS
    private const EMP_SAM = 2;
    private const EMP_SUE = 3;
    private const EMP_UMA = 4;
    private const SALARY_OF_SUE = 3;

    private const SERVICES = [
        Services::EMPLOYEE_SERVICE => EmployeeService::class,
        Services::EMPLOYEE_SALARY_SERVICE => EmployeeSalaryService::class,
        Services::CONFIG_SERVICE => ConfigService::class,
        Services::USER_SERVICE => UserService::class,
        Services::USER_ROLE_MANAGER_HELPER => UserRoleManagerHelper::class,
    ];

    protected function setUp(): void
    {
        $this->populateFixtures('EmployeeSalaryComponentAPI.yml', null, true);
        // Project admins, hiring managers and interviewers get extra roles; other test classes leave such rows
        TestDataService::truncateSpecificTables([ProjectAdmin::class, Vacancy::class, InterviewInterviewer::class]);
    }

    private function personalDetails(int $userId, string $method, int $empNumber, array $query, array $body = []): array
    {
        return $this->handleRestRequest(
            $userId,
            EmployeePersonalDetailAPI::class,
            $method,
            ['empNumber' => $empNumber],
            $query,
            $body,
            self::SERVICES,
            'empNumber'
        );
    }

    private function salaryComponents(int $userId, string $method, int $empNumber, array $query, array $body = []): array
    {
        return $this->handleRestRequest(
            $userId,
            EmployeeSalaryComponentAPI::class,
            $method,
            ['empNumber' => $empNumber],
            $query,
            $body,
            self::SERVICES
        );
    }

    private function getLastName(int $empNumber): string
    {
        return $this->getEntityManager()->find(Employee::class, $empNumber)->getLastName();
    }

    public function testEssReadsOwnPersonalDetails(): void
    {
        $data = $this->assertHandled($this->personalDetails(self::USER_SUE, 'GET', self::EMP_SUE, []));
        $this->assertSame('Subordinate', $data['lastName']);

        $data = $this->assertHandled(
            $this->personalDetails(self::USER_SUE, 'GET', self::EMP_SUE, ['empNumber' => (string)self::EMP_SUE])
        );
        $this->assertSame('Subordinate', $data['lastName']);
    }

    public function testEssCannotReadOtherPersonalDetailsWithOwnQueryValue(): void
    {
        $this->assertConflictRejected(
            $this->personalDetails(self::USER_SUE, 'GET', self::EMP_UMA, ['empNumber' => (string)self::EMP_SUE]),
            'empNumber'
        );
        // Without the query value the accessible-employee rule refuses it
        $this->assertConflictRejected($this->personalDetails(self::USER_SUE, 'GET', self::EMP_UMA, []), 'empNumber');
    }

    public function testEssCannotUpdateOtherPersonalDetailsWithOwnQueryValue(): void
    {
        $response = $this->personalDetails(
            self::USER_SUE,
            'PUT',
            self::EMP_UMA,
            ['empNumber' => (string)self::EMP_SUE],
            ['firstName' => 'Uma', 'lastName' => 'Changed']
        );

        $this->assertConflictRejected($response, 'empNumber');
        $this->assertSame('Unrelated', $this->getLastName(self::EMP_UMA));
        $this->assertSame('Subordinate', $this->getLastName(self::EMP_SUE));
    }

    public function testEssUpdatesOwnPersonalDetails(): void
    {
        $data = $this->assertHandled(
            $this->personalDetails(
                self::USER_SUE,
                'PUT',
                self::EMP_SUE,
                ['empNumber' => (string)self::EMP_SUE],
                ['firstName' => 'Sue', 'lastName' => 'Changed']
            )
        );
        $this->assertSame('Changed', $data['lastName']);
        $this->assertSame('Changed', $this->getLastName(self::EMP_SUE));
    }

    public function testSupervisorCannotReadSubordinateSalaryWithOwnQueryValue(): void
    {
        $this->assertConflictRejected(
            $this->salaryComponents(self::USER_SAM, 'GET', self::EMP_SUE, ['empNumber' => (string)self::EMP_SAM]),
            'empNumber'
        );
        $this->assertConflictRejected(
            $this->salaryComponents(self::USER_SAM, 'GET', self::EMP_UMA, ['empNumber' => (string)self::EMP_SAM]),
            'empNumber'
        );
    }

    public function testSupervisorCannotDeleteSubordinateSalaryWithOwnQueryValue(): void
    {
        $response = $this->salaryComponents(
            self::USER_SAM,
            'DELETE',
            self::EMP_SUE,
            ['empNumber' => (string)self::EMP_SAM],
            ['ids' => [self::SALARY_OF_SUE]]
        );

        $this->assertConflictRejected($response, 'empNumber');
        $this->assertNotNull($this->getEntityManager()->find(EmployeeSalary::class, self::SALARY_OF_SUE));
    }

    public function testSupervisorReadsOwnSalary(): void
    {
        $data = $this->assertHandled(
            $this->salaryComponents(self::USER_SAM, 'GET', self::EMP_SAM, ['empNumber' => (string)self::EMP_SAM])
        );
        $this->assertSame(['Supervisor Base'], array_column($data, 'salaryName'));
    }
}
