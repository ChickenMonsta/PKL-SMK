<div class="modal fade" id="tambahPerusahaanModal" tabindex="-1" aria-labelledby="tambahPerusahaanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tambahPerusahaanModalLabel">
                    <i class="fas fa-plus-circle text-yellow-600 mr-2"></i>
                    Tambah Perusahaan Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formTambahPerusahaan" method="POST">
                <div class="modal-body">
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label for="nama_perusahaan" class="form-label block text-sm font-semibold text-gray-700 mb-2">
                                Nama Perusahaan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" class="form-input" id="nama_perusahaan" name="nama_perusahaan" 
                                   placeholder="Masukkan nama perusahaan" required>
                        </div>
                        
                        <div>
                            <label for="alamat" class="form-label block text-sm font-semibold text-gray-700 mb-2">
                                Alamat Lengkap <span class="text-red-500">*</span>
                            </label>
                            <textarea class="form-input" id="alamat" name="alamat" rows="3" 
                                      placeholder="Masukkan alamat lengkap perusahaan" required></textarea>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="kontak" class="form-label block text-sm font-semibold text-gray-700 mb-2">
                                    Kontak <span class="text-red-500">*</span>
                                </label>
                                <input type="text" class="form-input" id="kontak" name="kontak" 
                                       placeholder="Telepon/Email" required>
                            </div>
                            
                            <div>
                                <label for="kuota" class="form-label block text-sm font-semibold text-gray-700 mb-2">
                                    Kuota PKL <span class="text-red-500">*</span>
                                </label>
                                <input type="number" class="form-input" id="kuota" name="kuota" 
                                       min="1" value="5" required>
                                <p class="text-xs text-gray-500 mt-1">Jumlah siswa yang dapat diterima</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-gray-50 px-6 py-4 border-t">
                    <button type="button" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold px-6 py-2 rounded-lg transition-colors" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold px-6 py-2 rounded-lg transition-colors">
                        <i class="fas fa-save mr-2"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>