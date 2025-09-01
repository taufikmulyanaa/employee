<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Satria HR System - Dashboard</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Chart.js for graphics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- DataTables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* Custom scrollbar styling */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 9999;
        }
        
        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
            animation: modalFadeIn 0.3s ease-out;
        }
        
        @keyframes modalFadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes modalSlideIn {
            from { 
                opacity: 0;
                transform: translateY(-20px) scale(0.9);
            }
            to { 
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        .modal-content {
            animation: modalSlideIn 0.3s ease-out;
            max-height: 90vh;
            overflow-y: auto;
        }

        /* DataTables custom styling */
        .dataTables_wrapper {
            font-family: 'Inter', sans-serif;
        }
        
        .dataTables_filter input {
            border: 1px solid #d1d5db !important;
            border-radius: 0.375rem !important;
            padding: 0.5rem 0.75rem !important;
            font-size: 0.875rem !important;
        }
        
        .dataTables_length select {
            border: 1px solid #d1d5db !important;
            border-radius: 0.375rem !important;
            padding: 0.25rem 0.5rem !important;
            font-size: 0.875rem !important;
        }
        
        .dataTables_info {
            color: #6b7280 !important;
            font-size: 0.875rem !important;
        }
        
        .dataTables_paginate .paginate_button {
            padding: 0.5rem 0.75rem !important;
            margin: 0 0.125rem !important;
            border-radius: 0.375rem !important;
            border: 1px solid #d1d5db !important;
            background: white !important;
            color: #374151 !important;
        }
        
        .dataTables_paginate .paginate_button:hover {
            background: #f9fafb !important;
            border-color: #d1d5db !important;
        }
        
        .dataTables_paginate .paginate_button.current {
            background: #3b82f6 !important;
            color: white !important;
            border-color: #3b82f6 !important;
        }
        
        .dt-buttons {
            display: none !important;
        }
        
        .dataTables_filter {
            display: none !important;
        }
        
        /* Export Dropdown Styles */
        .export-dropdown {
            position: relative;
            display: inline-block;
        }
        
        .export-dropdown-content {
            display: none;
            position: absolute;
            background-color: white;
            min-width: 200px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            z-index: 1000;
            top: 100%;
            right: 0;
            margin-top: 0.25rem;
        }
        
        .export-dropdown-content.show {
            display: block;
        }
        
        .export-option {
            padding: 0.75rem 1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: background-color 0.2s;
            font-size: 0.875rem;
            color: #374151;
        }
        
        .export-option:hover {
            background-color: #f9fafb;
        }
        
        .export-option:first-child {
            border-radius: 0.5rem 0.5rem 0 0;
        }
        
        .export-option:last-child {
            border-radius: 0 0 0.5rem 0.5rem;
        }
        
        .export-option svg {
            width: 1rem;
            height: 1rem;
        }
        
        .export-excel-icon { color: #059669; }
        .export-pdf-icon { color: #dc2626; }
        .export-print-icon { color: #7c3aed; }
        
        /* Action Buttons Styling */
        .action-btn {
            transition: all 0.2s ease-in-out;
            transform: scale(1);
        }
        
        .action-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .action-btn:active {
            transform: scale(0.95);
        }
        
        /* Success alert animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fadeIn {
            animation: fadeIn 0.3s ease-in-out;
        }
        
        @keyframes slideUp {
            from { opacity: 1; transform: translateY(0); }
            to { opacity: 0; transform: translateY(-10px); }
        }
        
        .animate-slideUp {
            animation: slideUp 0.3s ease-in-out forwards;
        }

        /* Loading spinner */
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .animate-spin {
            animation: spin 1s linear infinite;
        }
    </style>
    
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
            <div class="max-w-6xl mx-auto w-full flex items-center justify-between">
                <div class="flex items-center">
                    <div class="flex items-center gap-3">
                        <!-- Logo Icon -->
                        <svg width="24" height="24" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-primary">
                           <path fill-rule="evenodd" clip-rule="evenodd" d="M22.5 25C22.5 23.6193 23.6193 22.5 25 22.5H75C76.3807 22.5 77.5 23.6193 77.5 25V37.5H22.5V25ZM22.5 42.5H77.5V57.5H22.5V42.5ZM22.5 62.5H77.5V75C77.5 76.3807 76.3807 77.5 75 77.5H25C23.6193 77.5 22.5 76.3807 22.5 75V62.5Z" fill="currentColor"/>
                        </svg>
                        <h1 class="text-xl font-semibold text-foreground">Satria HR System</h1>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <button onclick="refreshData()" class="px-4 py-2 text-sm font-medium border border-border rounded-lg flex items-center gap-2 hover:bg-accent transition-colors" title="Refresh Data (Ctrl+R)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Refresh
                    </button>
                    
                    <!-- Export Dropdown -->
                    <div class="export-dropdown">
                        <button onclick="toggleExportDropdown()" class="px-4 py-2 text-sm font-medium bg-green-600 text-white rounded-lg flex items-center gap-2 hover:bg-green-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Export Data
                            <svg class="w-4 h-4 transition-transform duration-200" id="export-dropdown-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div class="export-dropdown-content" id="export-dropdown-menu">
                            <div class="export-option" onclick="exportData('xlsx')">
                                <svg class="export-excel-icon" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                                </svg>
                                <div>
                                    <div class="font-medium">Export Excel</div>
                                    <div class="text-xs text-gray-500">Real XLSX format</div>
                                </div>
                            </div>
                            <div class="export-option" onclick="exportData('csv')">
                                <svg class="text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                                    <circle cx="12" cy="14" r="2" fill="white"/>
                                </svg>
                                <div>
                                    <div class="font-medium">Export CSV</div>
                                    <div class="text-xs text-gray-500">Download as .csv file</div>
                                </div>
                            </div>
                            <div class="export-option" onclick="exportData('json')">
                                <svg class="text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M5,3H7V5H5V10A2,2 0 0,1 3,12A2,2 0 0,1 5,14V19H7V21H5C3.93,20.73 3,20.1 3,19V15A2,2 0 0,0 1,13H0V11H1A2,2 0 0,0 3,9V5C3,3.9 3.9,3 5,3M19,3A2,2 0 0,1 21,5V9A2,2 0 0,0 23,11H24V13H23A2,2 0 0,0 21,15V19A2,2 0 0,1 19,21H17V19H19V14A2,2 0 0,1 21,12A2,2 0 0,1 19,10V5H17V3H19Z"/>
                                </svg>
                                <div>
                                    <div class="font-medium">Export JSON</div>
                                    <div class="text-xs text-gray-500">Download as .json file</div>
                                </div>
                            </div>
                            <div class="export-option" onclick="exportData('pdf')">
                                <svg class="export-pdf-icon" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                                </svg>
                                <div>
                                    <div class="font-medium">Export PDF</div>
                                    <div class="text-xs text-gray-500">Download as PDF file</div>
                                </div>
                            </div>
                            <div class="export-option" onclick="printData()">
                                <svg class="export-print-icon" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M18,3H6V7H18M19,12A1,1 0 0,1 18,11A1,1 0 0,1 19,10A1,1 0 0,1 20,11A1,1 0 0,1 19,12M16,19H8V14H16M19,8H5A3,3 0 0,0 2,11V17H6V21H18V17H22V11A3,3 0 0,0 19,8Z"/>
                                </svg>
                                <div>
                                    <div class="font-medium">Print</div>
                                    <div class="text-xs text-gray-500">Print data directly</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <button onclick="openAddEmployeeModal()" class="px-4 py-2 text-sm font-medium bg-primary text-white rounded-lg flex items-center gap-2 hover:bg-primary/90 transition-colors" title="Add New Employee (Ctrl+N)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Add Employee
                    </button>
                    <a href="import.php" class="px-4 py-2 text-sm font-medium border border-border rounded-lg flex items-center gap-2 hover:bg-accent transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                        </svg>
                        Import Excel
                    </a>
                </div>
            </div>
        </header>

        <main class="max-w-6xl mx-auto p-6 space-y-6">
            <!-- Success Message -->
            <div id="success-notification" class="hidden bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg animate-fadeIn">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span id="success-message"></span>
                    </div>
                    <button onclick="closeSuccessAlert()" class="text-green-600 hover:text-green-800 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-card p-5 rounded-xl border border-border">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <p class="text-sm text-muted-foreground">Total Employees</p>
                            <p class="text-2xl font-bold mt-2" id="stat-total">0</p>
                        </div>
                        <div class="w-8 h-8 bg-blue-500/10 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-card p-5 rounded-xl border border-border">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <p class="text-sm text-muted-foreground">Replacement</p>
                            <p class="text-2xl font-bold mt-2 text-yellow-500" id="stat-replacement">0</p>
                        </div>
                        <div class="w-8 h-8 bg-yellow-500/10 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.752.932A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1z"/>
                                <path d="M5.707 14.707A1 1 0 015 13.586V10a1 1 0 112 0v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.752.932A5.002 5.002 0 0110.001 14H7a1 1 0 01-.293-.707z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-card p-5 rounded-xl border border-border">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <p class="text-sm text-muted-foreground">New Headcount</p>
                            <p class="text-2xl font-bold mt-2 text-green-500" id="stat-new-headcount">0</p>
                        </div>
                        <div class="w-8 h-8 bg-green-500/10 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-card p-5 rounded-xl border border-border">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <p class="text-sm text-muted-foreground">New Request</p>
                            <p class="text-2xl font-bold mt-2 text-purple-500" id="stat-new-request">0</p>
                        </div>
                        <div class="w-8 h-8 bg-purple-500/10 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Advanced Filters -->
            <div class="bg-card p-6 rounded-xl border border-border">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-foreground">Filter Data</h3>
                    <button onclick="resetFilters()" class="text-sm text-muted-foreground hover:text-foreground transition-colors">
                        Reset All Filters
                    </button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">Division</label>
                        <select id="filter-division" class="w-full border border-border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent bg-background">
                            <option value="">All Divisions</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">Status</label>
                        <select id="filter-status" class="w-full border border-border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent bg-background">
                            <option value="">All Status</option>
                            <option value="Replacement">Replacement</option>
                            <option value="New Headcount">New Headcount</option>
                            <option value="New Request">New Request</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">Assignment Month</label>
                        <input type="month" id="filter-month" class="w-full border border-border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent bg-background">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">Search</label>
                        <div class="relative">
                            <input type="text" id="datatable-search" placeholder="Search employees... (Ctrl+E)" class="w-full border border-border rounded-lg px-3 py-2 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent bg-background">
                            <svg class="absolute right-3 top-2.5 w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Employee DataTable -->
            <div class="bg-card rounded-xl border border-border overflow-hidden">
                <div class="px-6 py-4 border-b border-border">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-medium text-foreground">Employee Data</h3>
                            <p class="text-sm text-muted-foreground mt-1">Manage employee data easily</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <table id="employees-table" class="w-full table-auto">
                        <thead>
                            <tr>
                                <th class="text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">No</th>
                                <th class="text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Name</th>
                                <th class="text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Division</th>
                                <th class="text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Status</th>
                                <th class="text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Replacing</th>
                                <th class="text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Date</th>
                                <th class="text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Add Employee Modal -->
    <div id="add-employee-modal" class="modal">
        <div class="modal-content bg-white rounded-xl shadow-2xl max-w-2xl w-full mx-4">
            <div class="px-6 py-4 border-b border-gray-200 rounded-t-xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <h2 class="text-lg font-medium text-foreground">Add New Employee</h2>
                    </div>
                    <button onclick="closeModal('add-employee-modal')" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <p class="text-sm text-muted-foreground mt-1">Complete employee information to be added</p>
            </div>
            
            <form id="add-employee-form" class="p-6 space-y-6">
                <div>
                    <label for="add-nama" class="block text-sm font-medium text-foreground mb-2">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="add-nama" 
                        name="nama" 
                        class="w-full border border-border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent bg-background"
                        placeholder="Enter employee full name"
                        required
                    >
                </div>

                <div>
                    <label for="add-division" class="block text-sm font-medium text-foreground mb-2">
                        Division <span class="text-red-500">*</span>
                    </label>
                    <select 
                        id="add-division" 
                        name="division" 
                        class="w-full border border-border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-background"
                        required
                    >
                        <option value="">Select Division</option>
                        <option value="other">Other Division...</option>
                    </select>
                </div>

                <div id="add-custom-division" class="hidden">
                    <label for="add-custom-division-input" class="block text-sm font-medium text-foreground mb-2">
                        New Division Name
                    </label>
                    <input 
                        type="text" 
                        id="add-custom-division-input" 
                        name="custom_division" 
                        class="w-full border border-border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent bg-background"
                        placeholder="Enter new division name"
                    >
                </div>

                <div>
                    <label for="add-status" class="block text-sm font-medium text-foreground mb-2">
                        Headcount Status <span class="text-red-500">*</span>
                    </label>
                    <select 
                        id="add-status" 
                        name="status_headcount" 
                        class="w-full border border-border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-background"
                        required
                    >
                        <option value="">Select Status</option>
                        <option value="Replacement">Replacement</option>
                        <option value="New Headcount">New Headcount</option>
                        <option value="New Request">New Request</option>
                    </select>
                </div>

                <div id="add-replace-person-field" class="hidden">
                    <label for="add-replace-person" class="block text-sm font-medium text-foreground mb-2">
                        Replacing
                    </label>
                    <input 
                        type="text" 
                        id="add-replace-person" 
                        name="replace_person" 
                        class="w-full border border-border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent bg-background"
                        placeholder="Name of person being replaced"
                    >
                </div>

                <div>
                    <label for="add-assign-month" class="block text-sm font-medium text-foreground mb-2">
                        Assignment Date <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="date" 
                        id="add-assign-month" 
                        name="assign_month" 
                        class="w-full border border-border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent bg-background"
                        required
                    >
                </div>

                <div class="flex justify-end space-x-4 pt-6 border-t border-border">
                    <button type="button" onclick="closeModal('add-employee-modal')" class="px-4 py-2 text-sm font-medium border border-border rounded-lg hover:bg-accent transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary border border-transparent rounded-lg hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Add Employee
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Employee Modal -->
    <div id="edit-employee-modal" class="modal">
        <div class="modal-content bg-white rounded-xl shadow-2xl max-w-2xl w-full mx-4">
            <div class="px-6 py-4 border-b border-gray-200 rounded-t-xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <h2 class="text-lg font-medium text-foreground">Edit Employee</h2>
                    </div>
                    <button onclick="closeModal('edit-employee-modal')" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <p class="text-sm text-muted-foreground mt-1">Edit employee information</p>
            </div>
            
            <form id="edit-employee-form" class="p-6 space-y-6">
                <input type="hidden" id="edit-id" name="id">
                
                <div>
                    <label for="edit-nama" class="block text-sm font-medium text-foreground mb-2">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="edit-nama" 
                        name="nama" 
                        class="w-full border border-border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent bg-background"
                        placeholder="Enter employee full name"
                        required
                    >
                </div>

                <div>
                    <label for="edit-division" class="block text-sm font-medium text-foreground mb-2">
                        Division <span class="text-red-500">*</span>
                    </label>
                    <select 
                        id="edit-division" 
                        name="division" 
                        class="w-full border border-border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-background"
                        required
                    >
                        <option value="">Select Division</option>
                        <option value="other">Other Division...</option>
                    </select>
                </div>

                <div id="edit-custom-division" class="hidden">
                    <label for="edit-custom-division-input" class="block text-sm font-medium text-foreground mb-2">
                        New Division Name
                    </label>
                    <input 
                        type="text" 
                        id="edit-custom-division-input" 
                        name="custom_division" 
                        class="w-full border border-border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent bg-background"
                        placeholder="Enter new division name"
                    >
                </div>

                <div>
                    <label for="edit-status" class="block text-sm font-medium text-foreground mb-2">
                        Headcount Status <span class="text-red-500">*</span>
                    </label>
                    <select 
                        id="edit-status" 
                        name="status_headcount" 
                        class="w-full border border-border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-background"
                        required
                    >
                        <option value="">Select Status</option>
                        <option value="Replacement">Replacement</option>
                        <option value="New Headcount">New Headcount</option>
                        <option value="New Request">New Request</option>
                    </select>
                </div>

                <div id="edit-replace-person-field" class="hidden">
                    <label for="edit-replace-person" class="block text-sm font-medium text-foreground mb-2">
                        Replacing
                    </label>
                    <input 
                        type="text" 
                        id="edit-replace-person" 
                        name="replace_person" 
                        class="w-full border border-border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent bg-background"
                        placeholder="Name of person being replaced"
                    >
                </div>

                <div>
                    <label for="edit-assign-month" class="block text-sm font-medium text-foreground mb-2">
                        Assignment Date <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="date" 
                        id="edit-assign-month" 
                        name="assign_month" 
                        class="w-full border border-border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent bg-background"
                        required
                    >
                </div>

                <div class="flex justify-end space-x-4 pt-6 border-t border-border">
                    <button type="button" onclick="closeModal('edit-employee-modal')" class="px-4 py-2 text-sm font-medium border border-border rounded-lg hover:bg-accent transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary border border-transparent rounded-lg hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            Update Employee
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let employeesTable;
        let divisions = [];
        let statistics = {};
        
        $(document).ready(function() {
            // Load initial data
            loadStatistics();
            loadDivisions();
            
            // Initialize DataTable
            employeesTable = $('#employees-table').DataTable({
                processing: true,
                serverSide: false,
                searching: true,
                ajax: {
                    url: 'api/employees.php',
                    type: 'GET',
                    data: function(d) {
                        d.division = $('#filter-division').val();
                        d.status = $('#filter-status').val();
                        d.month = $('#filter-month').val();
                    },
                    error: function(xhr, error, code) {
                        console.error('DataTable AJAX Error:', error);
                        console.error('Status:', xhr.status);
                        console.error('Response:', xhr.responseText);
                        showErrorMessage('Failed to load data: ' + error);
                    },
                    dataSrc: function(json) {
                        console.log('API Response:', json);
                        if (json.success && json.data) {
                            return json.data.map((emp, index) => {
                                const statusColors = {
                                    'Replacement': 'bg-yellow-100 text-yellow-800',
                                    'New Headcount': 'bg-green-100 text-green-800',
                                    'New Request': 'bg-purple-100 text-purple-800'
                                };
                                const colorClass = statusColors[emp.status_headcount] || 'bg-gray-100 text-gray-800';
                                
                                return [
                                    index + 1,
                                    emp.nama,
                                    emp.division,
                                    `<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${colorClass}">${emp.status_headcount}</span>`,
                                    emp.replace_person || '-',
                                    new Date(emp.assign_month).toLocaleDateString('id-ID'),
                                    `<div class="flex items-center gap-2">
                                        <button onclick="openEditEmployeeModal(${emp.id})" 
                                                class="action-btn inline-flex items-center justify-center w-8 h-8 text-blue-600 hover:text-white hover:bg-blue-600 rounded-lg border border-blue-200 hover:border-blue-600 transition-all duration-200"
                                                title="Edit Karyawan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        <button onclick="deleteEmployee(${emp.id}, '${emp.nama}')" 
                                                class="action-btn inline-flex items-center justify-center w-8 h-8 text-red-600 hover:text-white hover:bg-red-600 rounded-lg border border-red-200 hover:border-red-600 transition-all duration-200"
                                                title="Hapus Karyawan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>`
                                ];
                            });
                        } else {
                            console.error('API Error:', json);
                            showErrorMessage(json.message || 'Failed to load data');
                            return [];
                        }
                    }
                },
                columns: [
                    { orderable: false, width: '60px' },
                    { name: 'nama' },
                    { name: 'division' },
                    { name: 'status', orderable: false, width: '120px' },
                    { name: 'replace_person', orderable: false },
                    { name: 'assign_month', width: '100px' },
                    { name: 'actions', orderable: false, width: '90px' }
                ],
                dom: 'Blrtip',
                buttons: [
                    {
                        extend: 'pdf',
                        title: 'Employee Data - Satria HR System',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5]
                        },
                        customize: function(doc) {
                            doc.defaultStyle.fontSize = 9;
                            doc.styles.tableHeader.fontSize = 10;
                            doc.styles.tableHeader.fillColor = '#f8f9fa';
                            doc.styles.title.fontSize = 16;
                            doc.styles.title.alignment = 'center';
                            doc.content[1].margin = [0, 10, 0, 10];
                            
                            doc.content.splice(1, 0, {
                                text: 'Printed on: ' + new Date().toLocaleDateString('en-US') + ' at ' + new Date().toLocaleTimeString('en-US'),
                                style: { fontSize: 10, alignment: 'center' },
                                margin: [0, 0, 0, 10]
                            });
                        }
                    }
                ],
                pageLength: 25,
                lengthMenu: [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
                language: {
                    processing: "Processing...",
                    search: "",
                    searchPlaceholder: "",
                    lengthMenu: "Show _MENU_ entries per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    infoFiltered: "(filtered from _MAX_ total entries)",
                    loadingRecords: "Loading...",
                    zeroRecords: "No matching records found",
                    emptyTable: "No data available",
                    paginate: {
                        first: "First",
                        previous: "Previous", 
                        next: "Next",
                        last: "Last"
                    }
                },
                createdRow: function(row, data, dataIndex) {
                    $(row).addClass('hover:bg-muted/50 transition-colors');
                    $(row).find('td').addClass('px-6 py-4 whitespace-nowrap text-sm');
                }
            });

            // Filter event handlers
            $('#filter-division, #filter-status, #filter-month').on('change', function() {
                employeesTable.ajax.reload();
            });

            // DataTable search
            $('#datatable-search').on('keyup', function() {
                employeesTable.search(this.value).draw();
            });

            // Form handlers
            setupFormHandlers();
            
            // Set default date
            const today = new Date().toISOString().split('T')[0];
            $('#add-assign-month').val(today);
        });

        // Load statistics
        function loadStatistics() {
            $.get('api/employees.php?stats=true')
                .done(function(response) {
                    console.log('Statistics response:', response);
                    if (response.success) {
                        statistics = response.data;
                        $('#stat-total').text(statistics.total || 0);
                        $('#stat-replacement').text(statistics.by_status?.Replacement || 0);
                        $('#stat-new-headcount').text(statistics.by_status?.['New Headcount'] || 0);
                        $('#stat-new-request').text(statistics.by_status?.['New Request'] || 0);
                    } else {
                        console.error('Statistics error:', response);
                    }
                })
                .fail(function(xhr, status, error) {
                    console.error('Statistics AJAX Error:', error);
                    console.error('Response:', xhr.responseText);
                });
        }

        // Load divisions
        function loadDivisions() {
            $.get('api/employees.php')
                .done(function(response) {
                    console.log('Divisions response:', response);
                    if (response.success && response.data) {
                        const uniqueDivisions = [...new Set(response.data.map(emp => emp.division))].sort();
                        divisions = uniqueDivisions;
                        
                        const filterSelect = $('#filter-division');
                        filterSelect.find('option:not(:first)').remove();
                        uniqueDivisions.forEach(div => {
                            filterSelect.append(`<option value="${div}">${div}</option>`);
                        });
                        
                        updateModalDivisions();
                    } else {
                        console.error('Divisions error:', response);
                    }
                })
                .fail(function(xhr, status, error) {
                    console.error('Divisions AJAX Error:', error);
                    console.error('Response:', xhr.responseText);
                });
        }

        // Update modal division dropdowns
        function updateModalDivisions() {
            const addSelect = $('#add-division');
            const editSelect = $('#edit-division');
            
            [addSelect, editSelect].forEach(select => {
                select.find('option:not(:first):not([value="other"])').remove();
                divisions.forEach(div => {
                    select.find('[value="other"]').before(`<option value="${div}">${div}</option>`);
                });
            });
        }

        // Setup form handlers
        function setupFormHandlers() {
            $('#add-division').on('change', function() {
                const customDiv = $('#add-custom-division');
                const customInput = $('#add-custom-division-input');
                
                if (this.value === 'other') {
                    customDiv.removeClass('hidden');
                    customInput.prop('required', true);
                } else {
                    customDiv.addClass('hidden');
                    customInput.prop('required', false);
                    customInput.val('');
                }
            });

            $('#add-status').on('change', function() {
                const replaceField = $('#add-replace-person-field');
                const replaceInput = $('#add-replace-person');
                
                if (this.value === 'Replacement') {
                    replaceField.removeClass('hidden');
                    replaceInput.prop('required', true);
                } else {
                    replaceField.addClass('hidden');
                    replaceInput.prop('required', false);
                    replaceInput.val('');
                }
            });

            $('#edit-division').on('change', function() {
                const customDiv = $('#edit-custom-division');
                const customInput = $('#edit-custom-division-input');
                
                if (this.value === 'other') {
                    customDiv.removeClass('hidden');
                    customInput.prop('required', true);
                } else {
                    customDiv.addClass('hidden');
                    customInput.prop('required', false);
                    customInput.val('');
                }
            });

            $('#edit-status').on('change', function() {
                const replaceField = $('#edit-replace-person-field');
                const replaceInput = $('#edit-replace-person');
                
                if (this.value === 'Replacement') {
                    replaceField.removeClass('hidden');
                    replaceInput.prop('required', true);
                } else {
                    replaceField.addClass('hidden');
                    replaceInput.prop('required', false);
                    replaceInput.val('');
                }
            });

            $('#add-employee-form').on('submit', function(e) {
                e.preventDefault();
                submitAddEmployee();
            });

            $('#edit-employee-form').on('submit', function(e) {
                e.preventDefault();
                submitEditEmployee();
            });
        }

        // Open add employee modal
        function openAddEmployeeModal() {
            $('#add-employee-form')[0].reset();
            $('#add-custom-division').addClass('hidden');
            $('#add-replace-person-field').addClass('hidden');
            
            const today = new Date().toISOString().split('T')[0];
            $('#add-assign-month').val(today);
            
            $('#add-employee-modal').addClass('show');
            
            setTimeout(() => {
                $('#add-nama').focus();
            }, 300);
        }

        // Open edit employee modal
        function openEditEmployeeModal(id) {
            $.get(`api/employees.php?id=${id}`)
                .done(function(response) {
                    console.log('Edit employee response:', response);
                    if (response.success && response.data) {
                        const emp = response.data;
                        
                        $('#edit-id').val(emp.id);
                        $('#edit-nama').val(emp.nama);
                        $('#edit-division').val(emp.division);
                        $('#edit-status').val(emp.status_headcount);
                        $('#edit-replace-person').val(emp.replace_person || '');
                        $('#edit-assign-month').val(emp.assign_month);
                        
                        if (emp.status_headcount === 'Replacement') {
                            $('#edit-replace-person-field').removeClass('hidden');
                            $('#edit-replace-person').prop('required', true);
                        }
                        
                        $('#edit-employee-modal').addClass('show');
                        
                        setTimeout(() => {
                            $('#edit-nama').focus();
                        }, 300);
                    } else {
                        showErrorMessage(response.message || 'Failed to load employee data');
                    }
                })
                .fail(function(xhr, status, error) {
                    console.error('Edit employee AJAX Error:', error);
                    console.error('Response:', xhr.responseText);
                    showErrorMessage('Failed to load employee data: ' + error);
                });
        }

        // Close modal
        function closeModal(modalId) {
            $('#' + modalId).removeClass('show');
        }

        // Submit add employee
        function submitAddEmployee() {
            const formData = new FormData($('#add-employee-form')[0]);
            
            if (formData.get('division') === 'other' && formData.get('custom_division')) {
                formData.set('division', formData.get('custom_division'));
            }
            
            $.ajax({
                url: 'api/employee_handler.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log('Add employee response:', response);
                    if (response.success) {
                        closeModal('add-employee-modal');
                        showSuccessMessage('Employee added successfully!');
                        refreshAllData();
                    } else {
                        showErrorMessage(response.message || 'Failed to add employee');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Add employee AJAX Error:', error);
                    console.error('Response:', xhr.responseText);
                    showErrorMessage('An error occurred while adding employee: ' + error);
                }
            });
        }

        // Submit edit employee
        function submitEditEmployee() {
            const formData = new FormData($('#edit-employee-form')[0]);
            formData.append('action', 'update');
            
            if (formData.get('division') === 'other' && formData.get('custom_division')) {
                formData.set('division', formData.get('custom_division'));
            }
            
            $.ajax({
                url: 'api/employee_handler.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log('Edit employee response:', response);
                    if (response.success) {
                        closeModal('edit-employee-modal');
                        showSuccessMessage('Employee data updated successfully!');
                        refreshAllData();
                    } else {
                        showErrorMessage(response.message || 'Failed to update employee');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Edit employee AJAX Error:', error);
                    console.error('Response:', xhr.responseText);
                    showErrorMessage('An error occurred while updating employee: ' + error);
                }
            });
        }

        // Delete employee
        function deleteEmployee(id, nama) {
            if (confirm(`Are you sure you want to delete employee "${nama}"? This action cannot be undone.`)) {
                $.ajax({
                    url: 'api/employee_handler.php',
                    type: 'POST',
                    data: {
                        action: 'delete',
                        id: id
                    },
                    success: function(response) {
                        console.log('Delete employee response:', response);
                        if (response.success) {
                            showSuccessMessage(`Employee "${nama}" deleted successfully`);
                            refreshAllData();
                        } else {
                            showErrorMessage(response.message || 'Failed to delete employee');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Delete employee AJAX Error:', error);
                        console.error('Response:', xhr.responseText);
                        showErrorMessage('An error occurred while deleting employee: ' + error);
                    }
                });
            }
        }

        // Show success message
        function showSuccessMessage(message) {
            $('#success-message').text(message);
            $('#success-notification').removeClass('hidden');
            
            setTimeout(() => {
                closeSuccessAlert();
            }, 5000);
        }

        // Show error message
        function showErrorMessage(message) {
            if ($('#error-notification').length === 0) {
                const errorHtml = `
                    <div id="error-notification" class="hidden bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg animate-fadeIn">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/>
                                </svg>
                                <span id="error-message"></span>
                            </div>
                            <button onclick="closeErrorAlert()" class="text-red-600 hover:text-red-800 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                `;
                $('#success-notification').after(errorHtml);
            }
            
            $('#error-message').text(message);
            $('#error-notification').removeClass('hidden');
            
            setTimeout(() => {
                closeErrorAlert();
            }, 7000);
        }

        // Close error alert
        function closeErrorAlert() {
            $('#error-notification').addClass('animate-slideUp');
            setTimeout(() => {
                $('#error-notification').addClass('hidden').removeClass('animate-slideUp');
            }, 300);
        }

        // Close success alert
        function closeSuccessAlert() {
            $('#success-notification').addClass('animate-slideUp');
            setTimeout(() => {
                $('#success-notification').addClass('hidden').removeClass('animate-slideUp');
            }, 300);
        }

        // Refresh all data
        function refreshAllData() {
            loadStatistics();
            loadDivisions();
            employeesTable.ajax.reload(null, false);
        }

        function resetFilters() {
            $('#filter-division').val('');
            $('#filter-status').val(''); 
            $('#filter-month').val('');
            $('#datatable-search').val('');
            employeesTable.search('').ajax.reload();
        }

        // ========== FIXED EXPORT FUNCTIONS ==========
        
        // Export functions
        function exportData(format) {
            document.getElementById('export-dropdown-menu').classList.remove('show');
            document.getElementById('export-dropdown-arrow').style.transform = 'rotate(0deg)';
            
            if (format === 'pdf') {
                // Use DataTables PDF export
                employeesTable.button(0).trigger();
            } else {
                // Build export URL with current filters
                const params = new URLSearchParams({
                    format: format,
                    division: $('#filter-division').val(),
                    status: $('#filter-status').val(),
                    month: $('#filter-month').val(),
                    search: $('#datatable-search').val()
                });
                
                // Call the proper export handler
                window.location.href = 'api/export_data.php?' + params.toString();
            }
        }

        function printData() {
            document.getElementById('export-dropdown-menu').classList.remove('show');
            document.getElementById('export-dropdown-arrow').style.transform = 'rotate(0deg)';
            
            const data = employeesTable.rows({ search: 'applied' }).data();
            
            const printWindow = window.open('', '_blank');
            const printContent = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Employee Data - Satria HR System</title>
                    <style>
                        body { 
                            font-family: Arial, sans-serif; 
                            margin: 20px; 
                            color: #333;
                        }
                        .header { 
                            text-align: center; 
                            margin-bottom: 30px; 
                            border-bottom: 2px solid #4472C4;
                            padding-bottom: 20px;
                        }
                        .header h1 { 
                            margin: 0; 
                            color: #4472C4; 
                            font-size: 24px;
                        }
                        .header p { 
                            margin: 5px 0; 
                            color: #666; 
                        }
                        .summary {
                            background: #f8f9fa;
                            padding: 15px;
                            border-radius: 5px;
                            margin-bottom: 20px;
                            text-align: center;
                        }
                        table { 
                            width: 100%; 
                            border-collapse: collapse; 
                            margin-top: 20px; 
                            font-size: 12px;
                        }
                        th, td { 
                            border: 1px solid #ddd; 
                            padding: 8px; 
                            text-align: left; 
                        }
                        th { 
                            background-color: #4472C4; 
                            color: white;
                            font-weight: bold; 
                            text-align: center;
                        }
                        tbody tr:nth-child(even) {
                            background-color: #f8f9fa;
                        }
                        tbody tr:hover {
                            background-color: #e3f2fd;
                        }
                        .status-badge {
                            padding: 2px 6px;
                            border-radius: 3px;
                            font-size: 10px;
                            font-weight: bold;
                        }
                        .status-replacement { background-color: #fff3cd; color: #856404; }
                        .status-new-headcount { background-color: #d4edda; color: #155724; }
                        .status-new-request { background-color: #e2e3e5; color: #383d41; }
                        @media print {
                            body { margin: 0; }
                            .no-print { display: none; }
                            table { font-size: 10px; }
                            th, td { padding: 6px; }
                        }
                        @page {
                            size: A4 landscape;
                            margin: 1cm;
                        }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h1>Employee Data Report</h1>
                        <p><strong>Satria HR System</strong></p>
                        <p>Generated on: ${new Date().toLocaleDateString('en-US', { 
                            weekday: 'long', 
                            year: 'numeric', 
                            month: 'long', 
                            day: 'numeric' 
                        })} at ${new Date().toLocaleTimeString('en-US')}</p>
                    </div>
                    
                    <div class="summary">
                        <strong>Total Employees: ${data.length}</strong>
                        ${$('#filter-division').val() ? ' | Division: ' + $('#filter-division').val() : ''}
                        ${$('#filter-status').val() ? ' | Status: ' + $('#filter-status').val() : ''}
                        ${$('#filter-month').val() ? ' | Month: ' + $('#filter-month').val() : ''}
                        ${$('#datatable-search').val() ? ' | Search: "' + $('#datatable-search').val() + '"' : ''}
                    </div>
                    
                    <table>
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="25%">Name</th>
                                <th width="20%">Division</th>
                                <th width="15%">Status</th>
                                <th width="20%">Replacing</th>
                                <th width="15%">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${Array.from(data).map((row, index) => {
                                let statusClass = 'status-badge ';
                                const status = row[3].replace(/<[^>]*>/g, '').trim();
                                if (status === 'Replacement') statusClass += 'status-replacement';
                                else if (status === 'New Headcount') statusClass += 'status-new-headcount';
                                else statusClass += 'status-new-request';
                                
                                return `
                                    <tr>
                                        <td style="text-align: center;">${index + 1}</td>
                                        <td><strong>${row[1]}</strong></td>
                                        <td>${row[2]}</td>
                                        <td><span class="${statusClass}">${status}</span></td>
                                        <td>${row[4] === '-' ? '<em>N/A</em>' : row[4]}</td>
                                        <td style="text-align: center;">${row[5]}</td>
                                    </tr>
                                `;
                            }).join('')}
                        </tbody>
                    </table>
                    
                    <div style="margin-top: 30px; text-align: center; color: #666; font-size: 11px;">
                        <p>This report was automatically generated by Satria HR System</p>
                        <p>For questions or support, please contact your system administrator</p>
                    </div>
                </body>
                </html>
            `;
            
            printWindow.document.write(printContent);
            printWindow.document.close();
            
            setTimeout(() => {
                printWindow.print();
                setTimeout(() => {
                    printWindow.close();
                }, 1000);
            }, 500);
        }

        function toggleExportDropdown() {
            const dropdown = document.getElementById('export-dropdown-menu');
            const arrow = document.getElementById('export-dropdown-arrow');
            
            if (dropdown.classList.contains('show')) {
                dropdown.classList.remove('show');
                arrow.style.transform = 'rotate(0deg)';
            } else {
                dropdown.classList.add('show');
                arrow.style.transform = 'rotate(180deg)';
            }
        }

        // Enhanced refresh function with better UX
        function refreshData() {
            const refreshBtn = event.target.closest('button');
            const icon = refreshBtn.querySelector('svg');
            const originalText = refreshBtn.innerHTML;
            
            // Show loading state
            refreshBtn.innerHTML = `
                <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <span class="ml-2">Refreshing...</span>
            `;
            refreshBtn.disabled = true;
            
            // Refresh data
            refreshAllData();
            
            // Reset button after delay
            setTimeout(() => {
                refreshBtn.innerHTML = originalText;
                refreshBtn.disabled = false;
                showSuccessMessage('Data refreshed successfully');
            }, 1000);
        }

        // Close dropdown when clicking outside (enhanced)
        document.addEventListener('click', function(event) {
            const dropdown = document.querySelector('.export-dropdown');
            const dropdownMenu = document.getElementById('export-dropdown-menu');
            const arrow = document.getElementById('export-dropdown-arrow');
            
            if (dropdown && !dropdown.contains(event.target)) {
                dropdownMenu.classList.remove('show');
                arrow.style.transform = 'rotate(0deg)';
            }
        });

        // Keyboard shortcuts for common actions
        document.addEventListener('keydown', function(event) {
            // Ctrl+N or Cmd+N: Add new employee
            if ((event.ctrlKey || event.metaKey) && event.key === 'n') {
                event.preventDefault();
                openAddEmployeeModal();
            }
            
            // Ctrl+R or Cmd+R: Refresh (prevent default browser refresh)
            if ((event.ctrlKey || event.metaKey) && event.key === 'r') {
                event.preventDefault();
                refreshData();
            }
            
            // Escape: Close modal
            if (event.key === 'Escape') {
                $('.modal.show').removeClass('show');
            }
            
            // Ctrl+E or Cmd+E: Focus search
            if ((event.ctrlKey || event.metaKey) && event.key === 'e') {
                event.preventDefault();
                $('#datatable-search').focus();
            }
        });

        // Close modal when clicking outside
        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.classList.remove('show');
            }
        });

        // Show welcome message on first load
        $(document).ready(function() {
            if (!localStorage.getItem('welcome_shown')) {
                setTimeout(() => {
                    showSuccessMessage('Welcome to Satria HR System! Use Ctrl+N to add new employee, Ctrl+R to refresh.');
                    localStorage.setItem('welcome_shown', 'true');
                }, 1000);
            }
        });
    </script>
</body>
</html>