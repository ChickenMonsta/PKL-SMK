$(document).ready(function() {
    // CSRF Token for forms
    const csrfToken = generateCSRFToken();

    // Jurusan selection handler
    $('#jurusan_id').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const kuota = selectedOption.data('kuota');
        
        if (kuota) {
            $('#info-kuota').html(`Kuota tersedia: <strong>${kuota} siswa</strong>`);
        } else {
            $('#info-kuota').html('');
        }
    });

    // Date validation
    $('#tanggal_mulai, #tanggal_selesai').on('change', function() {
        const mulai = new Date($('#tanggal_mulai').val());
        const selesai = new Date($('#tanggal_selesai').val());
        
        if (mulai && selesai) {
            if (selesai <= mulai) {
                $('#tanggal_selesai').val('');
                showAlert('error', 'Error', 'Tanggal selesai harus setelah tanggal mulai');
                $('#info-durasi').html('');
                return;
            }
            
            const diffTime = Math.abs(selesai - mulai);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            $('#info-durasi').html(`Durasi PKL: <strong>${diffDays} hari</strong>`);
        }
    });

    // Form validation
    $('#alasan_pkl').on('input', function() {
        const text = $(this).val();
        const charCount = text.length;
        
        if (charCount > 0 && charCount < 100) {
            $(this).addClass('border-yellow-500').removeClass('border-green-500');
        } else if (charCount >= 100) {
            $(this).addClass('border-green-500').removeClass('border-yellow-500');
        } else {
            $(this).removeClass('border-yellow-500 border-green-500');
        }
    });

    // File validation
    $('input[type="file"]').on('change', function() {
        const file = this.files[0];
        if (file) {
            const fileSize = file.size / 1024 / 1024; // in MB
            if (fileSize > 5) {
                showAlert('error', 'Error', 'Ukuran file maksimal 5MB');
                $(this).val('');
            }
        }
    });

    // Form submission
    $('#formPendaftaranPKL').on('submit', function(e) {
        e.preventDefault();
        
        // Basic validation
        const alasanPkl = $('#alasan_pkl').val();
        if (alasanPkl.length < 100) {
            showAlert('error', 'Error', 'Alasan PKL minimal 100 karakter');
            $('#alasan_pkl').focus();
            return;
        }

        const formData = new FormData(this);
        formData.append('csrf_token', csrfToken);
        
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        // Show loading state
        submitBtn.html('<div class="loading mx-auto"></div> Mengirim...');
        submitBtn.prop('disabled', true);
        
        $.ajax({
            url: '../../process_pendaftaran.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                const result = JSON.parse(response);
                if (result.status === 'success') {
                    showAlert('success', 'Berhasil', result.message)
                        .then(() => {
                            window.location.href = 'index.php?page=siswa&section=dashboard';
                        });
                } else {
                    showAlert('error', 'Gagal', result.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                showAlert('error', 'Error', 'Terjadi kesalahan saat mengirim formulir');
            },
            complete: function() {
                submitBtn.html(originalText);
                submitBtn.prop('disabled', false);
            }
        });
    });

    // Set minimum date for tanggal_selesai based on tanggal_mulai
    $('#tanggal_mulai').on('change', function() {
        const minDate = $(this).val();
        $('#tanggal_selesai').attr('min', minDate);
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