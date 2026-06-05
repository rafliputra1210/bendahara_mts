@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Input Kas Masuk</h2>
</div>

<div class="bg-white rounded-xl shadow max-w-3xl">
    <form action="{{ route('incomes.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Transaksi</label>
                <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Siswa (Kosongkan jika bukan dari siswa)</label>
                <select name="student_id" class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                    <option value="">-- Pilih Siswa --</option>
                    @foreach($students as $siswa)
                        <option value="{{ $siswa->id }}">{{ $siswa->nis }} - {{ $siswa->nama }} ({{ $siswa->kelas }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Pembayaran</label>
                <select name="jenis_pembayaran" class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" required>
                    <option value="SPP">SPP</option>
                    <option value="Kas Bulanan">Kas Bulanan</option>
                    <option value="Daftar Ulang">Daftar Ulang</option>
                    <option value="Infaq">Infaq</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nominal (Rp)</label>
                <input type="number" name="nominal" class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" required>
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Upload Bukti (Opsional - Gambar/PDF max 2MB)</label>
            <input type="file" name="bukti" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
        </div>
        
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan (Bulan, Catatan, dll)</label>
            <textarea name="keterangan" rows="2" class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"></textarea>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t">
            <a href="{{ route('incomes.index') }}" class="px-6 py-2 bg-gray-200 rounded-lg font-medium">Batal</a>
            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg font-medium">Simpan Transaksi</button>
        </div>
    </form>
</div>
@endsection