@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Edit Data Siswa</h2>
    <p class="text-gray-600 text-sm">Perbarui informasi untuk siswa: <strong>{{ $student->nama }}</strong></p>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden max-w-4xl">
    <form action="{{ route('students.update', $student->id) }}" method="POST" class="p-6">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">NIS / NISN</label>
                <input type="text" name="nis" value="{{ old('nis', $student->nis) }}" class="w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 @error('nis') border-red-500 @enderror" required>
                @error('nis') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                <input type="text" name="nama" value="{{ old('nama', $student->nama) }}" class="w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kelas</label>
                <input type="text" name="kelas" value="{{ old('kelas', $student->kelas) }}" class="w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin</label>
                <select name="jenis_kelamin" class="w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500" required>
                    <option value="L" {{ old('jenis_kelamin', $student->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ old('jenis_kelamin', $student->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">No. HP Wali</label>
                <input type="text" name="no_hp_wali" value="{{ old('no_hp_wali', $student->no_hp_wali) }}" class="w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500" required>
                    <option value="aktif" {{ old('status', $student->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="tidak_aktif" {{ old('status', $student->status) == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Lengkap</label>
            <textarea name="alamat" rows="3" class="w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">{{ old('alamat', $student->alamat) }}</textarea>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
            <a href="{{ route('students.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">Batal</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">Perbarui Data</button>
        </div>
    </form>
</div>
@endsection