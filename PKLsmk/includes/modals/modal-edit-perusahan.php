<div class="modal fade" id="editPerusahaanModal" tabindex="-1" aria-labelledby="editPerusahaanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPerusahaanModalLabel">
                    <i class="fas fa-edit text-yellow-600 mr-2"></i>
                    Edit Data Perusahaan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditPerusahaan" method="POST">
                <input type="hidden" id="edit_id" name="id">
                <div class="modal-body">
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label for="edit_nama_perusahaan" class="form-label block text-sm font-semibold text-gray-700 mb-2">
                                Nama Perusahaan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" class="form-input" id="edit_nama_perusahaan" name="nama_perusahaan" required>
                        </div>
                        
                        <div>
                            <label for="edit_alamat" class="form-label block text-sm font-semibold text-gray-700 mb-2">
                                Alamat Lengkap <span class="text-red-500">*</span>
                            </label>
                            <textarea class="form-input" id="edit_alamat" name="alamat" rows="3" required></textarea>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="edit_kontak" class="form-label block text-sm font-semibold text-gray-700 mb-2">
                                    Kontak <span class="text-red-500">*</span>
                                </label>
                                <input type="text" class="form-input" id="edit_kontak" name="kontak" required>
                            </div>
                            
                            <div>
                                <label for="edit_kuota" class="form-label block text-sm font-semibold text-gray-700 mb-2">
                                    Kuota PKL <span class="text-red-500">*</span>
                                </label>
                                <input type="number" class="form-input" id="edit_kuota" name="kuota" min="1" required>
                            </div>
                        </div>
                        
                        <div>
                            <label for="edit_status" class="form-label block text-sm font-semibold text-gray-700 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select class="form-input" id="edit_status" name="status" required>
                                <option value="active">Aktif</option>
                                <option value="inactive">Nonaktif</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-gray-50 px-6 py-4 border-t">
                    <button type="button" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold px-6 py-2 rounded-lg transition-colors" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold px-6 py-2 rounded-lg transition-colors">
                        <i class="fas fa-save mr-2"></i>Perbarui
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>