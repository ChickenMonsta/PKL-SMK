$(document).ready(function() {
    // Initialize DataTable
    $('#tabelPerusahaan').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        },
        "responsive": true,
        "autoWidth": false,
        "pageLength": 25,
        "order": [[0, 'desc']]
    });

    // CSRF Token for forms
    const csrfToken = generateCSRFToken();

    // Handle form tambah perusahaan
    $('#formTambahPerusahaan').on('submit', function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize() + '&csrf_token=' + csrfToken;
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        // Show loading state
        submitBtn.html('<div class="loading mx-auto"></div> Menyimpan...');
        submitBtn.prop('disabled', true);
        
        $.ajax({
            url: '../../ajax/admin/perusahaan/tambah.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                const result = JSON.parse(response);
                if (result.status === 'success') {
                    $('#tambahPerusahaanModal').modal('hide');
                    $('#formTambahPerusahaan')[0].reset();
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

    // Handle edit button
    $(document).on('click', '.edit-btn', function() {
        const id = $(this).data('id');
        const nama = $(this).data('nama');
        const alamat = $(this).data('alamat');
        const kontak = $(this).data('kontak');
        const kuota = $(this).data('kuota');
        const status = $(this).data('status');
        
        $('#edit_id').val(id);
        $('#edit_nama_perusahaan').val(nama);
        $('#edit_alamat').val(alamat);
        $('#edit_kontak').val(kontak);
        $('#edit_kuota').val(kuota);
        $('#edit_status').val(status);
        
        $('#editPerusahaanModal').modal('show');
    });

    // Handle form edit perusahaan
    $('#formEditPerusahaan').on('submit', function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize() + '&csrf_token=' + csrfToken;
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        // Show loading state
        submitBtn.html('<div class="loading mx-auto"></div> Memperbarui...');
        submitBtn.prop('disabled', true);
        
        $.ajax({
            url: '../../ajax/admin/perusahaan/edit.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                const result = JSON.parse(response);
                if (result.status === 'success') {
                    $('#editPerusahaanModal').modal('hide');
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

    // Handle nonaktifkan perusahaan
    $(document).on('click', '.nonaktif-btn', function() {
        const id = $(this).data('id');
        const nama = $(this).closest('tr').find('td:eq(1)').text();
        
        showConfirm(
            'Nonaktifkan Perusahaan?',
            `Apakah Anda yakin ingin menonaktifkan perusahaan "${nama}"? Perusahaan ini tidak akan dapat dipilih untuk PKL.`,
            'Ya, Nonaktifkan!'
        ).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '../../ajax/admin/perusahaan/nonaktifkan.php',
                    type: 'POST',
                    data: { 
                        id: id,
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
                        showAlert('error', 'Error', 'Terjadi kesalahan saat menonaktifkan perusahaan');
                    }
                });
            }
        });
    });

    // Handle aktifkan perusahaan
    $(document).on('click', '.aktif-btn', function() {
        const id = $(this).data('id');
        const nama = $(this).closest('tr').find('td:eq(1)').text();
        
        showConfirm(
            'Aktifkan Perusahaan?',
            `Apakah Anda yakin ingin mengaktifkan perusahaan "${nama}"? Perusahaan ini akan dapat dipilih untuk PKL.`,
            'Ya, Aktifkan!'
        ).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '../../ajax/admin/perusahaan/aktifkan.php',
                    type: 'POST',
                    data: { 
                        id: id,
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
                        showAlert('error', 'Error', 'Terjadi kesalahan saat mengaktifkan perusahaan');
                    }
                });
            }
        });
    });

    // Modal event handlers
    $('#tambahPerusahaanModal').on('shown.bs.modal', function() {
        $('#nama_perusahaan').focus();
    });

    $('#editPerusahaanModal').on('shown.bs.modal', function() {
        $('#edit_nama_perusahaan').focus();
    });

    // Form validation: ensure kuota is not less than 1
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