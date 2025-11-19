$(document).ready(function() {
    // Initialize DataTable
    $('#tabelJurusan').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        },
        "responsive": true,
        "autoWidth": false,
        "pageLength": 25,
        "order": [[0, 'asc']],
        "columnDefs": [
            { "width": "10%", "targets": 0 }, // Kode
            { "width": "20%", "targets": 1 }, // Nama
            { "width": "30%", "targets": 2 }, // Deskripsi
            { "width": "10%", "targets": 3 }, // Kuota
            { "width": "10%", "targets": 4 }, // Status
            { "width": "20%", "targets": 5 }  // Aksi
        ]
    });

    // CSRF Token for forms
    const csrfToken = generateCSRFToken();

    // Handle form tambah jurusan
    $('#formTambahJurusan').on('submit', function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize() + '&csrf_token=' + csrfToken;
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        // Show loading state
        submitBtn.html('<div class="loading mx-auto"></div> Menyimpan...');
        submitBtn.prop('disabled', true);
        
        $.ajax({
            url: '../../ajax/admin/jurusan/tambah.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                const result = JSON.parse(response);
                if (result.status === 'success') {
                    $('#tambahJurusanModal').modal('hide');
                    $('#formTambahJurusan')[0].reset();
                    showAlert('success', 'Berhasil', result.message);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert('error', 'Gagal', result.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                showAlert('error', 'Error', 'Terjadi kesalahan saat mengirim data');
            },
            complete: function() {
                submitBtn.html(originalText);
                submitBtn.prop('disabled', false);
            }
        });
    });

    // Handle edit jurusan button
    $(document).on('click', '.edit-jurusan-btn', function() {
        const id = $(this).data('id');
        const kode = $(this).data('kode');
        const nama = $(this).data('nama');
        const deskripsi = $(this).data('deskripsi');
        const kuota = $(this).data('kuota');
        const status = $(this).data('status');
        
        $('#edit_id_jurusan').val(id);
        $('#edit_kode_jurusan').val(kode);
        $('#edit_nama_jurusan').val(nama);
        $('#edit_deskripsi').val(deskripsi);
        $('#edit_kuota').val(kuota);
        $('#edit_status_jurusan').val(status);
        
        $('#editJurusanModal').modal('show');
    });

    // Handle form edit jurusan
    $('#formEditJurusan').on('submit', function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize() + '&csrf_token=' + csrfToken;
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        // Show loading state
        submitBtn.html('<div class="loading mx-auto"></div> Memperbarui...');
        submitBtn.prop('disabled', true);
        
        $.ajax({
            url: '../../ajax/admin/jurusan/edit.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                const result = JSON.parse(response);
                if (result.status === 'success') {
                    $('#editJurusanModal').modal('hide');
                    showAlert('success', 'Berhasil', result.message);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert('error', 'Gagal', result.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                showAlert('error', 'Error', 'Terjadi kesalahan saat mengirim data');
            },
            complete: function() {
                submitBtn.html(originalText);
                submitBtn.prop('disabled', false);
            }
        });
    });

    // Handle nonaktifkan jurusan
    $(document).on('click', '.nonaktif-jurusan-btn', function() {
        const id = $(this).data('id');
        const nama = $(this).closest('tr').find('td:eq(1)').text();
        
        showConfirm(
            'Nonaktifkan Jurusan?',
            `Apakah Anda yakin ingin menonaktifkan jurusan "${nama}"? Jurusan ini tidak akan dapat dipilih untuk pendaftaran PKL.`,
            'Ya, Nonaktifkan!'
        ).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '../../ajax/admin/jurusan/hapus.php',
                    type: 'POST',
                    data: { 
                        id: id,
                        action: 'nonaktifkan',
                        csrf_token: csrfToken
                    },
                    success: function(response) {
                        const result = JSON.parse(response);
                        if (result.status === 'success') {
                            showAlert('success', 'Berhasil', result.message);
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            showAlert('error', 'Gagal', result.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        showAlert('error', 'Error', 'Terjadi kesalahan saat menonaktifkan jurusan');
                    }
                });
            }
        });
    });

    // Handle aktifkan jurusan
    $(document).on('click', '.aktif-jurusan-btn', function() {
        const id = $(this).data('id');
        const nama = $(this).closest('tr').find('td:eq(1)').text();
        
        showConfirm(
            'Aktifkan Jurusan?',
            `Apakah Anda yakin ingin mengaktifkan jurusan "${nama}"? Jurusan ini akan dapat dipilih untuk pendaftaran PKL.`,
            'Ya, Aktifkan!'
        ).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '../../ajax/admin/jurusan/hapus.php',
                    type: 'POST',
                    data: { 
                        id: id,
                        action: 'aktifkan',
                        csrf_token: csrfToken
                    },
                    success: function(response) {
                        const result = JSON.parse(response);
                        if (result.status === 'success') {
                            showAlert('success', 'Berhasil', result.message);
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            showAlert('error', 'Gagal', result.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        showAlert('error', 'Error', 'Terjadi kesalahan saat mengaktifkan jurusan');
                    }
                });
            }
        });
    });

    // Modal event handlers
    $('#tambahJurusanModal').on('shown.bs.modal', function() {
        $('#kode_jurusan').focus();
    });

    $('#editJurusanModal').on('shown.bs.modal', function() {
        $('#edit_kode_jurusan').focus();
    });

    // Auto-uppercase for kode jurusan
    $('#kode_jurusan, #edit_kode_jurusan').on('input', function() {
        $(this).val($(this).val().toUpperCase());
    });

    // Form validation
    $('input[type="number"]').on('input', function() {
        const value = parseInt($(this).val());
        if (value < 1) {
            $(this).val(1);
        }
    });

    // Generate CSRF Token
    function generateCSRFToken() {
        let token = $('meta[name="csrf-token"]').attr('content');
        if (!token) {
            token = Math.random().toString(36).substring(2) + Date.now().toString(36);
            $('head').append(`<meta name="csrf-token" content="${token}">`);
        }
        return token;
    }
});