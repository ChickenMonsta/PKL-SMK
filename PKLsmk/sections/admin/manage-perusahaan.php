<?php
require_once __DIR__ . '/../../bootstrap.php';
requireAdmin();

// Ambil data perusahaan
$stmt = $pdo->query("SELECT * FROM perusahaan ORDER BY created_at DESC");
$perusahaan = $stmt->fetchAll();
?>

<div class="min-h-screen bg-gray-50 py-6">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Kelola Perusahaan</h1>
                    <p class="text-gray-600">Kelola data perusahaan mitra PKL</p>
                </div>
                <button onclick="tambahPerusahaan()" 
                        class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold px-6 py-3 rounded-lg transition-colors flex items-center">
                    <i class="fas fa-plus mr-2"></i>Tambah Perusahaan
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 font-semibold">Total Perusahaan</p>
                        <div class="text-2xl font-bold text-gray-800 mt-2"><?= count($perusahaan) ?></div>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-building text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 font-semibold">Aktif</p>
                        <div class="text-2xl font-bold text-green-600 mt-2">
                            <?= count(array_filter($perusahaan, fn($p) => $p['status'] === 'active')) ?>
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 font-semibold">Nonaktif</p>
                        <div class="text-2xl font-bold text-red-600 mt-2">
                            <?= count(array_filter($perusahaan, fn($p) => $p['status'] === 'inactive')) ?>
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-times-circle text-red-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800">Data Perusahaan</h2>
            </div>
            
            <div class="p-6">
                <table id="tabelPerusahaan" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Perusahaan</th>
                            <th>Alamat</th>
                            <th>Telepon</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Tanggal Ditambahkan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($perusahaan as $index => $p): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td class="font-semibold text-gray-800"><?= htmlspecialchars($p['nama_perusahaan']) ?></td>
                            <td><?= htmlspecialchars($p['alamat']) ?></td>
                            <td><?= htmlspecialchars($p['telepon'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($p['email'] ?? '-') ?></td>
                            <td>
                                <?php if ($p['status'] === 'active'): ?>
                                    <span class="px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        Aktif
                                    </span>
                                <?php else: ?>
                                    <span class="px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                        Nonaktif
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td><?= formatDateTime($p['created_at']) ?></td>
                            <td>
                                <div class="flex space-x-2">
                                    <button onclick="editPerusahaan(<?= $p['id'] ?>)" 
                                            class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm transition-colors">
                                        <i class="fas fa-edit mr-1"></i>Edit
                                    </button>
                                    <?php if ($p['status'] === 'active'): ?>
                                        <button onclick="toggleStatus(<?= $p['id'] ?>, 'nonaktifkan')" 
                                                class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm transition-colors">
                                            <i class="fas fa-ban mr-1"></i>Nonaktifkan
                                        </button>
                                    <?php else: ?>
                                        <button onclick="toggleStatus(<?= $p['id'] ?>, 'aktifkan')" 
                                                class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm transition-colors">
                                            <i class="fas fa-check mr-1"></i>Aktifkan
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

<!-- Modal Tambah/Edit Perusahaan -->
<div id="modalPerusahaan" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 id="modalTitle" class="text-xl font-bold text-gray-800">Tambah Perusahaan</h3>
            <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="formPerusahaan">
            <input type="hidden" id="perusahaan_id" name="id">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Perusahaan</label>
                    <input type="text" id="nama_perusahaan" name="nama_perusahaan" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                    <textarea id="alamat" name="alamat" required rows="3"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-colors"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Telepon</label>
                    <input type="text" id="telepon" name="telepon"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" id="email" name="email"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-colors">
                </div>
            </div>
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="closeModal()" 
                        class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-semibold">
                    Batal
                </button>
                <button type="submit" 
                        class="px-6 py-3 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition-colors font-semibold">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Inisialisasi DataTable dengan destroy:true untuk menghindari reinitialization
$(document).ready(function() {
    // Destroy existing instance first
    if ($.fn.DataTable.isDataTable('#tabelPerusahaan')) {
        $('#tabelPerusahaan').DataTable().destroy();
    }
    
    // Initialize new DataTable
    $('#tabelPerusahaan').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        },
        "columnDefs": [
            { "orderable": false, "targets": [7] }, // Kolom aksi tidak bisa diurutkan
            { "searchable": false, "targets": [7] } // Kolom aksi tidak bisa dicari
        ],
        "order": [[6, "desc"]] // Urutkan berdasarkan tanggal dibuat terbaru
    });
});

function tambahPerusahaan() {
    document.getElementById('modalTitle').textContent = 'Tambah Perusahaan';
    document.getElementById('formPerusahaan').reset();
    document.getElementById('perusahaan_id').value = '';
    document.getElementById('modalPerusahaan').classList.remove('hidden');
}

function editPerusahaan(id) {
    // Ambil data perusahaan via AJAX
    $.ajax({
        url: 'ajax/perusahaan/edit.php',
        type: 'GET',
        data: { id: id },
        success: function(response) {
            const data = JSON.parse(response);
            if (data.success) {
                document.getElementById('modalTitle').textContent = 'Edit Perusahaan';
                document.getElementById('perusahaan_id').value = data.data.id;
                document.getElementById('nama_perusahaan').value = data.data.nama_perusahaan;
                document.getElementById('alamat').value = data.data.alamat;
                document.getElementById('telepon').value = data.data.telepon || '';
                document.getElementById('email').value = data.data.email || '';
                document.getElementById('modalPerusahaan').classList.remove('hidden');
            } else {
                alert('Error: ' + data.message);
            }
        }
    });
}

function closeModal() {
    document.getElementById('modalPerusahaan').classList.add('hidden');
}

function toggleStatus(id, action) {
    if (confirm(`Apakah Anda yakin ingin ${action} perusahaan ini?`)) {
        const url = action === 'aktifkan' ? 'ajax/perusahaan/aktifkan.php' : 'ajax/perusahaan/nonaktifkan.php';
        
        $.ajax({
            url: url,
            type: 'POST',
            data: { id: id },
            success: function(response) {
                const result = JSON.parse(response);
                if (result.success) {
                    alert(`Perusahaan berhasil di${action}`);
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            }
        });
    }
}

// Handle form submission
document.getElementById('formPerusahaan').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const url = formData.get('id') ? 'ajax/perusahaan/edit.php' : 'ajax/perusahaan/tambah.php';
    
    $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            const result = JSON.parse(response);
            if (result.success) {
                alert('Data perusahaan berhasil disimpan');
                closeModal();
                location.reload();
            } else {
                alert('Error: ' + result.message);
            }
        }
    });
});
</script>