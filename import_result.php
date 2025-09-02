<?php
/**
 * Import Results Page
 * File: import_result.php
 */

$status = $_GET['status'] ?? 'error';
$message = $_GET['message'] ?? 'Unknown error occurred';
$data = json_decode($_GET['data'] ?? '{}', true);

$isSuccess = $status === 'success';
$totalProcessed = $data['total_processed'] ?? 0;
$successfulImports = $data['successful_imports'] ?? 0;
$failedImports = $data['failed_imports'] ?? 0;
$errors = $data['errors'] ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Results - Satria HR System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        border: 'hsl(214.3, 31.8%, 91.4%)',
                        input: 'hsl(214.3, 31.8%, 91.4%)',
                        ring: 'hsl(222.2, 84%, 4.9%)',
                        background: 'hsl(0, 0%, 100%)',
                        foreground: 'hsl(222.2, 84%, 4.9%)',
                        primary: {
                            DEFAULT: 'hsl(221.2, 83.2%, 53.3%)',
                            foreground: 'hsl(210, 40%, 98%)',
                        },
                        secondary: {
                            DEFAULT: 'hsl(210, 40%, 96.1%)',
                            foreground: 'hsl(222.2, 47.4%, 11.2%)',
                        },
                        muted: {
                            DEFAULT: 'hsl(210, 40%, 96.1%)',
                            foreground: 'hsl(215.4, 16.3%, 46.9%)',
                        },
                        accent: {
                            DEFAULT: 'hsl(210, 40%, 96.1%)',
                            foreground: 'hsl(222.2, 47.4%, 11.2%)',
                        },
                        card: {
                            DEFAULT: 'hsl(0, 0%, 100%)',
                            foreground: 'hsl(222.2, 84%, 4.9%)',
                        },
                    },
                    borderRadius: {
                        lg: `0.5rem`,
                        md: `calc(0.5rem - 2px)`,
                        sm: `calc(0.5rem - 4px)`,
                    },
                }
            }
        }
    </script>
</head>
<body class="bg-muted/40 font-sans text-foreground antialiased">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="h-16 flex items-center justify-between px-6 border-b border-border bg-card flex-shrink-0 sticky top-0 z-10">
            <div class="max-w-7xl mx-auto w-full flex items-center justify-between">
                <div class="flex items-center">
                    <a href="index.php" class="text-primary hover:text-primary/80 mr-4 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <div class="flex items-center gap-3">
                        <!-- Logo Icon -->
                        <svg width="24" height="24" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-primary">
                           <path fill-rule="evenodd" clip-rule="evenodd" d="M22.5 25C22.5 23.6193 23.6193 22.5 25 22.5H75C76.3807 22.5 77.5 23.6193 77.5 25V37.5H22.5V25ZM22.5 42.5H77.5V57.5H22.5V42.5ZM22.5 62.5H77.5V75C77.5 76.3807 76.3807 77.5 75 77.5H25C23.6193 77.5 22.5 76.3807 22.5 75V62.5Z" fill="currentColor"/>
                        </svg>
                        <h1 class="text-xl font-semibold text-foreground">Import Results</h1>
                    </div>
                </div>
                <a href="index.php" class="text-muted-foreground hover:text-foreground transition-colors">
                    Back to Dashboard
                </a>
            </div>
        </header>

        <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Result Summary -->
            <div class="mb-8">
                <?php if ($isSuccess): ?>
                    <div class="bg-green-50 border border-green-200 rounded-xl p-6">
                        <div class="flex items-center">
                            <svg class="w-8 h-8 text-green-500 mr-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <h2 class="text-xl font-semibold text-green-800">Import Successful!</h2>
                                <p class="text-green-700 mt-1"><?= htmlspecialchars($message) ?></p>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="bg-red-50 border border-red-200 rounded-xl p-6">
                        <div class="flex items-center">
                            <svg class="w-8 h-8 text-red-500 mr-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <h2 class="text-xl font-semibold text-red-800">Import Failed</h2>
                                <p class="text-red-700 mt-1"><?= htmlspecialchars($message) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($totalProcessed > 0): ?>
            <!-- Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-card p-6 rounded-xl border border-border">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">Total Processed</p>
                            <p class="text-2xl font-bold text-foreground"><?= $totalProcessed ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-card p-6 rounded-xl border border-border">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">Successfully Imported</p>
                            <p class="text-2xl font-bold text-green-600"><?= $successfulImports ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-card p-6 rounded-xl border border-border">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">Failed</p>
                            <p class="text-2xl font-bold text-red-600"><?= $failedImports ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Error Details -->
            <?php if (!empty($errors)): ?>
            <div class="bg-card rounded-xl border border-border mb-8">
                <div class="px-6 py-4 border-b border-border">
                    <h3 class="text-lg font-medium text-foreground">Import Errors (<?= count($errors) ?>)</h3>
                    <p class="text-sm text-muted-foreground mt-1">The following rows could not be imported</p>
                </div>
                
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-border">
                                    <th class="text-left py-3 px-4 font-medium text-muted-foreground">Row</th>
                                    <th class="text-left py-3 px-4 font-medium text-muted-foreground">Name</th>
                                    <th class="text-left py-3 px-4 font-medium text-muted-foreground">Error</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($errors as $error): ?>
                                <tr class="border-b border-border">
                                    <td class="py-3 px-4 text-sm"><?= htmlspecialchars($error['row']) ?></td>
                                    <td class="py-3 px-4 text-sm font-medium"><?= htmlspecialchars($error['name']) ?></td>
                                    <td class="py-3 px-4 text-sm text-red-600"><?= htmlspecialchars($error['error']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="flex justify-center space-x-4">
                <a href="index.php" class="px-6 py-3 text-sm font-medium text-white bg-primary border border-transparent rounded-lg hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h0a2 2 0 012 2v0a2 2 0 01-2 2H10a2 2 0 01-2-2v0z"/>
                        </svg>
                        View Dashboard
                    </span>
                </a>
                
                <a href="import.php" class="px-6 py-3 text-sm font-medium text-foreground bg-background border border-border rounded-lg hover:bg-accent focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                        </svg>
                        Import More Data
                    </span>
                </a>
            </div>

            <!-- Tips -->
            <?php if (!empty($errors)): ?>
            <div class="mt-8 bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                <div class="flex">
                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Tips to Fix Import Errors</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Make sure all required fields are filled</li>
                                <li>Use valid Headcount Status: "Replacement", "New Headcount", or "New Request"</li>
                                <li>Date format should be YYYY-MM-DD (e.g., 2024-01-15)</li>
                                <li>For "Replacement" status, the "Replacing" field is required</li>
                                <li>Check for empty rows in your file</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>