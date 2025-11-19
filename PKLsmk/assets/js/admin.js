// Admin specific JavaScript
document.addEventListener('DOMContentLoaded', function() {
    initializeAdminSidebar();
    initializeDataTables();
    initializeAdminForms();
});

function initializeAdminSidebar() {
    // Mobile sidebar toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    const adminSidebar = document.querySelector('.admin-sidebar');
    const adminMain = document.querySelector('.admin-main');
    
    if (sidebarToggle && adminSidebar) {
        sidebarToggle.addEventListener('click', function() {
            adminSidebar.classList.toggle('active');
        });
    }
    
    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(event) {
        if (window.innerWidth <= 768 && adminSidebar && adminSidebar.classList.contains('active')) {
            if (!adminSidebar.contains(event.target) && !sidebarToggle.contains(event.target)) {
                adminSidebar.classList.remove('active');
            }
        }
    });
}

function initializeDataTables() {
    // Initialize DataTables for admin tables
    const adminTables = document.querySelectorAll('.admin-table table');
    
    adminTables.forEach(table => {
        if (!$.fn.DataTable.isDataTable(table)) {
            $(table).DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
                },
                "responsive": true,
                "autoWidth": false,
                "pageLength": 25
            });
        }
    });
}

function initializeAdminForms() {
    // Enhanced form handling for admin
    document.addEventListener('submit', function(e) {
        const form = e.target;
        
        if (form.classList.contains('admin-form')) {
            e.preventDefault();
            handleAdminFormSubmit(form);
        }
    });
}

function handleAdminFormSubmit(form) {
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Show loading state
    submitBtn.innerHTML = '<div class="loading mx-auto"></div> Memproses...';
    submitBtn.disabled = true;
    
    // Simulate API call (replace with actual AJAX)
    setTimeout(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        
        showAlert('success', 'Berhasil', 'Data berhasil disimpan!');
    }, 1500);
}

// Admin specific utility functions
function confirmAction(message, callback) {
    showConfirm('Konfirmasi', message)
        .then((result) => {
            if (result.isConfirmed) {
                callback();
            }
        });
}

function handleDeleteAction(url, data, successMessage) {
    confirmAction('Apakah Anda yakin ingin menghapus data ini?', function() {
        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            success: function(response) {
                const result = JSON.parse(response);
                if (result.status === 'success') {
                    showAlert('success', 'Berhasil', successMessage || result.message)
                        .then(() => location.reload());
                } else {
                    showAlert('error', 'Gagal', result.message);
                }
            },
            error: function() {
                showAlert('error', 'Error', 'Terjadi kesalahan saat menghapus data');
            }
        });
    });
}