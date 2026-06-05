@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Input Pengeluaran</h2>
</div>

<div class="bg-white rounded-xl shadow max-w-3xl">
    <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pengeluaran</label>
                <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="w-full border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kategori Pengeluaran</label>
                <select name="kategori" class="w-full border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500" required>
                    <option value="Konsumsi">Konsumsi</option>
                    <option value="Kegiatan">Kegiatan</option>
                    <option value="Kebersihan">Kebersihan</option>
                    <option value="ATK">ATK</option>
                    <option value="Listrik">Listrik & Air</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Nominal (Rp)</label>
                <input type="number" name="nominal" class="w-full border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500" required>
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Upload Nota/Bukti (Opsional)</label>
            <input type="file" name="bukti" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
        </div>
        
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan / Rincian Belanja</label>
            <textarea name="keterangan" rows="2" class="w-full border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500"></textarea>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t">
            <a href="{{ route('expenses.index') }}" class="px-6 py-2 bg-gray-200 rounded-lg font-medium">Batal</a>
            <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg font-medium">Simpan Pengeluaran</button>
        </div>
    </form>
</div>
@endsection