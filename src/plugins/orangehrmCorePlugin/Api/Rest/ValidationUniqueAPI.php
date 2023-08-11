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

namespace OrangeHRM\Core\Api\Rest;

use Exception;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\ResourceEndpoint;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Dao\ValidationUniqueDao;

class ValidationUniqueAPI extends Endpoint implements ResourceEndpoint
{
    public const PARAMETER_VALUE = 'value';
    public const PARAMETER_ENTITY_ID = 'entityId';
    public const PARAMETER_ENTITY_ID_FIELD = 'entityIdField';
    public const PARAMETER_ENTITY_NAME = 'entityName';
    public const PARAMETER_ATTRIBUTE_NAME = 'attributeName';
    public const PARAMETER_MATCH_BY_FIELD = 'matchByField';
    public const PARAMETER_MATCH_BY_VALUE = 'matchByValue';
    public const PARAMETER_IS_UNIQUE_RECORD = 'valid';
    public const PARAM_RULE_VALUE_MAX_LENGTH = 500;
    public const PARAM_RULE_ENTITY_NAME_MAX_LENGTH = 50;
    public const PARAM_RULE_ATTRIBUTE_NAME_MAX_LENGTH = 100;
    public const ADMIN_VALIDATION_ENTITIES = [
        'EmailSubscriber',
        'EmploymentStatus',
        'JobCategory',
        'JobTitle',
        'Location',
        'Nationality',
        'PayGrade',
        'Education',
        'Language',
        'License',
        'Membership',
        'Skill',
        'User',
        'WorkShift',
    ];
    public const CLAIM_VALIDATION_ENTITIES = ['ClaimEvent', 'ExpenseType'];
    public const LEAVE_VALIDATION_ENTITIES = ['LeaveType'];
    public const PIM_VALIDATION_ENTITIES = ['Employee', 'TerminationReason'];
    public const RECRUITMENT_VALIDATION_ENTITIES = ['Vacancy'];
    public const VALIDATION_ENTITY_MAP = [
        ...self::ADMIN_VALIDATION_ENTITIES,
        ...self::CLAIM_VALIDATION_ENTITIES,
        ...self::LEAVE_VALIDATION_ENTITIES,
        ...self::PIM_VALIDATION_ENTITIES,
        ...self::RECRUITMENT_VALIDATION_ENTITIES,
    ];

    private ?ValidationUniqueDao $validationUniqueDao = null;

    /**
     * @OA\Get(
     *     path="/api/v2/core/validation/unique",
     *     tags={"Core/Unique Validation"},
     *     summary="Validate Uniqueness",
     *     operationId="validate-uniqueness",
     *     @OA\Parameter(
     *         name="value",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="entityName",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string", enum=OrangeHRM\Core\Api\Rest\ValidationUniqueAPI::VALIDATION_ENTITY_MAP)
     *     ),
     *     @OA\Parameter(
     *         name="attributeName",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="entityId",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="entityIdField",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="matchByField",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="matchByValue",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="valid", type="boolean")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Bad Request - Invalid field name given",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="object",
     *                 @OA\Property(property="status", type="string", default="400"),
     *                 @OA\Property(property="message", type="string", default="Entity does not contain one or more of the given fields")
     *             )
     *         )
     *     )
     * )
     *
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        $value = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_QUERY, self::PARAMETER_VALUE);
        $entityName = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_QUERY, self::PARAMETER_ENTITY_NAME);
        $attributeName = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_QUERY, self::PARAMETER_ATTRIBUTE_NAME);
        $entityId = $this->getRequestParams()->getIntOrNull(RequestParams::PARAM_TYPE_QUERY, self::PARAMETER_ENTITY_ID);
        $entityIdField = $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_QUERY, self::PARAMETER_ENTITY_ID_FIELD);
        $matchByField = $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_QUERY, self::PARAMETER_MATCH_BY_FIELD);
        $matchByValue = $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_QUERY, self::PARAMETER_MATCH_BY_VALUE);

        try {
            $unique = $this->getValidationUniqueDao()->isValueUnique(
                $value,
                $entityName,
                $attributeName,
                $entityId,
                $entityIdField,
                $matchByField,
                $matchByValue
            );
        } catch (Exception $exception) {
            throw $this->getBadRequestException("Entity does not contain one or more of the given fields");
        }

        return new EndpointResourceResult(
            ArrayModel::class,
            [
                self::PARAMETER_IS_UNIQUE_RECORD => $unique,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_VALUE,
                new Rule(Rules::LENGTH, [null, self::PARAM_RULE_VALUE_MAX_LENGTH])
            ),
            new ParamRule(
                self::PARAMETER_ENTITY_NAME,
                new Rule(Rules::LENGTH, [null, self::PARAM_RULE_ENTITY_NAME_MAX_LENGTH]),
                new Rule(Rules::IN, [self::VALIDATION_ENTITY_MAP])
            ),
            new ParamRule(
                self::PARAMETER_ATTRIBUTE_NAME,
                new Rule(Rules::LENGTH, [null, self::PARAM_RULE_ATTRIBUTE_NAME_MAX_LENGTH])
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(self::PARAMETER_ENTITY_ID, new Rule(Rules::POSITIVE))
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_ENTITY_ID_FIELD,
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_ATTRIBUTE_NAME_MAX_LENGTH])
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_MATCH_BY_FIELD,
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_ENTITY_NAME_MAX_LENGTH])
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_MATCH_BY_VALUE,
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_ATTRIBUTE_NAME_MAX_LENGTH])
                )
            )
        );
    }

    /**
     * @return ValidationUniqueDao
     */
    public function getValidationUniqueDao(): ValidationUniqueDao
    {
        return $this->validationUniqueDao ??= new ValidationUniqueDao();
    }

    /**
     * @inheritDoc
     */
    public function update(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function delete(): EndpointResult
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
