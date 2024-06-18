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
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\PerformanceTracker;
use OrangeHRM\Performance\Api\Model\DetailedPerformanceTrackerModel;
use OrangeHRM\Performance\Api\Model\PerformanceTrackerModel;
use OrangeHRM\Performance\Dto\PerformanceTrackerSearchFilterParams;
use OrangeHRM\Performance\Exception\PerformanceTrackerServiceException;
use OrangeHRM\Performance\Traits\Service\PerformanceTrackerServiceTrait;

class PerformanceTrackerAPI extends Endpoint implements CrudEndpoint
{
    use AuthUserTrait;
    use DateTimeHelperTrait;
    use PerformanceTrackerServiceTrait;

    public const PARAMETER_TRACKER_NAME = 'trackerName';
    public const PARAM_RULE_TRACKER_NAME_MAX_LENGTH = 200;
    public const PARAMETER_REVIEWER_EMP_NUMBERS = 'reviewerEmpNumbers';

    /**
     * @OA\Get(
     *     path="/api/v2/performance/config/trackers",
     *     tags={"Performance/Tracker Configuration"},
     *     summary="List All Performance Trackers",
     *     operationId="list-all-performance-trackers",
     *     @OA\Parameter(
     *         name="empNumber",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="sortField",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=PerformanceTrackerSearchFilterParams::ALLOWED_SORT_FIELDS)
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
     *                 @OA\Items(ref="#/components/schemas/Performance-PerformanceTrackerModel")
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
        $performanceTrackerSearchParamHolder = new PerformanceTrackerSearchFilterParams();
        $this->setSortingAndPaginationParams($performanceTrackerSearchParamHolder);

        $performanceTrackerSearchParamHolder->setEmpNumber(
            $this->getRequestParams()->getIntOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                CommonParams::PARAMETER_EMP_NUMBER
            )
        );
        $performanceTrackers = $this->getPerformanceTrackerService()
            ->getPerformanceTrackerDao()
            ->getPerformanceTrackList($performanceTrackerSearchParamHolder);
        $count = $this->getPerformanceTrackerService()
            ->getPerformanceTrackerDao()
            ->getPerformanceTrackerCount($performanceTrackerSearchParamHolder);
        return new EndpointCollectionResult(
            PerformanceTrackerModel::class,
            $performanceTrackers,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $count])
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
                    CommonParams::PARAMETER_EMP_NUMBER,
                    new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
                ),
            ),
            ...$this->getSortingAndPaginationParamsRules(PerformanceTrackerSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v2/performance/config/trackers",
     *     tags={"Performance/Tracker Configuration"},
     *     summary="Create a Performance Tracker",
     *     operationId="create-a-performance-tracker",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="trackerName", type="string"),
     *             @OA\Property(property="empNumber", type="integer", description="Should be an existing EMployee Id"),
     *             @OA\Property(
     *                 property="reviewers",
     *                 type="array",
     *                 @OA\Items(
     *                     type="integer",
     *                     description="Should be an existing EMployee Id"
     *                 )
     *             ),
     *             required={"trackerName", "empNumber", "reviewers"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Performance-DetailedPerformanceTrackerModel"
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
        $performanceTracker = new PerformanceTracker();
        $reviewerEmpNumbers = $this->getRequestParams()->getArray(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_REVIEWER_EMP_NUMBERS
        );
        $this->setPerformanceTrackerParams($performanceTracker, true);
        $performanceTracker->setAddedDate($this->getDateTimeHelper()->getNow());
        $performanceTracker->setStatus(PerformanceTracker::STATUS_TRACKER_NOT_DELETED);
        $this->getPerformanceTrackerService()
            ->getPerformanceTrackerDao()
            ->savePerformanceTracker($performanceTracker, $reviewerEmpNumbers);
        return new EndpointResourceResult(DetailedPerformanceTrackerModel::class, $performanceTracker);
    }

    /**
     * @param PerformanceTracker $performanceTracker
     */
    private function setPerformanceTrackerParams(PerformanceTracker $performanceTracker, bool $notRestrictedUpdate): void
    {
        $performanceTracker->setTrackerName(
            $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_TRACKER_NAME)
        );
        $empNumber = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_EMP_NUMBER);
        if ($notRestrictedUpdate) {
            $performanceTracker->getDecorator()->setEmployeeByEmpNumber($empNumber);
        }
        $performanceTracker->getDecorator()->setAddedByByEmpNumber($this->getAuthUser()->getEmpNumber());
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            ...$this->getCommonBodyParamRulesCollection()
        );
    }

    /**
     * @return array
     */
    protected function getCommonBodyParamRulesCollection(): array
    {
        return [
            new ParamRule(
                self::PARAMETER_TRACKER_NAME,
                new Rule(Rules::LENGTH, [null, self::PARAM_RULE_TRACKER_NAME_MAX_LENGTH])
            ),
            new ParamRule(
                CommonParams::PARAMETER_EMP_NUMBER,
                new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
            ),
            new ParamRule(
                self::PARAMETER_REVIEWER_EMP_NUMBERS,
                new Rule(Rules::INT_ARRAY)
            ),
        ];
    }

    /**
     * @OA\Delete(
     *     path="/api/v2/performance/config/trackers",
     *     tags={"Performance/Tracker Configuration"},
     *     summary="Delete Performance Trackers",
     *     operationId="delete-performance-trackers",
     *     @OA\RequestBody(ref="#/components/requestBodies/DeleteRequestBody"),
     *     @OA\Response(response="200", ref="#/components/responses/DeleteResponse"),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     */
    public function delete(): EndpointResult
    {
        $ids = $this->getPerformanceTrackerService()->getPerformanceTrackerDao()->getExistingPerformanceTrackerIds(
            $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS)
        );
        $this->throwRecordNotFoundExceptionIfEmptyIds($ids);
        $this->getPerformanceTrackerService()
            ->getPerformanceTrackerDao()
            ->deletePerformanceTracker($ids);
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
     *     path="/api/v2/performance/config/trackers/{id}",
     *     tags={"Performance/Tracker Configuration"},
     *     summary="Get a Performance Tracker",
     *     operationId="get-a-performance-tracker",
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
     *                 ref="#/components/schemas/Performance-DetailedPerformanceTrackerModel"
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
        $performanceTracker = $this->getPerformanceTrackerService()
            ->getPerformanceTrackerDao()
            ->getPerformanceTracker($id);
        $this->throwRecordNotFoundExceptionIfNotExist($performanceTracker, PerformanceTracker::class);
        return new EndpointResourceResult(DetailedPerformanceTrackerModel::class, $performanceTracker);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(new ParamRule(
            CommonParams::PARAMETER_ID,
            new Rule(Rules::POSITIVE)
        ));
    }

    /**
     * @OA\Put(
     *     path="/api/v2/performance/config/trackers/{id}",
     *     tags={"Performance/Tracker Configuration"},
     *     summary="Update a Performance Tracker",
     *     operationId="update-a-performance-tracker",
     *     @OA\PathParameter(
     *         name="id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="trackerName", type="string"),
     *             @OA\Property(property="empNumber", type="integer", description="Should be an existing EMployee Id"),
     *             @OA\Property(
     *                 property="reviewers",
     *                 type="array",
     *                 @OA\Items(
     *                     type="integer",
     *                     description="Should be an existing EMployee Id"
     *                 )
     *             ),
     *             required={"trackerName", "empNumber", "reviewers"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Performance-DetailedPerformanceTrackerModel"
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
        $performanceTracker = $this->getPerformanceTrackerService()
            ->getPerformanceTrackerDao()
            ->getPerformanceTracker($id);
        $this->throwRecordNotFoundExceptionIfNotExist($performanceTracker, PerformanceTracker::class);
        $trackerOwnerEditable = $this->getPerformanceTrackerService()
            ->getPerformanceTrackerDao()->isTrackerOwnerEditable($performanceTracker->getId());
        $this->setPerformanceTrackerParams($performanceTracker, $trackerOwnerEditable);
        $performanceTracker->setModifiedDate($this->getDateTimeHelper()->getNow());
        $reviewerEmpNumbers = $this->getRequestParams()->getArray(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_REVIEWER_EMP_NUMBERS
        );
        try {
            $this->getPerformanceTrackerService()->getPerformanceTrackerDao()
                ->updatePerformanceTracker($performanceTracker, $reviewerEmpNumbers);
            return new EndpointResourceResult(DetailedPerformanceTrackerModel::class, $performanceTracker);
        } catch (PerformanceTrackerServiceException $e) {
            throw $this->getBadRequestException($e->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_ID, new Rule(Rules::POSITIVE)),
            ...$this->getCommonBodyParamRulesCollection()
        );
    }
}
