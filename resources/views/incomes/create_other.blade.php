@extends('layouts.admin')
@section('header_title', 'Input Pemasukan Lainnya')

@section('content')
<div class="mb-5 sm:mb-6 flex items-center gap-3">
    <a href="{{ route('incomes.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div>
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Input Pemasukan Lainnya</h2>
        <p class="text-gray-500 text-sm mt-0.5">Catat kas masuk di luar pembayaran siswa (bantuan, hibah, dll).</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden max-w-2xl">
    <div class="px-4 sm:px-6 py-4 border-b border-gray-100 bg-emerald-50/50">
        <h3 class="text-sm font-bold text-gray-700 flex items-center gap-2">
            <i class="fas fa-wallet text-emerald-500"></i>
            Detail Pemasukan
        </h3>
    </div>
    <form action="{{ route('incomes.store') }}" method="POST" enctype="multipart/form-data" class="p-4 sm:p-6">
        @csrf
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5 mb-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" 
                       class="w-full border-gray-300 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 text-sm py-2.5" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Jenis/Sumber Pemasukan <span class="text-red-500">*</span></label>
                <select name="jenis_pembayaran" class="w-full border-gray-300 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 text-sm py-2.5" required>
                    <option value="Bantuan Operasional">Bantuan Operasional</option>
                    <option value="Pemindahan Buku">Pemindahan Buku</option>
                    <option value="Donasi / Hibah">Donasi / Hibah</option>
                    <option value="Sponsorship">Sponsorship</option>
                    <option value="Dana BOS">Dana BOS</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>
            
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nominal (Rp) <span class="text-red-500">*</span></label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-500 font-bold text-sm">Rp</span>
                    <input type="number" name="nominal" 
                           class="w-full pl-10 border-gray-300 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 font-bold text-lg text-emerald-700 py-2.5" 
                           placeholder="0" min="0" required>
                </div>
            </div>
        </div>

        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Upload Bukti <span class="text-gray-400 text-xs font-normal">(Opsional, max 2MB)</span></label>
            <div class="border-2 border-dashed border-gray-200 rounded-xl p-4 hover:border-emerald-300 transition cursor-pointer" onclick="document.getElementById('buktiFile').click()">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-emerald-50 rounded-lg flex items-center justify-center shrink-0">
                        <i class="fas fa-upload text-emerald-400"></i>
                    </div>
                    <div class="min-w-0">
                        <div id="buktiLabel" class="text-sm text-gray-500">Klik untuk pilih file atau drag & drop</div>
                        <div class="text-xs text-gray-400 mt-0.5">JPG, PNG, PDF hingga 2MB</div>
                    </div>
                </div>
            </div>
            <input type="file" name="bukti" id="buktiFile" class="hidden" accept="image/*,.pdf">
        </div>
        
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Keterangan / Rincian Tambahan</label>
            <textarea name="keterangan" rows="2" 
                      class="w-full border-gray-300 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 text-sm resize-none"
                      placeholder="Misal: Dana dari Kemenag untuk renovasi kelas"></textarea>
        </div>

        <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 pt-4 border-t border-gray-100">
            <a href="{{ route('incomes.index') }}" 
               class="w-full sm:w-auto px-6 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition font-medium text-sm text-center">
                Batal
            </a>
            <button type="submit" 
                    class="w-full sm:w-auto px-6 py-2.5 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition font-medium text-sm shadow-sm">
                <i class="fas fa-save mr-1.5"></i> Simpan Pemasukan
            </button>
        </div>
    </form>
</div>

<script>
    document.getElementById('buktiFile').addEventListener('change', function() {
        const label = document.getElementById('buktiLabel');
        if (this.files[0]) {
            label.textContent = this.files[0].name;
            label.classList.add('text-gray-800', 'font-medium');
        } else {
            label.textContent = 'Klik untuk pilih file atau drag & drop';
            label.classList.remove('text-gray-800', 'font-medium');
        }
    });
</script>
@endsection
