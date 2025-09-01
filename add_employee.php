<?php
/**
 * Add Employee Form with Auto Redirect
 * File: add_employee.php
 */

require_once 'models/Employee.php';

$employee = new Employee();
$message = '';
$error = '';

// Handle form submission
if ($_POST) {
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
    
    // Validation
    if (empty($data['nama'])) {
        $error = 'Nama harus diisi';
    } elseif (empty($data['division'])) {
        $error = 'Divisi harus diisi';
    } elseif (empty($data['status_headcount'])) {
        $error = 'Status headcount harus dipilih';
    } elseif (empty($data['assign_month'])) {
        $error = 'Tanggal assignment harus diisi';
    } else {
        if ($employee->createEmployee($data)) {
            // Success - Redirect to index with success message
            $employeeName = htmlspecialchars($data['nama']);
            header('Location: index.php?success=1&message=' . urlencode("Karyawan {$employeeName} berhasil ditambahkan!"));
            exit;
        } else {
            $error = 'Terjadi kesalahan saat menambahkan karyawan';
        }
    }
}

$divisions = $employee->getDivisions();
$statusOptions = ['Replacement', 'New Headcount', 'New Request'];
?>

<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Karyawan - Satria HR System</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
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
        /* Success animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fadeIn {
            animation: fadeIn 0.3s ease-in-out;
        }
    </style>
</head>
<body class="bg-muted/40 font-sans text-foreground antialiased">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="h-16 flex items-center justify-between px-6 border-b border-border bg-card flex-shrink-0 sticky top-0 z-10">
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
                    <h1 class="text-xl font-semibold text-foreground">Tambah Karyawan</h1>
                </div>
            </div>
            <a href="index.php" class="text-muted-foreground hover:text-foreground transition-colors">
                Kembali ke Dashboard
            </a>
        </header>

        <main class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Error Message -->
            <?php if ($error): ?>
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6 animate-fadeIn">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <?= htmlspecialchars($error) ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <div class="bg-card shadow-sm rounded-xl border border-border">
                <div class="px-6 py-4 border-b border-border">
                    <h2 class="text-lg font-medium text-foreground">Data Karyawan Baru</h2>
                    <p class="text-sm text-muted-foreground mt-1">Lengkapi informasi karyawan yang akan ditambahkan</p>
                </div>
                
                <form method="POST" class="p-6 space-y-6" id="add-employee-form">
                    <div>
                        <label for="nama" class="block text-sm font-medium text-foreground mb-2">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="nama" 
                            name="nama" 
                            value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>"
                            class="w-full border border-border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent bg-background"
                            placeholder="Masukkan nama lengkap karyawan"
                            required
                        >
                    </div>

                    <div>
                        <label for="division" class="block text-sm font-medium text-foreground mb-2">
                            Divisi <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="division" 
                            name="division" 
                            class="w-full border border-border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-background"
                            required
                        >
                            <option value="">Pilih Divisi</option>
                            <?php foreach ($divisions as $div): ?>
                                <option value="<?= htmlspecialchars($div) ?>" <?= isset($_POST['division']) && $_POST['division'] === $div ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($div) ?>
                                </option>
                            <?php endforeach; ?>
                            <option value="other">Divisi Lainnya...</option>
                        </select>
                    </div>

                    <!-- Custom division input -->
                    <div id="custom-division" class="hidden">
                        <label for="custom_division" class="block text-sm font-medium text-foreground mb-2">
                            Nama Divisi Baru
                        </label>
                        <input 
                            type="text" 
                            id="custom_division" 
                            name="custom_division" 
                            class="w-full border border-border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent bg-background"
                            placeholder="Masukkan nama divisi baru"
                        >
                    </div>

                    <div>
                        <label for="status_headcount" class="block text-sm font-medium text-foreground mb-2">
                            Status Headcount <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="status_headcount" 
                            name="status_headcount" 
                            class="w-full border border-border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-background"
                            required
                        >
                            <option value="">Pilih Status</option>
                            <?php foreach ($statusOptions as $status): ?>
                                <option value="<?= $status ?>" <?= isset($_POST['status_headcount']) && $_POST['status_headcount'] === $status ? 'selected' : '' ?>>
                                    <?= $status ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div id="replace-person-field" class="hidden">
                        <label for="replace_person" class="block text-sm font-medium text-foreground mb-2">
                            Menggantikan
                        </label>
                        <input 
                            type="text" 
                            id="replace_person" 
                            name="replace_person" 
                            value="<?= htmlspecialchars($_POST['replace_person'] ?? '') ?>"
                            class="w-full border border-border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent bg-background"
                            placeholder="Nama orang yang digantikan"
                        >
                    </div>

                    <div>
                        <label for="assign_month" class="block text-sm font-medium text-foreground mb-2">
                            Tanggal Assignment <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="date" 
                            id="assign_month" 
                            name="assign_month" 
                            value="<?= $_POST['assign_month'] ?? date('Y-m-d') ?>"
                            class="w-full border border-border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent bg-background"
                            required
                        >
                    </div>

                    <div class="flex justify-end space-x-4 pt-6 border-t border-border">
                        <a href="index.php" class="px-4 py-2 text-sm font-medium border border-border rounded-lg hover:bg-accent transition-colors">
                            Batal
                        </a>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary border border-transparent rounded-lg hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Tambah Karyawan
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        // Handle division selection
        document.getElementById('division').addEventListener('change', function() {
            const customDiv = document.getElementById('custom-division');
            const customInput = document.getElementById('custom_division');
            
            if (this.value === 'other') {
                customDiv.classList.remove('hidden');
                customInput.required = true;
            } else {
                customDiv.classList.add('hidden');
                customInput.required = false;
                customInput.value = '';
            }
        });

        // Handle status selection to show/hide replace person field
        document.getElementById('status_headcount').addEventListener('change', function() {
            const replaceField = document.getElementById('replace-person-field');
            const replaceInput = document.getElementById('replace_person');
            
            if (this.value === 'Replacement') {
                replaceField.classList.remove('hidden');
                replaceInput.required = true;
            } else {
                replaceField.classList.add('hidden');
                replaceInput.required = false;
                replaceInput.value = '';
            }
        });

        // Form submission handler
        document.getElementById('add-employee-form').addEventListener('submit', function(e) {
            const divisionSelect = document.getElementById('division');
            const customInput = document.getElementById('custom_division');
            
            // Handle custom division
            if (divisionSelect.value === 'other' && customInput.value) {
                // Create a hidden input to submit the custom division
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'division';
                hiddenInput.value = customInput.value;
                this.appendChild(hiddenInput);
                
                // Remove the name from division select to avoid conflict
                divisionSelect.removeAttribute('name');
            }
        });

        // Initialize fields based on current values
        document.addEventListener('DOMContentLoaded', function() {
            // Check status on load
            const statusSelect = document.getElementById('status_headcount');
            if (statusSelect.value === 'Replacement') {
                document.getElementById('replace-person-field').classList.remove('hidden');
                document.getElementById('replace_person').required = true;
            }
            
            // Check division on load
            const divisionSelect = document.getElementById('division');
            if (divisionSelect.value === 'other') {
                document.getElementById('custom-division').classList.remove('hidden');
                document.getElementById('custom_division').required = true;
            }
        });

        // Auto-focus first input
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('nama').focus();
        });
    </script>
</body>
</html>