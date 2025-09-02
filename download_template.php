<?php
/**
 * Download Clean CSV Template (No BOM)
 * File: download_template.php
 * 
 * Provides a downloadable CSV template without BOM issues
 */

// Set headers for CSV download - DO NOT ADD BOM
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=employee_import_template_clean.csv');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: public');

// Create file pointer to output
$output = fopen('php://output', 'w');

// DO NOT ADD BOM - this was causing the issue
// fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // <- REMOVED THIS LINE

// CSV Headers (exactly as expected by import)
$headers = [
    'Name',
    'Division', 
    'Headcount Status',
    'Replacing',
    'Assignment Date'
];

// Write headers
fputcsv($output, $headers);

// Sample data with various scenarios
$sampleData = [
    // Basic new headcount
    ['John Doe', 'IT', 'New Headcount', '', '2024-01-15'],
    
    // Replacement scenario
    ['Jane Smith', 'Finance', 'Replacement', 'Mike Johnson', '2024-01-20'],
    
    // New request
    ['Ahmad Satria', 'HR', 'New Request', '', '2024-02-01'],
    
    // Another new headcount with different division
    ['Sarah Wilson', 'Marketing', 'New Headcount', '', '2024-02-05'],
    
    // Another replacement
    ['David Chen', 'Operations', 'Replacement', 'Lisa Brown', '2024-02-10'],
    
    // Sales division example
    ['Maria Garcia', 'Sales', 'New Headcount', '', '2024-02-15'],
    
    // Legal division example
    ['Robert Taylor', 'Legal', 'New Request', '', '2024-02-20'],
    
    // Admin division with replacement
    ['Jennifer Lee', 'Admin', 'Replacement', 'Tom Wilson', '2024-02-25']
];

// Write sample data
foreach ($sampleData as $row) {
    fputcsv($output, $row);
}

// Add instructional section
fputcsv($output, []); // Empty row
fputcsv($output, ['--- INSTRUCTIONS (DELETE THESE ROWS BEFORE IMPORTING) ---']);
fputcsv($output, ['1. Keep the header row exactly as shown above']);
fputcsv($output, ['2. Required columns: Name, Division, Headcount Status, Assignment Date']);
fputcsv($output, ['3. Headcount Status values: Replacement, New Headcount, New Request']);
fputcsv($output, ['4. Replacing field: Required only when status is Replacement']);
fputcsv($output, ['5. Date format: YYYY-MM-DD (like 2024-01-15)']);
fputcsv($output, ['6. Delete all instruction rows (this section) before importing']);
fputcsv($output, ['7. Save file as plain CSV if editing in Excel']);

// Close file pointer
fclose($output);
exit;
?>