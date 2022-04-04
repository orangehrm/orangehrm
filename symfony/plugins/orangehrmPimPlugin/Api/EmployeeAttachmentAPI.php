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

namespace OrangeHRM\Pim\Api;

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Dto\Base64Attachment;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\EmployeeAttachment;
use OrangeHRM\Pim\Api\Model\EmployeeAttachmentModel;
use OrangeHRM\Pim\Service\EmployeeAttachmentService;

class EmployeeAttachmentAPI extends Endpoint implements CrudEndpoint
{
    use UserRoleManagerTrait;

    public const PARAMETER_SCREEN = 'screen';
    public const PARAMETER_ATTACHMENT = 'attachment';
    public const PARAMETER_DESCRIPTION = 'description';

    public const PARAM_RULE_ATTACHMENT_FILE_NAME_MAX_LENGTH = 100;
    public const PARAM_RULE_DESCRIPTION_MAX_LENGTH = 200;

    /**
     * @var EmployeeAttachmentService|null
     */
    protected ?EmployeeAttachmentService $employeeAttachmentService = null;

    /**
     * @return EmployeeAttachmentService
     */
    public function getEmployeeAttachmentService(): EmployeeAttachmentService
    {
        if (!$this->employeeAttachmentService instanceof EmployeeAttachmentService) {
            $this->employeeAttachmentService = new EmployeeAttachmentService();
        }
        return $this->employeeAttachmentService;
    }

    /**
     * @inheritDoc
     */
    public function getOne(): EndpointResourceResult
    {
        list($empNumber, $screen, $id) = $this->getUrlAttributes();
        $employeeAttachment = $this->getEmployeeAttachmentService()->getEmployeeAttachment($empNumber, $id, $screen);
        $this->throwRecordNotFoundExceptionIfNotExist($employeeAttachment, EmployeeAttachment::class);

        return new EndpointResourceResult(
            EmployeeAttachmentModel::class,
            $employeeAttachment,
            new ParameterBag(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                    self::PARAMETER_SCREEN => $screen,
                ]
            )
        );
    }

    /**
     * @return array
     */
    private function getUrlAttributes(): array
    {
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $screen = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_ATTRIBUTE, self::PARAMETER_SCREEN);
        $id = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_ID
        );
        return [$empNumber, $screen, $id];
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_ID, new Rule(Rules::POSITIVE)),
            $this->getEmpNumberRule(),
            $this->getScreenRule(),
        );
    }

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointCollectionResult
    {
        list($empNumber, $screen) = $this->getUrlAttributes();
        $employeeAttachments = $this->getEmployeeAttachmentService()->getEmployeeAttachments($empNumber, $screen);

        return new EndpointCollectionResult(
            EmployeeAttachmentModel::class,
            $employeeAttachments,
            new ParameterBag(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                    self::PARAMETER_SCREEN => $screen,
                    CommonParams::PARAMETER_TOTAL => count($employeeAttachments),
                ]
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getEmpNumberRule(),
            $this->getScreenRule(),
            ...$this->getSortingAndPaginationParamsRules()
        );
    }

    /**
     * @inheritDoc
     */
    public function create(): EndpointResourceResult
    {
        list($empNumber, $screen) = $this->getUrlAttributes();
        $attachment = $this->getRequestParams()->getAttachment(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_ATTACHMENT
        );
        $description = $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_DESCRIPTION
        );

        $employeeAttachment = new EmployeeAttachment();
        $employeeAttachment->getDecorator()->setEmployeeByEmpNumber($empNumber);
        $employeeAttachment->setScreen($screen);
        $employeeAttachment->setDescription($description);
        $this->setAttachmentAttributes($employeeAttachment, $attachment);

        $this->getEmployeeAttachmentService()->saveEmployeeAttachment($employeeAttachment);

        return new EndpointResourceResult(
            EmployeeAttachmentModel::class,
            $employeeAttachment,
            new ParameterBag(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                    self::PARAMETER_SCREEN => $screen,
                ]
            )
        );
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
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getEmpNumberRule(),
            $this->getScreenRule(),
            $this->getAttachmentRule(),
            $this->getValidationDecorator()->notRequiredParamRule($this->getDescriptionRule(), true),
        );
    }

    /**
     * @return ParamRule
     */
    private function getEmpNumberRule(): ParamRule
    {
        return new ParamRule(
            CommonParams::PARAMETER_EMP_NUMBER,
            new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
        );
    }

    /**
     * @return ParamRule
     */
    private function getScreenRule(): ParamRule
    {
        return new ParamRule(
            self::PARAMETER_SCREEN,
            new Rule(Rules::IN, [EmployeeAttachment::SCREENS])
        );
    }

    /**
     * @return ParamRule
     */
    private function getAttachmentRule(): ParamRule
    {
        return new ParamRule(
            self::PARAMETER_ATTACHMENT,
            new Rule(
                Rules::BASE_64_ATTACHMENT,
                [null, null, self::PARAM_RULE_ATTACHMENT_FILE_NAME_MAX_LENGTH]
            )
        );
    }

    /**
     * @return ParamRule
     */
    private function getDescriptionRule(): ParamRule
    {
        return new ParamRule(
            self::PARAMETER_DESCRIPTION,
            new Rule(
                Rules::LENGTH,
                [null, self::PARAM_RULE_DESCRIPTION_MAX_LENGTH]
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function update(): EndpointResourceResult
    {
        list($empNumber, $screen, $id) = $this->getUrlAttributes();
        $attachment = $this->getRequestParams()->getAttachmentOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_ATTACHMENT
        );
        $description = $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_DESCRIPTION,
            null,
            false
        );

        $employeeAttachment = $this->getEmployeeAttachmentService()->getEmployeeAttachment($empNumber, $id, $screen);
        $this->throwRecordNotFoundExceptionIfNotExist($employeeAttachment, EmployeeAttachment::class);

        if (!is_null($description)) {
            $employeeAttachment->setDescription($description);
        }

        if ($attachment) {
            $this->setAttachmentAttributes($employeeAttachment, $attachment);
        }

        $this->getEmployeeAttachmentService()->saveEmployeeAttachment($employeeAttachment);

        return new EndpointResourceResult(
            EmployeeAttachmentModel::class,
            $employeeAttachment,
            new ParameterBag(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                    self::PARAMETER_SCREEN => $screen,
                ]
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_ID, new Rule(Rules::POSITIVE)),
            $this->getEmpNumberRule(),
            $this->getScreenRule(),
            $this->getValidationDecorator()->notRequiredParamRule($this->getAttachmentRule()),
            $this->getValidationDecorator()->notRequiredParamRule($this->getDescriptionRule(), true),
        );
    }

    /**
     * @inheritDoc
     */
    public function delete(): EndpointResourceResult
    {
        list($empNumber, $screen) = $this->getUrlAttributes();
        $ids = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS);
        $this->getEmployeeAttachmentService()->deleteEmployeeAttachments($empNumber, $screen, $ids);
        return new EndpointResourceResult(ArrayModel::class, $ids);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_IDS,
                new Rule(Rules::ARRAY_TYPE)
            ),
            $this->getEmpNumberRule(),
            $this->getScreenRule(),
        );
    }
}
