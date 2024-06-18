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

namespace OrangeHRM\Performance\Api;

use DateTime;
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
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\PerformanceReview;
use OrangeHRM\Performance\Api\Model\DetailedPerformanceReviewModel;
use OrangeHRM\Performance\Api\Model\PerformanceReviewModel;
use OrangeHRM\Performance\Dto\PerformanceReviewSearchFilterParams;
use OrangeHRM\Performance\Exception\ReviewServiceException;
use OrangeHRM\Performance\Traits\Service\PerformanceReviewServiceTrait;
use OrangeHRM\Pim\Traits\Service\EmployeeServiceTrait;

class PerformanceReviewAPI extends Endpoint implements CrudEndpoint
{
    use PerformanceReviewServiceTrait;
    use DateTimeHelperTrait;
    use EmployeeServiceTrait;

    public const FILTER_REVIEWER_EMP_NUMBER = 'reviewerEmpNumber';
    public const PARAMETER_PERIOD_START_DATE = 'startDate';
    public const PARAMETER_PERIOD_END_DATE = 'endDate';
    public const PARAMETER_DUE_DATE = 'dueDate';
    public const PARAMETER_ACTIVATE = 'activate';
    public const FILTER_JOB_TITLE_ID = 'jobTitleId';
    public const FILTER_STATUS_ID = 'statusId';
    public const FILTER_FROM_DATE = 'fromDate';
    public const FILTER_TO_DATE = 'toDate';
    public const FILTER_INCLUDE_EMPLOYEES = 'includeEmployees';

