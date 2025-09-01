<?php
/**
 * AJAX Employee Handler
 * File: ajax_employee.php
 * 
 * Handles AJAX requests for employee CRUD operations
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once 'models/Employee.php';

try {
    $employee = new Employee();
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'add':
            handleAddEmployee($employee);
            break;
            
        case 'edit':
            handleEditEmployee($employee);
            break;
            
        case 'delete':
            handleDeleteEmployee($employee);
            break;
            
        case 'get':
            handleGetEmployee($employee);
            break;
            
        case 'statistics':
            handleGetStatistics($employee);
            break;
            
        case 'list':
            handleGetEmployeeList($employee);
            break;
            
        case 'divisions':
            handleGetDivisions($employee);
            break;
            
        default:
            throw new Exception('Invalid action');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}

function handleAddEmployee($employee) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed');
    }
    
    // Get POST data
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }
    
    // Validate required fields
    $requiredFields = ['nama', 'division', 'status_headcount', 'assign_month'];
    foreach ($requiredFields as $field) {
        if (empty($input[$field])) {
            echo json_encode([
                'success' => false,
                'message' => "Field {$field} harus diisi"
            ]);
            return;
        }
    }
    
    // Prepare data
    $data = [
        'nama' => trim($input['nama']),
        'division' => trim($input['division']),
        'status_headcount' => $input['status_headcount'],
        'replace_person' => !empty($input['replace_person']) ? trim($input['replace_person']) : null,
        'assign_month' => $input['assign_month']
    ];
    
    // Handle custom division
    if (isset($input['custom_division']) && !empty($input['custom_division'])) {
        $data['division'] = trim($input['custom_division']);
    }
    
    // Validate status vs replace_person
    if ($data['status_headcount'] === 'Replacement' && empty($data['replace_person'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Field "Menggantikan" harus diisi untuk status Replacement'
        ]);
        return;
    }
    
    // Create employee
    if ($employee->createEmployee($data)) {
        echo json_encode([
            'success' => true,
            'message' => 'Karyawan berhasil ditambahkan!'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Gagal menambahkan karyawan'
        ]);
    }
}

function handleEditEmployee($employee) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed');
    }
    
    // Get POST data
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }
    
    // Validate required fields
    $requiredFields = ['id', 'nama', 'division', 'status_headcount', 'assign_month'];
    foreach ($requiredFields as $field) {
        if (empty($input[$field])) {
            echo json_encode([
                'success' => false,
                'message' => "Field {$field} harus diisi"
            ]);
            return;
        }
    }
    
    $id = intval($input['id']);
    
    // Check if employee exists
    if (!$employee->getEmployeeById($id)) {
        echo json_encode([
            'success' => false,
            'message' => 'Karyawan tidak ditemukan'
        ]);
        return;
    }
    
    // Prepare data
    $data = [
        'nama' => trim($input['nama']),
        'division' => trim($input['division']),
        'status_headcount' => $input['status_headcount'],
        'replace_person' => !empty($input['replace_person']) ? trim($input['replace_person']) : null,
        'assign_month' => $input['assign_month']
    ];
    
    // Handle custom division
    if (isset($input['custom_division']) && !empty($input['custom_division'])) {
        $data['division'] = trim($input['custom_division']);
    }
    
    // Validate status vs replace_person
    if ($data['status_headcount'] === 'Replacement' && empty($data['replace_person'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Field "Menggantikan" harus diisi untuk status Replacement'
        ]);
        return;
    }
    
    // Update employee
    if ($employee->updateEmployee($id, $data)) {
        echo json_encode([
            'success' => true,
            'message' => 'Data karyawan berhasil diperbarui!'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Gagal memperbarui data karyawan'
        ]);
    }
}

function handleDeleteEmployee($employee) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed');
    }
    
    $id = intval($_GET['id'] ?? 0);
    if (!$id) {
        echo json_encode([
            'success' => false,
            'message' => 'ID karyawan tidak valid'
        ]);
        return;
    }
    
    // Check if employee exists
    $emp = $employee->getEmployeeById($id);
    if (!$emp) {
        echo json_encode([
            'success' => false,
            'message' => 'Karyawan tidak ditemukan'
        ]);
        return;
    }
    
    // Delete employee
    if ($employee->deleteEmployee($id)) {
        echo json_encode([
            'success' => true,
            'message' => 'Karyawan berhasil dihapus!'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Gagal menghapus karyawan'
        ]);
    }
}

function handleGetEmployee($employee) {
    $id = intval($_GET['id'] ?? 0);
    if (!$id) {
        echo json_encode([
            'success' => false,
            'message' => 'ID karyawan tidak valid'
        ]);
        return;
    }
    
    $emp = $employee->getEmployeeById($id);
    if ($emp) {
        echo json_encode([
            'success' => true,
            'data' => $emp
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Karyawan tidak ditemukan'
        ]);
    }
}

function handleGetStatistics($employee) {
    $stats = $employee->getStatistics();
    echo json_encode([
        'success' => true,
        'data' => $stats
    ]);
}

function handleGetEmployeeList($employee) {
    // Get filters
    $filters = [];
    if (isset($_GET['division']) && !empty($_GET['division'])) {
        $filters['division'] = $_GET['division'];
    }
    if (isset($_GET['status']) && !empty($_GET['status'])) {
        $filters['status'] = $_GET['status'];
    }
    if (isset($_GET['month']) && !empty($_GET['month'])) {
        $filters['month'] = $_GET['month'];
    }
    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $filters['search'] = $_GET['search'];
    }
    
    $employees = $employee->getAllEmployees($filters);
    
    // Format data for DataTables
    $data = [];
    foreach ($employees as $index => $emp) {
        $statusColors = [
            'Replacement' => 'bg-yellow-100 text-yellow-800',
            'New Headcount' => 'bg-green-100 text-green-800',
            'New Request' => 'bg-purple-100 text-purple-800'
        ];
        $colorClass = $statusColors[$emp['status_headcount']] ?? 'bg-gray-100 text-gray-800';
        
        $data[] = [
            $index + 1,
            htmlspecialchars($emp['nama']),
            htmlspecialchars($emp['division']),
            '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ' . $colorClass . '">' . 
            htmlspecialchars($emp['status_headcount']) . '</span>',
            $emp['replace_person'] ? htmlspecialchars($emp['replace_person']) : '-',
            date('d/m/Y', strtotime($emp['assign_month'])),
            '<div class="flex items-center gap-2">
                <button onclick="openEditEmployeeModal(' . $emp['id'] . ')" 
                   class="inline-flex items-center justify-center w-8 h-8 text-blue-600 hover:text-white hover:bg-blue-600 rounded-lg border border-blue-200 hover:border-blue-600 transition-all duration-200"
                   title="Edit Karyawan">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </button>
                <button onclick="deleteEmployee(' . $emp['id'] . ')" 
                   class="inline-flex items-center justify-center w-8 h-8 text-red-600 hover:text-white hover:bg-red-600 rounded-lg border border-red-200 hover:border-red-600 transition-all duration-200"
                   title="Hapus Karyawan">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>'
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $data,
        'recordsTotal' => count($employees),
        'recordsFiltered' => count($employees)
    ]);
}

function handleGetDivisions($employee) {
    $divisions = $employee->getDivisions();
    echo json_encode([
        'success' => true,
        'data' => $divisions
    ]);
}
?>