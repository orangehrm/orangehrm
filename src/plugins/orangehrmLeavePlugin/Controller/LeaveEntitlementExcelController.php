<?php

namespace OrangeHRM\Leave\Controller;

use OrangeHRM\Core\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Border;
use OrangeHRM\Leave\Report\EmployeeLeaveEntitlementUsageReport;
use OrangeHRM\Leave\Dto\EmployeeLeaveEntitlementUsageReportSearchFilterParams;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;


class LeaveEntitlementExcelController extends AbstractController
{
    public function execute(Request $request)
    {
        // 1️⃣ Build correct filter DTO (NOT FilterParams)
        $filterParams = new EmployeeLeaveEntitlementUsageReportSearchFilterParams();

        $filterParams->setEmpNumber(
            $request->query->getInt('empNumber')
        );

        $filterParams->setFromDate(
            new \DateTime($request->query->get('fromDate'))
        );

        $filterParams->setToDate(
            new \DateTime($request->query->get('toDate'))
        );

        if ($request->query->get('leaveTypeId')) {
            $filterParams->setLeaveTypeId(
                $request->query->getInt('leaveTypeId')
            );
        }

        // 2️⃣ Get report
        $report = new EmployeeLeaveEntitlementUsageReport();
        $reportDataObject = $report->getData($filterParams);

        // 3️⃣ Get normalized array
        $rows = $reportDataObject->normalize();

        // 4️⃣ Create Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // After creating sheet
        $drawing = new Drawing();
        $drawing->setName('Company Logo');
        $drawing->setDescription('Company Logo');

        $logoPath = dirname(__DIR__, 4) . '/web/images/logo.png';

        if (file_exists($logoPath)) {
            $drawing->setPath($logoPath);
            $drawing->setHeight(60);
            $drawing->setCoordinates('A1');
            $drawing->setWorksheet($sheet);
        }

        // Headers
        $headers = [
            'Employee Name',
            'Leave From Date',
            'Leave To Date',
            'Leave Type',
            'Entitlement Days',
            'Pending Approval Days',
            'Scheduled Days',
            'Taken Days',
            'Balance Days'
        ];

        // Title
        $sheet->mergeCells('A8:I8');
        $sheet->setCellValue('A8', 'Employee Leave Report');

        $sheet->getStyle('A8')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A8')->getAlignment()
              ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->fromArray($headers, null, 'A10');
        $headerRange = 'A10:I10';

// Bold text
        $sheet->getStyle($headerRange)->getFont()->setBold(true);

// White font color
        $sheet->getStyle($headerRange)->getFont()
              ->getColor()->setARGB(Color::COLOR_WHITE);

// Center alignment
        $sheet->getStyle($headerRange)->getAlignment()
              ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($headerRange)->getAlignment()
              ->setVertical(Alignment::VERTICAL_CENTER);

// Background color #0355A7
        $sheet->getStyle($headerRange)->getFill()
              ->setFillType(Fill::FILL_SOLID);

        $sheet->getStyle($headerRange)->getFill()
              ->getStartColor()->setARGB('FF0355A7');

// Border
        $sheet->getStyle($headerRange)->getBorders()
              ->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

// Optional: Increase header row height
        $sheet->getRowDimension(1)->setRowHeight(25);

// Data
        $sheet->fromArray($rows, null, 'A11');

        

        $filePath = sys_get_temp_dir() . '/leave_entitlement_usage.xlsx';
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        foreach (range('E', 'I') as $col) {
            $sheet->getColumnDimension($col)->setVisible(false);
        }
        (new Xlsx($spreadsheet))->save($filePath);

        return new BinaryFileResponse(
            $filePath,
            200,
            [],
            true,
            ResponseHeaderBag::DISPOSITION_ATTACHMENT
        );
    }
}