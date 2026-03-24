<?php

namespace OrangeHRM\Leave\Api;

use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Leave\Service\LeaveReportService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class LeaveEntitlementExcelAPI extends Endpoint
{
    public function getAll(): BinaryFileResponse
    {
        $params = $this->getRequestParams()->getAll();

        $reportService = new LeaveReportService();

        $reportName = $params['name'] ?? 'employee_leave_entitlements_and_usage';

        $reportData = $reportService->getReportData(
            $reportName,
            $params
        );

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray([
            [
                'Employee Name',
                'From Date',
                'To Date',
                'Leave Type',
                'Entitlement',
                'Pending',
                'Scheduled',
                'Taken',
                'Balance'
            ]
        ]);

        $row = 2;

        foreach ($reportData['data'] ?? [] as $record) {
            $sheet->fromArray([
                $record['employeeName'] ?? '',
                $record['leaveFromDate'] ?? '',
                $record['leaveToDate'] ?? '',
                $record['leaveType'] ?? '',
                $record['leaveEntitlementDays'] ?? '',
                $record['pendingApprovalDays'] ?? '',
                $record['scheduledDays'] ?? '',
                $record['takenDays'] ?? '',
                $record['balanceDays'] ?? ''
            ], null, 'A' . $row);

            $row++;
        }

        $filePath = sys_get_temp_dir() . '/leave_entitlement_usage.xlsx';
        (new Xlsx($spreadsheet))->save($filePath);

        return new BinaryFileResponse(
            $filePath,
            200,
            [],
            true,
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            false,
            false
        );
    }
}