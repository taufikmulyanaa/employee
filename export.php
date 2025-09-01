<?php
/**
 * Simple Export Handler for Employee Data
 * File: export.php (place in root folder)
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include required files
require_once 'models/Employee.php';

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
            exportToExcel($employees, $filename);
            break;
        case 'json':
            exportToJSON($employees, $filename);
            break;
        default:
            exportToCSV($employees, $filename);
    }
    
} catch (Exception $e) {
    // Show error for debugging
    header('Content-Type: text/html');
    echo "<h1>Export Error</h1>";
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>File: " . $e->getFile() . " Line: " . $e->getLine() . "</p>";
    echo "<a href='index.php'>Back to Dashboard</a>";
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
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
    
    $headers = ['No', 'Name', 'Division', 'Status', 'Replacing', 'Assignment Date'];
    fputcsv($output, $headers);
    
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
 * Export to Excel format
 */
function exportToExcel($employees, $filename) {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="' . $filename . '.xls"');
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: public');
    
    echo '<html>';
    echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head>';
    echo '<body>';
    echo '<table border="1">';
    echo '<tr style="background-color: #f2f2f2; font-weight: bold;">';
    echo '<td>No</td><td>Name</td><td>Division</td><td>Status</td><td>Replacing</td><td>Assignment Date</td>';
    echo '</tr>';
    
    foreach ($employees as $index => $emp) {
        echo '<tr>';
        echo '<td>' . ($index + 1) . '</td>';
        echo '<td>' . htmlspecialchars($emp['nama']) . '</td>';
        echo '<td>' . htmlspecialchars($emp['division']) . '</td>';
        echo '<td>' . htmlspecialchars($emp['status_headcount']) . '</td>';
        echo '<td>' . htmlspecialchars($emp['replace_person'] ?? '') . '</td>';
        echo '<td>' . date('Y-m-d', strtotime($emp['assign_month'])) . '</td>';
        echo '</tr>';
    }
    
    echo '</table>';
    echo '</body></html>';
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
?>