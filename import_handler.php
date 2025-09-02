<?php
/**
 * Import Handler for Employee Data
 * File: import_handler.php
 * 
 * Handles CSV/Excel file import for employee data
 */

require_once 'models/Employee.php';

// Set execution time limit for large files
set_time_limit(300); // 5 minutes
ini_set('memory_limit', '256M');

$response = [
    'success' => false,
    'message' => '',
    'data' => [
        'total_processed' => 0,
        'successful_imports' => 0,
        'failed_imports' => 0,
        'errors' => []
    ]
];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed');
    }

    // Check if file was uploaded
    if (!isset($_FILES['excel_file']) || $_FILES['excel_file']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No file uploaded or upload error occurred');
    }

    $uploadedFile = $_FILES['excel_file'];
    $fileName = $uploadedFile['name'];
    $fileTmpName = $uploadedFile['tmp_name'];
    $fileSize = $uploadedFile['size'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // Validate file size (max 10MB)
    if ($fileSize > 10 * 1024 * 1024) {
        throw new Exception('File size too large. Maximum allowed size is 10MB');
    }

    // Validate file extension
    $allowedExtensions = ['csv', 'xlsx', 'xls'];
    if (!in_array($fileExtension, $allowedExtensions)) {
        throw new Exception('Invalid file format. Only CSV, XLS, and XLSX files are allowed');
    }

    // Process the file based on extension
    $employeeData = [];
    
    if ($fileExtension === 'csv') {
        $employeeData = processCSVFile($fileTmpName);
    } else {
        $employeeData = processExcelFile($fileTmpName);
    }

    if (empty($employeeData)) {
        throw new Exception('No valid data found in the uploaded file');
    }

    // Import data to database
    $employee = new Employee();
    $importResult = importEmployeeData($employee, $employeeData);

    $response['success'] = true;
    $response['message'] = "Import completed successfully!";
    $response['data'] = $importResult;

} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    error_log("Import Error: " . $e->getMessage());
}

// Return JSON response for AJAX requests
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Redirect with message for regular form submission
$message = urlencode($response['message']);
$status = $response['success'] ? 'success' : 'error';
header("Location: import_result.php?status={$status}&message={$message}&data=" . urlencode(json_encode($response['data'])));
exit;

/**
 * Process CSV File
 */
function processCSVFile($filePath) {
    $employeeData = [];
    $row = 0;
    
    if (($handle = fopen($filePath, "r")) !== FALSE) {
        $headers = [];
        
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $row++;
            
            // Skip empty rows
            if (empty(array_filter($data))) {
                continue;
            }
            
            // First row is headers
            if ($row == 1) {
                $headers = array_map('trim', $data);
                // Validate headers
                $requiredHeaders = ['Name', 'Division', 'Headcount Status', 'Replacing', 'Assignment Date'];
                $missingHeaders = array_diff($requiredHeaders, $headers);
                if (!empty($missingHeaders)) {
                    throw new Exception('Missing required columns: ' . implode(', ', $missingHeaders));
                }
                continue;
            }
            
            // Process data row
            if (count($data) >= 5) {
                $employeeData[] = [
                    'nama' => trim($data[0]),
                    'division' => trim($data[1]),
                    'status_headcount' => trim($data[2]),
                    'replace_person' => !empty(trim($data[3])) ? trim($data[3]) : null,
                    'assign_month' => trim($data[4]),
                    'row_number' => $row
                ];
            }
        }
        fclose($handle);
    }
    
    return $employeeData;
}

/**
 * Process Excel File (basic implementation)
 */
function processExcelFile($filePath) {
    // For now, we'll convert Excel to CSV format and process
    // This is a simplified approach - for production, consider using PhpSpreadsheet
    
    $employeeData = [];
    
    // Try to read as CSV first (many Excel files can be read this way)
    try {
        return processCSVFile($filePath);
    } catch (Exception $e) {
        throw new Exception('Excel file processing failed. Please save your Excel file as CSV format and try again.');
    }
}

/**
 * Import Employee Data to Database
 */
function importEmployeeData($employee, $employeeData) {
    $result = [
        'total_processed' => count($employeeData),
        'successful_imports' => 0,
        'failed_imports' => 0,
        'errors' => []
    ];
    
    foreach ($employeeData as $data) {
        try {
            // Validate required fields
            if (empty($data['nama'])) {
                throw new Exception("Name is required");
            }
            if (empty($data['division'])) {
                throw new Exception("Division is required");
            }
            if (empty($data['status_headcount'])) {
                throw new Exception("Headcount Status is required");
            }
            if (empty($data['assign_month'])) {
                throw new Exception("Assignment Date is required");
            }
            
            // Validate status_headcount
            $validStatuses = ['Replacement', 'New Headcount', 'New Request'];
            if (!in_array($data['status_headcount'], $validStatuses)) {
                throw new Exception("Invalid Headcount Status. Must be one of: " . implode(', ', $validStatuses));
            }
            
            // Validate date format
            $assignDate = validateAndFormatDate($data['assign_month']);
            if (!$assignDate) {
                throw new Exception("Invalid Assignment Date format. Use YYYY-MM-DD or DD/MM/YYYY");
            }
            $data['assign_month'] = $assignDate;
            
            // Validate replacement logic
            if ($data['status_headcount'] === 'Replacement' && empty($data['replace_person'])) {
                throw new Exception("Replacement name is required when status is 'Replacement'");
            }
            
            // Try to create employee
            if ($employee->createEmployee($data)) {
                $result['successful_imports']++;
            } else {
                throw new Exception("Failed to save to database");
            }
            
        } catch (Exception $e) {
            $result['failed_imports']++;
            $result['errors'][] = [
                'row' => $data['row_number'] ?? 'Unknown',
                'name' => $data['nama'] ?? 'Unknown',
                'error' => $e->getMessage()
            ];
        }
    }
    
    return $result;
}

/**
 * Validate and format date
 */
function validateAndFormatDate($dateString) {
    $dateString = trim($dateString);
    
    // Try different date formats
    $formats = [
        'Y-m-d',     // 2024-01-15
        'd/m/Y',     // 15/01/2024
        'd-m-Y',     // 15-01-2024
        'm/d/Y',     // 01/15/2024
        'd.m.Y',     // 15.01.2024
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
?>