<?php
require_once __DIR__ . '/../../bootstrap.php';
requireAdmin();

// Ambil data perusahaan
$stmt = $pdo->query("SELECT * FROM perusahaan ORDER BY created_at DESC");
$perusahaan = $stmt->fetchAll();
?>

<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Kelola Perusahaan</h1>
            <p class="text-gray-600">Kelola data perusahaan tempat PKL</p>
        </div>

        <!-- Add Company Button -->
        <div class="mb-6">
            <button onclick="openTambahModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg transition-colors flex items-center">
                <i class="fas fa-plus mr-2"></i>Tambah Perusahaan
            </button>
        </div>

        <!-- Companies Table -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table id="tabelPerusahaan" class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Perusahaan</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Alamat</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kontak</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kuota</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($perusahaan as $p): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900"><?= htmlspecialchars($p['nama_perusahaan']) ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-xs"><?= htmlspecialchars($p['alamat']) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?= htmlspecialchars($p['kontak']) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?= $p['kuota'] ?> siswa</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($p['status'] == 'active'): ?>
                                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">Aktif</span>
                                <?php else: ?>
                                    <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-medium">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button onclick="editPerusahaan(<?= $p['id'] ?>)" class="text-blue-600 hover:text-blue-900 transition-colors">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <?php if ($p['status'] == 'active'): ?>
                                        <button onclick="nonaktifkanPerusahaan(<?= $p['id'] ?>)" class="text-yellow-600 hover:text-yellow-900 transition-colors">
                                            <i class="fas fa-pause"></i> Nonaktifkan
                                        </button>
                                    <?php else: ?>
                                        <button onclick="aktifkanPerusahaan(<?= $p['id'] ?>)" class="text-green-600 hover:text-green-900 transition-colors">
                                            <i class="fas fa-play"></i> Aktifkan
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modals akan dimuat via AJAX -->
<script>
function openTambahModal() {
    fetch('ajax/admin/perusahaan/tambah.php?action=form')
        .then(response => response.text())
        .then(html => {
            document.body.insertAdjacentHTML('beforeend', html);
        });
}

function editPerusahaan(id) {
    fetch(`ajax/admin/perusahaan/edit.php?action=form&id=${id}`)
        .then(response => response.text())
        .then(html => {
            document.body.insertAdjacentHTML('beforeend', html);
        });
}

function aktifkanPerusahaan(id) {
    if (confirm('Aktifkan perusahaan ini?')) {
        fetch(`ajax/admin/perusahaan/aktifkan.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                location.reload();
            });
    }
}

function nonaktifkanPerusahaan(id) {
    if (confirm('Nonaktifkan perusahaan ini?')) {
        fetch(`ajax/admin/perusahaan/nonaktifkan.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                location.reload();
            });
    }
}

// Initialize DataTable
$(document).ready(function() {
    $('#tabelPerusahaan').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json'
        }
    });
});
</script>