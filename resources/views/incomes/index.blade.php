@extends('layouts.admin')
@section('header_title', 'Kas Masuk')

@section('content')
<!-- html2pdf.js & html2canvas CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<!-- Notifikasi SweetAlert jika ada status -->
@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: "{{ session('success') }}",
        timer: 3000,
        showConfirmButton: false
    });
</script>
@endif
@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: "{{ session('error') }}",
        timer: 3000,
        showConfirmButton: false
    });
</script>
@endif

<div class="mb-5 sm:mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h2 class="text-xl sm:text-3xl font-bold text-gray-800 tracking-tight">Manajemen Kas Masuk & Pembayaran</h2>
        <p class="text-gray-500 text-sm mt-0.5">Atur jenis pembayaran, catat transaksi kas, dan kelola kuitansi pembayaran siswa.</p>
    </div>
    <div class="flex flex-wrap items-center gap-2">
        <button type="button" onclick="openSettingsModal()" 
           class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl font-medium shadow-sm transition text-sm shrink-0">
            <i class="fas fa-cog"></i> Pengaturan
        </button>
        <a href="{{ route('incomes.create_other') }}" 
           class="inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-xl font-medium shadow-sm transition text-sm shrink-0">
            <i class="fas fa-plus-circle"></i> Pemasukan Lainnya
        </a>
    </div>
</div>

