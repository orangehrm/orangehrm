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

namespace OrangeHRM\Pim\Api;

use Exception;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\Exception\RecordNotFoundException;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\ReportingMethod;
use OrangeHRM\Pim\Api\Model\ReportingMethodConfigurationModel;
use OrangeHRM\Pim\Dto\ReportingMethodSearchFilterParams;
use OrangeHRM\Pim\Service\ReportingMethodConfigurationService;

class ReportingMethodConfigurationAPI extends EndPoint implements CrudEndpoint
{
    public const PARAMETER_NAME = 'name';
    public const PARAM_RULE_NAME_MAX_LENGTH = 100;

    /**
     * @var null|ReportingMethodConfigurationService
     */
    protected ?ReportingMethodConfigurationService $reportingMethodService = null;

    /**
     * @return ReportingMethodConfigurationService
     */
    public function getReportingMethodService(): ReportingMethodConfigurationService
    {
        if (!$this->reportingMethodService instanceof ReportingMethodConfigurationService) {
            $this->reportingMethodService = new ReportingMethodConfigurationService();
        }
        return $this->reportingMethodService;
    }

    /**
     * @inheritDoc
     * @throws RecordNotFoundException
     * @throws Exception
     */
    public function getOne(): EndpointResourceResult
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $reportingMethod = $this->getReportingMethodService()->getReportingMethodById($id);
        $this->throwRecordNotFoundExceptionIfNotExist($reportingMethod, ReportingMethod::class);
        return new EndpointResourceResult(ReportingMethodConfigurationModel::class, $reportingMethod);
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
     * @throws Exception
     */
    public function getAll(): EndpointCollectionResult
    {
        $reportingMethodParamHolder = new ReportingMethodSearchFilterParams();
        $this->setSortingAndPaginationParams($reportingMethodParamHolder);
        $reportingMethods = $this->getReportingMethodService()->getReportingMethodList($reportingMethodParamHolder);
        $count = $this->getReportingMethodService()->getReportingMethodCount($reportingMethodParamHolder);
        return new EndpointCollectionResult(
            ReportingMethodConfigurationModel::class,
            $reportingMethods,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $count])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            ...$this->getSortingAndPaginationParamsRules(ReportingMethodSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function create(): EndpointResourceResult
    {
        $reportingMethod = $this->saveReportingMethod();
        return new EndpointResourceResult(ReportingMethodConfigurationModel::class, $reportingMethod);
    }

    /**
     * @return ReportingMethod
     * @throws DaoException
     * @throws RecordNotFoundException
     */
    public function saveReportingMethod(): ReportingMethod
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $name = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_NAME);
        if ($id) {
            $reportingMethod = $this->getReportingMethodService()->getReportingMethodById($id);
            $this->throwRecordNotFoundExceptionIfNotExist($reportingMethod, ReportingMethod::class);
        } else {
            $reportingMethod = new ReportingMethod();
        }
        $reportingMethod->setName($name);
        return $this->getReportingMethodService()->saveReportingMethod($reportingMethod);
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
                new Rule(Rules::LENGTH, [null, self::PARAM_RULE_NAME_MAX_LENGTH]),
            ),
        );
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function update(): EndpointResourceResult
    {
        $reportingMethod = $this->saveReportingMethod();
        return new EndpointResourceResult(ReportingMethodConfigurationModel::class, $reportingMethod);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE)
            ),
            new ParamRule(
                self::PARAMETER_NAME,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [null, self::PARAM_RULE_NAME_MAX_LENGTH]),
            ),
        );
    }

    /**
     * @return ParamRuleCollection
     */
    public function getValidationRuleForSaveReportingMethod(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE)
            ),
            new ParamRule(
                self::PARAMETER_NAME,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [null, self::PARAM_RULE_NAME_MAX_LENGTH]),
            ),
        );
    }

    /**
     * @return EndpointResourceResult
     * @throws Exception
     */
    public function delete(): EndpointResourceResult
    {
        $ids = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS);
        $this->getReportingMethodService()->deleteReportingMethods($ids);
        return new EndpointResourceResult(ArrayModel::class, $ids);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        $reportingMethodIdsInUse = $this->getReportingMethodService()->getReportingMethodIdsInUse();
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_IDS,
                new Rule(
                    Rules::EACH,
                    [
                        new Rules\Composite\AllOf(
                            new Rule(Rules::POSITIVE),
                            new Rule(Rules::NOT_IN, [$reportingMethodIdsInUse])
                        )
                    ]
                )
            ),
        );
    }
}
