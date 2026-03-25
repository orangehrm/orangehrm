<?php

namespace OrangeHRM\Time\Report;

use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Report\ReportData;
use OrangeHRM\Core\Traits\Service\NormalizerServiceTrait;
use OrangeHRM\Core\Traits\Service\NumberHelperTrait;
use OrangeHRM\Time\Traits\Service\TimesheetServiceTrait;
use OrangeHRM\Time\Dto\ClientReportSearchFilterParams;

class ClientReportData implements ReportData
{
    use TimesheetServiceTrait;
    use NumberHelperTrait;
    use NormalizerServiceTrait;

    private array $records = [];

    private ClientReportSearchFilterParams $filterParams;

    public function __construct(ClientReportSearchFilterParams $filterParams)
    {
        $this->filterParams = $filterParams;
    }

    public function normalize(): array
    {
        $this->records = $this->getTimesheetService()
            ->getTimesheetDao()
            ->getTimesheetItemsForClientReport($this->filterParams);

        $result = [];

        foreach ($this->records as $record) {

            $result[] = [
                ClientReport::PARAMETER_CUSTOMER => $record['customerName'],
                ClientReport::PARAMETER_PROJECT => $record['projectName'],
                ClientReport::PARAMETER_EMPLOYEE => $record['employeeName'],
                ClientReport::PARAMETER_HOURS_WORKED => $this->getNumberHelper()->numberFormat(
                    $record['totalDuration'] / 3600,
                    2
                )
            ];
        }

        return $result;
    }

    public function getMeta(): ?ParameterBag
    {
        $totalDuration = $this->getTimesheetService()
            ->getTimesheetDao()
            ->getTotalDurationForClientReport($this->filterParams);

        return new ParameterBag(
            [
                'total' => count($this->records),
                'sum' => [
                    'hours' => floor($totalDuration / 3600),
                    'minutes' => ($totalDuration / 60) % 60,
                    'label' => $this->getNumberHelper()->numberFormat(
                        $totalDuration / 3600,
                        2
                    )
                ]
            ]
        );
    }
}