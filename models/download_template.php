<?php
/**
 * Download Excel Template - English Version
 * File: download_template_en.php
 */

// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=employee_template.csv');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

// Create file pointer
$output = fopen('php://output', 'w');

// Add BOM for proper UTF-8 encoding in Excel
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// CSV Headers (English)
$headers = [
    'Name',
    'Division', 
    'Headcount Status',
    'Replacing',
    'Assignment Date'
];

fputcsv($output, $headers);

// Sample data
$sampleData = [
    ['John Doe', 'IT', 'New Headcount', '', '2024-01-15'],
    ['Jane Smith', 'Finance', 'Replacement', 'Mike Johnson', '2024-01-20'],
    ['Ahmad Satria', 'HR', 'New Request', '', '2024-02-01']
];

foreach ($sampleData as $row) {
    fputcsv($output, $row);
}

// Close file pointer
fclose($output);
exit;
?>