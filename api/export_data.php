<?php
/**
 * Fixed Export Handler for Employee Data
 * File: api/export_data.php
 * 
 * This file handles all export formats properly
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include required files with proper path handling
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
    // Log the error and show user-friendly message
    error_log("Export Error: " . $e->getMessage());
    header('Content-Type: text/html; charset=utf-8');
    echo '<div style="padding: 20px; font-family: Arial, sans-serif;">';
    echo '<h2>Export Error</h2>';
    echo '<p>Sorry, there was an error processing your export request:</p>';
    echo '<p style="color: red;">' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<p><a href="' . $_SERVER['HTTP_REFERER'] . '">← Back to Dashboard</a></p>';
    echo '</div>';
    exit;
}

/**
 * Export to CSV format
 */
function exportToCSV($employees, $filename) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: public');
    
    $output = fopen('php://output', 'w');
    
    // Add UTF-8 BOM for proper Excel encoding
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Headers
    $headers = ['No', 'Name', 'Division', 'Status', 'Replacing', 'Assignment Date'];
    fputcsv($output, $headers);
    
    // Data rows
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
    header('Content-Disposition: attachment; filename="' . $filename . '.json"');
    header('Cache-Control: no-cache, must-revalidate');
    
    $data = [
        'export_info' => [
            'exported_at' => date('c'),
            'total_records' => count($employees),
            'format' => 'json',
            'generated_by' => 'Satria HR System'
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
 * Export to XLSX format using simple but effective method
 */
function exportToXLSX($employees, $filename) {
    // Try creating real XLSX if ZipArchive is available
    if (extension_loaded('zip')) {
        exportToRealXLSX($employees, $filename);
    } else {
        // Fallback to Excel-compatible XML format
        exportToExcelXML($employees, $filename);
    }
}

/**
 * Create a real XLSX file using ZipArchive
 */
function exportToRealXLSX($employees, $filename) {
    $zip = new ZipArchive();
    $tempFile = tempnam(sys_get_temp_dir(), 'xlsx_');
    
    if ($zip->open($tempFile, ZipArchive::CREATE) !== TRUE) {
        throw new Exception('Cannot create XLSX file');
    }
    
    try {
        // Create the basic XLSX structure
        
        // 1. [Content_Types].xml
        $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
    <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
    <Default Extension="xml" ContentType="application/xml"/>
    <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
    <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
    <Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>
    <Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
</Types>';
        $zip->addFromString('[Content_Types].xml', $contentTypes);
        
        // 2. _rels/.rels
        $rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>';
        $zip->addFromString('_rels/.rels', $rels);
        
        // 3. xl/_rels/workbook.xml.rels
        $workbookRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
    <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>
    <Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
</Relationships>';
        $zip->addFromString('xl/_rels/workbook.xml.rels', $workbookRels);
        
        // 4. xl/workbook.xml
        $workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
    <sheets>
        <sheet name="Employee Data" sheetId="1" r:id="rId1"/>
    </sheets>
</workbook>';
        $zip->addFromString('xl/workbook.xml', $workbook);
        
        // 5. xl/styles.xml (basic styles with header formatting)
        $styles = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
    <fonts count="2">
        <font>
            <sz val="11"/>
            <name val="Calibri"/>
        </font>
        <font>
            <b/>
            <sz val="11"/>
            <name val="Calibri"/>
            <color rgb="FFFFFF"/>
        </font>
    </fonts>
    <fills count="3">
        <fill>
            <patternFill patternType="none"/>
        </fill>
        <fill>
            <patternFill patternType="gray125"/>
        </fill>
        <fill>
            <patternFill patternType="solid">
                <fgColor rgb="4472C4"/>
            </patternFill>
        </fill>
    </fills>
    <borders count="2">
        <border>
            <left/>
            <right/>
            <top/>
            <bottom/>
            <diagonal/>
        </border>
        <border>
            <left style="thin"/>
            <right style="thin"/>
            <top style="thin"/>
            <bottom style="thin"/>
            <diagonal/>
        </border>
    </borders>
    <cellStyleXfs count="1">
        <xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>
    </cellStyleXfs>
    <cellXfs count="3">
        <xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>
        <xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0"/>
        <xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0"/>
    </cellXfs>
</styleSheet>';
        $zip->addFromString('xl/styles.xml', $styles);
        
        // 6. Create shared strings
        $strings = ['No', 'Name', 'Division', 'Status', 'Replacing', 'Assignment Date'];
        foreach ($employees as $emp) {
            $strings[] = $emp['nama'];
            $strings[] = $emp['division'];
            $strings[] = $emp['status_headcount'];
            if ($emp['replace_person']) $strings[] = $emp['replace_person'];
        }
        $strings = array_unique($strings);
        $stringIndex = array_flip($strings);
        
        $sharedStrings = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="' . count($strings) . '" uniqueCount="' . count($strings) . '">';
        
        foreach ($strings as $string) {
            $sharedStrings .= '<si><t>' . htmlspecialchars($string, ENT_XML1) . '</t></si>';
        }
        $sharedStrings .= '</sst>';
        $zip->addFromString('xl/sharedStrings.xml', $sharedStrings);
        
        // 7. xl/worksheets/sheet1.xml (the actual data)
        $worksheet = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
    <sheetData>';
        
        // Header row with styling
        $worksheet .= '<row r="1">';
        $headers = ['No', 'Name', 'Division', 'Status', 'Replacing', 'Assignment Date'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F'];
        
        foreach ($headers as $i => $header) {
            $worksheet .= '<c r="' . $cols[$i] . '1" t="s" s="1"><v>' . $stringIndex[$header] . '</v></c>';
        }
        $worksheet .= '</row>';
        
        // Data rows
        foreach ($employees as $index => $emp) {
            $rowNum = $index + 2;
            $worksheet .= '<row r="' . $rowNum . '">';
            
            // No (number)
            $worksheet .= '<c r="A' . $rowNum . '" s="2"><v>' . ($index + 1) . '</v></c>';
            
            // Name (string)
            $worksheet .= '<c r="B' . $rowNum . '" t="s" s="2"><v>' . $stringIndex[$emp['nama']] . '</v></c>';
            
            // Division (string)
            $worksheet .= '<c r="C' . $rowNum . '" t="s" s="2"><v>' . $stringIndex[$emp['division']] . '</v></c>';
            
            // Status (string)
            $worksheet .= '<c r="D' . $rowNum . '" t="s" s="2"><v>' . $stringIndex[$emp['status_headcount']] . '</v></c>';
            
            // Replacing (string or empty)
            if ($emp['replace_person']) {
                $worksheet .= '<c r="E' . $rowNum . '" t="s" s="2"><v>' . $stringIndex[$emp['replace_person']] . '</v></c>';
            } else {
                $worksheet .= '<c r="E' . $rowNum . '" s="2"><v></v></c>';
            }
            
            // Assignment Date (inline string for simplicity)
            $worksheet .= '<c r="F' . $rowNum . '" t="inlineStr" s="2"><is><t>' . date('Y-m-d', strtotime($emp['assign_month'])) . '</t></is></c>';
            
            $worksheet .= '</row>';
        }
        
        $worksheet .= '</sheetData>
</worksheet>';
        $zip->addFromString('xl/worksheets/sheet1.xml', $worksheet);
        
        $zip->close();
        
        // Output the file
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '.xlsx"');
        header('Cache-Control: no-cache, must-revalidate');
        header('Content-Length: ' . filesize($tempFile));
        
        readfile($tempFile);
        unlink($tempFile);
        
    } catch (Exception $e) {
        $zip->close();
        if (file_exists($tempFile)) {
            unlink($tempFile);
        }
        throw $e;
    }
    
    exit;
}

/**
 * Fallback: Excel-compatible XML format (.xls)
 */
function exportToExcelXML($employees, $filename) {
    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '.xls"');
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: public');
    
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<?mso-application progid="Excel.Sheet"?>' . "\n";
    echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" xmlns:html="http://www.w3.org/TR/REC-html40">' . "\n";
    
    // Styles
    echo '<Styles>' . "\n";
    echo '<Style ss:ID="HeaderStyle">' . "\n";
    echo '<Font ss:Bold="1" ss:Color="#FFFFFF"/>' . "\n";
    echo '<Interior ss:Color="#4472C4" ss:Pattern="Solid"/>' . "\n";
    echo '<Borders>' . "\n";
    echo '<Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>' . "\n";
    echo '<Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>' . "\n";
    echo '<Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>' . "\n";
    echo '<Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>' . "\n";
    echo '</Borders>' . "\n";
    echo '</Style>' . "\n";
    echo '<Style ss:ID="DataStyle">' . "\n";
    echo '<Borders>' . "\n";
    echo '<Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CCCCCC"/>' . "\n";
    echo '<Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CCCCCC"/>' . "\n";
    echo '<Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CCCCCC"/>' . "\n";
    echo '<Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CCCCCC"/>' . "\n";
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
        echo '<Cell ss:StyleID="DataStyle"><Data ss:Type="String">' . date('Y-m-d', strtotime($emp['assign_month'])) . '</Data></Cell>' . "\n";
        echo '</Row>' . "\n";
    }
    
    echo '</Table>' . "\n";
    echo '</Worksheet>' . "\n";
    echo '</Workbook>' . "\n";
    
    exit;
}
?>