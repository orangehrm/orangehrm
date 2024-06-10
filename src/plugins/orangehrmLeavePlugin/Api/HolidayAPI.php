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
     * @OA\Get(
     *     path="/api/v2/leave/holidays/{id}",
     *     tags={"Leave/Holiday"},
     *     summary="Get a Holiday",
     *     operationId="get-a-holiday",
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
     *                 ref="#/components/schemas/Leave-HolidayModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
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
     * @OA\Get(
     *     path="/api/v2/leave/holidays",
     *     tags={"Leave/Holiday"},
     *     summary="List All Holidays",
     *     operationId="list-all-holidays",
     *     @OA\Parameter(
     *         name="toDate",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="fromDate",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="sortField",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=HolidaySearchFilterParams::ALLOWED_SORT_FIELDS)
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
     *                 ref="#/components/schemas/Leave-HolidayModel"
     *             ),
     *             @OA\Property(property="meta",
     *                 type="object",
     *                 @OA\Property(property="total", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
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
     * @OA\Post(
     *     path="/api/v2/leave/holidays",
     *     tags={"Leave/Holiday"},
     *     summary="Create a Holiday",
     *     operationId="create-a-holiday",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="date", type="string", format="date"),
     *             @OA\Property(
     *                 property="length",
     *                 type="integer",
     *                 enum={0, 4},
     *                 description="0 - working day, 4 - half day"
     *             ),
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 maxLength=OrangeHRM\Leave\Api\HolidayAPI::PARAM_RULE_NAME_MAX_LENGTH
     *             ),
     *             @OA\Property(property="recurring", type="boolean"),
     *             required={"name", "date"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Leave-HolidayModel"
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
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_NAME,
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_NAME_MAX_LENGTH])
                )
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
     * @OA\Put(
     *     path="/api/v2/leave/holidays/{id}",
     *     tags={"Leave/Holiday"},
     *     summary="Update a Holiday",
     *     operationId="Update a Holiday",
     *     @OA\PathParameter(
     *         name="id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="date", type="string", format="date"),
     *             @OA\Property(
     *                 property="length",
     *                 type="integer",
     *                 enum={ 0, 4},
     *                 description="0 - working day, 4 - half day"
     *             ),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="recurring", type="boolean"),
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Leave-HolidayModel"
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
     * @OA\Delete(
     *     path="/api/v2/leave/holidays",
     *     tags={"Leave/Holiday"},
     *     summary="Delete Holidays",
     *     operationId="delete-holidays",
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
        $ids = $this->getHolidayService()->getHolidayDao()->getExistingHolidayIds(
            $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS)
        );
        $this->throwRecordNotFoundExceptionIfEmptyIds($ids);
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
                new Rule(Rules::INT_ARRAY)
            )
        );
    }
}
