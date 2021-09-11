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
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\Holiday;
use OrangeHRM\Leave\Api\Model\HolidayModel;
use OrangeHRM\Leave\Dto\HolidaySearchFilterParams;
use OrangeHRM\Leave\Traits\Service\HolidayServiceTrait;

class HolidayAPI extends Endpoint implements CrudEndpoint
{
    use HolidayServiceTrait;

    public const PARAMETER_NAME = 'name';
    public const PARAMETER_DATE = 'date';
    public const PARAMETER_RECURRING = 'recurring';
    public const PARAMETER_LENGTH = 'length';

    public const FILTER_FROM_DATE = 'fromDate';
    public const FILTER_TO_DATE = 'toDate';

    public const PARAM_RULE_NAME_MAX_LENGTH = 200;

    /**
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        $holiday = $this->getHolidayService()->getHolidayDao()->getHolidayById($this->getIdFromUrlAttributes());
        $this->throwRecordNotFoundExceptionIfNotExist($holiday, Holiday::class);

        return new EndpointResourceResult(HolidayModel::class, $holiday);
    }

    /**
     * @return int
     */
    private function getIdFromUrlAttributes(): int
    {
        return $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
    }

    /**
     * @return ParamRule
     */
    private function getIdParamRule(): ParamRule
    {
        return new ParamRule(CommonParams::PARAMETER_ID, new Rule(Rules::POSITIVE));
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection($this->getIdParamRule());
    }

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        $fromDate = $this->getRequestParams()->getDateTime(RequestParams::PARAM_TYPE_QUERY, self::FILTER_FROM_DATE);
        $toDate = $this->getRequestParams()->getDateTime(RequestParams::PARAM_TYPE_QUERY, self::FILTER_TO_DATE);

        $holidaySearchFilterParams = new HolidaySearchFilterParams();
        $this->setSortingAndPaginationParams($holidaySearchFilterParams);
        $holidaySearchFilterParams->setFromDate($fromDate);
        $holidaySearchFilterParams->setToDate($toDate);
        $holidays = $this->getHolidayService()->searchHolidays($holidaySearchFilterParams);
        $total = $this->getHolidayService()->searchHolidaysCount($holidaySearchFilterParams);

        return new EndpointCollectionResult(
            HolidayModel::class,
            $holidays,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $total])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(self::FILTER_FROM_DATE, new Rule(Rules::API_DATE)),
            new ParamRule(self::FILTER_TO_DATE, new Rule(Rules::API_DATE)),
            ...$this->getSortingAndPaginationParamsRules()
        );
    }

    /**
     * @inheritDoc
     */
    public function create(): EndpointResult
    {
        $holiday = new Holiday();
        $this->setHolidayParams($holiday);
        $this->getHolidayService()->saveHoliday($holiday);
        return new EndpointResourceResult(HolidayModel::class, $holiday);
    }

    /**
     * @param Holiday $holiday
     */
    private function setHolidayParams(Holiday $holiday): void
    {
        $holiday->setName(
            $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_NAME)
        );
        $holiday->setDate($this->getRequestParams()->getDateTime(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_DATE));
        $holiday->setRecurring(
            $this->getRequestParams()->getBoolean(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_RECURRING)
        );
        $holiday->setLength($this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_LENGTH));
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
            new ParamRule(
                self::PARAMETER_NAME,
                new Rule(Rules::LENGTH, [null, self::PARAM_RULE_NAME_MAX_LENGTH])
            ),
            new ParamRule(
                self::PARAMETER_DATE,
                new Rule(Rules::API_DATE)
            ),
            new ParamRule(
                self::PARAMETER_RECURRING,
                new Rule(Rules::BOOL_TYPE)
            ),
            new ParamRule(
                self::PARAMETER_LENGTH,
                new Rule(Rules::IN, [array_keys(Holiday::HOLIDAY_LENGTH_MAP)])
            ),
        );
    }

    /**
     * @inheritDoc
     */
    public function update(): EndpointResult
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $holiday = $this->getHolidayService()->getHolidayDao()->getHolidayById($id);
        $this->throwRecordNotFoundExceptionIfNotExist($holiday, Holiday::class);

        $this->setHolidayParams($holiday);
        $this->getHolidayService()->saveHoliday($holiday);
        return new EndpointResourceResult(HolidayModel::class, $holiday);
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
     * @throws DaoException
     * @throws Exception
     */
    public function delete(): EndpointResourceResult
    {
        $ids = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS);
        $this->getHolidayService()->deleteHolidays($ids);
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
}
