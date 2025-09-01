<?php
/**
 * Delete Employee
 * File: delete_employee.php
 */

require_once 'models/Employee.php';

$employee = new Employee();

// Get employee ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) {
    header('Location: index.php?error=ID tidak valid');
    exit;
}

// Get employee data to show confirmation
$emp = $employee->getEmployeeById($id);
if (!$emp) {
    header('Location: index.php?error=Karyawan tidak ditemukan');
    exit;
}

$message = '';
$error = '';

// Handle deletion
if ($_POST && isset($_POST['confirm_delete'])) {
    if ($employee->deleteEmployee($id)) {
        header('Location: index.php?message=Karyawan berhasil dihapus');
        exit;
    } else {
        $error = 'Terjadi kesalahan saat menghapus karyawan';
    }
}
?>

<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hapus Karyawan - Satria HR System</title>
    
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
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 font-sans antialiased">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                <h2 class="mt-6 text-2xl font-bold text-gray-900">Konfirmasi Hapus</h2>
                <p class="mt-2 text-sm text-gray-600">
                    Yakin ingin menghapus data karyawan ini?
                </p>
            </div>

            <!-- Error Message -->
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <!-- Employee Details -->
            <div class="bg-white shadow-sm rounded-lg border p-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Nama</label>
                        <p class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($emp['nama']) ?></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Divisi</label>
                        <p class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($emp['division']) ?></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Status</label>
                        <p class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($emp['status_headcount']) ?></p>
                    </div>
                    <?php if ($emp['replace_person']): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Menggantikan</label>
                        <p class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($emp['replace_person']) ?></p>
                    </div>
                    <?php endif; ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Tanggal Assignment</label>
                        <p class="mt-1 text-sm text-gray-900"><?= date('d/m/Y', strtotime($emp['assign_month'])) ?></p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="space-y-4">
                <form method="POST" class="space-y-4">
                    <div class="flex items-center">
                        <input type="checkbox" id="confirm_delete" name="confirm_delete" value="1" class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded" required>
                        <label for="confirm_delete" class="ml-2 block text-sm text-gray-900">
                            Ya, saya yakin ingin menghapus data karyawan ini secara permanen
                        </label>
                    </div>
                    
                    <div class="flex space-x-4">
                        <button type="submit" class="flex-1 bg-red-600 text-white py-2 px-4 rounded-md text-sm font-medium hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed" id="delete-btn" disabled>
                            Hapus Karyawan
                        </button>
                        <a href="index.php" class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-md text-sm font-medium text-center hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Batal
                        </a>
                    </div>
                </form>
            </div>

            <!-- Back to Dashboard -->
            <div class="text-center">
                <a href="index.php" class="text-blue-600 hover:text-blue-500 text-sm">
                    ← Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>

    <script>
        // Enable/disable delete button based on confirmation checkbox
        document.getElementById('confirm_delete').addEventListener('change', function() {
            const deleteBtn = document.getElementById('delete-btn');
            deleteBtn.disabled = !this.checked;
        });
    </script>
</body>
</html>