<?php
/**
 * Employee Model - Fixed Path
 * File: models/Employee.php
 */

// Fix path issue - use absolute path relative to document root
require_once dirname(__FILE__) . '/../config/database.php';

class Employee {
    private $db;
    private $table = 'employees';

    public function __construct() {
        try {
            $database = new Database();
            $this->db = $database->getConnection();
            
            if (!$this->db || $database->getConnectionError()) {
                throw new Exception("Database connection failed: " . $database->getConnectionError());
            }
        } catch (Exception $e) {
            error_log("Employee Model Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get all employees with optional filters
     */
    public function getAllEmployees($filters = []) {
        $sql = "SELECT e.*, d.name as division_name 
                FROM {$this->table} e 
                LEFT JOIN divisions d ON e.division = d.name";
        
        $conditions = [];
        $params = [];

        // Apply filters
        if (!empty($filters['division'])) {
            $conditions[] = "e.division = :division";
            $params[':division'] = $filters['division'];
        }

        if (!empty($filters['status'])) {
            $conditions[] = "e.status_headcount = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['month'])) {
            $conditions[] = "DATE_FORMAT(e.assign_month, '%Y-%m') = :month";
            $params[':month'] = $filters['month'];
        }

        if (!empty($filters['search'])) {
            $conditions[] = "(e.nama LIKE :search OR e.replace_person LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY e.assign_month DESC, e.nama ASC";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error fetching employees: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get employee by ID
     */
    public function getEmployeeById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error fetching employee by ID: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Create new employee
     */
    public function createEmployee($data) {
        $sql = "INSERT INTO {$this->table} (nama, division, status_headcount, replace_person, assign_month) 
                VALUES (:nama, :division, :status_headcount, :replace_person, :assign_month)";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':nama' => $data['nama'],
                ':division' => $data['division'],
                ':status_headcount' => $data['status_headcount'],
                ':replace_person' => $data['replace_person'] ?? null,
                ':assign_month' => $data['assign_month']
            ]);
        } catch (PDOException $e) {
            error_log("Error creating employee: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update employee
     */
    public function updateEmployee($id, $data) {
        $sql = "UPDATE {$this->table} 
                SET nama = :nama, division = :division, status_headcount = :status_headcount, 
                    replace_person = :replace_person, assign_month = :assign_month 
                WHERE id = :id";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':nama' => $data['nama'],
                ':division' => $data['division'],
                ':status_headcount' => $data['status_headcount'],
                ':replace_person' => $data['replace_person'] ?? null,
                ':assign_month' => $data['assign_month']
            ]);
        } catch (PDOException $e) {
            error_log("Error updating employee: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete employee
     */
    public function deleteEmployee($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Error deleting employee: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get statistics
     */
    public function getStatistics() {
        $stats = [];
        
        // Total employees
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $stmt = $this->db->query($sql);
        $stats['total'] = $stmt->fetch()['total'];

        // By status
        $sql = "SELECT status_headcount, COUNT(*) as count 
                FROM {$this->table} 
                GROUP BY status_headcount";
        $stmt = $this->db->query($sql);
        $statusCounts = $stmt->fetchAll();
        
        foreach ($statusCounts as $row) {
            $stats['by_status'][$row['status_headcount']] = $row['count'];
        }

        // By division
        $sql = "SELECT division, COUNT(*) as count 
                FROM {$this->table} 
                GROUP BY division 
                ORDER BY count DESC";
        $stmt = $this->db->query($sql);
        $stats['by_division'] = $stmt->fetchAll();

        // Recent hires (last 30 days)
        $sql = "SELECT COUNT(*) as recent 
                FROM {$this->table} 
                WHERE assign_month >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        $stmt = $this->db->query($sql);
        $stats['recent'] = $stmt->fetch()['recent'];

        return $stats;
    }

    /**
     * Import employees from array (for Excel import)
     */
    public function importEmployees($employeesData) {
        $sql = "INSERT INTO {$this->table} (nama, division, status_headcount, replace_person, assign_month) 
                VALUES (:nama, :division, :status_headcount, :replace_person, :assign_month)
                ON DUPLICATE KEY UPDATE
                division = VALUES(division),
                status_headcount = VALUES(status_headcount),
                replace_person = VALUES(replace_person),
                assign_month = VALUES(assign_month)";
        
        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare($sql);
            
            $successCount = 0;
            foreach ($employeesData as $employee) {
                $result = $stmt->execute([
                    ':nama' => $employee['nama'],
                    ':division' => $employee['division'],
                    ':status_headcount' => $employee['status_headcount'],
                    ':replace_person' => $employee['replace_person'] ?? null,
                    ':assign_month' => $employee['assign_month']
                ]);
                
                if ($result) $successCount++;
            }
            
            $this->db->commit();
            return ['success' => true, 'imported' => $successCount];
            
        } catch (PDOException $e) {
            $this->db->rollback();
            error_log("Error importing employees: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Get all divisions
     */
    public function getDivisions() {
        $sql = "SELECT DISTINCT division FROM {$this->table} ORDER BY division";
        try {
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            error_log("Error fetching divisions: " . $e->getMessage());
            return [];
        }
    }
}
?>