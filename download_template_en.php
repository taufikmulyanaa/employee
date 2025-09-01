<?php
/**
 * Download Excel Template - English Version
 * File: download_template_en.php
 * 
 * This file provides a downloadable CSV template for importing employee data
 */

// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=employee_import_template.csv');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

// Create file pointer to output
$output = fopen('php://output', 'w');

// Add UTF-8 BOM for proper Excel encoding
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// CSV Headers (matching the expected import format)
$headers = [
    'Name',
    'Division', 
    'Headcount Status',
    'Replacing',
    'Assignment Date'
];

// Write headers
fputcsv($output, $headers);

// Add sample data to show the expected format
$sampleData = [
    ['John Doe', 'IT', 'New Headcount', '', '2024-01-15'],
    ['Jane Smith', 'Finance', 'Replacement', 'Mike Johnson', '2024-01-20'],
    ['Ahmad Satria', 'HR', 'New Request', '', '2024-02-01'],
    ['Sarah Wilson', 'Marketing', 'New Headcount', '', '2024-02-05'],
    ['David Chen', 'Operations', 'Replacement', 'Lisa Brown', '2024-02-10']
];

// Write sample data
foreach ($sampleData as $row) {
    fputcsv($output, $row);
}

// Close file pointer
fclose($output);
exit;
?>