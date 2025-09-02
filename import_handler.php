<?php
/**
 * Fixed Import Handler with BOM Handling
 * File: import_handler.php
 * 
 * Handles CSV/Excel file import for employee data with proper BOM handling
 */

require_once 'models/Employee.php';

// Set execution time limit and memory for large files
set_time_limit(300); // 5 minutes
ini_set('memory_limit', '512M');

// Initialize response
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
    // Only process if this is a POST request with file upload
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Check if file was uploaded
    if (!isset($_FILES['excel_file']) || $_FILES['excel_file']['error'] !== UPLOAD_ERR_OK) {
        $uploadErrors = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds the upload_max_filesize directive',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds the MAX_FILE_SIZE directive',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
        ];
        
        $errorCode = $_FILES['excel_file']['error'] ?? UPLOAD_ERR_NO_FILE;
        throw new Exception($uploadErrors[$errorCode] ?? 'Unknown upload error');
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

    // Validate file exists and is readable
    if (!file_exists($fileTmpName) || !is_readable($fileTmpName)) {
        throw new Exception('Uploaded file is not accessible');
    }

    // Process the file based on extension
    $employeeData = [];
    
    if ($fileExtension === 'csv') {
        $employeeData = processCSVFile($fileTmpName);
    } else {
        // For Excel files, we'll try to read them as CSV first
        // This works for many Excel files that are essentially CSV
        $employeeData = processExcelAsCSV($fileTmpName, $fileName);
    }

    if (empty($employeeData)) {
        throw new Exception('No valid data found in the uploaded file. Please check the file format and try again.');
    }

    // Import data to database
    $employee = new Employee();
    $importResult = importEmployeeData($employee, $employeeData);

    $response['success'] = true;
    $response['message'] = "Import completed successfully! {$importResult['successful_imports']} employees imported.";
    $response['data'] = $importResult;

} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    error_log("Import Error: " . $e->getMessage());
}

// Redirect to results page with data
$message = urlencode($response['message']);
$status = $response['success'] ? 'success' : 'error';
$data = urlencode(json_encode($response['data']));

header("Location: import_result.php?status={$status}&message={$message}&data={$data}");
exit;

/**
 * Remove UTF-8 BOM from string
 */
function removeBOM($str) {
    // UTF-8 BOM is EF BB BF
    if (substr($str, 0, 3) === "\xEF\xBB\xBF") {
        return substr($str, 3);
    }
    return $str;
}

/**
 * Process CSV File with proper BOM handling
 */
function processCSVFile($filePath) {
    $employeeData = [];
    $row = 0;
    
    // Read file content and remove BOM
    $content = file_get_contents($filePath);
    
    // Remove BOM if present
    $content = removeBOM($content);
    
    // Detect and convert encoding
    $encoding = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
    if ($encoding !== 'UTF-8') {
        $content = mb_convert_encoding($content, 'UTF-8', $encoding);
    }
    
    // Write cleaned content back to temp file
    $tempFile = tempnam(sys_get_temp_dir(), 'clean_csv_');
    file_put_contents($tempFile, $content);
    
    try {
        if (($handle = fopen($tempFile, "r")) !== FALSE) {
            $headers = [];
            $expectedHeaders = ['Name', 'Division', 'Headcount Status', 'Replacing', 'Assignment Date'];
            
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $row++;
                
                // Skip completely empty rows
                if (empty(array_filter($data, function($value) { return trim($value) !== ''; }))) {
                    continue;
                }
                
                // Clean each field from potential BOM or weird characters
                $data = array_map(function($field) {
                    $field = removeBOM(trim($field));
                    // Remove any non-printable characters except newlines and tabs
                    $field = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $field);
                    return $field;
                }, $data);
                
                // First row should be headers
                if ($row == 1) {
                    $headers = $data;
                    
                    // Debug: Log the actual headers received
                    error_log("CSV Headers received: " . json_encode($headers));
                    error_log("Expected headers: " . json_encode($expectedHeaders));
                    
                    // Validate headers count
                    if (count($headers) < 5) {
                        throw new Exception("CSV file must have at least 5 columns. Found " . count($headers) . " columns.");
                    }
                    
                    // Check if headers match expected format (case-insensitive and trimmed)
                    for ($i = 0; $i < 5; $i++) {
                        $actualHeader = strtolower(trim($headers[$i]));
                        $expectedHeader = strtolower(trim($expectedHeaders[$i]));
                        
                        if ($actualHeader !== $expectedHeader) {
                            $actualHeaders = implode(', ', array_slice($headers, 0, 5));
                            $expectedHeadersStr = implode(', ', $expectedHeaders);
                            throw new Exception("Invalid column headers. Expected: {$expectedHeadersStr}. Found: {$actualHeaders}. Please make sure the first row contains exact column names.");
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
    } finally {
        // Clean up temp file
        if (file_exists($tempFile)) {
            unlink($tempFile);
        }
    }
    
    return $employeeData;
}

/**
 * Process Excel File as CSV
 */
function processExcelAsCSV($filePath, $originalName) {
    try {
        // Try reading as CSV first (some .xls files are actually CSV)
        return processCSVFile($filePath);
    } catch (Exception $e) {
        // If that fails, provide helpful error message
        throw new Exception("Excel file processing failed: {$e->getMessage()}. Please save your Excel file as CSV format and try again. To convert: Open in Excel → File → Save As → Choose 'CSV (Comma delimited)' format.");
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
            
            // Validate status_headcount (case-insensitive)
            $validStatuses = ['Replacement', 'New Headcount', 'New Request'];
            $statusFound = false;
            foreach ($validStatuses as $validStatus) {
                if (strcasecmp($data['status_headcount'], $validStatus) === 0) {
                    $data['status_headcount'] = $validStatus; // Use the correct case
                    $statusFound = true;
                    break;
                }
            }
            
            if (!$statusFound) {
                throw new Exception("Invalid Headcount Status '{$data['status_headcount']}'. Must be one of: " . implode(', ', $validStatuses));
            }
            
            // Validate date format and convert
            $assignDate = validateAndFormatDate($data['assign_month']);
            if (!$assignDate) {
                throw new Exception("Invalid Assignment Date format '{$data['assign_month']}'. Use YYYY-MM-DD format (e.g., 2024-01-15)");
            }
            $data['assign_month'] = $assignDate;
            
            // Validate replacement logic
            if ($data['status_headcount'] === 'Replacement' && empty($data['replace_person'])) {
                throw new Exception("Replacement name is required when Headcount Status is 'Replacement'");
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
?>