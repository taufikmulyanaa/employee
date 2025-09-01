<?php
/**
 * Employee API Handler for AJAX Operations
 * File: api/employee_handler.php
 * 
 * Handles CRUD operations for employees via AJAX
 */

// Set content type to JSON
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Include required files with correct path
require_once dirname(__FILE__) . '/../models/Employee.php';

// Response function
function jsonResponse($success, $message = '', $data = null) {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data,
        'timestamp' => date('c')
    ]);
    exit;
}

try {
    $employee = new Employee();
    
    // Determine action
    $action = $_POST['action'] ?? 'create';
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        switch ($action) {
            case 'create':
                // Validate required fields
                if (empty($_POST['nama'])) {
                    jsonResponse(false, 'Name is required');
                }
                if (empty($_POST['division'])) {
                    jsonResponse(false, 'Division is required');
                }
                if (empty($_POST['status_headcount'])) {
                    jsonResponse(false, 'Headcount status must be selected');
                }
                if (empty($_POST['assign_month'])) {
                    jsonResponse(false, 'Assignment date is required');
                }
                
                // Prepare data
                $data = [
                    'nama' => trim($_POST['nama']),
                    'division' => trim($_POST['division']),
                    'status_headcount' => $_POST['status_headcount'],
                    'replace_person' => !empty($_POST['replace_person']) ? trim($_POST['replace_person']) : null,
                    'assign_month' => $_POST['assign_month']
                ];
                
                // Handle custom division
                if ($_POST['division'] === 'other' && !empty($_POST['custom_division'])) {
                    $data['division'] = trim($_POST['custom_division']);
                }
                
                // Additional validation for replacement
                if ($data['status_headcount'] === 'Replacement' && empty($data['replace_person'])) {
                    jsonResponse(false, 'Replacement name is required for Replacement status');
                }
                
                // Create employee
                if ($employee->createEmployee($data)) {
                    jsonResponse(true, 'Employee added successfully', $data);
                } else {
                    jsonResponse(false, 'Failed to add employee');
                }
                break;
                
            case 'update':
                // Validate ID
                if (empty($_POST['id'])) {
                    jsonResponse(false, 'Employee ID is required');
                }
                
                $id = intval($_POST['id']);
                
                // Validate required fields
                if (empty($_POST['nama'])) {
                    jsonResponse(false, 'Name is required');
                }
                if (empty($_POST['division'])) {
                    jsonResponse(false, 'Division is required');
                }
                if (empty($_POST['status_headcount'])) {
                    jsonResponse(false, 'Headcount status must be selected');
                }
                if (empty($_POST['assign_month'])) {
                    jsonResponse(false, 'Assignment date is required');
                }
                
                // Prepare data
                $data = [
                    'nama' => trim($_POST['nama']),
                    'division' => trim($_POST['division']),
                    'status_headcount' => $_POST['status_headcount'],
                    'replace_person' => !empty($_POST['replace_person']) ? trim($_POST['replace_person']) : null,
                    'assign_month' => $_POST['assign_month']
                ];
                
                // Handle custom division
                if ($_POST['division'] === 'other' && !empty($_POST['custom_division'])) {
                    $data['division'] = trim($_POST['custom_division']);
                }
                
                // Additional validation for replacement
                if ($data['status_headcount'] === 'Replacement' && empty($data['replace_person'])) {
                    jsonResponse(false, 'Replacement name is required for Replacement status');
                }
                
                // Update employee
                if ($employee->updateEmployee($id, $data)) {
                    jsonResponse(true, 'Employee data updated successfully', $data);
                } else {
                    jsonResponse(false, 'Failed to update employee data');
                }
                break;
                
            case 'delete':
                // Validate ID
                if (empty($_POST['id'])) {
                    jsonResponse(false, 'Employee ID is required');
                }
                
                $id = intval($_POST['id']);
                
                // Get employee data for confirmation
                $emp = $employee->getEmployeeById($id);
                if (!$emp) {
                    jsonResponse(false, 'Employee not found');
                }
                
                // Delete employee
                if ($employee->deleteEmployee($id)) {
                    jsonResponse(true, 'Employee deleted successfully', $emp);
                } else {
                    jsonResponse(false, 'Failed to delete employee');
                }
                break;
                
            default:
                jsonResponse(false, 'Invalid action');
                break;
        }
        
    } else {
        jsonResponse(false, 'Method not allowed. Use POST.');
    }
    
} catch (Exception $e) {
    error_log("Employee Handler Error: " . $e->getMessage());
    jsonResponse(false, 'Server error occurred: ' . $e->getMessage());
}
?>