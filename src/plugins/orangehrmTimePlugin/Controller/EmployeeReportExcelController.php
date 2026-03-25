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
use OrangeHRM\Time\Report\EmployeeReport;
use OrangeHRM\Time\Dto\EmployeeReportsSearchFilterParams;

class EmployeeReportExcelController extends AbstractController
{
    public function execute(Request $request)
    {
        // 1️⃣ Build Filter DTO
        $filterParams = new EmployeeReportsSearchFilterParams();

        $filterParams->setEmpNumber(
            $request->query->getInt('empNumber')
        );

        $filterParams->setProjectId(
            $request->query->getInt('projectId') ?: null
        );

        $filterParams->setActivityId(
            $request->query->getInt('activityId') ?: null
        );

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

        // 2️⃣ Get Report Data
        $report = new EmployeeReport();
        $reportData = $report->getData($filterParams);

        $rows = $reportData->normalize();
        $meta = $reportData->getMeta();

        // 3️⃣ Create Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // 4️⃣ Add Logo
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

        // 5️⃣ Report Title
        $sheet->mergeCells('A3:C3');
        $sheet->setCellValue('A3', 'Employee Timesheet Report');

        $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A3')->getAlignment()
              ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // 6️⃣ Headers
        $headers = [
            'Project Name',
            'Client Manager',
            'Activity Name',
            'Description',
            'Time (Hours)'
        ];

        $sheet->fromArray($headers, null, 'A5');
        $headerRange = 'A5:E5';

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

        

        $rowNumber = 6;

        foreach ($rows as $row) {

            $sheet->setCellValue("A{$rowNumber}", $row['projectName']);
            $sheet->setCellValue("B{$rowNumber}", $row['clientManager']);
            $sheet->setCellValue("C{$rowNumber}", $row['activityName']);
            $sheet->setCellValue("D{$rowNumber}", $row['description']); // ✅ NEW
            $sheet->setCellValue("E{$rowNumber}", $row['duration']);

            $rowNumber++;
        } 

        $dataEndRow = $rowNumber - 1;

        $sheet->getStyle("A6:E{$dataEndRow}")
              ->getBorders()
              ->getAllBorders()
              ->setBorderStyle(Border::BORDER_THIN);

       

        $sheet->setCellValue("A{$rowNumber}", 'Total Hours');
        $sheet->mergeCells("A{$rowNumber}:D{$rowNumber}");
        $sheet->setCellValue("E{$rowNumber}", $meta->get('sum')['label']);
        $sheet->getStyle("A{$rowNumber}:E{$rowNumber}")
              ->getFont()->setBold(true);

        // 9️⃣ Auto Size Columns
        foreach (range('A', 'E') as $col){
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // 🔟 Save & Download
        $filePath = sys_get_temp_dir() . '/employee_timesheet_report.xlsx';
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