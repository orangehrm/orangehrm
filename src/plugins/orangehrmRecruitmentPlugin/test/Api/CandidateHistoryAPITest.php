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

namespace OrangeHRM\Tests\Recruitment\Api;

use OrangeHRM\Admin\Service\UserService;
use OrangeHRM\Core\Api\V2\Exception\ForbiddenException;
use OrangeHRM\Core\Api\V2\Exception\RecordNotFoundException;
use OrangeHRM\Core\Authorization\Manager\UserRoleManagerFactory;
use OrangeHRM\Core\Service\ConfigService;
use OrangeHRM\Entity\CandidateHistory;
use OrangeHRM\Framework\Services;
use OrangeHRM\ORM\Doctrine;
use OrangeHRM\Pim\Service\EmployeeService;
use OrangeHRM\Recruitment\Api\CandidateHistoryAPI;
use OrangeHRM\Recruitment\Service\CandidateService;
use OrangeHRM\Recruitment\Service\VacancyService;
use OrangeHRM\Tests\Util\EndpointIntegrationTestCase;
use OrangeHRM\Tests\Util\Integration\TestCaseParams;

/**
 * @group Recruitment
 * @group APIv2
 */
class CandidateHistoryAPITest extends EndpointIntegrationTestCase
{
    // Users in CandidateHistoryAPITest.yaml
    private const ADMIN = 1;
    private const HIRING_MANAGER = 2;
    private const INTERVIEWER = 3;
    private const INTERVIEWER_AND_OTHER_HIRING_MANAGER = 4;
    private const NO_RECRUITMENT_ROLE = 5;

    private const FORBIDDEN = 'forbidden';
    private const NOT_FOUND = 'not found';

    protected function setUp(): void
    {
        $this->populateFixtures('CandidateHistoryAPITest.yaml');
    }

    /**
     * @param int $userId
     * @param string $operation getAll, getOne or update
     * @param array $attributes
     * @param array $body
     * @return string|array Normalized result, or FORBIDDEN / NOT_FOUND
     */
    private function call(int $userId, string $operation, array $attributes, array $body = [])
    {
        $testCaseParams = new TestCaseParams();
        $testCaseParams->setUserId($userId);
        $testCaseParams->setAttributes($attributes);
        $testCaseParams->setBody($body);
        $testCaseParams->setServices([
            Services::CANDIDATE_SERVICE => CandidateService::class,
            Services::CONFIG_SERVICE => ConfigService::class,
            Services::USER_SERVICE => UserService::class,
            Services::EMPLOYEE_SERVICE => EmployeeService::class,
            Services::VACANCY_SERVICE => VacancyService::class,
        ]);
        $testCaseParams->setFactories([
            Services::USER_ROLE_MANAGER => [
                UserRoleManagerFactory::class,
                'getNewUserRoleManager'
            ],
        ]);
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);
        $api = $this->getApiEndpointMock(CandidateHistoryAPI::class, $testCaseParams);

