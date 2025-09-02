<?php
/**
 * Database Setup Script
 * File: setup.php
 * 
 * Creates database and tables for Satria HR System
 */

// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'employee_db';

$success_messages = [];
$error_messages = [];
$is_setup_complete = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Connect to MySQL server (without database)
        $dsn = "mysql:host={$host};charset=utf8mb4";
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        // Create database if not exists
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $success_messages[] = "✓ Database '{$database}' created successfully";

        // Switch to the database
        $pdo->exec("USE `{$database}`");

        // Create employees table
        $createEmployeesTable = "
            CREATE TABLE IF NOT EXISTS `employees` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `nama` varchar(255) NOT NULL,
                `division` varchar(100) NOT NULL,
                `status_headcount` enum('Replacement','New Headcount','New Request') NOT NULL,
                `replace_person` varchar(255) DEFAULT NULL,
                `assign_month` date NOT NULL,
                `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `idx_division` (`division`),
                KEY `idx_status` (`status_headcount`),
                KEY `idx_assign_month` (`assign_month`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        
        $pdo->exec($createEmployeesTable);
        $success_messages[] = "✓ Employees table created successfully";

        // Create divisions table (optional - for future use)
        $createDivisionsTable = "
            CREATE TABLE IF NOT EXISTS `divisions` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(100) NOT NULL UNIQUE,
                `description` text,
                `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        
        $pdo->exec($createDivisionsTable);
        $success_messages[] = "✓ Divisions table created successfully";

        // Insert default divisions
        $defaultDivisions = [
            ['IT', 'Information Technology Department'],
            ['Finance', 'Finance & Accounting Department'],
            ['HR', 'Human Resources Department'],
            ['Operations', 'Operations Department'],
            ['Marketing', 'Marketing & Communications Department'],
            ['Sales', 'Sales Department'],
            ['Legal', 'Legal & Compliance Department'],
            ['Admin', 'Administration Department']
        ];

        $insertDivision = $pdo->prepare("INSERT IGNORE INTO divisions (name, description) VALUES (?, ?)");
        foreach ($defaultDivisions as $division) {
            $insertDivision->execute($division);
        }
        $success_messages[] = "✓ Default divisions inserted successfully";

        // Insert sample data if requested
        if (isset($_POST['insert_sample'])) {
            $sampleEmployees = [
                [
                    'nama' => 'John Doe',
                    'division' => 'IT',
                    'status_headcount' => 'New Headcount',
                    'replace_person' => null,
                    'assign_month' => '2024-01-15'
                ],
                [
                    'nama' => 'Jane Smith',
                    'division' => 'Finance',
                    'status_headcount' => 'Replacement',
                    'replace_person' => 'Mike Johnson',
                    'assign_month' => '2024-01-20'
                ],
                [
                    'nama' => 'Ahmad Satria',
                    'division' => 'HR',
                    'status_headcount' => 'New Request',
                    'replace_person' => null,
                    'assign_month' => '2024-02-01'
                ],
                [
                    'nama' => 'Sarah Wilson',
                    'division' => 'Marketing',
                    'status_headcount' => 'New Headcount',
                    'replace_person' => null,
                    'assign_month' => '2024-02-05'
                ],
                [
                    'nama' => 'David Chen',
                    'division' => 'Operations',
                    'status_headcount' => 'Replacement',
                    'replace_person' => 'Lisa Brown',
                    'assign_month' => '2024-02-10'
                ]
            ];

            $insertEmployee = $pdo->prepare("INSERT INTO employees (nama, division, status_headcount, replace_person, assign_month) VALUES (?, ?, ?, ?, ?)");
            
            foreach ($sampleEmployees as $employee) {
                $insertEmployee->execute([
                    $employee['nama'],
                    $employee['division'],
                    $employee['status_headcount'],
                    $employee['replace_person'],
                    $employee['assign_month']
                ]);
            }
            
            $success_messages[] = "✓ Sample employee data inserted successfully";
        }

        // Create a setup completion flag
        file_put_contents('setup_completed.lock', date('Y-m-d H:i:s'));
        $success_messages[] = "✓ Setup completed successfully!";
        $is_setup_complete = true;

    } catch (PDOException $e) {
        $error_messages[] = "Database Error: " . $e->getMessage();
    } catch (Exception $e) {
        $error_messages[] = "Error: " . $e->getMessage();
    }
}

