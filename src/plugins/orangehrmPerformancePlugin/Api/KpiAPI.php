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
use OrangeHRM\Core\Api\V2\Exception\BadRequestException;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Entity\JobTitle;
use OrangeHRM\Entity\Kpi;
use OrangeHRM\ORM\Exception\TransactionException;
use OrangeHRM\Performance\Api\Model\KpiModel;
use OrangeHRM\Performance\Dto\KpiSearchFilterParams;
use OrangeHRM\Performance\Exception\KpiServiceException;
use OrangeHRM\Performance\Traits\Service\KpiServiceTrait;

class KpiAPI extends Endpoint implements CrudEndpoint
{
    use KpiServiceTrait;

    public const PARAMETER_TITLE = 'title';
    public const PARAMETER_JOB_TITLE_CODE = 'jobTitleId';
    public const PARAMETER_MIN_RATING = 'minRating';
    public const PARAMETER_MAX_RATING = 'maxRating';
    public const PARAMETER_DEFAULT_KPI = 'isDefault';
    public const PARAMETER_EDITABLE = 'editable';

    public const FILTER_JOB_TITLE_ID = 'jobTitleId';

    public const PARAM_RULE_TITLE_MAX_LENGTH = 100;

    /**
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $kpi = $this->getKpiService()->getKpiDao()->getKpiById($id);
        $this->throwRecordNotFoundExceptionIfNotExist($kpi, Kpi::class);
        $editable = $this->getKpiService()->getKpiDao()->isKpiEditable($id);
        return new EndpointResourceResult(
            KpiModel::class,
            $kpi,
            new ParameterBag([self::PARAMETER_EDITABLE => $editable])
        );
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
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        $kpiSearchFilterParams = new KpiSearchFilterParams();
        $this->setSortingAndPaginationParams($kpiSearchFilterParams);

        $kpiSearchFilterParams->setJobTitleId(
            $this->getRequestParams()->getIntOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_JOB_TITLE_ID
            )
        );

        $kpis = $this->getKpiService()->getKpiDao()->getKpiList($kpiSearchFilterParams);
        $count = $this->getKpiService()->getKpiDao()->getKpiCount($kpiSearchFilterParams);

        return new EndpointCollectionResult(
            KpiModel::class,
            $kpis,
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
                new ParamRule(self::FILTER_JOB_TITLE_ID, new Rule(Rules::POSITIVE))
            ),
            ...$this->getSortingAndPaginationParamsRules(KpiSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @inheritDoc
     * @throws BadRequestException|TransactionException
     */
    public function create(): EndpointResult
    {
        $kpi = new Kpi();
        $this->setKpi($kpi, false);

        try {
            $kpi = $this->getKpiService()->saveKpi($kpi, false);
            return new EndpointResourceResult(KpiModel::class, $kpi);
        } catch (KpiServiceException $e) {
            throw $this->getBadRequestException($e->getMessage());
        }
    }

    /**
     * @param Kpi $kpi
     */
    private function setKpi(Kpi $kpi, bool $restrictedUpdate): void
    {
        if (!$restrictedUpdate) {
            $kpi->setTitle(
                $this->getRequestParams()->getString(
                    RequestParams::PARAM_TYPE_BODY,
                    self::PARAMETER_TITLE
                )
            );

            $kpi->setMinRating(
                $this->getRequestParams()->getInt(
                    RequestParams::PARAM_TYPE_BODY,
                    self::PARAMETER_MIN_RATING
                )
            );
            $kpi->setMaxRating(
                $this->getRequestParams()->getInt(
                    RequestParams::PARAM_TYPE_BODY,
                    self::PARAMETER_MAX_RATING
                )
            );
        }

        $jobTitleId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_JOB_TITLE_CODE
        );

        $kpi->getDecorator()->setJobTitleById($jobTitleId);

        $kpi->setDefaultKpi(
            $this->getRequestParams()->getBooleanOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_DEFAULT_KPI
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            ...$this->getCommonBodyValidationRules()
        );
    }

    /**
     * @return ParamRule[]
     */
    protected function getCommonBodyValidationRules(): array
    {
        return [
            new ParamRule(
                self::PARAMETER_TITLE,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [null, self::PARAM_RULE_TITLE_MAX_LENGTH])
            ),
            new ParamRule(
                self::PARAMETER_JOB_TITLE_CODE,
                new Rule(Rules::POSITIVE),
                new Rule(Rules::ENTITY_ID_EXISTS, [JobTitle::class])
            ),
            new ParamRule(
                self::PARAMETER_MIN_RATING,
                new Rule(Rules::INT_TYPE),
                new Rule(Rules::BETWEEN, [0, 100])
            ),
            new ParamRule(
                self::PARAMETER_MAX_RATING,
                new Rule(Rules::INT_TYPE),
                new Rule(Rules::BETWEEN, [0, 100])
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_DEFAULT_KPI,
                    new Rule(Rules::BOOL_TYPE)
                )
            )
        ];
    }

    /**
     * @inheritDoc
     * @throws BadRequestException|TransactionException
     */
    public function update(): EndpointResult
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $kpi = $this->getKpiService()->getKpiDao()->getKpiById($id);
        $this->throwRecordNotFoundExceptionIfNotExist($kpi, Kpi::class);
        $restrictedEdit = $this->getKpiService()->getKpiDao()->isKpiEditable($id);
        $this->setKpi($kpi, $restrictedEdit);
        try {
            $this->getKpiService()->saveKpi($kpi);
            return new EndpointResourceResult(KpiModel::class, $kpi);
        } catch (KpiServiceException $e) {
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
            ...$this->getCommonBodyValidationRules()
        );
    }

    /**
     * @inheritDoc
     */
    public function delete(): EndpointResult
    {
        $ids = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS);
        $this->getKpiService()->getKpiDao()->deleteKpi($ids);
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
