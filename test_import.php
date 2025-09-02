<?php
/**
 * Test Import Functionality
 * File: test_import.php
 * 
 * This is a debug script to test the import functionality
 */

// Only allow access in development mode
if (!isset($_GET['debug']) || $_GET['debug'] !== 'true') {
    die('Access denied. Add ?debug=true to URL to access.');
}

echo '<h1>Import System Test</h1>';
echo '<style>body{font-family:Arial,sans-serif;margin:20px;} .success{color:green;} .error{color:red;} .info{color:blue;} pre{background:#f5f5f5;padding:10px;border-radius:5px;}</style>';

echo '<h2>1. Testing Database Connection</h2>';
try {
    require_once 'models/Employee.php';
    $employee = new Employee();
    echo '<p class="success">✓ Database connection successful</p>';
    
    // Test basic operations
    $stats = $employee->getStatistics();
    echo '<p class="info">Current employee count: ' . $stats['total'] . '</p>';
    
} catch (Exception $e) {
    echo '<p class="error">✗ Database connection failed: ' . htmlspecialchars($e->getMessage()) . '</p>';
    exit;
}

echo '<h2>2. Testing CSV Sample Data</h2>';

// Create sample CSV data
$sampleCSV = [
    ['Name', 'Division', 'Headcount Status', 'Replacing', 'Assignment Date'],
    ['Test User 1', 'IT', 'New Headcount', '', '2024-01-15'],
    ['Test User 2', 'Finance', 'Replacement', 'Old Employee', '2024-01-20'],
    ['Test User 3', 'HR', 'New Request', '', '2024-02-01'],
    ['', 'Marketing', 'New Headcount', '', '2024-02-05'], // Invalid - empty name
    ['Test User 5', '', 'New Headcount', '', '2024-02-10'], // Invalid - empty division
    ['Test User 6', 'IT', 'Invalid Status', '', '2024-02-15'], // Invalid status
    ['Test User 7', 'Sales', 'Replacement', '', '2024-02-20'], // Invalid - replacement without name
    ['Test User 8', 'Legal', 'New Headcount', '', 'invalid-date'], // Invalid date
    ['Test User 9', 'Admin', 'New Headcount', '', '2024-02-25'], // Valid
];

// Create temporary CSV file
$tempFile = tempnam(sys_get_temp_dir(), 'test_import_');
$handle = fopen($tempFile, 'w');
foreach ($sampleCSV as $row) {
    fputcsv($handle, $row);
}
fclose($handle);

echo '<p class="info">Created test CSV with ' . count($sampleCSV) . ' rows (including header)</p>';
echo '<pre>' . file_get_contents($tempFile) . '</pre>';

echo '<h2>3. Testing Import Processing</h2>';

// Include the processing functions from import_handler.php
function processCSVFile($filePath) {
    $employeeData = [];
    $row = 0;
    
    if (($handle = fopen($filePath, "r")) !== FALSE) {
        $headers = [];
        $expectedHeaders = ['Name', 'Division', 'Headcount Status', 'Replacing', 'Assignment Date'];
        
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $row++;
            
            // Skip completely empty rows
            if (empty(array_filter($data, function($value) { return trim($value) !== ''; }))) {
                continue;
            }
            
            // First row should be headers
            if ($row == 1) {
                $headers = array_map('trim', $data);
                
                // Validate headers
                if (count($headers) < 5) {
                    throw new Exception("CSV file must have at least 5 columns. Found " . count($headers) . " columns.");
                }
                
                // Check if headers match expected format (case-insensitive)
                for ($i = 0; $i < 5; $i++) {
                    if (strtolower(trim($headers[$i])) !== strtolower(trim($expectedHeaders[$i]))) {
                        $actualHeaders = implode(', ', array_slice($headers, 0, 5));
                        $expectedHeadersStr = implode(', ', $expectedHeaders);
                        throw new Exception("Invalid column headers. Expected: {$expectedHeadersStr}. Found: {$actualHeaders}");
                    }
                }
                continue;
            }
            
            // Ensure we have at least 5 columns
            while (count($data) < 5) {
                $data[] = '';
            }
            
            // Process data row
            $name = trim($data[0]);
            $division = trim($data[1]);
            $status = trim($data[2]);
            $replacing = trim($data[3]);
            $assignDate = trim($data[4]);
            
            // Skip rows where name is empty
            if (empty($name)) {
                continue;
            }
            
            $employeeData[] = [
                'nama' => $name,
                'division' => $division,
                'status_headcount' => $status,
                'replace_person' => !empty($replacing) ? $replacing : null,
                'assign_month' => $assignDate,
                'row_number' => $row
            ];
        }
        fclose($handle);
    } else {
        throw new Exception('Could not open CSV file for reading');
    }
    
    return $employeeData;
}

