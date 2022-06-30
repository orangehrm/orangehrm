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
 * Boston, MA 02110-1301, USA
 */

namespace OrangeHRM\Tests\Recruitment\Controller;

use Exception;
use OrangeHRM\Authentication\Csrf\CsrfTokenManager;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Service\ConfigService;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Core\Service\NormalizerService;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\Candidate;
use OrangeHRM\Framework\Http\Response;
use OrangeHRM\Framework\Services;
use OrangeHRM\Recruitment\Controller\ApplicantController;
use OrangeHRM\Recruitment\Service\CandidateService;
use OrangeHRM\Recruitment\Service\RecruitmentAttachmentService;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;
use Symfony\Component\HttpFoundation\File\UploadedFile;

session_start();
class ApplicantControllerTest extends KernelTestCase
{
    use EntityManagerHelperTrait;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        $fixture = Config::get(Config::PLUGINS_DIR)
            . '/orangehrmRecruitmentPlugin/test/fixtures/PublicJobCandidate.yaml';
        TestDataService::populate($fixture);
    }

    /**
     * @param int $vacancyId
     * @param string $firstName
     * @param string $middleName
     * @param string $lastName
     * @param string $email
     * @param string $contactNumber
     * @param string $consentToKeepData
     * @dataProvider dataProviderForController
     * @return void
     */
    public function testHandle(
        int $vacancyId,
        string $firstName,
        string $middleName,
        string $lastName,
        string $email,
        string $contactNumber,
        string $consentToKeepData
    ): void {
        $q = $this->createQueryBuilder(Candidate::class, 'candidate');
        $q->leftJoin('candidate.candidateVacancy', 'candidateVacancy');
        $q->leftJoin('candidateVacancy.vacancy', 'vacancy');

        $this->assertEquals(5, $this->getPaginator($q)->count());
        $this->createKernelWithMockServices([
            Services::CANDIDATE_SERVICE => new CandidateService(),
            Services::RECRUITMENT_ATTACHMENT_SERVICE => new RecruitmentAttachmentService(),
            Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
            Services::NORMALIZER_SERVICE => new NormalizerService(),
            Services::CONFIG_SERVICE => new ConfigService(),
        ]);
        $csrfTokenManager = new CsrfTokenManager();
        $token = $csrfTokenManager->getToken('recruitment-applicant')->getValue();
        $pathToResume = __DIR__.'/../fixtures/resume.txt';
        $uploadedFile = new UploadedFile($pathToResume, 'resume.txt', 'text/plain', null, true);

        $applicantController = $this->getMockBuilder(ApplicantController::class)
            ->onlyMethods(['forward'])
            ->getMock();
        $applicantController->method('forward')
            ->willReturnCallback(function ($forwardedController, $attributes) {
                return new Response(

                );
            });

        $request = $this->getHttpRequest([], [
            '_token' => $token,
            'vacancyId' => $vacancyId,
            'firstName' => $firstName,
            'middleName' => $middleName,
            'lastName' => $lastName,
            'email' => $email,
            'contactNumber' => $contactNumber,
            'consentToKeepData' => $consentToKeepData,
        ], [], [], [
            'resume' => $uploadedFile,
        ]);
        $response = $applicantController->handle($request);
        $this->assertEquals(6, $this->getPaginator($q)->count());
        $this->assertEquals('Peter', $q->select('candidate.firstName')->where('candidate.id = 1')->getQuery()->execute()[0]['firstName']);
        $this->assertNotEquals('Zaman', $q->select('candidate.lastName')->where('candidate.id = 3')->getQuery()->execute()[0]['lastName']);
        $this->assertEquals('Senior Technical Supervisor', $q->select('vacancy.name')->where('vacancy.id = 2')->getQuery()->execute()[0]['name']);
        $this->assertEquals('APPLICATION INITIATED', $q->select('candidateVacancy.status')->where('candidateVacancy.id = 1')->getQuery()->execute()[0]['status']);
    }

    /**
     * @return array[]
     */
    public function dataProviderForController(): array
    {
        return [
            [
                2,
                'Md',
                'Saif',
                'Zaman',
                'saif@orangehrmlive.com',
                '114453645767',
                'on',
            ],
        ];
    }
}
