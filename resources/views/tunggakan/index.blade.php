@extends('layouts.admin')
@section('header_title', 'Analitik & Tunggakan')

@section('content')
<div class="mb-5 sm:mb-6 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
    <div>
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Laporan Tunggakan</h2>
        <p class="text-gray-500 text-sm mt-0.5">Daftar tagihan siswa yang belum lunas dari tahun ajaran sebelumnya.</p>
    </div>
    
    @if(count($tunggakanSiswa) > 0)
    <form action="{{ route('tunggakan.broadcast') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengirim pesan peringatan WA ke SEMUA siswa yang menunggak? Pastikan token WA sudah diatur.')">
        @csrf
        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-xl font-bold text-sm shadow-sm transition flex items-center gap-2">
            <i class="fab fa-whatsapp text-lg"></i>
            Kirim Peringatan Massal
        </button>
    </form>
    @endif
</div>

@if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl mb-4 flex items-center gap-2 text-sm">
        <i class="fas fa-check-circle shrink-0"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 flex items-center gap-2 text-sm">
        <i class="fas fa-exclamation-circle shrink-0"></i>
        <span>{{ session('error') }}</span>
    </div>
@endif

{{-- Summary Card --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
    <div class="bg-white rounded-2xl p-5 border border-red-100 shadow-sm relative overflow-hidden">
        <div class="absolute -right-6 -top-6 text-red-50 opacity-50">
            <i class="fas fa-file-invoice-dollar text-9xl"></i>
        </div>
        <div class="relative z-10">
            <p class="text-sm font-bold text-red-600 mb-1 uppercase tracking-wider">Total Piutang (Tunggakan)</p>
            <h3 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">Rp {{ number_format($totalTunggakanKeseluruhan, 0, ',', '.') }}</h3>
            <p class="text-xs text-gray-500 mt-2 font-medium">Potensi pemasukan dari tahun ajaran lama</p>
        </div>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-orange-100 shadow-sm relative overflow-hidden">
        <div class="absolute -right-6 -top-6 text-orange-50 opacity-50">
            <i class="fas fa-users text-9xl"></i>
        </div>
        <div class="relative z-10">
            <p class="text-sm font-bold text-orange-600 mb-1 uppercase tracking-wider">Jumlah Siswa Menunggak</p>
            <h3 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">{{ count($tunggakanSiswa) }} <span class="text-lg font-bold text-gray-500">Siswa</span></h3>
            <p class="text-xs text-gray-500 mt-2 font-medium">Siswa dengan tagihan belum lunas</p>
        </div>
    </div>
</div>

{{-- Data Table --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/80 border-b border-gray-200">
                    <th class="p-4 text-xs font-bold uppercase tracking-wider text-gray-500">Nama Siswa</th>
                    <th class="p-4 text-xs font-bold uppercase tracking-wider text-gray-500">Kelas / Status</th>
                    <th class="p-4 text-xs font-bold uppercase tracking-wider text-gray-500">Rincian Tunggakan</th>
                    <th class="p-4 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">Total Tunggakan</th>
                    <th class="p-4 text-xs font-bold uppercase tracking-wider text-gray-500 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($tunggakanSiswa as $studentId => $data)
                <tr class="hover:bg-gray-50/80 transition">
                    <td class="p-4">
                        <div class="font-semibold text-gray-900 text-sm">{{ $data['student']->nama }}</div>
                        <div class="text-[10px] text-gray-500 font-mono">{{ $data['student']->nis }}</div>
                    </td>
                    <td class="p-4 text-sm">
                        <span class="px-2 py-0.5 bg-gray-100 rounded-lg text-xs font-medium text-gray-600">{{ $data['student']->kelas }}</span>
                        <div class="mt-1">
                            <span class="text-[10px] px-1.5 py-0.5 rounded {{ $data['student']->status == 'aktif' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }} font-medium">
                                {{ ucfirst(str_replace('_', ' ', $data['student']->status)) }}
                            </span>
                        </div>
                    </td>
                    <td class="p-4 text-sm">
                        <div class="space-y-1">
                            @foreach($data['tagihans'] as $t)
                                <div class="text-xs flex items-center justify-between gap-4 border-b border-gray-50 pb-1 mb-1 last:border-0 last:mb-0 last:pb-0">
                                    <span class="text-gray-600">
                                        {{ $t->nama_tagihan }} <br>
                                        <span class="text-[9px] text-gray-400">TA: {{ $t->academicYear ? $t->academicYear->name : '-' }}</span>
                                    </span>
                                    <span class="font-bold text-red-600">Rp {{ number_format($t->total_tagihan - $t->total_dibayar, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>
                    </td>
                    <td class="p-4 text-sm font-black text-red-600 text-right whitespace-nowrap">
                        Rp {{ number_format($data['total_tunggakan'], 0, ',', '.') }}
                    </td>
                    <td class="p-4 text-center">
                        <button type="button" onclick="openBayarModal({{ $studentId }})"
                                class="bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white border border-blue-200 px-3 py-1.5 rounded-lg text-xs font-bold transition shadow-sm whitespace-nowrap">
                            <i class="fas fa-hand-holding-usd mr-1"></i> Bayar
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-10 text-center text-gray-400">
                        <i class="fas fa-check-circle text-4xl mb-3 block opacity-20 text-emerald-500"></i>
                        <p class="font-medium">Luar Biasa! Tidak ada tunggakan dari tahun ajaran sebelumnya.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL BAYAR TUNGGAKAN -->
<div id="bayarModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 hidden">
    <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
        <div class="flex items-center justify-between p-4 border-b border-gray-100 bg-gray-50 shrink-0">
            <h3 class="text-lg font-bold text-gray-800"><i class="fas fa-hand-holding-usd text-blue-600 mr-2"></i>Bayar Tunggakan Lama</h3>
            <button type="button" onclick="closeBayarModal()" class="text-gray-400 hover:text-gray-600 transition p-1">
                <i class="fas fa-times fa-lg"></i>
            </button>
        </div>
        <div class="p-5 overflow-y-auto custom-scrollbar">
            <div class="bg-blue-50 text-blue-800 p-3 rounded-lg text-xs font-medium border border-blue-100 flex items-start gap-2 mb-4">
                <i class="fas fa-info-circle mt-0.5"></i>
                <p>Pembayaran yang dicatat di sini akan langsung masuk ke total Pemasukan <strong>Tahun Ajaran Aktif</strong> saat ini.</p>
            </div>

            <form action="{{ route('tunggakan.store') }}" method="POST" id="formBayarTunggakan">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Pilih Tagihan yang Dibayar *</label>
                    <select name="tagihan_id" id="modalTagihanId" required class="w-full border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm" onchange="updateMaxNominal()">
                        <option value="">-- Pilih Tagihan --</option>
                        <!-- Options dipopulate oleh JS -->
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nominal Pembayaran (Rp) *</label>
                    <input type="number" name="nominal" id="modalNominal" required min="1" placeholder="Masukkan angka nominal" 
                           class="w-full border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm font-bold text-gray-900">
                    <p class="text-[10px] text-gray-500 mt-1" id="infoSisaTagihan"></p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Keterangan / Catatan Tambahan</label>
                    <textarea name="keterangan" rows="2" class="w-full border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="Contoh: Cicilan pertama tunggakan SPP tahun lalu..."></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                    <button type="button" onclick="closeBayarModal()" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition text-sm">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition shadow-sm text-sm">Simpan Pembayaran</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Menyimpan data tunggakan untuk keperluan modal
    const dataTunggakan = @json($tunggakanSiswa);

    function openBayarModal(studentId) {
        const studentData = dataTunggakan[studentId];
        if(!studentData) return;

        const selectTagihan = document.getElementById('modalTagihanId');
        selectTagihan.innerHTML = '<option value="">-- Pilih Tagihan --</option>';
        
        studentData.tagihans.forEach(t => {
            const sisa = t.total_tagihan - t.total_dibayar;
            const tahun = t.academic_year ? t.academic_year.name : 'Tahun Lama';
            
            const option = document.createElement('option');
            option.value = t.id;
            option.setAttribute('data-sisa', sisa);
            option.textContent = `${t.nama_tagihan} [TA: ${tahun}] - Sisa: Rp ${new Intl.NumberFormat('id-ID').format(sisa)}`;
            selectTagihan.appendChild(option);
        });

        document.getElementById('bayarModal').classList.remove('hidden');
    }

    function closeBayarModal() {
        document.getElementById('bayarModal').classList.add('hidden');
        document.getElementById('formBayarTunggakan').reset();
        document.getElementById('infoSisaTagihan').textContent = '';
    }

    function updateMaxNominal() {
        const select = document.getElementById('modalTagihanId');
        const selectedOption = select.options[select.selectedIndex];
        const inputNominal = document.getElementById('modalNominal');
        const infoSisa = document.getElementById('infoSisaTagihan');

        if (selectedOption && selectedOption.value !== "") {
            const sisa = selectedOption.getAttribute('data-sisa');
            inputNominal.value = sisa;
            inputNominal.max = sisa;
            infoSisa.textContent = `Maksimal pembayaran untuk tagihan ini adalah Rp ${new Intl.NumberFormat('id-ID').format(sisa)}`;
        } else {
            inputNominal.value = '';
            inputNominal.removeAttribute('max');
            infoSisa.textContent = '';
        }
    }
</script>
@endsection
