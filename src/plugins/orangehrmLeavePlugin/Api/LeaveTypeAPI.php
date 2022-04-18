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
     * @inheritDoc
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
     * @return EndpointCollectionResult
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
     * @return ParamRuleCollection
     */
    private function getCommonBodyParamRuleCollection(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(self::PARAMETER_NAME, new Rule(Rules::LENGTH, [null, self::PARAM_RULE_NAME_MAX_LENGTH])),
            new ParamRule(self::PARAMETER_SITUATIONAL, new Rule(Rules::BOOL_TYPE))
        );
    }

    /**
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
        $paramRules = $this->getCommonBodyParamRuleCollection();
        $paramRules->addParamValidation($this->getIdParamRule());
        return $paramRules;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function delete(): EndpointResourceResult
    {
        $ids = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS);
        $this->getLeaveTypeService()->getLeaveTypeDao()->deleteLeaveType($ids);
        return new EndpointResourceResult(ArrayModel::class, $ids);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_IDS),
        );
    }
}
