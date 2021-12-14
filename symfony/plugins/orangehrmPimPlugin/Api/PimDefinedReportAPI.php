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
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Service\ReportGeneratorService;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\Report;
use OrangeHRM\ORM\Exception\TransactionException;
use OrangeHRM\Pim\Api\Model\PimDefinedReportDetailedModel;
use OrangeHRM\Pim\Api\Model\PimDefinedReportModel;
use OrangeHRM\Pim\Dto\PimDefinedReportSearchFilterParams;

class PimDefinedReportAPI extends Endpoint implements CrudEndpoint
{
    use EntityManagerHelperTrait;

    public const PARAMETER_REPORT_NAME = 'name';
    public const PARAMETER_INCLUDE_TYPE = 'include';
    public const PARAMETER_CRITERIA = 'criteria';
    public const PARAMETER_FIELD_GROUP = 'fieldGroup';

    public const FILTER_NAME = 'name';
    public const FILTER_ID = 'reportId';
    public const PARAM_RULE_NAME_MAX_LENGTH = 255;

    /**
     * @var ReportGeneratorService|null
     */
    protected ?ReportGeneratorService $reportGeneratorService = null;

    /**
     * @return ReportGeneratorService
     */
    protected function getReportGeneratorService(): ReportGeneratorService
    {
        if (!$this->reportGeneratorService instanceof ReportGeneratorService) {
            $this->reportGeneratorService = new ReportGeneratorService();
        }
        return $this->reportGeneratorService;
    }

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        $pimDefinedReportSearchFilterParams = new PimDefinedReportSearchFilterParams();
        $this->setSortingAndPaginationParams($pimDefinedReportSearchFilterParams);
        $pimDefinedReportSearchFilterParams->setName(
            $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_QUERY, self::FILTER_NAME)
        );
        $pimDefinedReportSearchFilterParams->setReportId(
            $this->getRequestParams()->getIntOrNull(RequestParams::PARAM_TYPE_QUERY, self::FILTER_ID)
        );
        $pimDefinedReports = $this->getReportGeneratorService()
            ->getReportGeneratorDao()
            ->searchPimDefinedReports($pimDefinedReportSearchFilterParams);
        $pimDefinedReportCount = $this->getReportGeneratorService()
            ->getReportGeneratorDao()
            ->getSearchPimDefinedReportCount($pimDefinedReportSearchFilterParams);
        return new EndpointCollectionResult(
            PimDefinedReportModel::class,
            $pimDefinedReports,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $pimDefinedReportCount])
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
                    self::FILTER_NAME,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_NAME_MAX_LENGTH])
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_ID,
                    new Rule(Rules::POSITIVE),
                )
            ),
            ...$this->getSortingAndPaginationParamsRules(PimDefinedReportSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @inheritDoc
     * @throws TransactionException
     */
    public function create(): EndpointResult
    {
        $report = new Report();
        $this->setParamsToPimDefinedReport($report);
        $fieldGroup = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_FIELD_GROUP);
        $criterias = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_CRITERIA);
        $includeType = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_INCLUDE_TYPE
        );
        $this->getReportGeneratorService()->savePimDefinedReport($report, $fieldGroup, $criterias, $includeType);
        return new EndpointResourceResult(PimDefinedReportModel::class, $report);
    }

    /**
     * @param Report $report
     * @return void
     */
    private function setParamsToPimDefinedReport(Report $report): void
    {
        $reportGroup = $this->getReportGeneratorService()->getReportGeneratorDao()->getReportGroupByName('pim');
        $report->setName(
            $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_REPORT_NAME)
        );
        $report->setReportGroup($reportGroup);
        $report->setUseFilterField(true);
        $report->setType('PIM_DEFINED');
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            ...$this->getCommonBodyValidationRules(),
        );
    }

    /**
     * @inheritDoc
     */
    public function delete(): EndpointResult
    {
        $ids = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS);
        $this->getReportGeneratorService()->getReportGeneratorDao()->deletePimDefinedReport($ids);
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
                new Rule(Rules::ARRAY_TYPE),
                new Rule(
                    Rules::EACH,
                    [new Rules\Composite\AllOf(new Rule(Rules::POSITIVE))]
                )
            ),
        );
    }

    /**
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $report = $this->getReportGeneratorService()->getReportGeneratorDao()->getReportById($id);
        $this->throwRecordNotFoundExceptionIfNotExist($report, Report::class);
        return new EndpointResourceResult(PimDefinedReportDetailedModel::class, $report);
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
     * @throws TransactionException
     */
    public function update(): EndpointResult
    {
        $reportId = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $report = $this->getReportGeneratorService()->getReportGeneratorDao()->getReportById($reportId);
        $this->throwRecordNotFoundExceptionIfNotExist($report, Report::class);
        $this->setParamsToPimDefinedReport($report);
        $fieldGroup = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_FIELD_GROUP);
        $criterias = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_CRITERIA);
        $includeType = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_INCLUDE_TYPE
        );

        $this->beginTransaction();
        try {
            $this->getReportGeneratorService()->getReportGeneratorDao()->deleteExistingReportRecordsByReportId($report);
            $this->getReportGeneratorService()->savePimDefinedReport($report, $fieldGroup, $criterias, $includeType);
            $this->commitTransaction();
        } catch (Exception $e) {
            $this->rollBackTransaction();
            throw new TransactionException($e);
        }
        return new EndpointResourceResult(PimDefinedReportModel::class, $report);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_ID, new Rule(Rules::POSITIVE)),
            ...$this->getCommonBodyValidationRules(),
        );
    }

    /**
     * @return ParamRule[]
     */
    private function getCommonBodyValidationRules(): array
    {
        return [
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_REPORT_NAME,
                    new Rule(Rules::REQUIRED),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_NAME_MAX_LENGTH])
                ),
            ),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_INCLUDE_TYPE,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::CALLBACK, [
                        function () {
                            $includeType = $this->getRequestParams()->getString(
                                RequestParams::PARAM_TYPE_BODY,
                                self::PARAMETER_INCLUDE_TYPE
                            );
                            if ($includeType === 'onlyCurrent' || $includeType === 'currentAndPast' || $includeType === 'onlyPast') {
                                return true;
                            } else {
                                return false;
                            }
                        }
                    ])
                ),
            ),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(self::PARAMETER_FIELD_GROUP),
            ),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(self::PARAMETER_CRITERIA),
            ),
        ];
    }
}
