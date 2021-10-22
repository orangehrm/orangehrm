<?php

namespace OrangeHRM\Pim\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Core\Report\FilterField\Operator;
use OrangeHRM\Core\Report\FilterField\ValueXNormalizable;
use OrangeHRM\Core\Report\FilterField\ValueYNormalizable;
use OrangeHRM\Core\Service\ReportGeneratorService;
use OrangeHRM\Entity\Report;

class PimDefinedReportDetailedModel implements Normalizable
{
    /**
     * @var ReportGeneratorService|null
     */
    protected ?ReportGeneratorService $reportGeneratorService = null;

    /**
     * @var Report
     */
    private Report $report;

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
     * @param Report $report
     */
    public function __construct(Report $report)
    {
        $this->report = $report;
    }

    /**
     * @return Report
     */
    public function getReport(): Report
    {
        return $this->report;
    }

    public function toArray(): array
    {
        $detailedReport = $this->getReport();
        $selectedFilterFields = $this->getReportGeneratorService()
            ->getReportGeneratorDao()
            ->getSkippedSelectedFilterFieldsByReportId($detailedReport->getId());
        $selectedDisplayFieldGroups = $this->getReportGeneratorService()
            ->getReportGeneratorDao()
            ->getDisplayFieldGroupIdList($detailedReport->getId());

        $criteria = [];
        foreach ($selectedFilterFields as $selectedFilterField) {
            $filterFieldClassName = $selectedFilterField->getFilterField()->getClassName();
            $filterFieldClass = $this->getReportGeneratorService()
                ->getInitializedFilterFieldInstance($filterFieldClassName, $selectedFilterField);
            $filterFieldCriteria = [
                "x" => $selectedFilterField->getX(),
                "y" => $selectedFilterField->getY(),
                "operator" => $selectedFilterField->getOperator(),
            ];
            if ($filterFieldClass instanceof ValueXNormalizable) {
                $filterFieldCriteria['x'] = $filterFieldClass->toArrayXValue();
            }
            if ($filterFieldClass instanceof ValueYNormalizable) {
                $filterFieldCriteria['y'] = $filterFieldClass->toArrayYValue();
            }
            $criteria[$selectedFilterField->getFilterField()->getId()] = $filterFieldCriteria;
        }

        $fieldGroup = [];
        foreach ($selectedDisplayFieldGroups as $selectedDisplayFieldGroup) {
            $fieldGroup[$selectedDisplayFieldGroup] = [
                'fields' => $this->getReportGeneratorService()
                    ->getReportGeneratorDao()
                    ->getSelectedDisplayFieldIdByReportGroupId(
                        $detailedReport->getId(),
                        $selectedDisplayFieldGroup
                    ),
                'includeHeader' => count(
                        $this->getReportGeneratorService()->getReportGeneratorDao()->isIncludeHeader(
                            $detailedReport->getId(),
                            $selectedDisplayFieldGroup
                        )
                    ) == 1
            ];
        }

        $selectedFilterFieldOperator = $this->getReportGeneratorService()
            ->getReportGeneratorDao()
            ->getIncludeType($detailedReport->getId())
            ->getOperator();
        $includeType = ($selectedFilterFieldOperator === Operator::IS_NULL) ? 'onlyCurrent' : (($selectedFilterFieldOperator === Operator::IS_NOT_NULL) ? 'onlyPast' : 'currentAndPast');

        return [
            'id' => $detailedReport->getId(),
            'name' => $detailedReport->getName(),
            'include' => $includeType,
            'criteria' => $criteria,
            'fieldGroup' => $fieldGroup
        ];
    }
}
