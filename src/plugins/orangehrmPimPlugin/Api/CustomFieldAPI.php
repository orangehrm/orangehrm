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

use Exception;
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
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\CustomField;
use OrangeHRM\Pim\Api\Model\CustomFieldModel;
use OrangeHRM\Pim\Dto\CustomFieldSearchFilterParams;
use OrangeHRM\Pim\Service\CustomFieldService;

class CustomFieldAPI extends Endpoint implements CrudEndpoint
{
    public const PARAMETER_NAME = 'fieldName';
    public const PARAMETER_TYPE = 'fieldType';
    public const PARAMETER_SCREEN = 'screen';
    public const PARAMETER_EXTRA_DATA = 'extraData';

    public const PARAM_RULE_NAME_MAX_LENGTH = 250;
    public const PARAM_RULE_TYPE_MAX_LENGTH = 11;
    public const PARAM_RULE_SCREEN_MAX_LENGTH = 100;
    public const PARAM_RULE_EXTRA_DATA_MAX_LENGTH = 250;

    /**
     * @var null|CustomFieldService
     */
    protected ?CustomFieldService $customFieldService = null;

    /**
     * @return CustomFieldService
     */
    public function getCustomFieldService(): CustomFieldService
    {
        if (is_null($this->customFieldService)) {
            $this->customFieldService = new CustomFieldService();
        }
        return $this->customFieldService;
    }

    /**
     * @inheritDoc
     */
    public function getOne(): EndpointResourceResult
    {
        $id = $this->getUrlAttributes();
        $customField = $this->getCustomFieldService()
            ->getCustomFieldDao()
            ->getCustomFieldById($id);
        $this->throwRecordNotFoundExceptionIfNotExist($customField, CustomField::class);

        return new EndpointResourceResult(
            CustomFieldModel::class,
            $customField,
        );
    }

    /**
     * @return int|null
     */
    private function getUrlAttributes(): ?int
    {
        return $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_ID
        );
    }

    private function getBodyParameters(): array
    {
        $name = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_NAME
        );
        $type = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_TYPE
        );
        $screen = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_SCREEN
        );
        $extraData = $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_EXTRA_DATA
        );
        return [$name, $type, $screen, $extraData];
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE)
            ),
        );
    }

    /**
     * @return EndpointCollectionResult
     * @throws Exception
     */
    public function getAll(): EndpointCollectionResult
    {
        $customFieldSearchParams = new CustomFieldSearchFilterParams();
        $this->setSortingAndPaginationParams($customFieldSearchParams);

        $customFields = $this->getCustomFieldService()->getCustomFieldDao()->searchCustomField(
            $customFieldSearchParams
        );
        return new EndpointCollectionResult(
            CustomFieldModel::class,
            $customFields,
            new ParameterBag(
                [
                    CommonParams::PARAMETER_TOTAL => $this->getCustomFieldService()->getCustomFieldDao(
                    )->getSearchCustomFieldsCount(
                        $customFieldSearchParams
                    )
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
            ...$this->getSortingAndPaginationParamsRules(CustomFieldSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function create(): EndpointResourceResult
    {
        $customFieldSearchParams = new CustomFieldSearchFilterParams();
        $this->setSortingAndPaginationParams($customFieldSearchParams);
        $customFieldsCount = $this->getCustomFieldService()->getCustomFieldDao()->getSearchCustomFieldsCount(
            $customFieldSearchParams
        );
        if ($customFieldsCount >= 10) {
            throw $this->getBadRequestException();
        }
        $customField = new CustomField();
        $customField = $this->saveCustomField($customField);
        return new EndpointResourceResult(
            CustomFieldModel::class,
            $customField,
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            ...$this->getCommonBodyValidationRules(),
        );
    }

    /**
     * @return ParamRule[]
     */
    private function getCommonBodyValidationRules(): array
    {
        return [
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_NAME,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_NAME_MAX_LENGTH]),
                ),
                false
            ),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_TYPE,
                    new Rule(Rules::INT_TYPE),
                    new Rule(Rules::IN, [CustomField::FIELD_TYPES]),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_TYPE_MAX_LENGTH]),
                ),
                false
            ),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_SCREEN,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_SCREEN_MAX_LENGTH]),
                    new Rule(Rules::IN, [CustomField::SCREENS]),
                ),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_EXTRA_DATA,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_EXTRA_DATA_MAX_LENGTH]),
                ),
                true
            ),
        ];
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function update(): EndpointResourceResult
    {
        $id = $this->getUrlAttributes();
        list($name, $type, $screen, $extraData) = $this->getBodyParameters();
        $customField = $this->getCustomFieldService()
            ->getCustomFieldDao()
            ->getCustomFieldById($id);
        $this->throwRecordNotFoundExceptionIfNotExist($customField, CustomField::class);
        if ($extraData !== $customField->getExtraData() && is_string($extraData)) {
            $this->getCustomFieldService()->deleteRelatedEmployeeCustomFieldsExtraData($id, $extraData);
        }
        if ($type == $customField->getType() || !$this->getCustomFieldService()->getCustomFieldDao(
            )->isCustomFieldInUse($id)) {
            $this->saveCustomField($customField);
        } else {
            throw $this->getBadRequestException();
        }
        return new EndpointResourceResult(CustomFieldModel::class, $customField);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_ID, new Rule(Rules::REQUIRED), new Rule(Rules::POSITIVE)),
            ...$this->getCommonBodyValidationRules(),
        );
    }

    /**
     * @inheritDoc
     * @throws DaoException
     * @throws Exception
     */
    public function delete(): EndpointResourceResult
    {
        $ids = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS);

        if (count(array_intersect($ids, $this->getCustomFieldService()->getAllFieldsInUse()))==0) {
            $this->getCustomFieldService()->getCustomFieldDao()->deleteCustomFields($ids);
        } else {
            throw $this->getBadRequestException();
        }
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
            )
        );
    }

    public function setCustomField(CustomField $customField)
    {
        list($name, $type, $screen, $extraData) = $this->getBodyParameters();
        $customField->setName($name);
        $customField->setType($type);
        $customField->setScreen($screen);
        $customField->setExtraData($extraData);
    }

    /**
     * @param CustomField $customField
     * @return CustomField
     * @throws DaoException
     */
    public function saveCustomField(CustomField $customField): CustomField
    {
        $this->setCustomField($customField);
        return $this->getCustomFieldService()
            ->getCustomFieldDao()
            ->saveCustomField($customField);
    }
}
