<?php
/**
 * Employee API Endpoint - Fixed Path
 * File: api/employees.php
 */

// Set content type to JSON
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Only allow GET requests for now
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'error' => 'Method not allowed',
        'message' => 'Only GET requests are supported'
    ]);
    exit;
}

// Include required files with correct path
require_once dirname(__FILE__) . '/../models/Employee.php';

try {
    $employee = new Employee();
    
    // Check if requesting statistics only
    if (isset($_GET['stats']) && $_GET['stats'] === 'true') {
        $stats = $employee->getStatistics();
        
        echo json_encode([
            'success' => true,
            'data' => $stats,
            'timestamp' => date('c')
        ]);
        exit;
    }
    
    // Get specific employee by ID
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $emp = $employee->getEmployeeById($id);
        
        if ($emp) {
            echo json_encode([
                'success' => true,
                'data' => $emp,
                'timestamp' => date('c')
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => 'Employee not found',
                'message' => "Employee with ID {$id} does not exist"
            ]);
        }
        exit;
    }
    
    // Prepare filters
    $filters = [];
    
    // Filter by division
    if (isset($_GET['division']) && !empty($_GET['division'])) {
        $filters['division'] = $_GET['division'];
    }
    
    // Filter by status
    if (isset($_GET['status']) && !empty($_GET['status'])) {
        $filters['status'] = $_GET['status'];
    }
    
    // Filter by month
    if (isset($_GET['month']) && !empty($_GET['month'])) {
        $filters['month'] = $_GET['month'];
    }
    
    // Search filter
    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $filters['search'] = $_GET['search'];
    }
    
    // Get employees
    $employees = $employee->getAllEmployees($filters);
    
    // Prepare response
    $response = [
        'success' => true,
        'data' => $employees,
        'total' => count($employees),
        'filters' => $filters,
        'timestamp' => date('c')
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(500);
    
    echo json_encode([
        'success' => false,
        'error' => 'Internal server error',
        'message' => 'An error occurred while processing your request: ' . $e->getMessage(),
        'timestamp' => date('c')
    ]);
    
    // Log the error
    error_log("API Error: " . $e->getMessage());
}
?>