        try {
            $this->validate(array_merge($attributes, $body), $api->{'getValidationRuleFor' . ucfirst($operation)}());
            return $api->$operation()->normalize();
        } catch (ForbiddenException $e) {
            return self::FORBIDDEN;
        } catch (RecordNotFoundException $e) {
            return self::NOT_FOUND;
        }
    }

    /**
     * @param string|array $result
     * @return string|int Action id of a single record, or the FORBIDDEN / NOT_FOUND marker
     */
    private function actionOf($result)
    {
        return is_array($result) ? $result['action']['id'] : $result;
    }

    private function getNote(int $historyId): ?string
    {
        Doctrine::getEntityManager()->clear();
        return Doctrine::getEntityManager()->find(CandidateHistory::class, $historyId)->getNote();
    }

    /**
     * @dataProvider dataProviderForTestGetOne
     */
    public function testGetOne(int $userId, int $candidateId, int $historyId, $expected): void
    {
        $result = $this->call($userId, 'getOne', ['candidateId' => $candidateId, 'historyId' => $historyId]);
        $this->assertSame($expected, $this->actionOf($result));
        if (is_array($result)) {
            $this->assertSame($historyId, $result['id']);
            $this->assertSame($candidateId, $result['candidate']['id']);
        }
    }

    public function dataProviderForTestGetOne(): array
    {
        return [
            'Admin reads job offered' => [self::ADMIN, 1, 5, 7],
            'Admin reads rejected' => [self::ADMIN, 3, 8, 3],
            'Admin, record of another candidate' => [self::ADMIN, 1, 6, self::NOT_FOUND],
            'Admin, missing record' => [self::ADMIN, 1, 100, self::NOT_FOUND],
            'Hiring manager reads job offered' => [self::HIRING_MANAGER, 1, 5, 7],
            'Hiring manager reads interview passed' => [self::HIRING_MANAGER, 1, 4, 5],
            'Hiring manager reads rejected' => [self::HIRING_MANAGER, 3, 8, 3],
            'Interviewer reads application initiated' => [self::INTERVIEWER, 1, 1, 1],
            'Interviewer reads shortlisted' => [self::INTERVIEWER, 1, 2, 2],
            'Interviewer reads interview scheduled' => [self::INTERVIEWER, 1, 3, 4],
            'Interviewer reads interview passed' => [self::INTERVIEWER, 1, 4, 5],
            'Interviewer cannot read job offered' => [self::INTERVIEWER, 1, 5, self::FORBIDDEN],
            'Interviewer cannot read rejected' => [self::INTERVIEWER, 3, 8, self::FORBIDDEN],
            'Interviewer reads interview scheduled of candidate 3' => [self::INTERVIEWER, 3, 7, 4],
            'Interviewer, record of another candidate' => [self::INTERVIEWER, 1, 6, self::NOT_FOUND],
            'Interviewer, candidate not interviewed' => [self::INTERVIEWER, 2, 6, self::FORBIDDEN],
            'Interviewer who manages another vacancy reads interview passed' =>
                [self::INTERVIEWER_AND_OTHER_HIRING_MANAGER, 1, 4, 5],
            'Interviewer who manages another vacancy cannot read job offered' =>
                [self::INTERVIEWER_AND_OTHER_HIRING_MANAGER, 1, 5, self::FORBIDDEN],
            'Interviewer who manages another vacancy reads own vacancy record' =>
                [self::INTERVIEWER_AND_OTHER_HIRING_MANAGER, 2, 6, 2],
            'No recruitment role' => [self::NO_RECRUITMENT_ROLE, 1, 4, self::FORBIDDEN],
            'Admin reads hired' => [self::ADMIN, 5, 13, 9],
            'Admin reads offer declined' => [self::ADMIN, 6, 17, 8],
            'Hiring manager reads hired' => [self::HIRING_MANAGER, 5, 13, 9],
            'Hiring manager reads offer declined' => [self::HIRING_MANAGER, 6, 17, 8],
            'Interviewer reads interview scheduled of hired candidate' => [self::INTERVIEWER, 5, 11, 4],
            'Interviewer cannot read hired' => [self::INTERVIEWER, 5, 13, self::FORBIDDEN],
            'Interviewer cannot read offer declined' => [self::INTERVIEWER, 6, 17, self::FORBIDDEN],
            'Interviewer cannot read job offered of declined candidate' => [self::INTERVIEWER, 6, 16, self::FORBIDDEN],
            // Action 10 is outside the workflow map, so no role is given it (the list hides it too)
            'Interviewer cannot read legacy action 10' => [self::INTERVIEWER, 5, 14, self::FORBIDDEN],
            'Admin cannot read legacy action 10' => [self::ADMIN, 5, 14, self::FORBIDDEN],
            // No current vacancy: the interviewer no longer reaches the candidate at all
            'Interviewer, candidate with no current vacancy, rejected' => [self::INTERVIEWER, 7, 19, self::FORBIDDEN],
            'Interviewer, candidate with no current vacancy, interview scheduled' =>
                [self::INTERVIEWER, 7, 18, self::FORBIDDEN],
            'Hiring manager, candidate with no current vacancy' => [self::HIRING_MANAGER, 7, 19, self::FORBIDDEN],
            'Admin reads rejected, candidate with no current vacancy' => [self::ADMIN, 7, 19, 3],
            'Interviewer reads interview scheduled, candidate in two vacancies' => [self::INTERVIEWER, 8, 20, 4],
            'Interviewer cannot read job offered, candidate in two vacancies' =>
                [self::INTERVIEWER, 8, 21, self::FORBIDDEN],
            'Admin reads job offered, candidate in two vacancies' => [self::ADMIN, 8, 21, 7],
        ];
    }

    /**
     * @dataProvider dataProviderForTestUpdate
     */
    public function testUpdate(int $userId, int $candidateId, int $historyId, $expected): void
    {
        $noteBefore = $this->getNote($historyId);
        $result = $this->call(
            $userId,
            'update',
            ['candidateId' => $candidateId, 'historyId' => $historyId],
            ['note' => 'Updated note']
        );
        $this->assertSame($expected, $this->actionOf($result));
        $this->assertSame(is_array($result) ? 'Updated note' : $noteBefore, $this->getNote($historyId));
    }

    public function dataProviderForTestUpdate(): array
    {
        return [
            'Admin updates job offered' => [self::ADMIN, 1, 5, 7],
            'Hiring manager updates job offered' => [self::HIRING_MANAGER, 1, 5, 7],
            'Interviewer updates interview passed' => [self::INTERVIEWER, 1, 4, 5],
            'Interviewer cannot update job offered' => [self::INTERVIEWER, 1, 5, self::FORBIDDEN],
            'Interviewer cannot update rejected' => [self::INTERVIEWER, 3, 8, self::FORBIDDEN],
            'Interviewer, record of another candidate' => [self::INTERVIEWER, 1, 6, self::NOT_FOUND],
            'Interviewer who manages another vacancy cannot update job offered' =>
                [self::INTERVIEWER_AND_OTHER_HIRING_MANAGER, 1, 5, self::FORBIDDEN],
            'Interviewer who manages another vacancy, vacancy without hiring manager' =>
                [self::INTERVIEWER_AND_OTHER_HIRING_MANAGER, 4, 10, self::FORBIDDEN],
            'Admin, vacancy without hiring manager' => [self::ADMIN, 4, 10, 7],
            'No recruitment role' => [self::NO_RECRUITMENT_ROLE, 1, 4, self::FORBIDDEN],
            'Hiring manager updates hired' => [self::HIRING_MANAGER, 5, 13, 9],
            'Interviewer cannot update hired' => [self::INTERVIEWER, 5, 13, self::FORBIDDEN],
            'Interviewer cannot update offer declined' => [self::INTERVIEWER, 6, 17, self::FORBIDDEN],
            'Interviewer cannot update legacy action 10' => [self::INTERVIEWER, 5, 14, self::FORBIDDEN],
            'Interviewer, candidate with no current vacancy' => [self::INTERVIEWER, 7, 19, self::FORBIDDEN],
            // Existing rule of the update: a candidate without a current vacancy cannot be updated
            'Admin, candidate with no current vacancy' => [self::ADMIN, 7, 19, self::FORBIDDEN],
        ];
    }

    /**
     * The list and the single read must agree: a record is readable by its id exactly when the list shows it.
     *
     * @dataProvider dataProviderForTestListAndSingleReadAgree
     */
    public function testListAndSingleReadAgree(int $userId, int $candidateId, array $expectedIds): void
    {
        $list = $this->call($userId, 'getAll', ['candidateId' => $candidateId]);
        $listedIds = is_array($list) ? array_column($list, 'id') : [];
        sort($listedIds);
        $this->assertSame($expectedIds, $listedIds);

        $readableIds = [];
        $allIds = array_map(
            fn (CandidateHistory $history) => $history->getId(),
            Doctrine::getEntityManager()->getRepository(CandidateHistory::class)->findAll()
        );
        foreach ($allIds as $historyId) {
            $result = $this->call($userId, 'getOne', ['candidateId' => $candidateId, 'historyId' => $historyId]);
            if (is_array($result)) {
                $readableIds[] = $historyId;
            }
        }
        sort($readableIds);
        $this->assertSame($listedIds, $readableIds);
    }

    public function dataProviderForTestListAndSingleReadAgree(): array
    {
        return [
            'Admin, candidate 1' => [self::ADMIN, 1, [1, 2, 3, 4, 5]],
            'Admin, candidate 3' => [self::ADMIN, 3, [7, 8]],
            'Hiring manager, candidate 1' => [self::HIRING_MANAGER, 1, [1, 2, 3, 4, 5]],
            'Hiring manager, candidate 3' => [self::HIRING_MANAGER, 3, [7, 8]],
            'Interviewer, candidate 1' => [self::INTERVIEWER, 1, [1, 2, 3, 4]],
            'Interviewer, candidate 3' => [self::INTERVIEWER, 3, [7]],
            'Interviewer who manages another vacancy, candidate 1' =>
                [self::INTERVIEWER_AND_OTHER_HIRING_MANAGER, 1, [1, 2, 3, 4]],
            'Interviewer who manages another vacancy, candidate 2' =>
                [self::INTERVIEWER_AND_OTHER_HIRING_MANAGER, 2, [6]],
            'Admin, hired candidate' => [self::ADMIN, 5, [11, 12, 13]],
            'Interviewer, hired candidate' => [self::INTERVIEWER, 5, [11]],
            'Hiring manager, declined candidate' => [self::HIRING_MANAGER, 6, [15, 16, 17]],
            'Interviewer, declined candidate' => [self::INTERVIEWER, 6, [15]],
            'Admin, candidate with no current vacancy' => [self::ADMIN, 7, [18, 19]],
            'Interviewer, candidate with no current vacancy' => [self::INTERVIEWER, 7, []],
            'Admin, candidate in two vacancies' => [self::ADMIN, 8, [20, 21]],
            'Interviewer, candidate in two vacancies' => [self::INTERVIEWER, 8, [20]],
        ];
    }

    /**
     * @dataProvider dataProviderForTestGetAllVacancyWithoutHiringManager
     */
    public function testGetAllVacancyWithoutHiringManager(int $userId, array $expectedIds): void
    {
        $list = $this->call($userId, 'getAll', ['candidateId' => 4]);
        $listedIds = array_column($list, 'id');
        sort($listedIds);
        $this->assertSame($expectedIds, $listedIds);
    }

    public function dataProviderForTestGetAllVacancyWithoutHiringManager(): array
    {
        return [
            'Admin' => [self::ADMIN, [9, 10]],
            'Interviewer who manages another vacancy' => [self::INTERVIEWER_AND_OTHER_HIRING_MANAGER, [9]],
        ];
    }

    public function testCreate(): void
    {
        $api = new CandidateHistoryAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->create();
    }

    public function testDelete(): void
    {
        $api = new CandidateHistoryAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->delete();
    }
}
