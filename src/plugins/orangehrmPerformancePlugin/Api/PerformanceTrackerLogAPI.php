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
use OrangeHRM\Entity\PerformanceTrackerLog;
use OrangeHRM\Performance\Api\Model\PerformanceTrackerLogModel;
use OrangeHRM\Performance\Api\Traits\PerformanceTrackerPermissionTrait;
use OrangeHRM\Performance\Dto\PerformanceTrackerLogSearchFilterParams;
use OrangeHRM\Performance\Traits\Service\PerformanceTrackerLogServiceTrait;
use OrangeHRM\Performance\Traits\Service\PerformanceTrackerServiceTrait;

class PerformanceTrackerLogAPI extends Endpoint implements CrudEndpoint
{
    use AuthUserTrait;
    use PerformanceTrackerLogServiceTrait;
    use DateTimeHelperTrait;
    use PerformanceTrackerServiceTrait;
    use PerformanceTrackerPermissionTrait;

    public const PARAMETER_TRACKER_ID = 'trackerId';
    public const PARAMETER_NEGATIVE = 'negative';
    public const PARAMETER_POSITIVE = 'positive';
    public const PARAMETER_LOG = 'log';
    public const PARAMETER_COMMENT = 'comment';
    public const PARAMETER_ACHIEVEMENT = 'achievement';
    public const PARAM_RULE_TRACKER_LOG_LOG_MAX_LENGTH = 150;
    public const PARAM_RULE_TRACKER_LOG_COMMENT_MAX_LENGTH = 3000;

    /**
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
        $performanceTrackerLogCount = $this->getPerformanceTrackerLogService()
            ->getPerformanceTrackerLogDao()
            ->getPerformanceTrackerLogCountPerTrackerId($performanceTrackerLogParamHolder);
        $performanceTrackerLogsPositiveRatingCount = $this->getPerformanceTrackerLogService()
            ->getPerformanceTrackerLogDao()
            ->getPerformanceTrackerLogsRateCount(PerformanceTrackerLog::POSITIVE_RATING, $performanceTrackerLogParamHolder->getTrackerId());
        $performanceTrackerLogsNegativeRatingCount = $this->getPerformanceTrackerLogService()
            ->getPerformanceTrackerLogDao()
            ->getPerformanceTrackerLogsRateCount(PerformanceTrackerLog::NEGATIVE_RATING, $performanceTrackerLogParamHolder->getTrackerId());
        return new EndpointCollectionResult(
            PerformanceTrackerLogModel::class,
            $performanceTrackerLogs,
            new ParameterBag(
                [
                    CommonParams::PARAMETER_TOTAL => $performanceTrackerLogCount,
                    self::PARAMETER_POSITIVE => $performanceTrackerLogsPositiveRatingCount,
                    self::PARAMETER_NEGATIVE => $performanceTrackerLogsNegativeRatingCount,
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
                new Rule(Rules::POSITIVE)
            ),
            ...$this->getSortingAndPaginationParamsRules(PerformanceTrackerLogSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @inheritDoc
     */
    public function create(): EndpointResult
    {
        $performanceTrackerLog = new PerformanceTrackerLog();
        $this->setTrackerLogsParams($performanceTrackerLog);
        $performanceTrackerLog->getDecorator()->setPerformanceTrackerById($this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID));
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
     * @return void
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
                new Rule(Rules::POSITIVE)
            ),
            new ParamRule(
                self::PARAMETER_ACHIEVEMENT,
                new Rule(Rules::POSITIVE)
            ),
            new ParamRule(
                self::PARAMETER_COMMENT,
                new Rule(Rules::LENGTH, [null, self::PARAM_RULE_TRACKER_LOG_COMMENT_MAX_LENGTH])
            )
        ];
    }

    /**
     * @inheritDoc
     */
    public function delete(): EndpointResult
    {
        $ids = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS);
        foreach ($ids as $id) {
            $performanceTrackerLog = $this->getPerformanceTrackerLogService()
                ->getPerformanceTrackerLogDao()
                ->getPerformanceTrackerLogById($id);
            $this->throwRecordNotFoundExceptionIfNotExist($performanceTrackerLog, PerformanceTrackerLog::class);
            if ($this->checkTrackerLogEditable($performanceTrackerLog) == false) {
                throw $this->getForbiddenException();
            }
        }
        $this->getPerformanceTrackerLogService()->getPerformanceTrackerLogDao()->deletePerformanceTrackerLog($ids);
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
                new Rule(Rules::POSITIVE)
            ),
        );
    }

    /**
     * @inheritDoc
     */
    public function getOne(): EndpointResult  //TODO :: ADD VALID
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $performanceTrackerLog = $this->getPerformanceTrackerLogService()->getPerformanceTrackerLogDao()
            ->getPerformanceTrackerLogById($id);
        $this->throwRecordNotFoundExceptionIfNotExist($performanceTrackerLog, PerformanceTrackerLog::class);
        if ($this->checkTrackerLogEditable($performanceTrackerLog) == false) {
            throw $this->getForbiddenException();
        }
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
                new Rule(Rules::POSITIVE)
            ),
            new ParamRule(
                self::PARAMETER_TRACKER_ID,
                new Rule(Rules::POSITIVE)
            ),
        );
    }

    /**
     * @inheritDoc
     */
    public function update(): EndpointResult
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $performanceTrackerLog = $this->getPerformanceTrackerLogService()
            ->getPerformanceTrackerLogDao()
            ->getPerformanceTrackerLogById($id);
        $this->throwRecordNotFoundExceptionIfNotExist($performanceTrackerLog, PerformanceTrackerLog::class);
        if ($this->checkTrackerLogEditable($performanceTrackerLog) == false) {
            throw $this->getForbiddenException();
        }
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
            new ParamRule(CommonParams::PARAMETER_ID, new Rule(Rules::POSITIVE)),
            ...$this->getCommonBodyParamRulesCollection()
        );
    }
}
