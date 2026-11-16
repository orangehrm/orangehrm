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
use OrangeHRM\Core\Service\ConfigService;
use OrangeHRM\Entity\CandidateHistory;
use OrangeHRM\Entity\ProjectAdmin;
use OrangeHRM\Framework\Services;
use OrangeHRM\Pim\Service\EmployeeService;
use OrangeHRM\Recruitment\Api\CandidateHistoryAPI;
use OrangeHRM\Recruitment\Service\CandidateService;
use OrangeHRM\Recruitment\Service\VacancyService;
use OrangeHRM\Tests\Util\EndpointIntegrationTestCase;
use OrangeHRM\Tests\Util\Integration\RestControllerTestTrait;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * Candidate history reads `candidateId` from the route. A query value that differs from it is refused (422), so
 * the accessible-candidate rule always checks the candidate the handler works on.
 *
 * @group Recruitment
 * @group APIv2
 */
class CandidateHistoryQueryParamConflictTest extends EndpointIntegrationTestCase
{
    use RestControllerTestTrait;

    // In CandidateHistoryAPITest.yaml: user 3 interviews candidate 1, but not candidates 2 and 4
    private const INTERVIEWER = 3;
    private const OWN_CANDIDATE = 1;
    private const OTHER_CANDIDATE = 2;
    private const OTHER_CANDIDATE_HISTORY = 6;
    private const OFFER_OF_UNRELATED_CANDIDATE = [4, 10];

    private const SERVICES = [
        Services::CANDIDATE_SERVICE => CandidateService::class,
        Services::CONFIG_SERVICE => ConfigService::class,
        Services::USER_SERVICE => UserService::class,
        Services::EMPLOYEE_SERVICE => EmployeeService::class,
        Services::VACANCY_SERVICE => VacancyService::class,
    ];

    protected function setUp(): void
    {
        $this->populateFixtures('CandidateHistoryAPITest.yaml');
        // Project admins get an extra role; other test classes leave such rows
        TestDataService::truncateSpecificTables([ProjectAdmin::class]);
    }

    private function history(
        string $method,
        int $candidateId,
        ?int $historyId,
        array $query,
        array $body = []
    ): array {
        $attributes = ['candidateId' => $candidateId];
        if ($historyId !== null) {
            $attributes['historyId'] = $historyId;
        }
        return $this->handleRestRequest(
            self::INTERVIEWER,
            CandidateHistoryAPI::class,
            $method,
            $attributes,
            $query,
            $body,
            self::SERVICES,
            $historyId !== null ? 'historyId' : null
        );
    }

    public function testInterviewerReadsHistoryOfOwnCandidate(): void
    {
        $data = $this->assertHandled(
            $this->history('GET', self::OWN_CANDIDATE, null, ['candidateId' => (string)self::OWN_CANDIDATE])
        );
        $this->assertSame([4, 3, 2, 1], array_column($data, 'id'));
    }

    public function testInterviewerCannotListOtherHistoryWithOwnCandidateQueryValue(): void
    {
        $this->assertConflictRejected(
            $this->history('GET', self::OTHER_CANDIDATE, null, ['candidateId' => (string)self::OWN_CANDIDATE]),
            'candidateId'
        );
        // Without the query value the accessible-candidate rule refuses it (403)
        $this->expectException(ForbiddenException::class);
        $this->history('GET', self::OTHER_CANDIDATE, null, []);
    }

    public function testInterviewerCannotReadOtherRecordWithOwnCandidateQueryValue(): void
    {
        [$candidateId, $historyId] = self::OFFER_OF_UNRELATED_CANDIDATE;
        $this->assertConflictRejected(
            $this->history('GET', $candidateId, $historyId, ['candidateId' => (string)self::OWN_CANDIDATE]),
            'candidateId'
        );
    }

    public function testInterviewerCannotUpdateOtherRecordWithOwnCandidateQueryValue(): void
    {
        $response = $this->history(
            'PUT',
            self::OTHER_CANDIDATE,
            self::OTHER_CANDIDATE_HISTORY,
            ['candidateId' => (string)self::OWN_CANDIDATE],
            ['note' => 'Changed']
        );

        $this->assertConflictRejected($response, 'candidateId');
        $this->assertSame(
            'Shortlisted',
            $this->getEntityManager()->find(CandidateHistory::class, self::OTHER_CANDIDATE_HISTORY)->getNote()
        );
    }
}
