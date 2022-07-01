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

namespace OrangeHRM\Recruitment\Controller;

use Exception;
use OrangeHRM\Authentication\Csrf\CsrfTokenManager;
use OrangeHRM\Authentication\Exception\AuthenticationException;
use OrangeHRM\Core\Api\V2\Exception\InvalidParamException;
use OrangeHRM\Core\Api\V2\Validator\Helpers\ValidationDecorator;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Api\V2\Validator\ValidatorException;
use OrangeHRM\Core\Controller\AbstractController;
use OrangeHRM\Core\Controller\PublicControllerInterface;
use OrangeHRM\Core\Dto\Base64Attachment;
use OrangeHRM\Core\Traits\LoggerTrait;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Core\Traits\Service\NormalizerServiceTrait;
use OrangeHRM\Core\Traits\ValidatorTrait;
use OrangeHRM\Entity\Candidate;
use OrangeHRM\Entity\CandidateAttachment;
use OrangeHRM\Entity\CandidateHistory;
use OrangeHRM\Entity\CandidateVacancy;
use OrangeHRM\Entity\WorkflowStateMachine;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Http\Response;
use OrangeHRM\ORM\Exception\TransactionException;
use OrangeHRM\Recruitment\Service\CandidateService;
use OrangeHRM\Recruitment\Traits\Service\CandidateServiceTrait;
use OrangeHRM\Recruitment\Traits\Service\RecruitmentAttachmentServiceTrait;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ApplicantController extends AbstractController implements PublicControllerInterface
{
    use CandidateServiceTrait;
    use RecruitmentAttachmentServiceTrait;
    use ValidatorTrait;
    use EntityManagerHelperTrait;
    use NormalizerServiceTrait;
    use LoggerTrait;
    use DateTimeHelperTrait;

    public const PARAMETER_FIRST_NAME = 'firstName';
    public const PARAMETER_MIDDLE_NAME = 'middleName';
    public const PARAMETER_LAST_NAME = 'lastName';
    public const PARAMETER_EMAIL = 'email';
    public const PARAMETER_RESUME = 'resume';
    public const PARAMETER_CONTACT_NUMBER = 'contactNumber';
    public const PARAMETER_VACANCY_ID = 'vacancyId';
    public const PARAMETER_CONSENT_TO_KEEP_DATA = 'consentToKeepData';

    /**
     * @var ValidationDecorator|null
     */
    private ?ValidationDecorator $validationDecorator = null;

    /**
     * @throws AuthenticationException
     * @throws ValidatorException
     * @throws TransactionException
     */

    public function handle(Request $request): Response
    {
        $token = $request->request->get('_token');
        $csrfTokenManager = new CsrfTokenManager();
        if (!$csrfTokenManager->isValid('recruitment-applicant', $token)) {
            throw AuthenticationException::invalidCsrfToken();
        }

        /** @var UploadedFile $file */
        $file = $request->files->get(self::PARAMETER_RESUME);
        $attachment = Base64Attachment::createFromUploadedFile($file);
        $this->validateParameters($request, $attachment);
        $this->beginTransaction();
        try {
            $vacancyId = $request->request->get(self::PARAMETER_VACANCY_ID);
            $this->processTransaction($request, $attachment, $vacancyId);
            $this->commitTransaction();
            return $this->forward(
                ApplyJobVacancyViewController::class . '::handle',
                ['success' => true, 'id' => $vacancyId]
            );
        } catch (Exception $e) {
            $this->rollBackTransaction();
            $this->getLogger()->error($e->getMessage());
            $this->getLogger()->error($e->getTraceAsString());
            throw new TransactionException($e);
        }
    }

    /**
     * @param Request $request
     * @param Base64Attachment $attachment
     * @return void
     * @throws ValidatorException
     */
    private function validateParameters(Request $request, Base64Attachment $attachment): bool
    {
        $variables = $request->request->all();
        $variables[self::PARAMETER_RESUME] = [
            'name' => $attachment->getFilename(),
            'type' => $attachment->getFileType(),
            'base64' => $attachment->getBase64Content(),
            'size' => $attachment->getSize(),
        ];
        $paramRules = $this->getParamRuleCollection();
        $paramRules->addExcludedParamKey('_token');

        try {
            return $this->validate($variables, $paramRules);
        } catch (InvalidParamException $e) {
            $this->getLogger()->error($e->getMessage());
            $this->getLogger()->error($e->getTraceAsString());
        }
    }

    /**
     * @param Request $request
     * @param Base64Attachment $attachment
     * @param int $vacancyId
     * @return void
     */
    private function processTransaction(Request $request, Base64Attachment $attachment, int $vacancyId): void
    {
        $applicant = new Candidate();
        $this->setApplicant($applicant, $request);
        $applicant = $this->getCandidateService()->getCandidateDao()->saveCandidate($applicant);
        $lastInsertApplicantId = $applicant->getId();

        $applicantHistory = new CandidateHistory();
        $this->setCommonApplicantHistoryAttributes(
            $applicantHistory,
            $lastInsertApplicantId,
            CandidateService::RECRUITMENT_CANDIDATE_ACTION_ADD
        );
        $this->getCandidateService()->getCandidateDao()->saveCandidateHistory($applicantHistory);

        if (!is_null($vacancyId)) {
            $applicantVacancy = new CandidateVacancy();
            $this->setApplicantVacancy(
                $applicantVacancy,
                $lastInsertApplicantId,
                CandidateService::STATUS_MAP[WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_ATTACH_VACANCY],
                $vacancyId
            );
            $this->getCandidateService()->getCandidateDao()->saveCandidateVacancy($applicantVacancy);

            $applicantHistory = new CandidateHistory();
            $this->setCommonApplicantHistoryAttributes(
                $applicantHistory,
                $lastInsertApplicantId,
                CandidateService::RECRUITMENT_CANDIDATE_ACTION_ADD
            );
            $applicantHistory->getDecorator()->setVacancyById($vacancyId);
            $this->getCandidateService()->getCandidateDao()->saveCandidateHistory($applicantHistory);
        }

        $applicantAttachment = new CandidateAttachment();
        $this->setCandidateAttachment($applicantAttachment, $lastInsertApplicantId, $attachment);
        $this->getRecruitmentAttachmentService()->getRecruitmentAttachmentDao()->saveCandidateAttachment(
            $applicantAttachment
        );
    }

    /**
     * @param Candidate $applicant
     * @param Request $request
     * @return void
     */
    private function setApplicant(Candidate $applicant, Request $request): void
    {
        $applicant->setFirstName(
            $request->request->get(self::PARAMETER_FIRST_NAME)
        );
        $applicant->setMiddleName(
            $request->request->get(self::PARAMETER_MIDDLE_NAME)
        );
        $applicant->setLastName(
            $request->request->get(self::PARAMETER_LAST_NAME)
        );
        $applicant->setEmail(
            $request->request->get(self::PARAMETER_EMAIL)
        );
        $applicant->setContactNumber(
            $request->request->get(self::PARAMETER_CONTACT_NUMBER)
        );
        $applicant->setConsentToKeepData(
            $request->request->getBoolean(self::PARAMETER_CONSENT_TO_KEEP_DATA),
        );
        $applicant->setModeOfApplication(Candidate::MODE_OF_APPLICATION_ONLINE);
        $applicant->setDateOfApplication($this->getDateTimeHelper()->getNow());
    }

    /**
     * @param CandidateVacancy $candidateVacancy
     * @param int $applicantId
     * @param string $status
     * @param int $vacancyId
     */
    private function setApplicantVacancy(
        CandidateVacancy $candidateVacancy,
        int $applicantId,
        string $status,
        int $vacancyId
    ): void {
        $candidateVacancy->getDecorator()->setCandidateById($applicantId);
        $candidateVacancy->getDecorator()->setVacancyById($vacancyId);
        $candidateVacancy->setStatus($status);
        $candidateVacancy->setAppliedDate($this->getDateTimeHelper()->getNow());
    }

    /**
     * @param CandidateHistory $applicantHistory
     * @param int $applicantId
     * @param int $action
     * @return void
     */
    private function setCommonApplicantHistoryAttributes(
        CandidateHistory $applicantHistory,
        int $applicantId,
        int $action
    ): void {
        $applicantHistory->getDecorator()->setCandidateById($applicantId);
        $applicantHistory->setAction($action);
        $applicantHistory->setPerformedDate($this->getDateTimeHelper()->getNow());
    }


    /**
     * @return ValidationDecorator
     */
    protected function getValidationDecorator(): ValidationDecorator
    {
        if (!$this->validationDecorator instanceof ValidationDecorator) {
            $this->validationDecorator = new ValidationDecorator();
        }
        return $this->validationDecorator;
    }

    /**
     * @return ParamRuleCollection|null
     */
    protected function getParamRuleCollection(): ?ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_RESUME,
                new Rule(Rules::BASE_64_ATTACHMENT)
            ),
            new ParamRule(
                self::PARAMETER_FIRST_NAME,
                new Rule(Rules::STRING_TYPE)
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_MIDDLE_NAME,
                    new Rule(Rules::STRING_TYPE)
                ),
                true
            ),
            new ParamRule(
                self::PARAMETER_LAST_NAME,
                new Rule(Rules::STRING_TYPE)
            ),
            new ParamRule(
                self::PARAMETER_EMAIL,
                new Rule(Rules::EMAIL)
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_CONTACT_NUMBER,
                    new Rule(Rules::PHONE)
                )
            ),
            new ParamRule(
                self::PARAMETER_VACANCY_ID,
                new Rule(Rules::POSITIVE)
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_CONSENT_TO_KEEP_DATA,
                    new Rule(Rules::STRING_TYPE)
                )
            ),
        );
    }

    /**
     * @param CandidateAttachment $applicantAttachment
     * @param int $applicantId
     * @param Base64Attachment $resume
     */
    private function setCandidateAttachment(
        CandidateAttachment $applicantAttachment,
        int $applicantId,
        Base64Attachment $resume
    ) {
        $applicantAttachment->getDecorator()->setCandidateById($applicantId);
        $this->setBase64Attachment($applicantAttachment, $resume);
    }

    /**
     * @param CandidateAttachment $applicantAttachment
     * @param Base64Attachment $resume
     */
    private function setBase64Attachment(CandidateAttachment $applicantAttachment, Base64Attachment $resume): void
    {
        $applicantAttachment->setFileName($resume->getFilename());
        $applicantAttachment->setFileType($resume->getFileType());
        $applicantAttachment->setFileSize($resume->getSize());
        $applicantAttachment->setFileContent($resume->getBase64Content());
    }
}