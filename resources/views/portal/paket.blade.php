@extends('layouts.portal')

@section('title', 'Paket Internet')
@section('page_title', 'Paket Internet')
@section('page_subtitle', 'Lihat dan kelola paket langganan kamu')

@section('content')
@if(!$pelanggan)
    <div class="bg-amber-50 border border-amber-200 text-amber-800 text-sm px-4 py-3 rounded-xl">Data pelanggan belum terdaftar. Hubungi admin.</div>
@else
    <!-- Paket Aktif Saat Ini -->
    <div class="bg-gradient-to-r from-[#1D4ED8] via-[#2563EB] to-[#3B82F6] rounded-2xl p-6 sm:p-8 text-white relative overflow-hidden shadow-lg shadow-blue-600/20">
        <div class="absolute -right-10 -top-10 w-48 h-48 bg-white/10 rounded-full"></div>
        <div class="absolute right-20 -bottom-10 w-32 h-32 bg-white/10 rounded-full"></div>
        <div class="relative flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <p class="text-xs font-bold tracking-widest uppercase text-blue-200">Paket Aktif Kamu</p>
                @if($pelanggan->paket)
                    <h2 class="text-2xl font-black mt-1">{{ $pelanggan->paket->nama_paket }}</h2>
                    <p class="text-blue-100 text-sm mt-1">{{ $pelanggan->paket->kecepatan ?? '-' }} &bull; Unlimited kuota</p>
                    <p class="text-[11px] text-blue-200 mt-2 line-clamp-2">{{ $pelanggan->paket->deskripsi ?? '' }}</p>
                @else
                    <h2 class="text-xl font-bold mt-1">Belum Memilih Paket</h2>
                    <p class="text-blue-100 text-sm mt-1">Pilih salah satu paket di bawah untuk mulai berlangganan.</p>
                @endif
            </div>
            <div class="shrink-0 bg-white rounded-2xl px-6 py-4 text-center shadow-sm">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Tagihan / Bulan</p>
                <p class="text-2xl font-black text-slate-900 font-mono mt-1">Rp {{ number_format($pelanggan->paket->harga ?? 0, 0, ',', '.') }}</p>
                @if($pelanggan->paket)
                    <span class="inline-block mt-2 px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-[11px] font-bold">✓ Aktif</span>
                @endif
            </div>
        </div>
    </div>
@endif

<!-- Daftar Semua Paket Tersedia -->
<div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-6">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h3 class="font-bold text-base text-slate-900">Paket Tersedia</h3>
            <p class="text-xs text-slate-400 mt-0.5">Pilih paket yang paling sesuai dengan kebutuhan kamu</p>
        </div>
        <span class="text-xs font-bold text-slate-500 bg-slate-100 px-3 py-1.5 rounded-full">{{ $pakets->count() }} paket</span>
    </div>

    @if($pakets->isEmpty())
        <div class="py-10 text-center text-slate-400">
            <span class="text-3xl">📦</span>
            <p class="text-sm font-semibold text-slate-600 mt-2">Belum ada paket tersedia</p>
            <p class="text-xs text-slate-400">Admin belum menambahkan paket internet.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($pakets as $paket)
                @php $isActive = $pelanggan && $pelanggan->paket_id === $paket->id; @endphp
                <div class="rounded-2xl border-2 p-5 flex flex-col transition {{ $isActive ? 'border-[#2563EB] bg-blue-50/50 shadow-md shadow-blue-500/10' : 'border-slate-200 bg-white hover:border-slate-300 hover:shadow-sm' }}">
                    @if($isActive)
                        <span class="inline-block w-fit px-2.5 py-1 rounded-full bg-[#2563EB] text-white text-[10px] font-bold mb-3">✓ Paket Kamu</span>
                    @endif
                    <h4 class="font-extrabold text-slate-900 text-base leading-tight">{{ $paket->nama_paket }}</h4>
                    <p class="text-xs font-bold text-[#2563EB] mt-1">{{ $paket->kecepatan ?? '-' }}</p>
                    @if($paket->deskripsi)
                        <p class="text-xs text-slate-500 mt-2 line-clamp-2">{{ $paket->deskripsi }}</p>
                    @endif
                    <div class="mt-4">
                        <span class="text-xl font-black text-slate-900 font-mono">Rp {{ number_format($paket->harga, 0, ',', '.') }}</span>
                        <span class="text-xs text-slate-400">/bulan</span>
                    </div>
                    <div class="mt-4 flex-1"></div>
                    @if($isActive)
                        <button disabled class="w-full py-2.5 rounded-xl bg-slate-200 text-slate-500 text-xs font-bold cursor-not-allowed">Paket Aktif</button>
                    @else
                        <form method="POST" action="/portal/paket/pilih" onsubmit="return confirm('{{ $pelanggan && $pelanggan->paket_id ? 'Ganti' : 'Pilih' }} paket ke {{ $paket->nama_paket }} (Rp {{ number_format($paket->harga, 0, ',', '.') }}/bln)?')">
                            @csrf
                            <input type="hidden" name="paket_id" value="{{ $paket->id }}">
                            <button type="submit" class="w-full py-2.5 rounded-xl {{ $pelanggan && $pelanggan->paket_id ? 'border-2 border-[#2563EB] text-[#2563EB] hover:bg-blue-50' : 'bg-[#2563EB] hover:bg-blue-700 text-white shadow-md shadow-blue-500/20' }} text-xs font-bold transition">
                                {{ $pelanggan && $pelanggan->paket_id ? 'Ganti ke Paket Ini' : 'Pilih Paket Ini' }}
                            </button>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
