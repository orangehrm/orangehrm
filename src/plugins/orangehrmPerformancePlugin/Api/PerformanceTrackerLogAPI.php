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
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\PerformanceTracker;
use OrangeHRM\Entity\PerformanceTrackerLog;
use OrangeHRM\Performance\Api\Model\PerformanceTrackerLogModel;
use OrangeHRM\Performance\Dto\PerformanceTrackerLogSearchFilterParams;
use OrangeHRM\Performance\Traits\Service\PerformanceTrackerLogServiceTrait;
use OrangeHRM\Performance\Traits\Service\PerformanceTrackerServiceTrait;

class PerformanceTrackerLogAPI extends Endpoint implements CrudEndpoint
{
    use AuthUserTrait;
    use PerformanceTrackerLogServiceTrait;
    use DateTimeHelperTrait;
    use PerformanceTrackerServiceTrait;
    use UserRoleManagerTrait;

    public const PARAMETER_TRACKER_ID = 'trackerId';
    public const PARAMETER_NEGATIVE = 'negative';
    public const PARAMETER_POSITIVE = 'positive';
    public const PARAMETER_LOG = 'log';
    public const PARAMETER_COMMENT = 'comment';
    public const PARAMETER_ACHIEVEMENT = 'achievement';
    public const PARAM_RULE_TRACKER_LOG_LOG_MAX_LENGTH = 150;
    public const PARAM_RULE_TRACKER_LOG_COMMENT_MAX_LENGTH = 3000;

