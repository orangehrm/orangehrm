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
     * @OA\Get(
     *     path="/api/v2/pim/custom-fields/{id}",
     *     tags={"PIM/Custom Field"},
     *     summary="Get a Custom Field",
     *     operationId="get-a-custom-field",
     *     description="This endpoint lets you retrieve a PIM custom field by providing its numerical ID.",
     *     @OA\PathParameter(
     *         name="id",
     *         description="Specify the numerical ID of the desired custom field",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Pim-CustomFieldModel"
     *             ),
     *             @OA\Property(property="meta", type="object", additionalProperties=false)
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
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
     * @OA\Get(
     *     path="/api/v2/pim/custom-fields",
     *     tags={"PIM/Custom Field"},
     *     summary="List All Custom Fields",
     *     operationId="list-all-custom-fields",
     *     description="This endpoint lists all custom PIM fields.",
     *     @OA\Parameter(
     *         name="sortField",
     *         description="Sort the custom field list by its name, type or PIM screen",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=CustomFieldSearchFilterParams::ALLOWED_SORT_FIELDS)
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/sortOrder"),
     *     @OA\Parameter(ref="#/components/parameters/limit"),
     *     @OA\Parameter(ref="#/components/parameters/offset"),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Pim-CustomFieldModel")
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="total", description="The total number of custom fields", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
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
     * @OA\Post(
     *     path="/api/v2/pim/custom-fields",
     *     tags={"PIM/Custom Field"},
     *     summary="Create a Custom Field",
     *     operationId="create-a-custom-field",
     *     description="This endpoint allows you to create a new custom PIM field. You can create up to a maximum of 10 fields.",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="fieldName",
     *                 description="Specify the name of the custom field",
     *                 type="string",
     *                 maxLength=OrangeHRM\Pim\Api\CustomFieldAPI::PARAM_RULE_NAME_MAX_LENGTH
     *             ),
     *             @OA\Property(
     *                 property="fieldType",
     *                 description="Specify whether the field is a text/number field or a dropdown field",
     *                 type="integer",
     *                 enum=OrangeHRM\Entity\CustomField::FIELD_TYPES,
     *                 maxLength=OrangeHRM\Pim\Api\CustomFieldAPI::PARAM_RULE_TYPE_MAX_LENGTH
     *             ),
     *             @OA\Property(
     *                 property="screen",
     *                 description="Specify which PIM screen this field should be displayed",
     *                 type="string",
     *                 enum=OrangeHRM\Entity\CustomField::SCREENS,
     *                 maxLength=OrangeHRM\Pim\Api\CustomFieldAPI::PARAM_RULE_SCREEN_MAX_LENGTH
     *             ),
     *             @OA\Property(
     *                 property="extraData",
     *                 description="Specify a comma separated list of options for the dropdown type custom fields",
     *                 type="string",
     *                 maxLength=OrangeHRM\Pim\Api\CustomFieldAPI::PARAM_RULE_EXTRA_DATA_MAX_LENGTH
     *             ),
     *         ),
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Pim-CustomFieldModel"
     *             ),
     *             @OA\Property(property="meta", type="object", additionalProperties=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="object",
     *                 @OA\Property(property="status", type="string", default="400"),
     *                 @OA\Property(property="message", type="string")
     *             )
     *         )
     *     )
     * )
     *
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
                )
            ),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_TYPE,
                    new Rule(Rules::INT_TYPE),
                    new Rule(Rules::IN, [CustomField::FIELD_TYPES]),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_TYPE_MAX_LENGTH]),
                )
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
     * @OA\Put(
     *     path="/api/v2/pim/custom-fields/{id}",
     *     tags={"PIM/Custom Field"},
     *     summary="Update a Custom Field",
     *     operationId="update-a-custom-field",
     *     description="This endpoint allows you to update a custom PIM field.",
     *     @OA\PathParameter(
     *         name="id",
     *         description="Specify the numerical ID of the desired custom field",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="fieldName",
     *                 description="Specify the name of the custom field",
     *                 type="string",
     *                 maxLength=OrangeHRM\Pim\Api\CustomFieldAPI::PARAM_RULE_NAME_MAX_LENGTH
     *             ),
     *             @OA\Property(
     *                 property="fieldType",
     *                 description="Specify whether the field is a text/number field or a dropdown field",
     *                 type="integer",
     *                 enum=OrangeHRM\Entity\CustomField::FIELD_TYPES,
     *                 maxLength=OrangeHRM\Pim\Api\CustomFieldAPI::PARAM_RULE_TYPE_MAX_LENGTH
     *             ),
     *             @OA\Property(
     *                 property="screen",
     *                 description="Specify which PIM screen this field should be displayed",
     *                 type="string",
     *                 enum=OrangeHRM\Entity\CustomField::SCREENS,
     *                 maxLength=OrangeHRM\Pim\Api\CustomFieldAPI::PARAM_RULE_SCREEN_MAX_LENGTH
     *             ),
     *             @OA\Property(
     *                 property="extraData",
     *                 description="Specify a comma separated list of options for the dropdown type custom fields",
     *                 type="string",
     *                 maxLength=OrangeHRM\Pim\Api\CustomFieldAPI::PARAM_RULE_EXTRA_DATA_MAX_LENGTH
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Pim-CustomFieldModel"
     *             ),
     *             @OA\Property(property="meta", type="object", additionalProperties=false)
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound"),
     *     @OA\Response(
     *         response="400",
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="object",
     *                 @OA\Property(property="status", type="string", default="400"),
     *                 @OA\Property(property="message", type="string", default="Bad Request")
     *             )
     *         )
     *     )
     * )
     *
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
     * @OA\Delete(
     *     path="/api/v2/pim/custom-fields",
     *     tags={"PIM/Custom Field"},
     *     summary="Delete Custom Fields",
     *     operationId="delete-custom-fields",
     *     description="This endpoint allows you to delete PIM custom fields.",
     *     @OA\RequestBody(ref="#/components/requestBodies/DeleteRequestBody"),
     *     @OA\Response(response="200", ref="#/components/responses/DeleteResponse")
     * )
     *
     * @inheritDoc
     * @throws Exception
     */
    public function delete(): EndpointResourceResult
    {
        $ids = $this->getCustomFieldService()->getCustomFieldDao()->getExistingCustomFieldIds(
            $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS)
        );
        $this->throwRecordNotFoundExceptionIfEmptyIds($ids);

        if (count(array_intersect($ids, $this->getCustomFieldService()->getAllFieldsInUse())) == 0) {
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
     */
    public function saveCustomField(CustomField $customField): CustomField
    {
        $this->setCustomField($customField);
        return $this->getCustomFieldService()
            ->getCustomFieldDao()
            ->saveCustomField($customField);
    }
}
