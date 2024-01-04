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

namespace OrangeHRM\Pim\Api;

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\Exception\BadRequestException;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\ResourceEndpoint;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Dto\Base64Attachment;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\EmpContract;
use OrangeHRM\Entity\EmployeeAttachment;
use OrangeHRM\Pim\Api\Model\EmploymentContractModel;
use OrangeHRM\Pim\Dto\PartialEmployeeAttachment;
use OrangeHRM\Pim\Service\EmploymentContractService;

class EmploymentContractAPI extends Endpoint implements ResourceEndpoint
{
    use UserRoleManagerTrait;

    public const PARAMETER_START_DATE = 'startDate';
    public const PARAMETER_END_DATE = 'endDate';
    public const PARAMETER_CONTRACT_ATTACHMENT = 'contractAttachment';
    public const PARAMETER_CURRENT_CONTRACT_ATTACHMENT = 'currentContractAttachment';

    public const CONTRACT_ATTACHMENT_KEEP_CURRENT = 'keepCurrent';
    public const CONTRACT_ATTACHMENT_DELETE_CURRENT = 'deleteCurrent';
    public const CONTRACT_ATTACHMENT_REPLACE_CURRENT = 'replaceCurrent';

    public const PARAM_RULE_ATTACHMENT_FILE_NAME_MAX_LENGTH = 100;

    /**
     * @var EmploymentContractService|null
     */
    protected ?EmploymentContractService $employmentContractService = null;

    /**
     * @return EmploymentContractService
     */
    public function getEmploymentContractService(): EmploymentContractService
    {
        if (!$this->employmentContractService instanceof EmploymentContractService) {
            $this->employmentContractService = new EmploymentContractService();
        }
        return $this->employmentContractService;
    }

    /**
     * @OA\Get(
     *     path="/api/v2/pim/employees/{empNumber}/employment-contract",
     *     tags={"PIM/Employment Contract"},
     *     summary="Get an Employee's Employment Contract",
     *     operationId="get-an-employees-employment-contract",
     *     @OA\PathParameter(
     *         name="empNumber",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Pim-EmploymentContractModel"
     *             ),
     *             @OA\Property(property="meta",
     *                 type="object",
     *                 @OA\Property(property="empNumber", type="integer")
     *             )
     *         )
     *     ),
     * )
     *
     * @inheritDoc
     */
    public function getOne(): EndpointResourceResult
    {
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );

        $employmentContract = $this->getEmploymentContractService()
            ->getEmploymentContractDao()
            ->getEmploymentContractByEmpNumber($empNumber);
        if (!$employmentContract instanceof EmpContract) {
            $employmentContract = new EmpContract();
            $employmentContract->getDecorator()->setEmployeeByEmpNumber($empNumber);
        }
        return new EndpointResourceResult(
            EmploymentContractModel::class,
            $employmentContract,
            new ParameterBag([CommonParams::PARAMETER_EMP_NUMBER => $empNumber])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_EMP_NUMBER,
                new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
            ),
        );
    }

    /**
     * @OA\Put(
     *     path="/api/v2/pim/employees/{empNumber}/employment-contract",
     *     tags={"PIM/Employment Contract"},
     *     summary="Update an Employee's Employment Contract",
     *     operationId="update-an-employees-employment-contract",
     *     @OA\PathParameter(
     *         name="empNumber",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="startDate", type="string", format="date"),
     *             @OA\Property(property="endDate", type="string", format="date"),
     *             @OA\Property(
     *                 property="currentContractAttachment",
     *                 type="string",
     *                 enum={
     *                     OrangeHRM\Pim\Api\EmploymentContractAPI::CONTRACT_ATTACHMENT_REPLACE_CURRENT,
     *                     OrangeHRM\Pim\Api\EmploymentContractAPI::CONTRACT_ATTACHMENT_KEEP_CURRENT,
     *                     OrangeHRM\Pim\Api\EmploymentContractAPI::CONTRACT_ATTACHMENT_DELETE_CURRENT
     *                 }
     *             ),
     *             @OA\Property(property="contractAttachment", ref="#/components/schemas/Base64Attachment"),
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Pim-EmploymentContractModel"
     *             ),
     *             @OA\Property(property="empNumber", type="integer")
     *         )
     *     ),
     * )
     *
     * @inheritDoc
     */
    public function update(): EndpointResourceResult
    {
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );

        $this->updateContractAttachment($empNumber);
        $employmentContract = $this->updateEmploymentContract($empNumber);

        return new EndpointResourceResult(
            EmploymentContractModel::class,
            $employmentContract,
            new ParameterBag([CommonParams::PARAMETER_EMP_NUMBER => $empNumber])
        );
    }

    /**
     * @param int $empNumber
     * @return EmpContract
     */
    private function updateEmploymentContract(int $empNumber): EmpContract
    {
        $startDate = $this->getRequestParams()->getDateTimeOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_START_DATE
        );
        $endDate = $this->getRequestParams()->getDateTimeOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_END_DATE
        );

        $employmentContract = $this->getEmploymentContractService()
            ->getEmploymentContractDao()
            ->getEmploymentContractByEmpNumber($empNumber);

        if (!$employmentContract instanceof EmpContract) {
            $employmentContract = new EmpContract();
            $employmentContract->setContractId('1');
            $employmentContract->getDecorator()->setEmployeeByEmpNumber($empNumber);
        }

        $employmentContract->setStartDate($startDate);
        $employmentContract->setEndDate($endDate);

        return $this->getEmploymentContractService()
            ->getEmploymentContractDao()
            ->saveEmploymentContract($employmentContract);
    }

    /**
     * @param int $empNumber
     * @throws BadRequestException
     */
    private function updateContractAttachment(int $empNumber): void
    {
        $base64Attachment = $this->getRequestParams()->getAttachmentOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_CONTRACT_ATTACHMENT
        );
        $currentContractAttachment = $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_CURRENT_CONTRACT_ATTACHMENT
        );

        $partialContractAttachment = $this->getEmploymentContractService()->getContractAttachment($empNumber);

        if (!$partialContractAttachment instanceof PartialEmployeeAttachment && $currentContractAttachment) {
            throw $this->getBadRequestException(
                "`" . self::PARAMETER_CURRENT_CONTRACT_ATTACHMENT . "` should not define if there is no contract attachment"
            );
        } elseif ($partialContractAttachment instanceof PartialEmployeeAttachment && !$currentContractAttachment) {
            throw $this->getBadRequestException(
                "`" . self::PARAMETER_CURRENT_CONTRACT_ATTACHMENT . "` should define if there is contract attachment"
            );
        }

        if (!$partialContractAttachment instanceof PartialEmployeeAttachment && $base64Attachment) {
            $contractAttachment = new EmployeeAttachment();
            $contractAttachment->getDecorator()->setEmployeeByEmpNumber($empNumber);
            $this->setAttachmentAttributes($contractAttachment, $base64Attachment);
            $this->getEmploymentContractService()->saveContractAttachment($contractAttachment);
        } elseif ($currentContractAttachment === self::CONTRACT_ATTACHMENT_DELETE_CURRENT) {
            $contractAttachment = $this->getEmploymentContractService()
                ->getContractAttachmentById($empNumber, $partialContractAttachment->getAttachId());
            $this->getEmploymentContractService()->deleteContractAttachment($contractAttachment);
        } elseif ($currentContractAttachment === self::CONTRACT_ATTACHMENT_REPLACE_CURRENT) {
            $contractAttachment = $this->getEmploymentContractService()
                ->getContractAttachmentById($empNumber, $partialContractAttachment->getAttachId());
            $this->setAttachmentAttributes($contractAttachment, $base64Attachment);
            $this->getEmploymentContractService()->saveContractAttachment($contractAttachment);
        }
    }

    /**
     * @param EmployeeAttachment $employeeAttachment
     * @param Base64Attachment $base64Attachment
     * @return EmployeeAttachment
     */
    private function setAttachmentAttributes(
        EmployeeAttachment $employeeAttachment,
        Base64Attachment $base64Attachment
    ): EmployeeAttachment {
        $employeeAttachment->setFilename($base64Attachment->getFilename());
        $employeeAttachment->setSize($base64Attachment->getSize());
        $employeeAttachment->setFileType($base64Attachment->getFileType());
        $employeeAttachment->setAttachment($base64Attachment->getContent());

        $employeeAttachment->setAttachedBy($this->getUserRoleManager()->getUser()->getId());
        $employeeAttachment->setAttachedByName($this->getUserRoleManager()->getUser()->getUserName());
        return $employeeAttachment;
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        $endDate = $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_END_DATE);
        $currentContractAttachment = $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_CURRENT_CONTRACT_ATTACHMENT
        );
        $startDateRules = [new Rule(Rules::API_DATE)];
        if ($endDate) {
            $startDateRules[] = new Rule(Rules::LESS_THAN, [$endDate]);
        }

        $paramRules = new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_EMP_NUMBER,
                new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_START_DATE,
                    ...$startDateRules
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_END_DATE,
                    new Rule(Rules::API_DATE)
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_CURRENT_CONTRACT_ATTACHMENT,
                    new Rule(
                        Rules::IN,
                        [
                            [
                                self::CONTRACT_ATTACHMENT_KEEP_CURRENT,
                                self::CONTRACT_ATTACHMENT_DELETE_CURRENT,
                                self::CONTRACT_ATTACHMENT_REPLACE_CURRENT
                            ]
                        ]
                    )
                )
            )
        );
        $contractAttachmentRule = new ParamRule(
            self::PARAMETER_CONTRACT_ATTACHMENT,
            new Rule(
                Rules::BASE_64_ATTACHMENT,
                [null, null, self::PARAM_RULE_ATTACHMENT_FILE_NAME_MAX_LENGTH]
            )
        );
        if (!in_array(
            $currentContractAttachment,
            [self::CONTRACT_ATTACHMENT_KEEP_CURRENT, self::CONTRACT_ATTACHMENT_DELETE_CURRENT]
        )) {
            if (is_null($currentContractAttachment)) {
                $paramRules->addParamValidation(
                    $this->getValidationDecorator()->notRequiredParamRule($contractAttachmentRule)
                );
            } elseif ($currentContractAttachment === self::CONTRACT_ATTACHMENT_REPLACE_CURRENT) {
                $paramRules->addParamValidation($contractAttachmentRule);
            }
        }
        return $paramRules;
    }

    /**
     * @inheritDoc
     */
    public function delete(): EndpointResourceResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }
}
