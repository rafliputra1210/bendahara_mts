@extends('layouts.admin')
@section('header_title', 'Input Kas Masuk')

@section('content')
<div class="mb-5 sm:mb-6 flex items-center gap-3">
    <a href="{{ route('incomes.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div>
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Input Kas Masuk</h2>
        <p class="text-gray-500 text-sm mt-0.5">Pencatatan pembayaran dengan integrasi cicilan tagihan otomatis.</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden max-w-2xl">
    <div class="px-4 sm:px-6 py-4 border-b border-gray-100 bg-emerald-50/50">
        <h3 class="text-sm font-bold text-gray-700 flex items-center gap-2">
            <i class="fas fa-arrow-down text-emerald-500"></i>
            Detail Pemasukan Kas
        </h3>
    </div>
    <form action="{{ route('incomes.store') }}" method="POST" enctype="multipart/form-data" class="p-4 sm:p-6">
        @csrf
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5 mb-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Transaksi <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" 
                       class="w-full border-gray-300 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 text-sm py-2.5" required>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Siswa (Pembayar)</label>
                <select name="student_id" id="student_id" 
                        class="w-full border-gray-300 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 text-sm py-2.5">
                    <option value="">-- Pilih Siswa / Bukan Siswa --</option>
                    @foreach($students as $siswa)
                        <option value="{{ $siswa->id }}">{{ $siswa->nis }} - {{ $siswa->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Pilih Tagihan / Cicilan <span class="text-gray-400 text-xs font-normal">(Opsional)</span></label>
                <select name="tagihan_id" id="tagihan_id" 
                        class="w-full border-gray-300 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 text-sm py-2.5 bg-gray-50" disabled>
                    <option value="">-- Pilih Siswa Dahulu --</option>
                </select>
                <p id="info_tagihan" class="text-xs text-emerald-600 mt-1 hidden"></p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Jenis Pembayaran <span class="text-red-500">*</span></label>
                <select name="jenis_pembayaran" id="jenis_pembayaran" 
                        class="w-full border-gray-300 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 text-sm py-2.5" required>
                    <option value="SPP">SPP</option>
                    <option value="Kas Bulanan">Kas Bulanan</option>
                    <option value="Daftar Ulang">Daftar Ulang</option>
                    <option value="Uang Gedung">Uang Gedung</option>
                    <option value="Infaq">Infaq</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>
            
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nominal Pembayaran (Rp) <span class="text-red-500">*</span></label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-500 font-bold text-sm">Rp</span>
                    <input type="number" name="nominal" id="nominal" 
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
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Keterangan / Catatan Tambahan</label>
            <textarea name="keterangan" rows="2" 
                      class="w-full border-gray-300 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 text-sm resize-none"
                      placeholder="Misal: Pembayaran cicilan ke-2"></textarea>
        </div>

        <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 pt-4 border-t border-gray-100">
            <a href="{{ route('incomes.index') }}" 
               class="w-full sm:w-auto px-6 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition font-medium text-sm text-center">
                Batal
            </a>
            <button type="submit" 
                    class="w-full sm:w-auto px-6 py-2.5 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition font-medium text-sm shadow-sm">
                <i class="fas fa-save mr-1.5"></i> Simpan Kas Masuk
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

    document.addEventListener('DOMContentLoaded', function() {
        const studentSelect = document.getElementById('student_id');
        const tagihanSelect = document.getElementById('tagihan_id');
        const infoTagihan = document.getElementById('info_tagihan');
        const nominalInput = document.getElementById('nominal');
        const jenisSelect = document.getElementById('jenis_pembayaran');

        let currentTagihans = [];

        studentSelect.addEventListener('change', function() {
            const studentId = this.value;
            
            tagihanSelect.innerHTML = '<option value="">-- Sedang memuat data... --</option>';
            tagihanSelect.disabled = true;
            infoTagihan.classList.add('hidden');
            
            if (studentId) {
                fetch(`/api/tagihan-siswa/${studentId}`)
                    .then(response => response.json())
                    .then(data => {
                        currentTagihans = data;
                        
                        if(data.length === 0) {
                            tagihanSelect.innerHTML = '<option value="">-- Siswa tidak memiliki tunggakan --</option>';
                        } else {
                            tagihanSelect.innerHTML = '<option value="">-- Pilih Tagihan yang Ingin Dibayar --</option>';
                            tagihanSelect.disabled = false;
                            tagihanSelect.classList.remove('bg-gray-50');
                            
                            data.forEach(tagihan => {
                                let sisa = tagihan.total_tagihan - tagihan.total_dibayar;
                                let formattedSisa = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(sisa);
                                
                                let option = document.createElement('option');
                                option.value = tagihan.id;
                                option.text = `${tagihan.nama_tagihan} (Sisa: ${formattedSisa})`;
                                tagihanSelect.appendChild(option);
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        tagihanSelect.innerHTML = '<option value="">-- Gagal memuat data --</option>';
                    });
            } else {
                tagihanSelect.innerHTML = '<option value="">-- Pilih Siswa Dahulu --</option>';
                tagihanSelect.disabled = true;
                tagihanSelect.classList.add('bg-gray-50');
            }
        });

        tagihanSelect.addEventListener('change', function() {
            const selectedId = this.value;
            if(selectedId) {
                const tagihan = currentTagihans.find(t => t.id == selectedId);
                if(tagihan) {
                    let sisa = tagihan.total_tagihan - tagihan.total_dibayar;
                    nominalInput.value = sisa;
                    
                    let optionExists = Array.from(jenisSelect.options).some(opt => opt.value === tagihan.nama_tagihan);
                    if(!optionExists) {
                        let newOpt = new Option(tagihan.nama_tagihan, tagihan.nama_tagihan);
                        jenisSelect.add(newOpt);
                    }
                    jenisSelect.value = tagihan.nama_tagihan;

                    infoTagihan.innerHTML = `Total Tagihan: Rp ${tagihan.total_tagihan.toLocaleString('id-ID')} | Sudah Dibayar: Rp ${tagihan.total_dibayar.toLocaleString('id-ID')}`;
                    infoTagihan.classList.remove('hidden');
                }
            } else {
                nominalInput.value = '';
                infoTagihan.classList.add('hidden');
            }
        });
    });
</script>
@endsection