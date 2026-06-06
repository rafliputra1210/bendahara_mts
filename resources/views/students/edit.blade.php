@extends('layouts.admin')
@section('header_title', 'Edit Siswa')

@section('content')
<div class="mb-5 sm:mb-6 flex items-center gap-3">
    <a href="{{ route('students.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div>
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Edit Data Siswa</h2>
        <p class="text-gray-500 text-sm mt-0.5">Perbarui informasi untuk: <strong class="text-gray-700">{{ $student->nama }}</strong></p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden max-w-4xl">
    <div class="px-4 sm:px-6 py-4 border-b border-gray-100 bg-blue-50/50">
        <h3 class="text-sm font-bold text-gray-700 flex items-center gap-2">
            <i class="fas fa-user-edit text-blue-500"></i>
            Edit Data Identitas
        </h3>
    </div>
    <form action="{{ route('students.update', $student->id) }}" method="POST" class="p-4 sm:p-6">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">NIS / NISN</label>
                <input type="text" name="nis" value="{{ old('nis', $student->nis) }}" 
                       class="w-full border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm py-2.5 @error('nis') border-red-400 @enderror" required>
                @error('nis') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap</label>
                <input type="text" name="nama" value="{{ old('nama', $student->nama) }}" 
                       class="w-full border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm py-2.5" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Kelas</label>
                <input type="text" name="kelas" value="{{ old('kelas', $student->kelas) }}" 
                       class="w-full border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm py-2.5" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Jenis Kelamin</label>
                <select name="jenis_kelamin" class="w-full border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm py-2.5" required>
                    <option value="L" {{ old('jenis_kelamin', $student->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ old('jenis_kelamin', $student->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">No. HP Wali</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 text-sm">+62</span>
                    <input type="text" name="no_hp_wali" value="{{ old('no_hp_wali', $student->no_hp_wali) }}" 
                           class="w-full pl-12 border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm py-2.5">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Status</label>
                <select name="status" class="w-full border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm py-2.5" required>
                    <option value="aktif" {{ old('status', $student->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="tidak_aktif" {{ old('status', $student->status) == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat Lengkap</label>
            <textarea name="alamat" rows="3" 
                      class="w-full border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm resize-none">{{ old('alamat', $student->alamat) }}</textarea>
        </div>

        <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 pt-4 border-t border-gray-100">
            <a href="{{ route('students.index') }}" 
               class="w-full sm:w-auto px-6 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition font-medium text-sm text-center">
                Batal
            </a>
            <button type="submit" 
                    class="w-full sm:w-auto px-6 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition font-medium text-sm shadow-sm">
                <i class="fas fa-save mr-1.5"></i> Perbarui Data
            </button>
        </div>
    </form>
</div>
@endsection