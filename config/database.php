<?php
/**
 * Database Configuration
 * Updated with new database name
 */

class Database {
    private $host = 'localhost';
    private $database = 'employee_db';
    private $username = 'root';
    private $password = '';
    private $charset = 'utf8mb4';
    private $pdo;

    private $connectionError = null;

    public function __construct() {
        $this->connect();
    }

    private function connect() {
        $dsn = "mysql:host={$this->host};dbname={$this->database};charset={$this->charset}";
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ];

        try {
            $this->pdo = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            $this->connectionError = $e->getMessage();
            
            // Try to provide more helpful error messages
            if (strpos($e->getMessage(), 'Access denied') !== false) {
                $error = "Database access denied. Please check username and password in config/database.php";
            } elseif (strpos($e->getMessage(), 'Unknown database') !== false) {
                $error = "Database '{$this->database}' does not exist. Please run setup.php first.";
            } elseif (strpos($e->getMessage(), "Can't connect to MySQL server") !== false) {
                $error = "Cannot connect to MySQL server. Please make sure MySQL/MariaDB is running.";
            } else {
                $error = "Database connection failed: " . $e->getMessage();
            }
            
            // Log error for debugging
            error_log("Database Error: " . $e->getMessage());
            
            // Show user-friendly error page instead of dying
            $this->showErrorPage($error);
        }
    }
    
    private function showErrorPage($error) {
        header('HTTP/1.1 503 Service Unavailable');
        header('Content-Type: text/html; charset=utf-8');
        
        $html = '<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Connection Error - Satria HR System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans">
    <div class="min-h-screen flex items-center justify-center py-12 px-4">
        <div class="max-w-md w-full">
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                <h2 class="mt-6 text-xl font-bold text-gray-900">Database Connection Error</h2>
                <p class="mt-2 text-sm text-gray-600">' . htmlspecialchars($error) . '</p>
            </div>
            
            <div class="mt-6 bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Possible Solutions:</h3>
                <ul class="text-sm text-gray-600 space-y-2">
                    <li>• Check if MySQL/MariaDB service is running</li>
                    <li>• Verify database credentials in config/database.php</li>
                    <li>• Run setup.php if you haven\'t completed the installation</li>
                    <li>• Check if the database exists</li>
                </ul>
                
                <div class="mt-6 flex space-x-3">
                    <a href="setup.php" class="flex-1 bg-blue-600 text-white text-center py-2 px-4 rounded-md text-sm hover:bg-blue-700">Run Setup</a>
                    <a href="debug.php" class="flex-1 bg-gray-600 text-white text-center py-2 px-4 rounded-md text-sm hover:bg-gray-700">Debug Mode</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>';
        
        echo $html;
        exit;
    }

    public function getConnection() {
        if ($this->connectionError) {
            return null;
        }
        return $this->pdo;
    }

    public function close() {
        $this->pdo = null;
    }

    // Test connection method
    public function testConnection() {
        if ($this->connectionError) {
            return false;
        }
        
        try {
            $stmt = $this->pdo->query('SELECT 1');
            return $stmt !== false;
        } catch (PDOException $e) {
            error_log("Connection test failed: " . $e->getMessage());
            return false;
        }
    }
    
    public function getConnectionError() {
        return $this->connectionError;
    }
}

// Database configuration constants
define('DB_HOST', 'localhost');
define('DB_NAME', 'employee_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Initialize database connection
function getDBConnection() {
    static $database = null;
    
    if ($database === null) {
        $database = new Database();
    }
    
    return $database->getConnection();
}
?>