    /**
     * @OA\Get(
     *     path="/api/v2/performance/trackers/{trackerId}/logs",
     *     tags={"Performance/Tracker Logs"},
     *     summary="List Logs for a Performance Tracker",
     *     operationId="list-logs-for-a-performance-tracker",
     *     @OA\PathParameter(
     *         name="trackerId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="sortField",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=PerformanceTrackerLogSearchFilterParams::ALLOWED_SORT_FIELDS)
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
     *                 @OA\Items(ref="#/components/schemas/Performance-PerformanceTrackerLogModel")
     *             ),
     *             @OA\Property(property="meta",
     *                 type="object",
     *                 @OA\Property(property="total", type="integer"),
     *                 @OA\Property(property="positive", type="integer"),
     *                 @OA\Property(property="negative", type="integer")
     *             )
     *         )
     *     )
     * )
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        $performanceTrackerLogParamHolder = new PerformanceTrackerLogSearchFilterParams();
        $performanceTrackerLogParamHolder->setTrackerId(
            $this->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_ATTRIBUTE,
                self::PARAMETER_TRACKER_ID
            )
        );
        $this->setSortingAndPaginationParams($performanceTrackerLogParamHolder);

        $performanceTrackerLogs = $this->getPerformanceTrackerLogService()
            ->getPerformanceTrackerLogDao()
            ->getPerformanceTrackerLogsByTrackerId($performanceTrackerLogParamHolder);
        $performanceTrackerLogsPositiveAchievementCount = $this->getPerformanceTrackerLogService()
            ->getPerformanceTrackerLogDao()
            ->getPerformanceTrackerLogsRateCount(
                PerformanceTrackerLog::POSITIVE_ACHIEVEMENT,
                $performanceTrackerLogParamHolder->getTrackerId()
            );
        $performanceTrackerLogsNegativeAchievementCount = $this->getPerformanceTrackerLogService()
            ->getPerformanceTrackerLogDao()
            ->getPerformanceTrackerLogsRateCount(
                PerformanceTrackerLog::NEGATIVE_ACHIEVEMENT,
                $performanceTrackerLogParamHolder->getTrackerId()
            );
        return new EndpointCollectionResult(
            PerformanceTrackerLogModel::class,
            $performanceTrackerLogs,
            new ParameterBag(
                [
                    CommonParams::PARAMETER_TOTAL =>
                        $performanceTrackerLogsNegativeAchievementCount +
                        $performanceTrackerLogsPositiveAchievementCount,
                    self::PARAMETER_POSITIVE => $performanceTrackerLogsPositiveAchievementCount,
                    self::PARAMETER_NEGATIVE => $performanceTrackerLogsNegativeAchievementCount,
                ]
            ),
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_TRACKER_ID,
                new Rule(Rules::POSITIVE),
                new Rule(Rules::IN_ACCESSIBLE_ENTITY_ID, [PerformanceTracker::class])
            ),
            ...$this->getSortingAndPaginationParamsRules(
                PerformanceTrackerLogSearchFilterParams::ALLOWED_SORT_FIELDS
            )
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v2/performance/trackers/{trackerId}/logs",
     *     tags={"Performance/Tracker Logs"},
     *     summary="Create a Log for a Performance Tracker",
     *     operationId="create-a-log-for-a-performance-tracker",
     *     @OA\PathParameter(
     *         name="trackerId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="achievement",
     *                 type="integer",
     *                 description="Should be either 1 for postive and 2 for egative",
     *                 default=1
     *             ),
     *             @OA\Property(property="comment", type="string"),
     *             @OA\Property(property="log", type="string"),
     *             required={"achievement", "comment", "log"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Performance-PerformanceTrackerLogModel"
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
        $performanceTrackerLog = new PerformanceTrackerLog();
        $this->setTrackerLogsParams($performanceTrackerLog);
        $performanceTrackerLog->getDecorator()
            ->setPerformanceTrackerById(
                $this->getRequestParams()
                    ->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID)
            );
        $performanceTrackerLog->setAddedDate($this->getDateTimeHelper()->getNow());
        $performanceTrackerLog->getDecorator()->setPerformanceTrackerById(
            $this->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_ATTRIBUTE,
                self::PARAMETER_TRACKER_ID
            )
        );
        $performanceTrackerLog->getDecorator()->setUserByUserId($this->getAuthUser()->getUserId());
        $performanceTrackerLog->getDecorator()->setReviewerByEmpNumber($this->getAuthUser()->getEmpNumber());
        $this->getPerformanceTrackerLogService()
            ->getPerformanceTrackerLogDao()->savePerformanceTrackerLog($performanceTrackerLog);
        return new EndpointResourceResult(PerformanceTrackerLogModel::class, $performanceTrackerLog);
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
     * @param PerformanceTrackerLog $performanceTrackerLog
     */
    public function setTrackerLogsParams(PerformanceTrackerLog $performanceTrackerLog): void
    {
        $performanceTrackerLog->setLog(
            $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_LOG)
        );
        $performanceTrackerLog->setAchievement(
            $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_ACHIEVEMENT)
        );
        $performanceTrackerLog->setComment(
            $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_COMMENT)
        );
        $performanceTrackerLog->setStatus(PerformanceTrackerLog::STATUS_NOT_DELETED);
    }

    /**
     * @return array
     */
    private function getCommonBodyParamRulesCollection(): array
    {
        return [
            new ParamRule(
                self::PARAMETER_LOG,
                new Rule(Rules::LENGTH, [null, self::PARAM_RULE_TRACKER_LOG_LOG_MAX_LENGTH])
            ),
            new ParamRule(
                self::PARAMETER_TRACKER_ID,
                new Rule(Rules::POSITIVE),
                new Rule(Rules::IN_ACCESSIBLE_ENTITY_ID, [PerformanceTracker::class])
            ),
            new ParamRule(
                self::PARAMETER_ACHIEVEMENT,
                new Rule(Rules::POSITIVE),
                new Rule(Rules::IN, [[
                    PerformanceTrackerLog::POSITIVE_ACHIEVEMENT,
                    PerformanceTrackerLog::NEGATIVE_ACHIEVEMENT
                ]])
            ),
            new ParamRule(
                self::PARAMETER_COMMENT,
                new Rule(Rules::LENGTH, [null, self::PARAM_RULE_TRACKER_LOG_COMMENT_MAX_LENGTH])
            )
        ];
    }

    /**
     * @OA\Delete(
     *     path="/api/v2/performance/trackers/{trackerId}/logs",
     *     tags={"Performance/Tracker Logs"},
     *     summary="Remove Logs from a Performance Tracker",
     *     operationId="remove-logs-from-a-performance-tracker",
     *     @OA\PathParameter(
     *         name="trackerId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(ref="#/components/requestBodies/DeleteRequestBody"),
     *     @OA\Response(response="200", ref="#/components/responses/DeleteResponse"),
     *     @OA\Response(response="403", ref="#/components/responses/ForbiddenResponse"),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     */
    public function delete(): EndpointResult
    {
        $trackerId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            self::PARAMETER_TRACKER_ID
        );
        $ids = $this->getPerformanceTrackerLogService()->getPerformanceTrackerLogDao()->getExistingPerformanceTrackerLogIdsForTrackerId(
            $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS),
            $trackerId
        );
        $this->throwRecordNotFoundExceptionIfEmptyIds($ids);
        foreach ($ids as $id) {
            if (!$this->getUserRoleManager()->isEntityAccessible(PerformanceTrackerLog::class, $id)) {
                throw $this->getForbiddenException();
            }
        }
        $this->getPerformanceTrackerLogService()
            ->getPerformanceTrackerLogDao()
            ->deletePerformanceTrackerLog($ids);
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
            new ParamRule(
                self::PARAMETER_TRACKER_ID,
                new Rule(Rules::POSITIVE),
                new Rule(Rules::IN_ACCESSIBLE_ENTITY_ID, [PerformanceTracker::class])
            ),
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v2/performance/trackers/{trackerId}/logs/{id}",
     *     tags={"Performance/Tracker Logs"},
     *     summary="Get a Log from a Performance Tracker",
     *     operationId="get-a-log-from-a-performance-tracker",
     *     @OA\PathParameter(
     *         name="trackerId",
     *         @OA\Schema(type="integer")
     *     ),
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
     *                 ref="#/components/schemas/Performance-PerformanceTrackerLogModel"
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
        $id = $this->getRequestParams()
            ->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $performanceTrackerLog = $this->getPerformanceTrackerLogService()
            ->getPerformanceTrackerLogDao()
            ->getPerformanceTrackerLogById($id);
        $this->throwRecordNotFoundExceptionIfNotExist($performanceTrackerLog, PerformanceTrackerLog::class);
        return new EndpointResourceResult(PerformanceTrackerLogModel::class, $performanceTrackerLog);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE),
                new Rule(Rules::IN_ACCESSIBLE_ENTITY_ID, [PerformanceTrackerLog::class])
            ),
            new ParamRule(
                self::PARAMETER_TRACKER_ID,
                new Rule(Rules::POSITIVE),
                new Rule(Rules::IN_ACCESSIBLE_ENTITY_ID, [PerformanceTracker::class])
            ),
        );
    }

    /**
     * @OA\Put(
     *     path="/api/v2/performance/trackers/{trackerId}/logs/{id}",
     *     tags={"Performance/Tracker Logs"},
     *     summary="Update a Log from a Performance Tracker",
     *     operationId="update-a-log-from-a-performance-tracker",
     *     @OA\PathParameter(
     *         name="trackerId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\PathParameter(
     *         name="id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="achievement",
     *                 type="integer",
     *                 description="Should be either 1 for postive and 2 for egative",
     *                 default=1
     *             ),
     *             @OA\Property(property="comment", type="string"),
     *             @OA\Property(property="log", type="string"),
     *             required={"achievement", "comment", "log"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Performance-PerformanceTrackerLogModel"
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
        $id = $this->getRequestParams()
            ->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $performanceTrackerLog = $this->getPerformanceTrackerLogService()
            ->getPerformanceTrackerLogDao()
            ->getPerformanceTrackerLogById($id);
        $this->throwRecordNotFoundExceptionIfNotExist($performanceTrackerLog, PerformanceTrackerLog::class);
        $this->setTrackerLogsParams($performanceTrackerLog);
        $performanceTrackerLog->getDecorator()->setUserByUserId($this->getAuthUser()->getUserId());
        $performanceTrackerLog->setModifiedDate($this->getDateTimeHelper()->getNow());
        $this->getPerformanceTrackerLogService()
            ->getPerformanceTrackerLogDao()->savePerformanceTrackerLog($performanceTrackerLog);
        return new EndpointResourceResult(PerformanceTrackerLogModel::class, $performanceTrackerLog);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE),
                new Rule(Rules::IN_ACCESSIBLE_ENTITY_ID, [PerformanceTrackerLog::class])
            ),
            ...$this->getCommonBodyParamRulesCollection()
        );
    }
}