    /**
     * @OA\Get(
     *     path="/api/v2/performance/manage/reviews",
     *     tags={"Performance/Review Configuration"},
     *     summary="List All Performance Reviews",
     *     operationId="list-all-performance-reviews",
     *     @OA\Parameter(
     *         name="empNumber",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="jobTitleId",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="statusId",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="reviewerEmpNumber",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="fromDate",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="toDate",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="includeEmployees",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=PerformanceReviewSearchFilterParams::INCLUDE_EMPLOYEES)
     *     ),
     *     @OA\Parameter(
     *         name="sortField",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=PerformanceReviewSearchFilterParams::PERFORMANCE_REVIEW_ALLOWED_SORT_FIELDS)
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
     *                 @OA\Items(ref="#/components/schemas/Performance-PerformanceReviewModel")
     *             ),
     *             @OA\Property(property="meta",
     *                 type="object",
     *                 @OA\Property(property="total", type="integer")
     *             )
     *         )
     *     )
     * )
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        $performanceReviewSearchFilterParams = new PerformanceReviewSearchFilterParams();
        $this->setSortingAndPaginationParams($performanceReviewSearchFilterParams);

        $performanceReviewSearchFilterParams->setReviewerEmpNumber(
            $this->getRequestParams()->getIntOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_REVIEWER_EMP_NUMBER
            )
        );
        $this->getFilterParams($performanceReviewSearchFilterParams);

        $performanceReviewList = $this->getPerformanceReviewService()
            ->getPerformanceReviewDao()
            ->getPerformanceReviewList($performanceReviewSearchFilterParams);
        $performanceReviewCount = $this->getPerformanceReviewService()
            ->getPerformanceReviewDao()
            ->getPerformanceReviewCount($performanceReviewSearchFilterParams);

        return new EndpointCollectionResult(
            PerformanceReviewModel::class,
            $performanceReviewList,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $performanceReviewCount])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_REVIEWER_EMP_NUMBER,
                    new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_STATUS_ID,
                    new Rule(Rules::POSITIVE),
                    new Rule(Rules::IN, [PerformanceReviewSearchFilterParams::PERFORMANCE_REVIEW_STATUSES])
                )
            ),
            ...$this->getFilterParamRules(),
            ...$this->getSortingAndPaginationParamsRules(
                PerformanceReviewSearchFilterParams::PERFORMANCE_REVIEW_ALLOWED_SORT_FIELDS
            )
        );
    }

    /**
     * @param PerformanceReviewSearchFilterParams $performanceReviewSearchFilterParams
     */
    protected function getFilterParams(PerformanceReviewSearchFilterParams $performanceReviewSearchFilterParams): void
    {
        $performanceReviewSearchFilterParams->setEmpNumber(
            $this->getRequestParams()->getIntOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                CommonParams::PARAMETER_EMP_NUMBER
            )
        );
        $performanceReviewSearchFilterParams->setJobTitleId(
            $this->getRequestParams()->getIntOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_JOB_TITLE_ID
            )
        );
        $performanceReviewSearchFilterParams->setStatusId(
            $this->getRequestParams()->getIntOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_STATUS_ID
            )
        );

        $fromDate = $this->getRequestParams()->getDateTimeOrNull(
            RequestParams::PARAM_TYPE_QUERY,
            self::FILTER_FROM_DATE
        );
        $toDate = $this->getRequestParams()->getDateTimeOrNull(
            RequestParams::PARAM_TYPE_QUERY,
            self::FILTER_TO_DATE
        );

        if (is_null($fromDate) && is_null($toDate)) {
            $currentYear = $this->getDateTimeHelper()->getNow()->format('Y');
            $performanceReviewSearchFilterParams->setFromDate(DateTime::createFromFormat('Y-m-d', "$currentYear-01-01"));
            $performanceReviewSearchFilterParams->setToDate(DateTime::createFromFormat('Y-m-d', "$currentYear-12-31"));
        } else {
            $performanceReviewSearchFilterParams->setFromDate($fromDate);
            $performanceReviewSearchFilterParams->setToDate($toDate);
        }

        $performanceReviewSearchFilterParams->setIncludeEmployees(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_INCLUDE_EMPLOYEES,
                PerformanceReviewSearchFilterParams::INCLUDE_EMPLOYEES_ONLY_CURRENT
            )
        );
    }

    /**
     * @return ParamRule[]
     */
    protected function getFilterParamRules(): array
    {
        return [
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    CommonParams::PARAMETER_EMP_NUMBER,
                    new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(self::FILTER_JOB_TITLE_ID, new Rule(Rules::POSITIVE))
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(self::FILTER_FROM_DATE, new Rule(Rules::API_DATE))
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(self::FILTER_TO_DATE, new Rule(Rules::API_DATE))
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_INCLUDE_EMPLOYEES,
                    new Rule(Rules::IN, [PerformanceReviewSearchFilterParams::INCLUDE_EMPLOYEES])
                )
            )
        ];
    }

    /**
     * @OA\Post(
     *     path="/api/v2/performance/manage/reviews",
     *     tags={"Performance/Review Configuration"},
     *     summary="Create a Performance Review",
     *     operationId="create-a-performance-review",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="empNumber", type="integer"),
     *             @OA\Property(property="reviewerEmpNumber", type="integer"),
     *             @OA\Property(property="startDate", type="number"),
     *             @OA\Property(property="endDate", type="number"),
     *             @OA\Property(property="dueDate", type="number"),
     *             @OA\Property(property="activate", type="boolean"),
     *             required={"empNumber", "reviewerEmpNumber", "startDate", "endDate", "dueDate"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Performance-PerformanceReviewModel"
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
        $performanceReview = new PerformanceReview();
        $this->setReviewParams($performanceReview);
        $reviewerEmpNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_BODY,
            self::FILTER_REVIEWER_EMP_NUMBER
        );
        $reportToRecord = $this->getPerformanceReviewService()->getPerformanceReviewDao()
            ->getSupervisorRecord($performanceReview->getEmployee()->getEmpNumber(), $reviewerEmpNumber);
        if ($reportToRecord == null) {
            throw $this->getBadRequestException();
        }
        if ($this->getRequestParams()
                ->getBooleanOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_ACTIVATE) == true
        ) {
            try {
                $performanceReview->setActivatedDate($this->getDateTimeHelper()->getNow());
                $performanceReview->setStatusId(PerformanceReview::STATUS_ACTIVATED);
                $this->getPerformanceReviewService()->activateReview($performanceReview, $reviewerEmpNumber);
            } catch (ReviewServiceException $e) {
                throw $this->getBadRequestException($e->getMessage());
            }
        } else {
            $performanceReview->setStatusId(PerformanceReview::STATUS_INACTIVE);
            $this->getPerformanceReviewService()->getPerformanceReviewDao()->createReview($performanceReview, $reviewerEmpNumber);
        }
        return new EndpointResourceResult(DetailedPerformanceReviewModel::class, $performanceReview);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            ...$this->getCommonValidationRules()
        );
    }

    /**
     * @return array
     */
    protected function getCommonValidationRules(): array
    {
        return [
            new ParamRule(
                CommonParams::PARAMETER_EMP_NUMBER,
                new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
            ),
            new ParamRule(
                self::FILTER_REVIEWER_EMP_NUMBER,
                new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
            ),
            new ParamRule(
                self::PARAMETER_PERIOD_START_DATE,
                new Rule(Rules::API_DATE)
            ),
            new ParamRule(
                self::PARAMETER_PERIOD_END_DATE,
                new Rule(Rules::API_DATE)
            ),
            new ParamRule(
                self::PARAMETER_DUE_DATE,
                new Rule(Rules::API_DATE)
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_ACTIVATE,
                    new Rule(Rules::BOOL_VAL)
                )
            ),
        ];
    }

    /**
     * @param PerformanceReview $performanceReview
     * @return void
     */
    private function setReviewParams(PerformanceReview $performanceReview): void
    {
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_BODY,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $performanceReview->getDecorator()->setEmployeeByEmpNumber($empNumber);
        $performanceReview->setReviewPeriodStart(
            $this->getRequestParams()->getDateTime(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_PERIOD_START_DATE)
        );
        $performanceReview->setReviewPeriodEnd(
            $this->getRequestParams()->getDateTime(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_PERIOD_END_DATE)
        );
        $performanceReview->setDueDate(
            $this->getRequestParams()->getDateTime(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_DUE_DATE)
        );
        $employee = $this->getEmployeeService()->getEmployeeDao()->getEmployeeByEmpNumber($empNumber);
        $performanceReview->setJobTitle($employee->getJobTitle());
        $performanceReview->setSubunit($employee->getSubDivision());
    }

    /**
     * @OA\Delete(
     *     path="/api/v2/performance/manage/reviews",
     *     tags={"Performance/Review Configuration"},
     *     summary="Delete Performance Reviews",
     *     operationId="delete-performance-reviews",
     *     @OA\RequestBody(ref="#/components/requestBodies/DeleteRequestBody"),
     *     @OA\Response(response="200", ref="#/components/responses/DeleteResponse"),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     */
    public function delete(): EndpointResult
    {
        $ids = $this->getPerformanceReviewService()->getPerformanceReviewDao()->getExistingPerformanceReviewIds(
            $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS)
        );
        $this->throwRecordNotFoundExceptionIfEmptyIds($ids);
        $this->getPerformanceReviewService()->getPerformanceReviewDao()->deletePerformanceReviews($ids);
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

    /**
     * @OA\Get(
     *     path="/api/v2/performance/manage/reviews/{id}",
     *     tags={"Performance/Review Configuration"},
     *     summary="Get a Performance Review",
     *     operationId="get-a-performance-review",
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
     *                 ref="#/components/schemas/Performance-PerformanceReviewModel"
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
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $review = $this->getPerformanceReviewService()->getPerformanceReviewDao()->getEditableReviewById($id);
        $this->throwRecordNotFoundExceptionIfNotExist($review, PerformanceReview::class);
        return new EndpointResourceResult(PerformanceReviewModel::class, $review);
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
            )
        );
    }

    /**
     * @OA\Put(
     *     path="/api/v2/performance/manage/reviews/{id}",
     *     tags={"Performance/Review Configuration"},
     *     summary="Update a Performance Review",
     *     operationId="update-a-performance-review",
     *     @OA\PathParameter(
     *         name="id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="empNumber", type="integer"),
     *             @OA\Property(property="reviewerEmpNumber", type="integer"),
     *             @OA\Property(property="startDate", type="number"),
     *             @OA\Property(property="endDate", type="number"),
     *             @OA\Property(property="dueDate", type="number"),
     *             @OA\Property(property="activate", type="boolean"),
     *             required={"empNumber", "reviewerEmpNumber", "startDate", "endDate", "dueDate"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Performance-PerformanceReviewModel"
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
        $review = $this->getPerformanceReviewService()->getPerformanceReviewDao()->getEditableReviewById($id);
        $this->throwRecordNotFoundExceptionIfNotExist($review, PerformanceReview::class);
        $this->setReviewParams($review);
        $reviewerEmpNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_BODY,
            self::FILTER_REVIEWER_EMP_NUMBER
        );
        $reportToRecord = $this->getPerformanceReviewService()->getPerformanceReviewDao()
            ->getSupervisorRecord($review->getEmployee()->getEmpNumber(), $reviewerEmpNumber);
        if ($reportToRecord == null) {
            throw $this->getBadRequestException();
        }
        if ($this->getRequestParams()->getBooleanOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_ACTIVATE) == true) {
            try {
                $review->setActivatedDate($this->getDateTimeHelper()->getNow());
                $review->setStatusId(PerformanceReview::STATUS_ACTIVATED);
                $this->getPerformanceReviewService()->updateActivateReview($review, $reviewerEmpNumber);
            } catch (ReviewServiceException $e) {
                throw $this->getBadRequestException($e->getMessage());
            }
        } else {
            $review->setStatusId(PerformanceReview::STATUS_INACTIVE);
            $this->getPerformanceReviewService()->getPerformanceReviewDao()->updateReview($review, $reviewerEmpNumber);
        }
        return new EndpointResourceResult(PerformanceReviewModel::class, $review);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_ID, new Rule(Rules::POSITIVE)),
            ...$this->getCommonValidationRules()
        );
    }
}