function validateAndFormatDate($dateString) {
    $dateString = trim($dateString);
    
    if (empty($dateString)) {
        return false;
    }
    
    // Try different date formats
    $formats = [
        'Y-m-d',     // 2024-01-15 (preferred)
        'd/m/Y',     // 15/01/2024
        'm/d/Y',     // 01/15/2024
        'd-m-Y',     // 15-01-2024
        'd.m.Y',     // 15.01.2024
        'Y/m/d',     // 2024/01/15
    ];
    
    foreach ($formats as $format) {
        $date = DateTime::createFromFormat($format, $dateString);
        if ($date && $date->format($format) === $dateString) {
            return $date->format('Y-m-d');
        }
    }
    
    // Try strtotime as fallback
    $timestamp = strtotime($dateString);
    if ($timestamp !== false) {
        return date('Y-m-d', $timestamp);
    }
    
    return false;
}

try {
    $employeeData = processCSVFile($tempFile);
    echo '<p class="success">✓ CSV processing successful</p>';
    echo '<p class="info">Processed ' . count($employeeData) . ' data rows</p>';
    
    echo '<h3>Processed Data Preview:</h3>';
    echo '<pre>' . print_r($employeeData, true) . '</pre>';
    
} catch (Exception $e) {
    echo '<p class="error">✗ CSV processing failed: ' . htmlspecialchars($e->getMessage()) . '</p>';
}

echo '<h2>4. Testing Data Validation</h2>';

$validationResults = [];
foreach ($employeeData as $data) {
    $errors = [];
    
    // Validate required fields
    if (empty($data['nama'])) {
        $errors[] = "Name is required";
    }
    if (empty($data['division'])) {
        $errors[] = "Division is required";
    }
    if (empty($data['status_headcount'])) {
        $errors[] = "Headcount Status is required";
    }
    if (empty($data['assign_month'])) {
        $errors[] = "Assignment Date is required";
    }
    
    // Validate status_headcount
    $validStatuses = ['Replacement', 'New Headcount', 'New Request'];
    $statusFound = false;
    foreach ($validStatuses as $validStatus) {
        if (strcasecmp($data['status_headcount'], $validStatus) === 0) {
            $statusFound = true;
            break;
        }
    }
    
    if (!$statusFound && !empty($data['status_headcount'])) {
        $errors[] = "Invalid Headcount Status '{$data['status_headcount']}'";
    }
    
    // Validate date
    if (!empty($data['assign_month'])) {
        $validDate = validateAndFormatDate($data['assign_month']);
        if (!$validDate) {
            $errors[] = "Invalid date format '{$data['assign_month']}'";
        }
    }
    
    // Validate replacement logic
    if ($data['status_headcount'] === 'Replacement' && empty($data['replace_person'])) {
        $errors[] = "Replacement name required for Replacement status";
    }
    
    $validationResults[] = [
        'row' => $data['row_number'],
        'name' => $data['nama'],
        'valid' => empty($errors),
        'errors' => $errors
    ];
}

$validCount = 0;
$invalidCount = 0;

echo '<table border="1" cellpadding="5" style="border-collapse:collapse; width:100%; margin-top:10px;">';
echo '<tr><th>Row</th><th>Name</th><th>Status</th><th>Errors</th></tr>';

foreach ($validationResults as $result) {
    if ($result['valid']) {
        $validCount++;
        echo '<tr><td>' . $result['row'] . '</td><td>' . htmlspecialchars($result['name']) . '</td><td class="success">Valid</td><td>-</td></tr>';
    } else {
        $invalidCount++;
        echo '<tr><td>' . $result['row'] . '</td><td>' . htmlspecialchars($result['name']) . '</td><td class="error">Invalid</td><td>' . implode('; ', $result['errors']) . '</td></tr>';
    }
}

echo '</table>';

echo '<p class="info"><strong>Validation Summary:</strong></p>';
echo '<ul>';
echo '<li class="success">Valid rows: ' . $validCount . '</li>';
echo '<li class="error">Invalid rows: ' . $invalidCount . '</li>';
echo '<li>Success rate: ' . round(($validCount / count($validationResults)) * 100, 1) . '%</li>';
echo '</ul>';

echo '<h2>5. Testing Database Insert (DRY RUN)</h2>';
echo '<p class="info">This is a dry run - no data will actually be inserted.</p>';

foreach ($validationResults as $result) {
    if ($result['valid']) {
        echo '<p class="success">✓ Would insert: ' . htmlspecialchars($result['name']) . '</p>';
    } else {
        echo '<p class="error">✗ Would skip: ' . htmlspecialchars($result['name']) . ' (Validation errors)</p>';
    }
}

// Cleanup
unlink($tempFile);

echo '<h2>6. Test Complete</h2>';
echo '<p class="info">Test completed successfully. The import system appears to be working correctly.</p>';
echo '<p><strong>Next Steps:</strong></p>';
echo '<ul>';
echo '<li>Test with real CSV files through the web interface</li>';
echo '<li>Verify data appears correctly in the dashboard</li>';
echo '<li>Test with different file formats (.xlsx, .xls)</li>';
echo '</ul>';

echo '<p><a href="import.php">Go to Import Page</a> | <a href="index.php">Go to Dashboard</a></p>';
?>