<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Excel - Satria HR System</title>
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
                        <h1 class="text-xl font-semibold text-foreground">Import Excel Data</h1>
                    </div>
                </div>
                <a href="index.php" class="text-muted-foreground hover:text-foreground transition-colors">
                    Back to Dashboard
                </a>
            </div>
        </header>

        <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Upload Form -->
                <div class="bg-card shadow-sm rounded-xl border border-border">
                    <div class="px-6 py-4 border-b border-border">
                        <h2 class="text-lg font-medium text-foreground">Upload Excel/CSV File</h2>
                    </div>
                    
                    <form method="POST" enctype="multipart/form-data" class="p-6">
                        <div class="space-y-6">
                            <div>
                                <label for="excel_file" class="block text-sm font-medium text-foreground mb-2">
                                    Select Excel/CSV File <span class="text-red-500">*</span>
                                </label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-border border-dashed rounded-lg">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-muted-foreground" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-muted-foreground">
                                            <label for="excel_file" class="relative cursor-pointer bg-background rounded-md font-medium text-primary hover:text-primary/80 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary">
                                                <span>Upload file</span>
                                                <input id="excel_file" name="excel_file" type="file" class="sr-only" accept=".xls,.xlsx,.csv" required>
                                            </label>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-muted-foreground">Excel (.xlsx, .xls) or CSV up to 10MB</p>
                                    </div>
                                </div>
                                <div id="file-info" class="mt-2 text-sm text-muted-foreground hidden"></div>
                            </div>

                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex">
                                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-blue-800">Expected File Format</h3>
                                        <div class="mt-2 text-sm text-blue-700">
                                            <p>Required columns (in order):</p>
                                            <ol class="list-decimal list-inside mt-1">
                                                <li>Name</li>
                                                <li>Division</li>
                                                <li>Headcount Status</li>
                                                <li>Replacing (optional)</li>
                                                <li>Assignment Date (format: YYYY-MM-DD)</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end space-x-4">
                                <a href="index.php" class="px-4 py-2 text-sm font-medium text-foreground bg-background border border-border rounded-lg hover:bg-accent focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors">
                                    Cancel
                                </a>
                                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary border border-transparent rounded-lg hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors">
                                    Import Data
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Instructions -->
                <div class="bg-card shadow-sm rounded-xl border border-border">
                    <div class="px-6 py-4 border-b border-border">
                        <h2 class="text-lg font-medium text-foreground">Import Instructions</h2>
                    </div>
                    
                    <div class="p-6 space-y-4">
                        <div class="space-y-2">
                            <h3 class="font-medium text-foreground">1. File Format</h3>
                            <p class="text-sm text-muted-foreground">
                                Upload Excel (.xlsx, .xls) or CSV file with the correct column structure.
                            </p>
                        </div>

                        <div class="space-y-2">
                            <h3 class="font-medium text-foreground">2. Data Structure</h3>
                            <p class="text-sm text-muted-foreground">
                                Make sure the first row contains column headers, and data starts from the second row.
                            </p>
                        </div>

                        <div class="space-y-2">
                            <h3 class="font-medium text-foreground">3. Headcount Status</h3>
                            <p class="text-sm text-muted-foreground">
                                Valid values: "Replacement", "New Headcount", "New Request"
                            </p>
                        </div>

                        <div class="space-y-2">
                            <h3 class="font-medium text-foreground">4. Duplicate Data</h3>
                            <p class="text-sm text-muted-foreground">
                                If an employee name already exists, the data will be updated with the latest information.
                            </p>
                        </div>

                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex">
                                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-800">
                                        <strong>Warning:</strong> Make sure your data is correct before importing. 
                                        Backup old data if necessary.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sample Template -->
            <div class="mt-8 bg-card shadow-sm rounded-xl border border-border">
                <div class="px-6 py-4 border-b border-border">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-medium text-foreground">CSV Template</h2>
                        <a href="download_template_en.php" class="px-4 py-2 text-sm font-medium text-primary bg-primary/10 border border-primary/20 rounded-lg hover:bg-primary/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors">
                            Download Template
                        </a>
                    </div>
                </div>
                
                <div class="p-6">
                    <p class="text-sm text-muted-foreground mb-4">
                        Example CSV file format that can be used:
                    </p>
                    
                    <div class="bg-muted/50 rounded-lg p-4 text-sm font-mono">
                        Name,Division,Headcount Status,Replacing,Assignment Date<br>
                        John Doe,IT,New Headcount,,2024-01-15<br>
                        Jane Smith,Finance,Replacement,Mike Johnson,2024-01-20<br>
                        Ahmad Satria,HR,New Request,,2024-02-01
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Handle file selection
        document.getElementById('excel_file').addEventListener('change', function() {
            const fileInfo = document.getElementById('file-info');
            const file = this.files[0];
            
            if (file) {
                fileInfo.innerHTML = `
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        File selected: <strong>${file.name}</strong> (${(file.size/1024/1024).toFixed(2)} MB)
                    </div>
                `;
                fileInfo.classList.remove('hidden');
            } else {
                fileInfo.classList.add('hidden');
            }
        });
    </script>
</body>
</html>