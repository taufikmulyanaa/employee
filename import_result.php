<?php
/**
 * Import Results Page - Improved Version
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

// Calculate success rate
$successRate = $totalProcessed > 0 ? round(($successfulImports / $totalProcessed) * 100, 1) : 0;
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
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fadeIn {
            animation: fadeIn 0.6s ease-out;
        }
        
        .success-pulse {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
    </style>
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

        <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 animate-fadeIn">
            
            <!-- Main Result Card -->
            <div class="mb-8">
                <?php if ($isSuccess): ?>
                    <div class="bg-green-50 border-2 border-green-200 rounded-xl p-8 text-center success-pulse">
                        <div class="mx-auto w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-green-800 mb-2">Import Successful!</h2>
                        <p class="text-green-700 text-lg"><?= htmlspecialchars($message) ?></p>
                        <?php if ($successRate < 100 && $failedImports > 0): ?>
                            <p class="text-yellow-600 text-sm mt-2">
                                <strong>Note:</strong> Some rows had issues. See details below.
                            </p>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="bg-red-50 border-2 border-red-200 rounded-xl p-8 text-center">
                        <div class="mx-auto w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-red-800 mb-2">Import Failed</h2>
                        <p class="text-red-700 text-lg"><?= htmlspecialchars($message) ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($totalProcessed > 0): ?>
            <!-- Statistics Dashboard -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Processed -->
                <div class="bg-card p-6 rounded-xl border border-border">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">Total Rows</p>
                            <p class="text-3xl font-bold text-foreground"><?= $totalProcessed ?></p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Successful Imports -->
                <div class="bg-card p-6 rounded-xl border border-border">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">Successfully Imported</p>
                            <p class="text-3xl font-bold text-green-600"><?= $successfulImports ?></p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Failed Imports -->
                <div class="bg-card p-6 rounded-xl border border-border">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">Failed</p>
                            <p class="text-3xl font-bold text-red-600"><?= $failedImports ?></p>
                        </div>
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Success Rate -->
                <div class="bg-card p-6 rounded-xl border border-border">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">Success Rate</p>
                            <p class="text-3xl font-bold <?= $successRate >= 90 ? 'text-green-600' : ($successRate >= 70 ? 'text-yellow-600' : 'text-red-600') ?>">
                                <?= $successRate ?>%
                            </p>
                        </div>
                        <div class="w-12 h-12 <?= $successRate >= 90 ? 'bg-green-100' : ($successRate >= 70 ? 'bg-yellow-100' : 'bg-red-100') ?> rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 <?= $successRate >= 90 ? 'text-green-600' : ($successRate >= 70 ? 'text-yellow-600' : 'text-red-600') ?>" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm0 4a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1V8zm8 0a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1h-4a1 1 0 01-1-1V8z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="bg-card rounded-xl border border-border p-6 mb-8">
                <h3 class="text-lg font-medium text-foreground mb-4">Import Progress</h3>
                <div class="w-full bg-muted rounded-full h-4 mb-4">
                    <div class="bg-green-500 h-4 rounded-full transition-all duration-1000 ease-out" style="width: <?= $successRate ?>%"></div>
                </div>
                <div class="flex justify-between text-sm text-muted-foreground">
                    <span><?= $successfulImports ?> successful</span>
                    <span><?= $failedImports ?> failed</span>
                </div>
            </div>
            <?php endif; ?>

            <!-- Error Details -->
            <?php if (!empty($errors)): ?>
            <div class="bg-card rounded-xl border border-border mb-8">
                <div class="px-6 py-4 border-b border-border">
                    <h3 class="text-lg font-medium text-foreground flex items-center">
                        <svg class="w-5 h-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/>
                        </svg>
                        Import Errors (<?= count($errors) ?>)
                    </h3>
                    <p class="text-sm text-muted-foreground mt-1">The following rows could not be imported</p>
                </div>
                
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-border">
                                    <th class="text-left py-3 px-4 font-medium text-muted-foreground">Row #</th>
                                    <th class="text-left py-3 px-4 font-medium text-muted-foreground">Name</th>
                                    <th class="text-left py-3 px-4 font-medium text-muted-foreground">Error Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($errors as $index => $error): ?>
                                <tr class="border-b border-border <?= $index % 2 === 0 ? 'bg-muted/30' : '' ?>">
                                    <td class="py-3 px-4 text-sm font-mono"><?= htmlspecialchars($error['row']) ?></td>
                                    <td class="py-3 px-4 text-sm font-medium"><?= htmlspecialchars($error['name']) ?></td>
                                    <td class="py-3 px-4 text-sm text-red-600"><?= htmlspecialchars($error['error']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Export Errors Button -->
                    <?php if (count($errors) > 10): ?>
                    <div class="mt-4 text-center">
                        <button onclick="exportErrors()" class="px-4 py-2 text-sm font-medium text-primary bg-primary/10 border border-primary/20 rounded-lg hover:bg-primary/20 transition-colors">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Export Error Report
                            </span>
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row justify-center items-center space-y-4 sm:space-y-0 sm:space-x-6 mb-8">
                <a href="index.php" class="w-full sm:w-auto px-8 py-4 text-sm font-medium text-white bg-primary border border-transparent rounded-lg hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors">
                    <span class="flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h0a2 2 0 012 2v0a2 2 0 01-2 2H10a2 2 0 01-2-2v0z"/>
                        </svg>
                        View Dashboard
                    </span>
                </a>
                
                <a href="import.php" class="w-full sm:w-auto px-8 py-4 text-sm font-medium text-foreground bg-background border border-border rounded-lg hover:bg-accent focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors">
                    <span class="flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                        </svg>
                        Import More Data
                    </span>
                </a>
            </div>

            <!-- Troubleshooting Tips -->
            <?php if (!empty($errors) || !$isSuccess): ?>
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                <div class="flex">
                    <svg class="h-5 w-5 text-yellow-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Troubleshooting Tips</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Ensure all required fields are filled (Name, Division, Headcount Status, Assignment Date)</li>
                                <li>Use exactly these values for Headcount Status: <code class="bg-yellow-100 px-1 rounded">Replacement</code>, <code class="bg-yellow-100 px-1 rounded">New Headcount</code>, or <code class="bg-yellow-100 px-1 rounded">New Request</code></li>
                                <li>Date format should be <code class="bg-yellow-100 px-1 rounded">YYYY-MM-DD</code> (e.g., <code class="bg-yellow-100 px-1 rounded">2024-01-15</code>)</li>
                                <li>For "Replacement" status, the "Replacing" field must be filled</li>
                                <li>Remove any empty rows at the end of your file</li>
                                <li>Ensure column headers match exactly: <code class="bg-yellow-100 px-1 rounded">Name,Division,Headcount Status,Replacing,Assignment Date</code></li>
                                <li>Save Excel files as CSV format for best compatibility</li>
                            </ul>
                        </div>
                        <div class="mt-4">
                            <a href="download_template.php" class="text-yellow-800 underline hover:text-yellow-900">
                                Download the template file for reference →
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </main>
    </div>

    <script>
        // Export errors to CSV
        function exportErrors() {
            const errors = <?= json_encode($errors) ?>;
            const csvContent = "Row,Name,Error\n" + 
                errors.map(error => `${error.row},"${error.name}","${error.error}"`).join("\n");
            
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement("a");
            
            if (link.download !== undefined) {
                const url = URL.createObjectURL(blob);
                link.setAttribute("href", url);
                link.setAttribute("download", "import_errors_" + new Date().toISOString().slice(0,10) + ".csv");
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        }

        // Auto-redirect to dashboard if import was 100% successful
        <?php if ($isSuccess && $successRate == 100 && $successfulImports > 0): ?>
        setTimeout(() => {
            if (confirm('Import completed successfully! Would you like to go to the dashboard to view the imported data?')) {
                window.location.href = 'index.php';
            }
        }, 3000);
        <?php endif; ?>

        // Animate progress bar on load
        document.addEventListener('DOMContentLoaded', function() {
            const progressBar = document.querySelector('.bg-green-500');
            if (progressBar) {
                progressBar.style.width = '0%';
                setTimeout(() => {
                    progressBar.style.width = '<?= $successRate ?>%';
                }, 500);
            }
        });
    </script>
</body>
</html>