<!-- Input Pembayaran Kas -->
<div class="mb-6 sm:mb-8">
    <div class="w-full bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden transition hover:shadow-md">
        <div class="p-4 sm:p-5 border-b border-gray-100 bg-gradient-to-r from-emerald-50 to-white">
            <h3 class="text-base sm:text-lg font-bold text-gray-800 flex items-center gap-2.5">
                <span class="p-2 bg-emerald-100 text-emerald-800 rounded-lg shrink-0">
                    <i class="fas fa-hand-holding-usd text-sm"></i>
                </span>
                Input Pembayaran Kas
            </h3>
        </div>
        
        <form action="{{ route('incomes.store') }}" method="POST" enctype="multipart/form-data" class="p-4 sm:p-6 space-y-4" id="payment_form">
            @csrf
            <input type="hidden" name="kelas_filter" value="{{ $selectedClass }}">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 sm:gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Tanggal Transaksi <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" 
                           class="w-full border-gray-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 text-sm py-2.5" required>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Siswa (Pembayar) <span class="text-red-500">*</span></label>
                    <select name="student_id" id="student_id" 
                            class="w-full border-gray-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 text-sm py-2.5" required>
                        <option value="">-- Pilih Siswa --</option>
                        @foreach($students as $siswa)
                            <option value="{{ $siswa->id }}">{{ $siswa->nis }} - {{ $siswa->nama }} (Kelas {{ $siswa->kelas }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 sm:gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Pilih Tagihan / Cicilan <span class="text-red-500">*</span></label>
                    <select name="tagihan_id" id="tagihan_id" 
                            class="w-full border-gray-200 bg-gray-50 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 text-sm py-2.5" required disabled>
                        <option value="">-- Pilih Siswa Terlebih Dahulu --</option>
                    </select>
                    <p id="info_tagihan" class="text-xs text-emerald-600 font-medium mt-1 hidden"></p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Kategori Pembayaran <span class="text-red-500">*</span></label>
                    <select name="jenis_pembayaran" id="jenis_pembayaran" 
                            class="w-full border-gray-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 text-sm py-2.5" required>
                        <option value="Kas Bulanan">Kas Bulanan</option>
                        <option value="SPP">SPP</option>
                        <option value="Uang Gedung">Uang Gedung</option>
                        <option value="Daftar Ulang">Daftar Ulang</option>
                        <option value="Infaq">Infaq</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Nominal Pembayaran (Rp) <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-500 font-bold text-sm">Rp</span>
                        <input type="number" name="nominal" id="nominal" 
                               class="w-full pl-10 pr-4 py-2.5 border-gray-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 font-bold text-lg text-emerald-700" 
                               placeholder="0" min="0" required>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Keterangan / Bukti</label>
                    <div class="flex gap-2">
                        <input type="text" name="keterangan" 
                               class="w-full border-gray-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 text-sm py-2.5" 
                               placeholder="Catatan tambahan...">
                        <input type="file" name="bukti" class="hidden" id="buktiInput">
                        <label for="buktiInput" class="p-2.5 bg-gray-50 border border-gray-200 text-gray-500 hover:bg-emerald-50 hover:text-emerald-600 rounded-xl cursor-pointer transition flex items-center justify-center shrink-0 w-12" title="Upload Bukti">
                            <i class="fas fa-paperclip"></i>
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" 
                        class="w-full sm:w-auto px-8 py-3 bg-emerald-600 text-white font-bold rounded-xl hover:bg-emerald-700 active:scale-95 transition shadow hover:shadow-lg text-sm flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i> Simpan Pembayaran
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Bagian Bawah: Tabel Status Pembayaran Siswa -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    
    {{-- Header tabel --}}
    <div class="p-4 sm:p-6 border-b border-gray-100">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-base sm:text-lg font-bold text-gray-800 flex items-center flex-wrap gap-2">
                    Status Pembayaran Siswa 
                    @if($selectedClass)
                        <span class="text-sm font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-lg border border-emerald-100">Kelas {{ $selectedClass }}</span>
                    @endif
                </h3>
                <p class="text-xs text-gray-400 mt-0.5">Klik badge cicilan di bawah untuk melakukan pembayaran instan.</p>
            </div>
            
            <div class="flex flex-col xs:flex-row items-start xs:items-center gap-3">
                {{-- Filter Kelas --}}
                <div class="flex items-center gap-2">
                    <label class="text-xs font-bold text-gray-500 uppercase whitespace-nowrap">Kelas:</label>
                    <form action="{{ route('incomes.index') }}" method="GET">
                        <select name="kelas" onchange="this.form.submit()" 
                                class="border-gray-200 rounded-xl text-xs py-1.5 px-3 focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                            <option value="">Semua Kelas</option>
                            @foreach($classes as $cls)
                                <option value="{{ $cls }}" {{ $selectedClass == $cls ? 'selected' : '' }}>Kelas {{ $cls }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
                
                {{-- Legend --}}
                <div class="flex items-center gap-1.5 border-l pl-3 border-gray-200">
                    <span class="inline-flex items-center text-[10px] px-2 py-0.5 rounded bg-gray-100 text-gray-600 font-semibold border border-gray-200">Belum</span>
                    <span class="inline-flex items-center text-[10px] px-2 py-0.5 rounded bg-amber-50 text-amber-700 font-semibold border border-amber-200">Cicil</span>
                    <span class="inline-flex items-center text-[10px] px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 font-semibold border border-emerald-200">Lunas</span>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Table wrapper --}}
    <div class="overflow-x-auto -webkit-overflow-scrolling-touch">
        <table class="w-full text-left border-collapse min-w-[700px]">
            <thead>
                <tr class="bg-gray-50/80 border-b border-gray-150">
                    <th class="p-3 sm:p-4 text-xs font-bold uppercase tracking-wider text-gray-500 pl-4 sm:pl-6">Nama</th>
                    <th class="p-3 sm:p-4 text-xs font-bold uppercase tracking-wider text-gray-500">Kelas</th>
                    
                    <!-- Header Dinamis Kolom 1-5 -->
                    @for ($i = 1; $i <= 5; $i++)
                        <th class="p-3 sm:p-4 text-xs font-bold uppercase tracking-wider text-gray-500 text-center">
                            @if($selectedClass && isset($classTargets[$selectedClass][$i]))
                                <span class="truncate block max-w-[90px] sm:max-w-[120px] mx-auto" title="{{ $classTargets[$selectedClass][$i]['name'] }}">
                                    {{ $classTargets[$selectedClass][$i]['name'] }}
                                </span>
                            @else
                                Kat. {{ $i }}
                            @endif
                        </th>
                    @endfor
                    <th class="p-3 sm:p-4 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">Total</th>
                    <th class="p-3 sm:p-4 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">Sisa</th>
                    <th class="p-3 sm:p-4 text-xs font-bold uppercase tracking-wider text-gray-500 text-center pr-4 sm:pr-6">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($students as $siswa)
                    @php
                        $siswaTagihans = $siswa->tagihans->keyBy('urutan');
                        $lunasCount = 0;
                        $totalInstallments = 5;
                        $totalTagihanSiswa = 0;
                        $totalDibayarSiswa = 0;
                        for($i=1; $i<=5; $i++) {
                            $tagihan = $siswaTagihans->get($i);
                            if($tagihan) {
                                $totalTagihanSiswa += $tagihan->total_tagihan;
                                $totalDibayarSiswa += $tagihan->total_dibayar;
                            }
                        }
                        $sisaSiswa = $totalTagihanSiswa - $totalDibayarSiswa;
                    @endphp
                    <tr class="hover:bg-gray-50/80 transition duration-150">
                        <td class="p-3 sm:p-4 pl-4 sm:pl-6">
                            <div class="font-semibold text-gray-900 text-sm leading-tight">{{ $siswa->nama }}</div>
                            <div class="text-[10px] text-gray-400 mt-0.5">NIS: {{ $siswa->nis }}</div>
                        </td>
                        <td class="p-3 sm:p-4 text-sm text-gray-600 font-medium">
                            <span class="px-2 py-0.5 bg-gray-100 text-gray-700 rounded-lg text-xs">{{ $siswa->kelas }}</span>
                        </td>
                        
                        @for ($i = 1; $i <= 5; $i++)
                            @php
                                $tagihan = $siswaTagihans->get($i);
                                $configuredTarget = $classTargets[$siswa->kelas][$i] ?? null;
                            @endphp
                            <td class="p-2 sm:p-3 text-center">
                                @if($tagihan)
                                    @php
                                        $sisa = $tagihan->total_tagihan - $tagihan->total_dibayar;
                                    @endphp
                                    @if ($tagihan->status == 'lunas')
                                        @php $lunasCount++; @endphp
                                        <button type="button" 
                                            class="w-full max-w-[120px] sm:max-w-[130px] mx-auto py-1.5 sm:py-2 px-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-xl flex flex-col items-center gap-0.5 transition cursor-not-allowed"
                                            title="{{ $tagihan->nama_tagihan }} - Sudah Lunas" disabled>
                                            <span class="text-xs sm:text-sm font-black tracking-tight">{{ number_format($tagihan->total_dibayar, 0, ',', '.') }}</span>
                                            <span class="truncate w-full text-[9px] font-bold uppercase">{{ $tagihan->nama_tagihan }}</span>
                                        </button>
                                    @elseif ($tagihan->status == 'mencicil')
                                        <button type="button" 
                                            onclick="triggerQuickPayment({{ $siswa->id }}, {{ $tagihan->id }}, {{ $sisa }}, '{{ $tagihan->nama_tagihan }}')"
                                            class="w-full max-w-[120px] sm:max-w-[130px] mx-auto py-1.5 sm:py-2 px-2 bg-amber-50 hover:bg-amber-100 hover:border-amber-300 text-amber-700 border border-amber-200 rounded-xl flex flex-col items-center gap-0.5 transition cursor-pointer"
                                            title="Bayar Sisa {{ $tagihan->nama_tagihan }}">
                                            <span class="text-xs sm:text-sm font-black tracking-tight">Sisa {{ number_format($sisa, 0, ',', '.') }}</span>
                                            <span class="truncate w-full text-[9px] font-bold uppercase">{{ $tagihan->nama_tagihan }}</span>
                                        </button>
                                    @else
                                        <button type="button" 
                                            onclick="triggerQuickPayment({{ $siswa->id }}, {{ $tagihan->id }}, {{ $tagihan->total_tagihan }}, '{{ $tagihan->nama_tagihan }}')"
                                            class="w-full max-w-[120px] sm:max-w-[130px] mx-auto py-1.5 sm:py-2 px-2 bg-gray-50 hover:bg-gray-100 hover:border-gray-300 text-gray-500 border border-gray-200 rounded-xl flex flex-col items-center gap-0.5 transition cursor-pointer"
                                            title="Belum Bayar {{ $tagihan->nama_tagihan }}">
                                            <span class="text-xs sm:text-sm font-black tracking-tight">{{ number_format($tagihan->total_tagihan, 0, ',', '.') }}</span>
                                            <span class="truncate w-full text-[9px] font-bold uppercase">{{ $tagihan->nama_tagihan }}</span>
                                        </button>
                                    @endif
                                @else
                                    <button type="button" 
                                        class="w-full max-w-[120px] sm:max-w-[130px] mx-auto py-1.5 sm:py-2 px-2 bg-gray-50/50 text-gray-300 border border-gray-200 border-dashed rounded-xl text-[10px] font-medium cursor-not-allowed"
                                        title="Target Pembayaran Belum Diatur" disabled>
                                        Belum Diatur
                                    </button>
                                @endif
                            </td>
                        @endfor

                        <td class="p-3 sm:p-4 text-right">
                            <span class="text-sm font-black text-emerald-600 block">{{ number_format($totalDibayarSiswa, 0, ',', '.') }}</span>
                        </td>
                        <td class="p-3 sm:p-4 text-right">
                            <span class="text-sm font-black text-amber-600 block">{{ number_format($sisaSiswa, 0, ',', '.') }}</span>
                        </td>
                        <td class="p-3 sm:p-4 text-center pr-4 sm:pr-6">
                            @if ($lunasCount == $totalInstallments)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] sm:text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    Lunas ✓
                                </span>
                            @elseif ($siswa->tagihans->where('total_dibayar', '>', 0)->isNotEmpty())
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] sm:text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                                    {{ $lunasCount }}/{{ $totalInstallments }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] sm:text-xs font-semibold bg-red-100 text-red-800 border border-red-200">
                                    Belum
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="p-10 text-center text-gray-400">
                            <i class="fas fa-users text-4xl mb-3 block opacity-20"></i>
                            <p class="font-medium">Belum ada data siswa. Silakan pilih kelas lain atau tambahkan siswa.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal settings dihapus dari sini atau dibiarkan -->

