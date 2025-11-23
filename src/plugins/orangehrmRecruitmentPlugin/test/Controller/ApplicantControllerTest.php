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

namespace OrangeHRM\Tests\Recruitment\Controller;

use DateTime;
use Exception;
use OrangeHRM\Authentication\Csrf\CsrfTokenManager;
use OrangeHRM\Authentication\Traits\CsrfTokenManagerTrait;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Service\ConfigService;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Core\Service\NormalizerService;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\Candidate;
use OrangeHRM\Entity\CandidateAttachment;
use OrangeHRM\Entity\CandidateHistory;
use OrangeHRM\Entity\CandidateVacancy;
use OrangeHRM\Entity\Interview;
use OrangeHRM\Entity\InterviewAttachment;
use OrangeHRM\Entity\InterviewInterviewer;
use OrangeHRM\Entity\Vacancy;
use OrangeHRM\Entity\VacancyAttachment;
use OrangeHRM\Framework\Http\RedirectResponse;
use OrangeHRM\Framework\Services;
use OrangeHRM\Recruitment\Controller\PublicController\ApplicantController;
use OrangeHRM\Recruitment\Service\CandidateService;
use OrangeHRM\Recruitment\Service\RecruitmentAttachmentService;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\Mock\ArrayCsrfTokenStorage;
use OrangeHRM\Tests\Util\TestDataService;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ApplicantControllerTest extends KernelTestCase
{
    use EntityManagerHelperTrait;
    use CsrfTokenManagerTrait;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        $fixture = Config::get(Config::PLUGINS_DIR)
            . '/orangehrmRecruitmentPlugin/test/fixtures/ApplicantControllerTest.yaml';
        TestDataService::truncateSpecificTables(
            [
                VacancyAttachment::class,
                CandidateHistory::class,
                Interview::class,
                InterviewAttachment::class,
                InterviewInterviewer::class
            ]
        );
        TestDataService::populate($fixture);
    }

    /**
     * @dataProvider dataProviderForController
     */
    public function testHandle(
        int $vacancyId,
        string $firstName,
        ?string $middleName,
        string $lastName,
        string $email,
        ?string $contactNumber,
        $consentToKeepData,
        ?string $keywords,
        ?string $comment,
        array $expected,
        array $extraTestParams = []
    ): void {
        $candidateCount = 5;
        $candidateVacancyCount = 4;
        $candidateAttachmentCount = 4;
        $candidateHistoryCount = 0;
        $interviewCount = 0;
        $interviewAttachmentCount = 0;
        $interviewInterviewerCount = 0;
        $vacancyCount = 6;
        $vacancyAttachmentCount = 0;
        $this->assertEquals($candidateCount, $this->getRepository(Candidate::class)->count([]));
        $this->assertEquals($candidateVacancyCount, $this->getRepository(CandidateVacancy::class)->count([]));
        $this->assertEquals($candidateAttachmentCount, $this->getRepository(CandidateAttachment::class)->count([]));
        $this->assertEquals($candidateHistoryCount, $this->getRepository(CandidateHistory::class)->count([]));
        $this->assertEquals($interviewCount, $this->getRepository(Interview::class)->count([]));
        $this->assertEquals($interviewAttachmentCount, $this->getRepository(InterviewAttachment::class)->count([]));
        $this->assertEquals($interviewInterviewerCount, $this->getRepository(InterviewInterviewer::class)->count([]));
        $this->assertEquals($vacancyCount, $this->getRepository(Vacancy::class)->count([]));
        $this->assertEquals($vacancyAttachmentCount, $this->getRepository(VacancyAttachment::class)->count([]));

        $dateTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelper->method('getNow')
            ->willReturn(new DateTime('2021-10-04'));
        $this->createKernelWithMockServices([
            Services::CANDIDATE_SERVICE => new CandidateService(),
            Services::RECRUITMENT_ATTACHMENT_SERVICE => new RecruitmentAttachmentService(),
            Services::DATETIME_HELPER_SERVICE => $dateTimeHelper,
            Services::NORMALIZER_SERVICE => new NormalizerService(),
            Services::CONFIG_SERVICE => new ConfigService(),
        ]);
        $this->getContainer()->register(Services::CSRF_TOKEN_STORAGE, ArrayCsrfTokenStorage::class);
        $this->getContainer()->register(Services::CSRF_TOKEN_MANAGER, CsrfTokenManager::class);
        $applicantController = $this->getMockBuilder(ApplicantController::class)
            ->onlyMethods(['redirect'])
            ->getMock();
        $applicantController->method('redirect')
            ->willReturnCallback(function ($path) {
                return new RedirectResponse($path);
            });

        $token = $extraTestParams['token'] ?? $this->getCsrfTokenManager()
                ->getToken('recruitment-applicant')->getValue();
        $pathToResume = __DIR__ . '/../fixtures/resume.txt';
        $files = [];
        if (!isset($extraTestParams['excludeUploadedFile'])) {
            $uploadedFile = new UploadedFile(
                $pathToResume,
                'resume.txt',
                'text/plain',
                null,
                true
            );
            $files = ['resume' => $uploadedFile];
        }

        $requestParams = [
            '_token' => $token,
            'vacancyId' => $vacancyId,
            'firstName' => $firstName,
            'middleName' => $middleName,
            'lastName' => $lastName,
            'email' => $email,
            'contactNumber' => $contactNumber,
            'consentToKeepData' => $consentToKeepData,
            'keywords' => $keywords,
            'comment' => $comment,
        ];
        $request = $this->getHttpRequest([], $requestParams, [], [], $files);
        $response = $applicantController->handle($request);
        if (isset($expected['responseStatusCode'])) {
            $this->assertEquals($expected['responseStatusCode'], $response->getStatusCode());
            return;
        }
        $this->assertEquals("/recruitmentApply/applyVacancy/id/$vacancyId?success=true", $response->getTargetUrl());

        $this->assertEquals($candidateCount + 1, $this->getRepository(Candidate::class)->count([]));
        $this->assertEquals($candidateVacancyCount + 1, $this->getRepository(CandidateVacancy::class)->count([]));
        $this->assertEquals($candidateAttachmentCount + 1, $this->getRepository(CandidateAttachment::class)->count([]));
        $this->assertEquals($candidateHistoryCount + 1, $this->getRepository(CandidateHistory::class)->count([]));
        $this->assertEquals($interviewCount, $this->getRepository(Interview::class)->count([]));
        $this->assertEquals($interviewAttachmentCount, $this->getRepository(InterviewAttachment::class)->count([]));
        $this->assertEquals($interviewInterviewerCount, $this->getRepository(InterviewInterviewer::class)->count([]));
        $this->assertEquals($vacancyCount, $this->getRepository(Vacancy::class)->count([]));
        $this->assertEquals($vacancyAttachmentCount, $this->getRepository(VacancyAttachment::class)->count([]));

        $this->getEntityManager()->clear();
        /** @var Candidate $candidate */
        $candidate = $this->getRepository(Candidate::class)->findOneBy(
            ['firstName' => $firstName, 'lastName' => $lastName]
        );
        $this->assertEquals(6, $candidate->getId());
        $this->assertEquals($expected['middleName'], $candidate->getMiddleName());
        $this->assertEquals($expected['email'], $candidate->getEmail());
        $this->assertEquals($expected['contactNumber'], $candidate->getContactNumber());
        $this->assertEquals($expected['consentToKeepData'], $candidate->isConsentToKeepData());
        $this->assertEquals($expected['keywords'], $candidate->getKeywords());
        $this->assertEquals($expected['comment'], $candidate->getComment());
        $this->assertNull($candidate->getAddedPerson());
        $this->assertEquals(2, $candidate->getModeOfApplication());
        $this->assertEquals('2021-10-04', $candidate->getDateOfApplication()->format('Y-m-d'));

        $this->assertEquals(6, $candidate->getCandidateVacancy()[0]->getId());
        $this->assertEquals($vacancyId, $candidate->getCandidateVacancy()[0]->getVacancy()->getId());
        $this->assertEquals('APPLICATION INITIATED', $candidate->getCandidateVacancy()[0]->getStatus());
        $this->assertEquals('2021-10-04', $candidate->getCandidateVacancy()[0]->getAppliedDate()->format('Y-m-d'));

        $this->assertEquals(5, $candidate->getCandidateAttachment()[0]->getId());
        $this->assertEquals('resume.txt', $candidate->getCandidateAttachment()[0]->getFileName());
        $this->assertEquals('text/plain', $candidate->getCandidateAttachment()[0]->getFileType());
        $this->assertEquals('80', $candidate->getCandidateAttachment()[0]->getFileSize());
        $this->assertEquals(
            file_get_contents($pathToResume),
            $candidate->getCandidateAttachment()[0]->getDecorator()->getFileContent()
        );

        /** @var Vacancy $vacancy */
        $vacancy = $this->getRepository(Vacancy::class)->find($vacancyId);
        $this->assertEquals(1, $candidate->getCandidateHistory()[0]->getId());
        $this->assertEquals($candidate->getId(), $candidate->getCandidateHistory()[0]->getCandidate()->getId());
        $this->assertEquals($vacancyId, $candidate->getCandidateHistory()[0]->getVacancy()->getId());
        $this->assertEquals($vacancy->getName(), $candidate->getCandidateHistory()[0]->getCandidateVacancyName());
        $this->assertNull($candidate->getCandidateHistory()[0]->getInterview());
        $this->assertEquals(17, $candidate->getCandidateHistory()[0]->getAction());
        $this->assertNull($candidate->getCandidateHistory()[0]->getPerformedBy());
        $this->assertEquals('2021-10-04', $candidate->getCandidateHistory()[0]->getPerformedDate()->format('Y-m-d'));
        $this->assertNull($candidate->getCandidateHistory()[0]->getNote());
    }

    /**
     * @return array[]
     */
    public function dataProviderForController(): array
    {
        return [
            [
                2,
                'Abbey',
                '',
                'Kayla',
                'abbey@example.com',
                '',
                'true',
                '',
                '',
                [
                    'middleName' => null,
                    'email' => 'abbey@example.com',
                    'contactNumber' => null,
                    'consentToKeepData' => true,
                    'keywords' => null,
                    'comment' => null
                ]
            ],
            [
                3,
                'Linda',
                'Jane',
                'Anderson',
                'linda@example.com',
                '1878648628323',
                'false',
                'Git,PHP,Vue',
                'This is my first job.',
                [
                    'middleName' => 'Jane',
                    'email' => 'linda@example.com',
                    'contactNumber' => '1878648628323',
                    'consentToKeepData' => false,
                    'keywords' => 'Git,PHP,Vue',
                    'comment' => 'This is my first job.'
                ]
            ],
            [
                3,
                'Linda',
                null,
                'Anderson',
                'linda@example.com',
                null,
                false,
                null,
                null,
                [
                    'middleName' => null,
                    'email' => 'linda@example.com',
                    'contactNumber' => null,
                    'consentToKeepData' => false,
                    'keywords' => null,
                    'comment' => null
                ]
            ],
            [
                6, // Deactivated job vacancy
                'Linda',
                null,
                'Anderson',
                'linda@example.com',
                '1878648628323',
                false,
                null,
                null,
                ['responseStatusCode' => 400]
            ],
            [
                4,
                'Lengthy First name ++++++++++++++++++++',
                null,
                'Anderson',
                'linda@example.com',
                '1878648628323',
                false,
                null,
                null,
                ['responseStatusCode' => 400]
            ],
            [
                4,
                'Linda',
                '',
                'Lengthy Last name ++++++++++++++++++++',
                'linda@example.com',
                '1878648628323',
                false,
                null,
                null,
                ['responseStatusCode' => 400]
            ],
            [
                4,
                'Linda',
                'Lengthy Middle name ++++++++++++++++++++',
                'Anderson',
                'linda@example.com',
                '1878648628323',
                false,
                null,
                null,
                ['responseStatusCode' => 400]
            ],
            [
                4,
                'Linda',
                '',
                'Anderson',
                'invalid@mail', // invalid
                '1878648628323',
                false,
                null,
                null,
                ['responseStatusCode' => 400]
            ],
            [
                4,
                'Linda',
                '',
                'Anderson',
                'linda@example.com',
                'invalid', // invalid
                false,
                null,
                null,
                ['responseStatusCode' => 400]
            ],
            [
                4,
                'Linda',
                '',
                'Anderson',
                'linda@example.com',
                null,
                false,
                str_repeat('Lengthy', 36), // more than 250 chars
                null,
                ['responseStatusCode' => 400]
            ],
            [
                4,
                'Linda',
                '',
                'Anderson',
                'linda@example.com',
                null,
                false,
                null,
                str_repeat('Lengthy', 36), // more than 250 chars
                ['responseStatusCode' => 400]
            ],
            [
                3,
                'Linda',
                null,
                'Anderson',
                'linda@example.com',
                null,
                false,
                null,
                null,
                ['responseStatusCode' => 400],
                ['excludeUploadedFile' => true]
            ],
            [
                100, // Not existing job vacancy
                'Linda',
                null,
                'Anderson',
                'linda@example.com',
                '1878648628323',
                false,
                null,
                null,
                ['responseStatusCode' => 400]
            ],
        ];
    }
}
