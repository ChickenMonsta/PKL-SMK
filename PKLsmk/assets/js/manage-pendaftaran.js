$(document).ready(function() {
    // Initialize DataTable
    const table = $('#tabelPendaftaran').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        },
        "responsive": true,
        "autoWidth": false,
        "pageLength": 25,
        "order": [[0, 'desc']],
        "columnDefs": [
            { "width": "20%", "targets": 0 }, // Siswa
            { "width": "10%", "targets": 1 }, // NIS
            { "width": "15%", "targets": 2 }, // Jurusan
            { "width": "15%", "targets": 3 }, // Perusahaan
            { "width": "15%", "targets": 4 }, // Periode
            { "width": "10%", "targets": 5 }, // Status
            { "width": "15%", "targets": 6 }  // Aksi
        ]
    });

    // CSRF Token for forms
    const csrfToken = generateCSRFToken();

    // Filter functionality
    $('#filterStatus').on('change', function() {
        const status = $(this).val();
        table.column(5).search(status).draw();
    });

    $('#filterJurusan').on('change', function() {
        const jurusan = $(this).val();
        table.column(2).search(jurusan).draw();
    });

    $('#resetFilter').on('click', function() {
        $('#filterStatus').val('');
        $('#filterJurusan').val('');
        table.columns().search('').draw();
    });

    // Handle detail pendaftaran button
    $(document).on('click', '.detail-pendaftaran-btn', function() {
        const id = $(this).data('id');
        showDetailPendaftaran(id);
    });

    // Handle terima pendaftaran
    $(document).on('click', '.terima-pendaftaran-btn', function() {
        const id = $(this).data('id');
        const nama = $(this).closest('tr').find('td:eq(0) .font-medium').text();
        
        showConfirm(
            'Terima Pendaftaran?',
            `Apakah Anda yakin ingin menerima pendaftaran dari "${nama}"?`,
            'Ya, Terima!'
        ).then((result) => {
            if (result.isConfirmed) {
                updateStatusPendaftaran(id, 'diterima');
            }
        });
    });

    // Handle tolak pendaftaran
    $(document).on('click', '.tolak-pendaftaran-btn', function() {
        const id = $(this).data('id');
        const nama = $(this).closest('tr').find('td:eq(0) .font-medium').text();
        
        showConfirm(
            'Tolak Pendaftaran?',
            `Apakah Anda yakin ingin menolak pendaftaran dari "${nama}"?`,
            'Ya, Tolak!'
        ).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Alasan Penolakan',
                    input: 'textarea',
                    inputLabel: 'Berikan alasan penolakan (opsional)',
                    inputPlaceholder: 'Masukkan alasan penolakan...',
                    showCancelButton: true,
                    confirmButtonText: 'Tolak Pendaftaran',
                    cancelButtonText: 'Batal',
                    inputValidator: (value) => {
                        // Optional field, no validation needed
                        return new Promise((resolve) => {
                            resolve();
                        });
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        updateStatusPendaftaran(id, 'ditolak', result.value);
                    }
                });
            }
        });
    });

    // Function to show detail pendaftaran
    function showDetailPendaftaran(id) {
        $('#detailPendaftaranContent').html(`
            <div class="text-center py-8">
                <div class="loading mx-auto mb-4"></div>
                <p class="text-gray-600">Memuat data pendaftaran...</p>
            </div>
        `);
        
        $('#detailPendaftaranModal').modal('show');

        $.ajax({
            url: '../../ajax/admin/pendaftaran/detail.php',
            type: 'GET',
            data: { id: id },
            success: function(response) {
                $('#detailPendaftaranContent').html(response);
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                $('#detailPendaftaranContent').html(`
                    <div class="text-center py-8">
                        <i class="fas fa-exclamation-triangle text-red-500 text-5xl mb-4"></i>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Terjadi Kesalahan</h3>
                        <p class="text-gray-600 mb-4">Gagal memuat data pendaftaran</p>
                    </div>
                `);
            }
        });
    }

    // Function to update status pendaftaran
    function updateStatusPendaftaran(id, status, catatan = '') {
        $.ajax({
            url: '../../ajax/admin/pendaftaran/update_status.php',
            type: 'POST',
            data: { 
                id: id,
                status: status,
                catatan: catatan,
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
                showAlert('error', 'Error', 'Terjadi kesalahan saat memperbarui status');
            }
        });
    }

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