<script>
    function openSettingsModal() {
        document.getElementById('settingsModal').classList.remove('hidden');
    }
    function closeSettingsModal() {
        document.getElementById('settingsModal').classList.add('hidden');
    }
</script>

<!-- MODAL PENGATURAN TARGET -->
<div id="settingsModal" class="fixed inset-0 z-50 flex items-start sm:items-center justify-center bg-black/60 backdrop-blur-sm p-3 sm:p-4 overflow-y-auto hidden">
    <div class="relative bg-white rounded-2xl sm:rounded-3xl max-w-lg w-full shadow-2xl overflow-hidden my-auto">
        <div class="flex items-center justify-between px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-100 bg-gray-50">
            <h4 class="font-bold text-gray-800 text-sm sm:text-base flex items-center gap-2">
                <i class="fas fa-cog text-blue-600"></i> Pengaturan Target & Jenis Bayar
            </h4>
            <button onclick="closeSettingsModal()" class="text-gray-400 hover:text-gray-600 focus:outline-none p-1.5 bg-white rounded-full border border-gray-200">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
        <form action="{{ route('incomes.update-target') }}" method="POST" class="p-4 sm:p-6 space-y-4" id="target_settings_form">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Pilih Kelas <span class="text-red-500">*</span></label>
                <select name="kelas" id="setting_kelas" 
                        class="w-full border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm py-2.5" required>
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($classes as $kelas)
                        <option value="{{ $kelas }}">Kelas {{ $kelas }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-400 mt-1">Sesuaikan jenis pembayaran & nominal target per slot (1-5).</p>
            </div>

            <div class="space-y-2.5 bg-gray-50 p-3 sm:p-4 rounded-2xl border border-gray-100" id="nominal_container">
                <div class="grid grid-cols-12 gap-2 sm:gap-3 text-left mb-1">
                    <div class="col-span-6 text-[10px] font-bold uppercase text-gray-400">Jenis Pembayaran</div>
                    <div class="col-span-6 text-[10px] font-bold uppercase text-gray-400">Target Nominal</div>
                </div>
                @for ($i = 1; $i <= 5; $i++)
                <div class="grid grid-cols-12 gap-2 sm:gap-3 items-center">
                    <div class="col-span-6">
                        <input type="text" name="nama_{{ $i }}" id="target_nama_{{$i}}" 
                               class="w-full px-3 py-2 border-gray-200 rounded-lg text-sm text-gray-700 font-medium focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="Contoh: Buku Paket, dll" required>
                    </div>
                    <div class="col-span-6 relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 text-sm font-bold">Rp</span>
                        <input type="number" name="nominal_{{ $i }}" id="target_nominal_{{$i}}" 
                               class="w-full pl-9 pr-3 py-2 border-gray-200 rounded-lg text-sm text-gray-700 font-semibold focus:ring-blue-500 focus:border-blue-500" 
                               value="0" min="0" required>
                    </div>
                </div>
                @endfor
            </div>

            <div class="pt-2">
                <button type="submit" 
                        class="w-full px-5 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 active:scale-95 transition shadow hover:shadow-lg text-sm flex justify-center items-center gap-2">
                    <i class="fas fa-save"></i> Simpan & Terapkan Pembayaran
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL KUITANSI PEMBAYARAN KAS -->
@if($receipt)
<div id="receiptModal" class="fixed inset-0 z-50 flex items-start sm:items-center justify-center bg-black/60 backdrop-blur-sm p-3 sm:p-4 overflow-y-auto">
    <div class="relative bg-white rounded-2xl sm:rounded-3xl max-w-lg w-full shadow-2xl overflow-hidden my-auto">
        
        <!-- Header Modal -->
        <div class="flex items-center justify-between px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-100 bg-gray-50">
            <h4 class="font-bold text-gray-800 text-sm sm:text-base">Kuitansi Pembayaran Digital</h4>
            <button onclick="closeReceiptModal()" class="text-gray-400 hover:text-gray-600 focus:outline-none p-1.5 bg-white rounded-full border border-gray-200">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <div class="p-4 sm:p-6">
            <!-- Tampilan Kuitansi -->
            <div id="receipt_card" class="bg-white p-4 sm:p-6 border border-gray-200 rounded-xl sm:rounded-2xl relative shadow-sm" style="font-family: 'Inter', sans-serif;">
                <!-- Hiasan Garis Robekan -->
                <div class="absolute inset-y-0 left-0 flex flex-col justify-between py-4 -ml-[5px]">
                    @for ($i = 0; $i < 12; $i++)
                        <span class="w-[10px] h-[10px] bg-white rounded-full border-r border-gray-200"></span>
                    @endfor
                </div>

                <!-- Kop Kuitansi -->
                <div class="flex justify-between items-start border-b-2 border-dashed border-gray-200 pb-3 mb-3">
                    <div>
                        <h5 class="text-emerald-700 font-black text-lg sm:text-xl tracking-wider">Bendahara</h5>
                        <p class="text-[10px] text-gray-400 font-semibold tracking-wide">MTS MIFTAHUL ULUM DAMPIT</p>
                    </div>
                    <div class="text-right">
                        <span class="px-2.5 py-1 bg-emerald-50 text-emerald-800 border border-emerald-100 text-[10px] font-bold rounded-lg uppercase">BUKTI LUNAS</span>
                        <p class="text-[10px] text-gray-400 mt-1.5 font-medium">No: <span class="font-bold text-gray-800 uppercase">KUI-{{ str_pad($receipt->id, 5, '0', STR_PAD_LEFT) }}</span></p>
                    </div>
                </div>

                <!-- Detail Penerima -->
                <div class="space-y-2 mb-5 text-sm text-gray-700">
                    <div class="flex flex-wrap sm:flex-nowrap gap-1">
                        <span class="w-full sm:w-36 text-[10px] text-gray-400 font-bold uppercase tracking-wider">Telah Diterima Dari</span>
                        <span class="font-semibold text-gray-900 text-xs sm:text-sm">: {{ $receipt->student->nama ?? 'Siswa' }} ({{ $receipt->student->nis ?? '-' }})</span>
                    </div>
                    <div class="flex flex-wrap sm:flex-nowrap gap-1">
                        <span class="w-full sm:w-36 text-[10px] text-gray-400 font-bold uppercase tracking-wider">Kelas / Rombel</span>
                        <span class="font-medium text-xs sm:text-sm">: Kelas {{ $receipt->student->kelas ?? '-' }}</span>
                    </div>
                    <div class="flex flex-wrap sm:flex-nowrap gap-1">
                        <span class="w-full sm:w-36 text-[10px] text-gray-400 font-bold uppercase tracking-wider">Guna Pembayaran</span>
                        <span class="font-medium text-xs sm:text-sm">: {{ $receipt->jenis_pembayaran }} ({{ $receipt->tagihan->nama_tagihan ?? 'Slot ' . ($receipt->tagihan->urutan ?? '') }})</span>
                    </div>
                    <div class="flex flex-wrap sm:flex-nowrap gap-1">
                        <span class="w-full sm:w-36 text-[10px] text-gray-400 font-bold uppercase tracking-wider">Uang Sejumlah</span>
                        <span class="font-black text-emerald-700 text-base sm:text-lg">: Rp {{ number_format($receipt->nominal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex flex-wrap sm:flex-nowrap items-start gap-1">
                        <span class="w-full sm:w-36 text-[10px] text-gray-400 font-bold uppercase tracking-wider pt-0.5">Terbilang</span>
                        <span class="font-semibold italic text-gray-600 bg-gray-50 px-2 py-1 rounded-lg border border-gray-100 flex-1 text-[10px] sm:text-xs" id="receipt_terbilang">
                            -- Memuat Terbilang --
                        </span>
                    </div>
                </div>

                <!-- Tanda Tangan -->
                <div class="flex justify-between items-end border-t border-gray-100 pt-3">
                    <div class="text-[10px] text-gray-400">
                        <div>Tanggal:</div>
                        <div class="font-bold text-gray-700 text-xs">{{ \Carbon\Carbon::parse($receipt->tanggal)->format('d M Y') }}</div>
                    </div>
                    <div class="text-center w-36">
                        <div class="text-[9px] text-gray-400 font-semibold mb-10 uppercase">Bendahara Penerima,</div>
                        <div class="font-bold text-xs text-gray-800 border-b border-gray-300 pb-1">{{ Auth::user()->name }}</div>
                        <div class="text-[9px] text-gray-400 mt-0.5">Sistem BendaharaPro</div>
                    </div>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="grid grid-cols-3 gap-2 sm:gap-3 mt-4 sm:mt-6">
                <button onclick="downloadPDF()" class="py-2.5 px-2 sm:px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-[10px] sm:text-xs font-bold flex items-center justify-center gap-1 sm:gap-2 shadow-sm hover:shadow active:scale-95 transition">
                    <i class="fas fa-file-pdf text-sm"></i> <span>Simpan PDF</span>
                </button>
                <button onclick="downloadJPG()" class="py-2.5 px-2 sm:px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-[10px] sm:text-xs font-bold flex items-center justify-center gap-1 sm:gap-2 shadow-sm hover:shadow active:scale-95 transition">
                    <i class="fas fa-image text-sm"></i> <span>Simpan JPG</span>
                </button>
                <button onclick="shareWhatsApp()" class="py-2.5 px-2 sm:px-4 bg-green-500 hover:bg-green-600 text-white rounded-xl text-[10px] sm:text-xs font-bold flex items-center justify-center gap-1 sm:gap-2 shadow-sm hover:shadow active:scale-95 transition">
                    <i class="fab fa-whatsapp text-sm"></i> <span>Share WA</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    const receiptNominal = {{ $receipt->nominal }};
    const studentName = "{{ $receipt->student->nama ?? 'Siswa' }}";
    const receiptNo = "KUI-{{ str_pad($receipt->id, 5, '0', STR_PAD_LEFT) }}";
    const paymentName = "{{ $receipt->jenis_pembayaran }} ({{ $receipt->tagihan->nama_tagihan ?? 'Tagihan' }})";
    const formattedNominal = "{{ number_format($receipt->nominal, 0, ',', '.') }}";
    const paymentDate = "{{ \Carbon\Carbon::parse($receipt->tanggal)->format('d M Y') }}";
    const waliPhone = "{{ $receipt->student->no_hp_wali ?? '' }}";

    document.addEventListener('DOMContentLoaded', function() {
        const textTerbilang = helperTerbilang(receiptNominal) + " Rupiah";
        document.getElementById('receipt_terbilang').innerText = textTerbilang.trim();
    });

    function helperTerbilang(nominal) {
        const bilangan = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];
        let temp = "";
        if (nominal < 12) {
            temp = " " + bilangan[nominal];
        } else if (nominal < 20) {
            temp = helperTerbilang(nominal - 10) + " Belas";
        } else if (nominal < 100) {
            temp = helperTerbilang(Math.floor(nominal / 10)) + " Puluh" + helperTerbilang(nominal % 10);
        } else if (nominal < 200) {
            temp = " Seratus" + helperTerbilang(nominal - 100);
        } else if (nominal < 1000) {
            temp = helperTerbilang(Math.floor(nominal / 100)) + " Ratus" + helperTerbilang(nominal % 100);
        } else if (nominal < 2000) {
            temp = " Seribu" + helperTerbilang(nominal - 1000);
        } else if (nominal < 1000000) {
            temp = helperTerbilang(Math.floor(nominal / 1000)) + " Ribu" + helperTerbilang(nominal % 1000);
        } else if (nominal < 1000000000) {
            temp = helperTerbilang(Math.floor(nominal / 1000000)) + " Juta" + helperTerbilang(nominal % 1000000);
        }
        return temp;
    }

    function closeReceiptModal() {
        document.getElementById('receiptModal').classList.add('hidden');
    }

    function downloadPDF() {
        const element = document.getElementById('receipt_card');
        const opt = {
            margin:       0.5,
            filename:     'Kuitansi_' + receiptNo + '.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2 },
            jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(element).save();
    }

    function downloadJPG() {
        const element = document.getElementById('receipt_card');
        html2canvas(element, { scale: 3 }).then(canvas => {
            const link = document.createElement('a');
            link.download = 'Kuitansi_' + receiptNo + '.png';
            link.href = canvas.toDataURL('image/png');
            link.click();
        });
    }

    function shareWhatsApp() {
        if (!waliPhone) {
            Swal.fire({
                icon: 'warning',
                title: 'No HP Wali Kosong',
                text: 'Siswa ini tidak memiliki nomor HP wali murid terdaftar.',
            });
            return;
        }

        const textTerbilang = helperTerbilang(receiptNominal) + " Rupiah";
        const message = `Yth. Bapak/Ibu Wali dari *${studentName}*,\n\nBerikut adalah bukti pembayaran yang telah diterima:\n` +
            `- No. Kuitansi: *${receiptNo}*\n` +
            `- Tanggal: *${paymentDate}*\n` +
            `- Pembayaran: *${paymentName}*\n` +
            `- Jumlah: *Rp ${formattedNominal}*\n` +
            `- Terbilang: _${textTerbilang.trim()}_\n\n` +
            `Status: *LUNAS*\n\n` +
            `Terima kasih atas kerja samanya.\n-- Bendahara MTs Al-Muttaqin --`;

        let formattedPhone = waliPhone.trim();
        if (formattedPhone.startsWith('0')) {
            formattedPhone = '62' + formattedPhone.substring(1);
        } else if (!formattedPhone.startsWith('62')) {
            formattedPhone = '62' + formattedPhone;
        }

        const waUrl = `https://api.whatsapp.com/send?phone=${formattedPhone}&text=${encodeURIComponent(message)}`;
        window.open(waUrl, '_blank');
    }
</script>
@endif

<style>
    /* Styling khusus Select2 agar mirip dengan form Tailwind */
    .select2-container .select2-selection--single {
        height: 44px !important;
        border: 1px solid #e5e7eb !important;
        border-radius: 0.75rem !important;
        display: flex;
        align-items: center;
        padding-left: 0.25rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 42px !important;
        top: 1px !important;
        right: 8px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #374151 !important;
        font-size: 0.875rem !important;
    }
    .select2-search__field {
        border-radius: 0.5rem !important;
        border: 1px solid #e5e7eb !important;
        padding: 0.5rem !important;
        font-size: 0.875rem !important;
        outline: none !important;
    }
    .select2-search__field:focus {
        border-color: #10b981 !important;
        box-shadow: 0 0 0 1px #10b981 !important;
    }
    .select2-dropdown {
        border: 1px solid #e5e7eb !important;
        border-radius: 0.75rem !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
        overflow: hidden;
    }
    .select2-results__option {
        font-size: 0.875rem !important;
        padding: 0.5rem 1rem !important;
    }
    .select2-results__option--highlighted[aria-selected] {
        background-color: #10b981 !important;
    }
</style>

<script>
    const classTargets = @json($classTargets);

    document.addEventListener('DOMContentLoaded', function() {
        const studentSelect = document.getElementById('student_id');
        const tagihanSelect = document.getElementById('tagihan_id');
        const infoTagihan = document.getElementById('info_tagihan');
        const nominalInput = document.getElementById('nominal');
        const jenisSelect = document.getElementById('jenis_pembayaran');
        const settingKelas = document.getElementById('setting_kelas');

        // Inisialisasi Select2
        $(document).ready(function() {
            $('#student_id').select2({
                placeholder: "-- Cari & Pilih Siswa --",
                width: '100%',
                language: {
                    noResults: function() {
                        return "Siswa tidak ditemukan";
                    }
                }
            });

            // Menangkap event dari Select2 dan meneruskannya ke event listener native
            $('#student_id').on('select2:select', function (e) {
                studentSelect.dispatchEvent(new Event('change'));
            });
            $('#student_id').on('select2:clear', function (e) {
                studentSelect.dispatchEvent(new Event('change'));
            });
        });

        settingKelas.addEventListener('change', function() {
            const selectedClass = this.value;
            if(selectedClass && classTargets[selectedClass]) {
                const targets = classTargets[selectedClass];
                for(let i = 1; i <= 5; i++) {
                    const nameInput = document.getElementById('target_nama_' + i);
                    const nominalInput = document.getElementById('target_nominal_' + i);
                    if(targets[i]) {
                        if(nameInput) nameInput.value = targets[i].name || '';
                        if(nominalInput) nominalInput.value = targets[i].nominal || 0;
                    } else {
                        if(nameInput) nameInput.value = 'Cicilan ' + i;
                        if(nominalInput) nominalInput.value = 0;
                    }
                }
            } else {
                for(let i = 1; i <= 5; i++) {
                    const nameInput = document.getElementById('target_nama_' + i);
                    const nominalInput = document.getElementById('target_nominal_' + i);
                    if(nameInput) nameInput.value = '';
                    if(nominalInput) nominalInput.value = 0;
                }
            }
        });

        studentSelect.addEventListener('change', function() {
            loadStudentTagihans(this.value);
        });

        function loadStudentTagihans(studentId, callback) {
            if (!studentId) {
                tagihanSelect.innerHTML = '<option value="">-- Pilih Siswa Terlebih Dahulu --</option>';
                tagihanSelect.disabled = true;
                tagihanSelect.classList.add('bg-gray-50');
                infoTagihan.classList.add('hidden');
                return;
            }

            tagihanSelect.innerHTML = '<option value="">-- Sedang memuat... --</option>';
            tagihanSelect.disabled = true;
            tagihanSelect.classList.add('bg-gray-50');

            fetch(`/api/tagihan-siswa/${studentId}`)
                .then(response => response.json())
                .then(data => {
                    if(data.length === 0) {
                        tagihanSelect.innerHTML = '<option value="">-- Tidak ada tunggakan --</option>';
                    } else {
                        tagihanSelect.innerHTML = '<option value="">-- Pilih Cicilan/Tagihan --</option>';
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

                        if(callback) callback(data);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    tagihanSelect.innerHTML = '<option value="">-- Gagal memuat data --</option>';
                });
        }

        tagihanSelect.addEventListener('change', function() {
            const selectedId = this.value;
            if(selectedId) {
                fetch(`/api/tagihan-siswa/${studentSelect.value}`)
                    .then(r => r.json())
                    .then(data => {
                        const tagihan = data.find(t => t.id == selectedId);
                        if(tagihan) {
                            let sisa = tagihan.total_tagihan - tagihan.total_dibayar;
                            nominalInput.value = sisa;
                            infoTagihan.innerHTML = `Total: Rp ${parseFloat(tagihan.total_tagihan).toLocaleString('id-ID')} | Sudah Dibayar: Rp ${parseFloat(tagihan.total_dibayar).toLocaleString('id-ID')}`;
                            infoTagihan.classList.remove('hidden');

                            let optionExists = Array.from(jenisSelect.options).some(opt => opt.value === tagihan.nama_tagihan);
                            if(!optionExists) {
                                let newOpt = new Option(tagihan.nama_tagihan, tagihan.nama_tagihan);
                                jenisSelect.add(newOpt);
                            }
                            jenisSelect.value = tagihan.nama_tagihan;
                        }
                    });
            } else {
                nominalInput.value = '';
                infoTagihan.classList.add('hidden');
            }
        });
    });

    function triggerQuickPayment(studentId, tagihanId, remainingAmount, namaTagihan) {
        const studentSelect = document.getElementById('student_id');
        const tagihanSelect = document.getElementById('tagihan_id');
        const nominalInput = document.getElementById('nominal');
        const infoTagihan = document.getElementById('info_tagihan');
        const jenisSelect = document.getElementById('jenis_pembayaran');

        studentSelect.value = studentId;
        tagihanSelect.innerHTML = '<option value="">-- Memuat... --</option>';
        tagihanSelect.disabled = true;
        
        fetch(`/api/tagihan-siswa/${studentId}`)
            .then(response => response.json())
            .then(data => {
                tagihanSelect.innerHTML = '';
                tagihanSelect.disabled = false;
                tagihanSelect.classList.remove('bg-gray-50');
                
                data.forEach(tagihan => {
                    let sisa = tagihan.total_tagihan - tagihan.total_dibayar;
                    let formattedSisa = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(sisa);
                    
                    let option = document.createElement('option');
                    option.value = tagihan.id;
                    option.text = `${tagihan.nama_tagihan} (Sisa: ${formattedSisa})`;
                    if(tagihan.id == tagihanId) {
                        option.selected = true;
                    }
                    tagihanSelect.appendChild(option);
                });

                nominalInput.value = remainingAmount;

                let optionExists = Array.from(jenisSelect.options).some(opt => opt.value === namaTagihan);
                if(!optionExists) {
                    let newOpt = new Option(namaTagihan, namaTagihan);
                    jenisSelect.add(newOpt);
                }
                jenisSelect.value = namaTagihan;

                // Scroll ke form dengan smooth
                const formCard = document.getElementById('payment_form').closest('.bg-white');
                if(formCard) {
                    formCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    formCard.classList.add('ring-2', 'ring-emerald-500');
                    setTimeout(() => {
                        formCard.classList.remove('ring-2', 'ring-emerald-500');
                    }, 1500);
                }
            });
    }
</script>
@endsection