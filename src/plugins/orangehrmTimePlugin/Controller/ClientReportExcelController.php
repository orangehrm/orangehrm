<?php

namespace OrangeHRM\Time\Controller;

use OrangeHRM\Core\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Border;

use OrangeHRM\Time\Report\ClientReport;
use OrangeHRM\Time\Dto\ClientReportSearchFilterParams;

class ClientReportExcelController extends AbstractController
{
    public function execute(Request $request)
    {
        // Build Filter DTO
        $filterParams = new ClientReportSearchFilterParams();

        $filterParams->setCustomerId(
            $request->query->getInt('customerId') ?: null
        );

        $filterParams->setProjectId(
            $request->query->getInt('projectId') ?: null
        );

        $filterParams->setEmpNumber(
            $request->query->getInt('empNumber') ?: null
        );

        // SAME STYLE AS EMPLOYEE
        $fromDateParam = $request->query->get('fromDate');
        $toDateParam   = $request->query->get('toDate');

        if (!empty($fromDateParam) && $fromDateParam !== 'null') {
            $filterParams->setFromDate(new \DateTime($fromDateParam));
        } else {
            $filterParams->setFromDate(null);
        }

        if (!empty($toDateParam) && $toDateParam !== 'null') {
            $filterParams->setToDate(new \DateTime($toDateParam));
        } else {
            $filterParams->setToDate(null);
        }

        // Get Report Data
        $report = new ClientReport();
        $reportData = $report->getData($filterParams);

        $rows = $reportData->normalize();
        $meta = $reportData->getMeta();

        // Create Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add Logo
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

        $sheet->getRowDimension(1)->setRowHeight(70);

        // Title
        $sheet->mergeCells('A3:D3');
        $sheet->setCellValue('A3', 'Client Report');

        $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A3')->getAlignment()
              ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Headers
        $headers = [
            'Customer',
            'Project',
            'Employee',
            'Hours Worked'
        ];

        $sheet->fromArray($headers, null, 'A5');
        $headerRange = 'A5:D5';

        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFont()
              ->getColor()->setARGB(Color::COLOR_WHITE);

        $sheet->getStyle($headerRange)->getAlignment()
              ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle($headerRange)->getFill()
              ->setFillType(Fill::FILL_SOLID);

        $sheet->getStyle($headerRange)->getFill()
              ->getStartColor()->setARGB('FF0355A7');

        $sheet->getStyle($headerRange)->getBorders()
              ->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Data Rows
        $rowNumber = 6;

        foreach ($rows as $row) {
            $sheet->setCellValue("A{$rowNumber}", $row[ClientReport::PARAMETER_CUSTOMER] ?? '');
            $sheet->setCellValue("B{$rowNumber}", $row[ClientReport::PARAMETER_PROJECT] ?? '');
            $sheet->setCellValue("C{$rowNumber}", $row[ClientReport::PARAMETER_EMPLOYEE] ?? '');
            $sheet->setCellValue("D{$rowNumber}", $row[ClientReport::PARAMETER_HOURS_WORKED] ?? '');
            $rowNumber++;
        }

        $dataEndRow = $rowNumber - 1;

        if ($dataEndRow >= 6) {
            $sheet->getStyle("A6:D{$dataEndRow}")
                  ->getBorders()
                  ->getAllBorders()
                  ->setBorderStyle(Border::BORDER_THIN);
        }

        // Total Row
        $sheet->setCellValue("A{$rowNumber}", 'Total Hours');
        $sheet->mergeCells("A{$rowNumber}:C{$rowNumber}");

        // SAFE META (important)
        $sum = $meta->get('sum');
        $total = is_array($sum) && isset($sum['label']) ? $sum['label'] : '0.00';

        $sheet->setCellValue("D{$rowNumber}", $total);

        $sheet->getStyle("A{$rowNumber}:D{$rowNumber}")
              ->getFont()->setBold(true);

        // Auto Size
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Save & Download
        $filePath = sys_get_temp_dir() . '/client_report.xlsx';
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