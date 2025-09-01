<?php
/**
 * Error Page
 * File: error_page.php
 */

$error_message = $e->getMessage() ?? 'An unexpected error occurred';
?>

<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Error - Satria HR System</title>
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
<body class="bg-gray-50 font-sans antialiased">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h2 class="mt-6 text-3xl font-bold text-gray-900">System Error</h2>
                <p class="mt-2 text-sm text-gray-600">
                    We're experiencing some technical difficulties
                </p>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <div class="space-y-4">
                    <div class="bg-red-50 border border-red-200 rounded-md p-4">
                        <div class="flex">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Error Details</h3>
                                <p class="text-sm text-red-700 mt-1"><?= htmlspecialchars($error_message) ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                        <div class="flex">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Troubleshooting Steps</h3>
                                <ul class="text-sm text-blue-700 mt-1 space-y-1">
                                    <li>• Check if MySQL/MariaDB is running</li>
                                    <li>• Verify database configuration</li>
                                    <li>• Run the setup wizard if not completed</li>
                                    <li>• Contact system administrator</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="flex space-x-3">
                        <?php if (!file_exists('setup_completed.lock')): ?>
                            <a href="setup.php" class="flex-1 bg-blue-600 text-white text-center py-2 px-4 rounded-md text-sm font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Run Setup
                            </a>
                        <?php endif; ?>
                        
                        <a href="debug.php" class="flex-1 bg-gray-600 text-white text-center py-2 px-4 rounded-md text-sm font-medium hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Debug Mode
                        </a>
                        
                        <button onclick="window.location.reload()" class="flex-1 bg-green-600 text-white text-center py-2 px-4 rounded-md text-sm font-medium hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Retry
                        </button>
                    </div>
                </div>
            </div>

            <div class="text-center">
                <p class="text-sm text-gray-500">
                    Error ID: <?= date('YmdHis') . '-' . substr(md5($error_message), 0, 8) ?>
                </p>
                <p class="text-xs text-gray-400 mt-1">
                    Reference this ID when contacting support
                </p>
            </div>
        </div>
    </div>
</body>
</html>