// Check if setup has been completed before
$setup_already_done = file_exists('setup_completed.lock');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup - Satria HR System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 font-sans">
    <div class="min-h-screen flex items-center justify-center py-12 px-4">
        <div class="max-w-2xl w-full">
            <div class="text-center mb-8">
                <!-- Logo -->
                <div class="flex justify-center mb-6">
                    <svg width="48" height="48" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-blue-600">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M22.5 25C22.5 23.6193 23.6193 22.5 25 22.5H75C76.3807 22.5 77.5 23.6193 77.5 25V37.5H22.5V25ZM22.5 42.5H77.5V57.5H22.5V42.5ZM22.5 62.5H77.5V75C77.5 76.3807 76.3807 77.5 75 77.5H25C23.6193 77.5 22.5 76.3807 22.5 75V62.5Z" fill="currentColor"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-900">Satria HR System</h1>
                <p class="text-lg text-gray-600 mt-2">Database Setup Wizard</p>
            </div>

            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">
                        <?php if ($setup_already_done && !$is_setup_complete): ?>
                            Setup Already Completed
                        <?php elseif ($is_setup_complete): ?>
                            Setup Completed Successfully!
                        <?php else: ?>
                            Initialize Database
                        <?php endif; ?>
                    </h2>
                </div>

                <div class="p-6">
                    <?php if ($setup_already_done && !$is_setup_complete): ?>
                        <div class="bg-green-50 border border-green-200 rounded-md p-4 mb-6">
                            <div class="flex">
                                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <div class="ml-3">
                                    <p class="text-sm text-green-800">
                                        <strong>Setup has already been completed!</strong><br>
                                        Database and tables are ready to use.
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex justify-center space-x-4">
                            <a href="index.php" class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 transition-colors font-medium">
                                Go to Dashboard
                            </a>
                            <button onclick="if(confirm('Are you sure you want to run setup again? This might reset your data.')) { document.getElementById('force-setup').submit(); }" 
                                    class="bg-gray-600 text-white px-6 py-3 rounded-md hover:bg-gray-700 transition-colors font-medium">
                                Force Re-setup
                            </button>
                        </div>
                        
                        <form id="force-setup" method="POST" class="hidden">
                            <?php unlink('setup_completed.lock'); ?>
                        </form>

                    <?php elseif ($is_setup_complete): ?>
                        <div class="space-y-4">
                            <?php foreach ($success_messages as $message): ?>
                                <div class="bg-green-50 border border-green-200 rounded-md p-3">
                                    <p class="text-sm text-green-800"><?= htmlspecialchars($message) ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="mt-8 text-center">
                            <a href="index.php" class="bg-blue-600 text-white px-8 py-4 rounded-md hover:bg-blue-700 transition-colors text-lg font-medium">
                                Go to Dashboard →
                            </a>
                        </div>

                    <?php else: ?>
                        <?php if (!empty($error_messages)): ?>
                            <div class="space-y-4 mb-6">
                                <?php foreach ($error_messages as $message): ?>
                                    <div class="bg-red-50 border border-red-200 rounded-md p-4">
                                        <div class="flex">
                                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                            </svg>
                                            <div class="ml-3">
                                                <p class="text-sm text-red-800"><?= htmlspecialchars($message) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-6">
                            <div class="flex">
                                <svg class="h-5 w-5 text-blue-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800">What will this setup do?</h3>
                                    <div class="mt-2 text-sm text-blue-700">
                                        <ul class="list-disc list-inside space-y-1">
                                            <li>Create the <strong>satria_hr_system</strong> database</li>
                                            <li>Create <strong>employees</strong> table with proper structure</li>
                                            <li>Create <strong>divisions</strong> table for reference data</li>
                                            <li>Insert default division options (IT, Finance, HR, etc.)</li>
                                            <li>Optionally add sample employee data for testing</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form method="POST" class="space-y-6">
                            <div class="bg-gray-50 rounded-md p-4">
                                <h3 class="font-medium text-gray-900 mb-3">Database Configuration</h3>
                                <div class="space-y-2 text-sm text-gray-600">
                                    <p><strong>Host:</strong> <?= htmlspecialchars($host) ?></p>
                                    <p><strong>Username:</strong> <?= htmlspecialchars($username) ?></p>
                                    <p><strong>Database:</strong> <?= htmlspecialchars($database) ?></p>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">
                                    Note: If these settings are incorrect, edit them in <code>setup.php</code>
                                </p>
                            </div>

                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <input type="checkbox" id="insert_sample" name="insert_sample" value="1" 
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="insert_sample" class="ml-2 block text-sm text-gray-900">
                                        Insert sample employee data (recommended for testing)
                                    </label>
                                </div>
                            </div>

                            <div class="flex justify-center">
                                <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-md hover:bg-blue-700 transition-colors text-lg font-medium">
                                    Initialize Database
                                </button>
                            </div>
                        </form>

                    <?php endif; ?>
                </div>
            </div>

            <div class="mt-6 text-center text-sm text-gray-500">
                <p>Satria HR System © 2024 - Employee Management System</p>
            </div>
        </div>
    </div>
</body>
</html>