<?php
/**
 * Export Handler for Employee Data
 * File: api/export.php
 * 
 * Handles export to various formats (CSV, XLSX)
 */

require_once dirname(__FILE__) . '/../models/Employee.php';

// Get export parameters
$format = $_GET['format'] ?? 'csv';
$division = $_GET['division'] ?? '';
$status = $_GET['status'] ?? '';
$month = $_GET['month'] ?? '';
$search = $_GET['search'] ?? '';

try {
    $employee = new Employee();
    
    // Prepare filters
    $filters = [];
    if (!empty($division)) $filters['division'] = $division;
    if (!empty($status)) $filters['status'] = $status;
    if (!empty($month)) $filters['month'] = $month;
    if (!empty($search)) $filters['search'] = $search;
    
    // Get filtered employees
    $employees = $employee->getAllEmployees($filters);
    
    // Generate filename with timestamp
    $timestamp = date('Y-m-d_H-i-s');
    $filename = "employee_data_{$timestamp}";
    
    if ($format === 'xlsx') {
        // Export as Excel (using simple XML format)
        exportToExcel($employees, $filename);
    } else {
        // Default to CSV
        exportToCSV($employees, $filename);
    }
    
} catch (Exception $e) {
    error_log("Export Error: " . $e->getMessage());
    header('HTTP/1.1 500 Internal Server Error');
    echo "Error: Failed to export data";
    exit;
}

/**
 * Export to CSV format
 */
function exportToCSV($employees, $filename) {
    // Set headers for CSV download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename . '.csv');
    header('Cache-Control: no-cache, must-revalidate');
    
    // Create output
    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
    
    // Headers
    $headers = ['No', 'Name', 'Division', 'Status', 'Replacing', 'Assignment Date'];
    fputcsv($output, $headers);
    
    // Data
    foreach ($employees as $index => $emp) {
        $row = [
            $index + 1,
            $emp['nama'],
            $emp['division'],
            $emp['status_headcount'],
            $emp['replace_person'] ?? '',
            date('Y-m-d', strtotime($emp['assign_month']))
        ];
        fputcsv($output, $row);
    }
    
    fclose($output);
    exit;
}

/**
 * Export to Excel format (using SpreadsheetML)
 */
function exportToExcel($employees, $filename) {
    // Set headers for Excel download
    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename . '.xls');
    header('Cache-Control: no-cache, must-revalidate');
    
    // Create Excel XML content
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<?mso-application progid="Excel.Sheet"?>' . "\n";
    echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" xmlns:html="http://www.w3.org/TR/REC-html40">' . "\n";
    
    // Styles
    echo '<Styles>' . "\n";
    echo '<Style ss:ID="HeaderStyle">' . "\n";
    echo '<Font ss:Bold="1"/>' . "\n";
    echo '<Interior ss:Color="#D3D3D3" ss:Pattern="Solid"/>' . "\n";
    echo '</Style>' . "\n";
    echo '</Styles>' . "\n";
    
    echo '<Worksheet ss:Name="Employee Data">' . "\n";
    echo '<Table>' . "\n";
    
    // Header row
    echo '<Row>' . "\n";
    $headers = ['No', 'Name', 'Division', 'Status', 'Replacing', 'Assignment Date'];
    foreach ($headers as $header) {
        echo '<Cell ss:StyleID="HeaderStyle"><Data ss:Type="String">' . htmlspecialchars($header) . '</Data></Cell>' . "\n";
    }
    echo '</Row>' . "\n";
    
    // Data rows
    foreach ($employees as $index => $emp) {
        echo '<Row>' . "\n";
        echo '<Cell><Data ss:Type="Number">' . ($index + 1) . '</Data></Cell>' . "\n";
        echo '<Cell><Data ss:Type="String">' . htmlspecialchars($emp['nama']) . '</Data></Cell>' . "\n";
        echo '<Cell><Data ss:Type="String">' . htmlspecialchars($emp['division']) . '</Data></Cell>' . "\n";
        echo '<Cell><Data ss:Type="String">' . htmlspecialchars($emp['status_headcount']) . '</Data></Cell>' . "\n";
        echo '<Cell><Data ss:Type="String">' . htmlspecialchars($emp['replace_person'] ?? '') . '</Data></Cell>' . "\n";
        echo '<Cell><Data ss:Type="String">' . date('Y-m-d', strtotime($emp['assign_month'])) . '</Data></Cell>' . "\n";
        echo '</Row>' . "\n";
    }
    
    echo '</Table>' . "\n";
    echo '</Worksheet>' . "\n";
    echo '</Workbook>' . "\n";
    
    exit;
}

/**
 * Alternative: Simple XLSX using PhpSpreadsheet (if available)
 * Uncomment this if you want to use PhpSpreadsheet library
 */
/*
function exportToXLSXWithPhpSpreadsheet($employees, $filename) {
    // Check if PhpSpreadsheet is available
    if (!class_exists('PhpOffice\PhpSpreadsheet\Spreadsheet')) {
        // Fallback to simple Excel XML
        exportToExcel($employees, $filename);
        return;
    }
    
    require_once 'vendor/autoload.php';
    
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
    use PhpOffice\PhpSpreadsheet\Style\Fill;
    use PhpOffice\PhpSpreadsheet\Style\Font;
    
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Employee Data');
    
    // Headers
    $headers = ['No', 'Name', 'Division', 'Status', 'Replacing', 'Assignment Date'];
    $column = 1;
    foreach ($headers as $header) {
        $sheet->setCellValueByColumnAndRow($column, 1, $header);
        $column++;
    }
    
    // Style header row
    $headerRange = 'A1:F1';
    $sheet->getStyle($headerRange)->getFont()->setBold(true);
    $sheet->getStyle($headerRange)->getFill()
          ->setFillType(Fill::FILL_SOLID)
          ->getStartColor()->setRGB('D3D3D3');
    
    // Data
    $row = 2;
    foreach ($employees as $index => $emp) {
        $sheet->setCellValueByColumnAndRow(1, $row, $index + 1);
        $sheet->setCellValueByColumnAndRow(2, $row, $emp['nama']);
        $sheet->setCellValueByColumnAndRow(3, $row, $emp['division']);
        $sheet->setCellValueByColumnAndRow(4, $row, $emp['status_headcount']);
        $sheet->setCellValueByColumnAndRow(5, $row, $emp['replace_person'] ?? '');
        $sheet->setCellValueByColumnAndRow(6, $row, date('Y-m-d', strtotime($emp['assign_month'])));
        $row++;
    }
    
    // Auto-size columns
    foreach (range('A', 'F') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }
    
    // Set headers and output
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '.xlsx"');
    header('Cache-Control: max-age=0');
    
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    
    exit;
}
*/
?>