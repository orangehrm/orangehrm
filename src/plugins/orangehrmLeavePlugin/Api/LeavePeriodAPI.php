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

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Service\MenuService;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Core\Traits\Service\NormalizerServiceTrait;
use OrangeHRM\Entity\LeavePeriodHistory;
use OrangeHRM\Framework\Services;
use OrangeHRM\Leave\Api\Model\LeavePeriodHistoryModel;
use OrangeHRM\Leave\Api\Model\LeavePeriodModel;
use OrangeHRM\Leave\Traits\Service\LeaveConfigServiceTrait;
use OrangeHRM\Leave\Traits\Service\LeavePeriodServiceTrait;

class LeavePeriodAPI extends Endpoint implements CrudEndpoint
{
    use LeavePeriodServiceTrait;
    use LeaveConfigServiceTrait;
    use NormalizerServiceTrait;
    use DateTimeHelperTrait;

    public const PARAMETER_START_MONTH = 'startMonth';
    public const PARAMETER_START_DAY = 'startDay';

    public const META_PARAMETER_LEAVE_PERIOD_DEFINED = 'leavePeriodDefined';
    public const META_PARAMETER_CURRENT_LEAVE_PERIOD = 'currentLeavePeriod';

    /**
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        $leavePeriodHistory = $this->getLeavePeriodService()->getCurrentLeavePeriodStartDateAndMonth();
        $leavePeriodDefined = $this->getLeaveConfigService()->isLeavePeriodDefined();
        if (!$leavePeriodDefined) {
            $leavePeriodHistory = new LeavePeriodHistory();
            $leavePeriodHistory->setStartMonth(1);
            $leavePeriodHistory->setStartDay(1);
            $leavePeriodHistory->setCreatedAt($this->getDateTimeHelper()->getNow());
        }
        return new EndpointResourceResult(
            LeavePeriodHistoryModel::class,
            $leavePeriodHistory,
            new ParameterBag(
                [
                    self::META_PARAMETER_LEAVE_PERIOD_DEFINED => $leavePeriodDefined,
                    self::META_PARAMETER_CURRENT_LEAVE_PERIOD => $this->getCurrentLeavePeriod($leavePeriodDefined),
                ]
            )
        );
    }

    /**
     * @param bool $leavePeriodDefined
     * @return array|null
     */
    private function getCurrentLeavePeriod(bool $leavePeriodDefined): ?array
    {
        return $leavePeriodDefined ?
            $this->getNormalizerService()->normalize(
                LeavePeriodModel::class,
                $this->getLeavePeriodService()->getCurrentLeavePeriod(true)
            ) : null;
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        $paramRules = new ParamRuleCollection();
        $paramRules->addExcludedParamKey(CommonParams::PARAMETER_ID);
        return $paramRules;
    }

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        $leavePeriodDefined = $this->getLeaveConfigService()->isLeavePeriodDefined();
        return new EndpointCollectionResult(
            LeavePeriodModel::class,
            $this->getLeavePeriodService()->getGeneratedLeavePeriodList(),
            new ParameterBag(
                [
                    self::META_PARAMETER_LEAVE_PERIOD_DEFINED => $leavePeriodDefined,
                    self::META_PARAMETER_CURRENT_LEAVE_PERIOD => $this->getCurrentLeavePeriod($leavePeriodDefined),
                ]
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection();
    }

    /**
     * @inheritDoc
     */
    public function update(): EndpointResult
    {
        $leavePeriodDefined = $this->getLeaveConfigService()->isLeavePeriodDefined();
        $leavePeriodHistory = new LeavePeriodHistory();
        $leavePeriodHistory->setStartMonth(
            $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_START_MONTH)
        );
        $leavePeriodHistory->setStartDay(
            $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_START_DAY)
        );
        $leavePeriodHistory->setCreatedAt($this->getDateTimeHelper()->getNow());
        $this->getLeavePeriodService()
            ->getLeavePeriodDao()
            ->saveLeavePeriodHistory($leavePeriodHistory);

        if (!$leavePeriodDefined) {
            /** @var MenuService $menuService */
            $menuService = $this->getContainer()->get(Services::MENU_SERVICE);
            $menuService->enableModuleMenuItems('leave');
        }
        return new EndpointResourceResult(
            LeavePeriodHistoryModel::class,
            $leavePeriodHistory,
            new ParameterBag(
                [
                    self::META_PARAMETER_LEAVE_PERIOD_DEFINED => $leavePeriodDefined,
                    self::META_PARAMETER_CURRENT_LEAVE_PERIOD => $this->getCurrentLeavePeriod($leavePeriodDefined),
                ]
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        $paramRules = new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_START_MONTH,
                new Rule(Rules::IN, [$this->getLeavePeriodService()->getMonthNumberList()])
            ),
            new ParamRule(
                self::PARAMETER_START_DAY,
                new Rule(Rules::POSITIVE),
                new Rule(Rules::CALLBACK, [
                    function (int $startDay) {
                        $startMonth = $this->getRequestParams()->getInt(
                            RequestParams::PARAM_TYPE_BODY,
                            self::PARAMETER_START_MONTH
                        );
                        $allowedDaysForMonth = $this->getLeavePeriodService()->getListOfDates($startMonth, false);
                        return in_array($startDay, $allowedDaysForMonth);
                    }
                ])
            ),
        );
        $paramRules->addExcludedParamKey(CommonParams::PARAMETER_ID);
        return $paramRules;
    }

    /**
     * @inheritDoc
     */
    public function create(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
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
