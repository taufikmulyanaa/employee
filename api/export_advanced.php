<?php
/**
 * Advanced Export Handler for Employee Data
 * File: api/export_advanced.php
 * 
 * Supports multiple formats with better XLSX generation
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
    
    switch ($format) {
        case 'xlsx':
            exportToXLSX($employees, $filename);
            break;
        case 'csv':
            exportToCSV($employees, $filename);
            break;
        case 'json':
            exportToJSON($employees, $filename);
            break;
        default:
            exportToCSV($employees, $filename);
    }
    
} catch (Exception $e) {
    error_log("Export Error: " . $e->getMessage());
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode([
        'error' => 'Export failed',
        'message' => $e->getMessage()
    ]);
    exit;
}

/**
 * Export to CSV format
 */
function exportToCSV($employees, $filename) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename . '.csv');
    header('Cache-Control: no-cache, must-revalidate');
    
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
 * Export to JSON format
 */
function exportToJSON($employees, $filename) {
    header('Content-Type: application/json; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename . '.json');
    header('Cache-Control: no-cache, must-revalidate');
    
    $data = [
        'export_info' => [
            'exported_at' => date('c'),
            'total_records' => count($employees),
            'format' => 'json'
        ],
        'employees' => []
    ];
    
    foreach ($employees as $index => $emp) {
        $data['employees'][] = [
            'no' => $index + 1,
            'name' => $emp['nama'],
            'division' => $emp['division'],
            'status' => $emp['status_headcount'],
            'replacing' => $emp['replace_person'] ?? '',
            'assignment_date' => date('Y-m-d', strtotime($emp['assign_month']))
        ];
    }
    
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Export to XLSX format (improved version)
 */
function exportToXLSX($employees, $filename) {
    // Try to use ZipArchive for real XLSX generation
    if (class_exists('ZipArchive')) {
        exportToRealXLSX($employees, $filename);
    } else {
        // Fallback to SpreadsheetML (Excel XML)
        exportToExcelXML($employees, $filename);
    }
}

/**
 * Generate real XLSX file using ZipArchive
 */
function exportToRealXLSX($employees, $filename) {
    $zip = new ZipArchive();
    $temp_file = tempnam(sys_get_temp_dir(), 'xlsx_export_');
    
    if ($zip->open($temp_file, ZipArchive::CREATE) !== TRUE) {
        // Fallback to Excel XML if zip creation fails
        exportToExcelXML($employees, $filename);
        return;
    }
    
    // Create basic XLSX structure
    
    // 1. [Content_Types].xml
    $content_types = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
    $content_types .= '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">';
    $content_types .= '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>';
    $content_types .= '<Default Extension="xml" ContentType="application/xml"/>';
    $content_types .= '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>';
    $content_types .= '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
    $content_types .= '<Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>';
    $content_types .= '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>';
    $content_types .= '</Types>';
    $zip->addFromString('[Content_Types].xml', $content_types);
    
    // 2. _rels/.rels
    $rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
    $rels .= '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">';
    $rels .= '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>';
    $rels .= '</Relationships>';
    $zip->addFromString('_rels/.rels', $rels);
    
    // 3. xl/_rels/workbook.xml.rels
    $wb_rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
    $wb_rels .= '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">';
    $wb_rels .= '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>';
    $wb_rels .= '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>';
    $wb_rels .= '<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>';
    $wb_rels .= '</Relationships>';
    $zip->addFromString('xl/_rels/workbook.xml.rels', $wb_rels);
    
    // 4. xl/workbook.xml
    $workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
    $workbook .= '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">';
    $workbook .= '<sheets>';
    $workbook .= '<sheet name="Employee Data" sheetId="1" r:id="rId1"/>';
    $workbook .= '</sheets>';
    $workbook .= '</workbook>';
    $zip->addFromString('xl/workbook.xml', $workbook);
    
    // 5. xl/styles.xml (basic styles)
    $styles = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
    $styles .= '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">';
    $styles .= '<fonts count="2">';
    $styles .= '<font><sz val="11"/><name val="Calibri"/></font>';
    $styles .= '<font><b/><sz val="11"/><name val="Calibri"/></font>';
    $styles .= '</fonts>';
    $styles .= '<fills count="2">';
    $styles .= '<fill><patternFill patternType="none"/></fill>';
    $styles .= '<fill><patternFill patternType="gray125"/></fill>';
    $styles .= '</fills>';
    $styles .= '<borders count="1">';
    $styles .= '<border><left/><right/><top/><bottom/><diagonal/></border>';
    $styles .= '</borders>';
    $styles .= '<cellStyleXfs count="1">';
    $styles .= '<xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>';
    $styles .= '</cellStyleXfs>';
    $styles .= '<cellXfs count="2">';
    $styles .= '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>';
    $styles .= '<xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0"/>';
    $styles .= '</cellXfs>';
    $styles .= '</styleSheet>';
    $zip->addFromString('xl/styles.xml', $styles);
    
    // 6. xl/sharedStrings.xml
    $strings = ['No', 'Name', 'Division', 'Status', 'Replacing', 'Assignment Date'];
    foreach ($employees as $emp) {
        $strings[] = $emp['nama'];
        $strings[] = $emp['division'];
        $strings[] = $emp['status_headcount'];
        if ($emp['replace_person']) $strings[] = $emp['replace_person'];
    }
    $strings = array_unique($strings);
    
    $shared_strings = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
    $shared_strings .= '<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="' . count($strings) . '" uniqueCount="' . count($strings) . '">';
    foreach ($strings as $string) {
        $shared_strings .= '<si><t>' . htmlspecialchars($string) . '</t></si>';
    }
    $shared_strings .= '</sst>';
    $zip->addFromString('xl/sharedStrings.xml', $shared_strings);
    
    // 7. xl/worksheets/sheet1.xml (the actual data)
    $string_index = array_flip($strings);
    
    $worksheet = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
    $worksheet .= '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">';
    $worksheet .= '<sheetData>';
    
    // Header row
    $worksheet .= '<row r="1">';
    $headers = ['No', 'Name', 'Division', 'Status', 'Replacing', 'Assignment Date'];
    $col = 1;
    foreach ($headers as $header) {
        $worksheet .= '<c r="' . chr(64 + $col) . '1" t="s" s="1"><v>' . $string_index[$header] . '</v></c>';
        $col++;
    }
    $worksheet .= '</row>';
    
    // Data rows
    $row = 2;
    foreach ($employees as $index => $emp) {
        $worksheet .= '<row r="' . $row . '">';
        $worksheet .= '<c r="A' . $row . '"><v>' . ($index + 1) . '</v></c>';
        $worksheet .= '<c r="B' . $row . '" t="s"><v>' . $string_index[$emp['nama']] . '</v></c>';
        $worksheet .= '<c r="C' . $row . '" t="s"><v>' . $string_index[$emp['division']] . '</v></c>';
        $worksheet .= '<c r="D' . $row . '" t="s"><v>' . $string_index[$emp['status_headcount']] . '</v></c>';
        $worksheet .= '<c r="E' . $row . '" t="s"><v>' . (isset($string_index[$emp['replace_person']]) ? $string_index[$emp['replace_person']] : '') . '</v></c>';
        $worksheet .= '<c r="F' . $row . '" t="inlineStr"><is><t>' . date('Y-m-d', strtotime($emp['assign_month'])) . '</t></is></c>';
        $worksheet .= '</row>';
        $row++;
    }
    
    $worksheet .= '</sheetData>';
    $worksheet .= '</worksheet>';
    $zip->addFromString('xl/worksheets/sheet1.xml', $worksheet);
    
    $zip->close();
    
    // Output the file
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '.xlsx"');
    header('Cache-Control: no-cache, must-revalidate');
    header('Content-Length: ' . filesize($temp_file));
    
    readfile($temp_file);
    unlink($temp_file);
    exit;
}

/**
 * Fallback Excel XML format
 */
function exportToExcelXML($employees, $filename) {
    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename . '.xls');
    header('Cache-Control: no-cache, must-revalidate');
    
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<?mso-application progid="Excel.Sheet"?>' . "\n";
    echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" xmlns:html="http://www.w3.org/TR/REC-html40">' . "\n";
    
    // Styles
    echo '<Styles>' . "\n";
    echo '<Style ss:ID="HeaderStyle">' . "\n";
    echo '<Font ss:Bold="1"/>' . "\n";
    echo '<Interior ss:Color="#E6E6FA" ss:Pattern="Solid"/>' . "\n";
    echo '<Borders>' . "\n";
    echo '<Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>' . "\n";
    echo '</Borders>' . "\n";
    echo '</Style>' . "\n";
    echo '<Style ss:ID="DataStyle">' . "\n";
    echo '<Borders>' . "\n";
    echo '<Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CCCCCC"/>' . "\n";
    echo '</Borders>' . "\n";
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
        echo '<Cell ss:StyleID="DataStyle"><Data ss:Type="Number">' . ($index + 1) . '</Data></Cell>' . "\n";
        echo '<Cell ss:StyleID="DataStyle"><Data ss:Type="String">' . htmlspecialchars($emp['nama']) . '</Data></Cell>' . "\n";
        echo '<Cell ss:StyleID="DataStyle"><Data ss:Type="String">' . htmlspecialchars($emp['division']) . '</Data></Cell>' . "\n";
        echo '<Cell ss:StyleID="DataStyle"><Data ss:Type="String">' . htmlspecialchars($emp['status_headcount']) . '</Data></Cell>' . "\n";
        echo '<Cell ss:StyleID="DataStyle"><Data ss:Type="String">' . htmlspecialchars($emp['replace_person'] ?? '') . '</Data></Cell>' . "\n";
        echo '<Cell ss:StyleID="DataStyle"><Data ss:Type="DateTime">' . date('Y-m-d\TH:i:s', strtotime($emp['assign_month'])) . '</Data></Cell>' . "\n";
        echo '</Row>' . "\n";
    }
    
    echo '</Table>' . "\n";
    echo '</Worksheet>' . "\n";
    echo '</Workbook>' . "\n";
    
    exit;
}
?>