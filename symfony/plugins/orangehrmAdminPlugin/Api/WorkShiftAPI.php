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

namespace OrangeHRM\Admin\Api;

use DateTime;
use OrangeHRM\Admin\Api\Model\WorkShiftDetailedModel;
use OrangeHRM\Admin\Api\Model\WorkShiftModel;
use OrangeHRM\Admin\Dto\WorkShiftSearchFilterParams;
use OrangeHRM\Admin\Traits\Service\WorkShiftServiceTrait;
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
use OrangeHRM\Entity\WorkShift;

class WorkShiftAPI extends EndPoint implements CrudEndpoint
{
    use WorkShiftServiceTrait;

    public const PARAMETER_NAME = 'name';
    public const PARAMETER_HOURS_PER_DAY = 'hoursPerDay';
    public const PARAMETER_START_TIME = 'startTime';
    public const PARAMETER_END_TIME = 'endTime';
    public const PARAMETER_EMP_NUMBERS = 'empNumbers';
    public const PARAM_RULE_NAME_MAX_LENGTH = 50;

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        $workShiftSearchFilterParams = new WorkShiftSearchFilterParams();
        $this->setSortingAndPaginationParams($workShiftSearchFilterParams);
        $workShifts = $this->getWorkShiftService()->getWorkShiftList($workShiftSearchFilterParams);
        $count = $this->getWorkShiftService()->getWorkShiftCount($workShiftSearchFilterParams);
        return new EndpointCollectionResult(
            WorkShiftModel::class,
            $workShifts,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $count])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            ...$this->getSortingAndPaginationParamsRules(WorkShiftSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $workShift = $this->getWorkShiftService()->getWorkShiftById($id);
        $this->throwRecordNotFoundExceptionIfNotExist($workShift, WorkShift::class);

        return new EndpointResourceResult(WorkShiftDetailedModel::class, $workShift);
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
     * @inheritDoc
     */
    public function create(): EndpointResult
    {
        $workShift = new WorkShift();
        $empNumbers = $this->getRequestParams()->getArray(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_EMP_NUMBERS
        );
        $this->setParamsToWorkShift($workShift);
        $this->getWorkShiftService()->getWorkShiftDao()->saveWorkShift($workShift, $empNumbers);

        return new EndpointResourceResult(WorkShiftModel::class, $workShift);
    }

    /**
     * @param WorkShift $workShift
     * @return void
     */
    private function setParamsToWorkShift(WorkShift $workShift): void
    {
        $workShiftName = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_NAME);
        $workShiftHoursPerDay = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_HOURS_PER_DAY
        );
        $workShiftStartTime = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_START_TIME
        );
        $workShiftEndTime = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_END_TIME
        );
        $workShift->setName($workShiftName);
        $workShift->setHoursPerDay($workShiftHoursPerDay);
        $workShift->setStartTime(new DateTime($workShiftStartTime));
        $workShift->setEndTime(new DateTime($workShiftEndTime));
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_NAME,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::REQUIRED),
                new Rule(Rules::LENGTH, [null, self::PARAM_RULE_NAME_MAX_LENGTH])
            ),
            new ParamRule(self::PARAMETER_HOURS_PER_DAY, new Rule(Rules::REQUIRED), new Rule(Rules::STRING_TYPE)),
            new ParamRule(self::PARAMETER_START_TIME, new Rule(Rules::REQUIRED), new Rule(Rules::DATE_TIME)),
            new ParamRule(self::PARAMETER_END_TIME, new Rule(Rules::REQUIRED), new Rule(Rules::DATE_TIME)),
            new ParamRule(self::PARAMETER_EMP_NUMBERS, new Rule(Rules::ARRAY_TYPE)),
        );
    }

    /**
     * @inheritDoc
     */
    public function delete(): EndpointResult
    {
        $ids = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS);
        $this->getWorkShiftService()->getWorkShiftDao()->deleteWorkShifts($ids);

        return new EndpointResourceResult(ArrayModel::class, $ids);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_IDS, new Rule(Rules::ARRAY_TYPE)),
        );
    }

    /**
     * @inheritDoc
     */
    public function update(): EndpointResult
    {
        $workShiftId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_ID
        );
        $empNumbers = $this->getRequestParams()->getArray(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_EMP_NUMBERS
        );
        $workShift = $this->getWorkShiftService()->getWorkShiftById($workShiftId);
        $this->throwRecordNotFoundExceptionIfNotExist($workShift, WorkShift::class);
        $this->setParamsToWorkShift($workShift);
        $this->getWorkShiftService()->getWorkShiftDao()->updateWorkShift($workShift, $empNumbers);

        return new EndpointResourceResult(WorkShiftModel::class, $workShift);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_ID, new Rule(Rules::POSITIVE)),
            new ParamRule(
                self::PARAMETER_NAME,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::REQUIRED),
                new Rule(Rules::LENGTH, [null, self::PARAM_RULE_NAME_MAX_LENGTH])
            ),
            new ParamRule(self::PARAMETER_HOURS_PER_DAY, new Rule(Rules::REQUIRED), new Rule(Rules::STRING_TYPE)),
            new ParamRule(self::PARAMETER_START_TIME, new Rule(Rules::REQUIRED), new Rule(Rules::DATE_TIME)),
            new ParamRule(self::PARAMETER_END_TIME, new Rule(Rules::REQUIRED), new Rule(Rules::DATE_TIME)),
            new ParamRule(self::PARAMETER_EMP_NUMBERS, new Rule(Rules::ARRAY_TYPE)),
        );
    }
}
