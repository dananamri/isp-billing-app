@extends('layouts.portal')

@section('title', 'Pengaturan Akun')
@section('page_title', 'Pengaturan')
@section('page_subtitle', 'Kelola data profil dan keamanan akun kamu')

@section('content')
<form method="POST" action="/portal/pengaturan" class="grid grid-cols-1 lg:grid-cols-2 gap-5">
    @csrf

    <!-- Kartu Data Profil -->
    <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-6 space-y-4">
        <div>
            <h3 class="font-bold text-base text-slate-900">Data Profil</h3>
            <p class="text-xs text-slate-400 mt-0.5">Informasi pribadi yang terlihat oleh admin &amp; tercetak di invoice</p>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
            <input type="text" name="nama" value="{{ old('nama', $user->name) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-[#2563EB]">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">No. HP / WhatsApp <span class="text-rose-500">*</span></label>
                <input type="text" name="telepon" value="{{ old('telepon', $user->phone) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-[#2563EB]">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Email <span class="text-rose-500">*</span></label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-[#2563EB]">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Lengkap</label>
            <textarea name="alamat" rows="3" placeholder="Jl. Contoh No. 12, RT/RW, Kelurahan, Kecamatan, Kota" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-[#2563EB]">{{ old('alamat', $pelanggan->alamat ?? '') }}</textarea>
        </div>

        @if($pelanggan)
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 text-xs space-y-1">
                <div class="flex justify-between"><span class="text-slate-500">Paket Internet</span><strong class="text-slate-800">{{ $pelanggan->paket->nama_paket ?? 'Belum ada paket' }}</strong></div>
                <div class="flex justify-between"><span class="text-slate-500">Status Akun</span><strong class="{{ $pelanggan->status === 'aktif' ? 'text-emerald-600' : 'text-amber-600' }}">{{ ucfirst($pelanggan->status ?? 'menunggu') }}</strong></div>
                <div class="flex justify-between"><span class="text-slate-500">Terdaftar Sejak</span><strong class="text-slate-800">{{ $pelanggan->created_at?->format('d M Y') ?? '-' }}</strong></div>
            </div>
        @endif
    </div>

    <!-- Kartu Keamanan -->
    <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-6 space-y-4">
        <div>
            <h3 class="font-bold text-base text-slate-900">Keamanan Akun</h3>
            <p class="text-xs text-slate-400 mt-0.5">Kosongkan jika tidak ingin mengganti password</p>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Password Saat Ini</label>
            <input type="password" name="current_password" autocomplete="current-password" placeholder="••••••••" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-[#2563EB]">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Password Baru (min. 6 karakter)</label>
            <input type="password" name="new_password" autocomplete="new-password" placeholder="••••••••" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-[#2563EB]">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Konfirmasi Password Baru</label>
            <input type="password" name="new_password_confirmation" autocomplete="new-password" placeholder="••••••••" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-[#2563EB]">
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-xl p-3.5 text-xs text-blue-800 flex gap-2">
            <span>🔒</span>
            <p>Password dipakai untuk login lewat nomor HP atau email. Jaga kerahasiaannya dan jangan bagikan ke siapapun.</p>
        </div>

        <button type="submit" class="w-full py-3 rounded-xl bg-[#2563EB] hover:bg-blue-700 text-white text-sm font-bold shadow-md shadow-blue-500/20 transition">
            💾 Simpan Perubahan
        </button>
    </div>
</form>
@endsection
