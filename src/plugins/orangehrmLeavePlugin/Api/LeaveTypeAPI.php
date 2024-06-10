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

namespace OrangeHRM\Leave\Api;

use Exception;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Api\V2\Validator\Rules\EntityUniquePropertyOption;
use OrangeHRM\Entity\Leave;
use OrangeHRM\Entity\LeaveType;
use OrangeHRM\Leave\Api\Model\LeaveTypeModel;
use OrangeHRM\Leave\Dto\LeaveTypeSearchFilterParams;
use OrangeHRM\Leave\Traits\Service\LeaveTypeServiceTrait;

class LeaveTypeAPI extends Endpoint implements CrudEndpoint
{
    use LeaveTypeServiceTrait;

    public const PARAMETER_NAME = 'name';
    public const PARAMETER_SITUATIONAL = 'situational';

    public const FILTER_NAME = 'name';

    public const PARAM_RULE_NAME_MAX_LENGTH = 50;

    /**
     * @OA\Get(
     *     path="/api/v2/leave/leave-types/{id}",
     *     tags={"Leave/Leave Type"},
     *     summary="Get a Leave Type",
     *     operationId="get-a-leave-type",
     *     @OA\PathParameter(
     *         name="id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Leave-LeaveTypeModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     * @throws Exception
     */
    public function getOne(): EndpointResult
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $leaveType = $this->getLeaveTypeService()->getLeaveTypeDao()->getLeaveTypeById($id);
        $this->throwRecordNotFoundExceptionIfNotExist($leaveType, LeaveType::class);
        return new EndpointResourceResult(LeaveTypeModel::class, $leaveType);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getIdParamRule()
        );
    }

    /**
     * @return ParamRule
     */
    private function getIdParamRule(): ParamRule
    {
        return new ParamRule(CommonParams::PARAMETER_ID, new Rule(Rules::POSITIVE));
    }

    /**
     * @OA\Get(
     *     path="/api/v2/leave/leave-types",
     *     tags={"Leave/Leave Type"},
     *     summary="List All Leave Types",
     *     operationId="list-all-leave-types",
     *     @OA\Parameter(
     *         name="sortField",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=LeaveTypeSearchFilterParams::ALLOWED_SORT_FIELDS)
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
     *                 ref="#/components/schemas/Leave-LeaveTypeModel"
     *             ),
     *             @OA\Property(property="meta",
     *                 type="object",
     *                 @OA\Property(property="total", type="integer")
     *             )
     *         )
     *     ),
     * )
     *
     * @inheritDoc
     * @throws Exception
     */
    public function getAll(): EndpointCollectionResult
    {
        $leaveTypeSearchFilterParams = new LeaveTypeSearchFilterParams();
        $this->setSortingAndPaginationParams($leaveTypeSearchFilterParams);
        $leaveTypeSearchFilterParams->setName(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_NAME
            )
        );

        $leaveTypes = $this->getLeaveTypeService()->getLeaveTypeDao()->searchLeaveType($leaveTypeSearchFilterParams);

        return new EndpointCollectionResult(
            LeaveTypeModel::class,
            $leaveTypes,
            new ParameterBag(
                [
                    CommonParams::PARAMETER_TOTAL => $this->getLeaveTypeService()->getLeaveTypeDao(
                    )->getSearchLeaveTypesCount(
                        $leaveTypeSearchFilterParams
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
            new ParamRule(self::FILTER_NAME),
            ...$this->getSortingAndPaginationParamsRules(LeaveTypeSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v2/leave/leave-types",
     *     tags={"Leave/Leave Type"},
     *     summary="Create a Leave Type",
     *     operationId="create-a-leave-type",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", maxLength=OrangeHRM\Leave\Api\LeaveTypeAPI::PARAM_RULE_NAME_MAX_LENGTH),
     *             @OA\Property(property="situational", type="boolean", default="false"),
     *             required={"name"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Leave-LeaveTypeModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     * )
     *
     * @inheritDoc
     */
    public function create(): EndpointResult
    {
        $leaveType = new LeaveType();
        $this->setLeaveTypeParams($leaveType);
        $this->getLeaveTypeService()->getLeaveTypeDao()->saveLeaveType($leaveType);
        return new EndpointResourceResult(LeaveTypeModel::class, $leaveType);
    }

    /**
     * @param LeaveType $leaveType
     */
    private function setLeaveTypeParams(LeaveType $leaveType): void
    {
        $leaveType->setName($this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_NAME));
        $leaveType->setSituational(
            $this->getRequestParams()->getBoolean(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_SITUATIONAL)
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return $this->getCommonBodyParamRuleCollection();
    }

    /**
     * @param EntityUniquePropertyOption|null $uniqueOption
     * @return ParamRuleCollection
     */
    private function getCommonBodyParamRuleCollection(?EntityUniquePropertyOption $uniqueOption = null): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_NAME,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_NAME_MAX_LENGTH]),
                    new Rule(Rules::ENTITY_UNIQUE_PROPERTY, [LeaveType::class, 'name', $uniqueOption])
                )
            ),
            new ParamRule(self::PARAMETER_SITUATIONAL, new Rule(Rules::BOOL_TYPE))
        );
    }

    /**
     * @OA\Put(
     *     path="/api/v2/leave/leave-types/{id}",
     *     tags={"Leave/Leave Type"},
     *     summary="Update a Leave Type",
     *     operationId="update-a-leave-type",
     *     @OA\PathParameter(
     *         name="id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="situational", type="boolean")
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Leave-LeaveTypeModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     */
    public function update(): EndpointResult
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $leaveType = $this->getLeaveTypeService()->getLeaveTypeDao()->getLeaveTypeById($id);
        $this->throwRecordNotFoundExceptionIfNotExist($leaveType, LeaveType::class);
        $this->setLeaveTypeParams($leaveType);
        $this->getLeaveTypeService()->getLeaveTypeDao()->saveLeaveType($leaveType);
        return new EndpointResourceResult(LeaveTypeModel::class, $leaveType);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        $uniqueOption = new EntityUniquePropertyOption();
        $uniqueOption->setIgnoreId($this->getAttributeId());

        $paramRules = $this->getCommonBodyParamRuleCollection($uniqueOption);
        $paramRules->addParamValidation($this->getIdParamRule());
        return $paramRules;
    }

    /**
     * @OA\Delete(
     *     path="/api/v2/leave/leave-types",
     *     tags={"Leave/Leave Type"},
     *     summary="Delete Leave Types",
     *     operationId="delete-leave-types",
     *     @OA\RequestBody(ref="#/components/requestBodies/DeleteRequestBody"),
     *     @OA\Response(response="200", ref="#/components/responses/DeleteResponse"),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     * @throws Exception
     */
    public function delete(): EndpointResourceResult
    {
        $ids = $this->getLeaveTypeService()->getLeaveTypeDao()->getExistingLeaveTypeIds(
            $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS)
        );
        $this->throwRecordNotFoundExceptionIfEmptyIds($ids);
        $this->getLeaveTypeService()->getLeaveTypeDao()->deleteLeaveType($ids);
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
                new Rule(Rules::INT_ARRAY)
            ),
        );
    }
}
