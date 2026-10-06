@extends('layouts.portal')

@section('title', 'Tagihan Saya')
@section('page_title', 'Tagihan Saya')
@section('page_subtitle', 'Lihat dan bayar semua tagihan internet kamu')

@section('content')
<!-- Ringkasan -->
<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
    <div class="bg-gradient-to-r from-rose-500 to-orange-500 rounded-2xl p-5 text-white shadow-lg shadow-rose-500/20">
        <p class="text-xs font-medium text-white/80">Total Belum Dibayar</p>
        <p class="text-2xl font-extrabold font-mono mt-1">Rp {{ number_format($totalBelumBayar, 0, ',', '.') }}</p>
        <a href="?status=belum_bayar" class="inline-block mt-2 text-[11px] font-bold bg-white/20 hover:bg-white/30 px-3 py-1.5 rounded-lg transition">Lihat yang belum bayar</a>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200/70 p-5 shadow-sm flex flex-col justify-center">
        <p class="text-xs font-medium text-slate-400">Paket Saat Ini</p>
        <p class="text-lg font-extrabold text-slate-900 mt-1">{{ $pelanggan->paket->nama_paket ?? 'Belum ada paket' }}</p>
        <p class="text-[11px] text-slate-400">{{ $pelanggan->paket->kecepatan ?? '-' }} &bull; Rp {{ number_format($pelanggan->paket->harga ?? 0, 0, ',', '.') }}/bln</p>
    </div>
</div>

<!-- Filter -->
<div class="bg-white rounded-2xl border border-slate-200/70 p-4 shadow-sm">
    <form method="GET" action="/portal/tagihan" class="flex flex-wrap items-center gap-2">
        <a href="/portal/tagihan" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ !request('status') ? 'bg-[#2563EB] text-white' : 'border border-slate-200 text-slate-600 hover:bg-slate-50' }}">Semua</a>
        <a href="/portal/tagihan?status=belum_bayar" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ request('status') === 'belum_bayar' ? 'bg-[#2563EB] text-white' : 'border border-slate-200 text-slate-600 hover:bg-slate-50' }}">Belum Bayar</a>
        <a href="/portal/tagihan?status=lunas" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ request('status') === 'lunas' ? 'bg-[#2563EB] text-white' : 'border border-slate-200 text-slate-600 hover:bg-slate-50' }}">Lunas</a>
    </form>
</div>

<!-- Tabel -->
<div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="text-[11px] text-slate-400 uppercase tracking-wider font-bold border-b border-slate-100">
                    <th class="px-6 py-3">Nomor Tagihan</th>
                    <th class="px-6 py-3">Periode</th>
                    <th class="px-6 py-3">Jatuh Tempo</th>
                    <th class="px-6 py-3">Jumlah</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tagihan as $t)
                <tr class="border-b border-slate-50 hover:bg-slate-50/60 transition">
                    <td class="px-6 py-4 font-semibold text-[13px] text-slate-900">{{ $t->nomor_tagihan }}</td>
                    <td class="px-6 py-4 text-[13px] text-slate-500">
                        {{ $t->tanggal_terbit instanceof \Carbon\Carbon ? $t->tanggal_terbit->format('M Y') : $t->tanggal_terbit }}<br>
                        <span class="text-[11px] text-slate-400">{{ $t->keterangan ?? '' }}</span>
                    </td>
                    <td class="px-6 py-4 text-[13px] text-slate-500">{{ $t->jatuh_tempo instanceof \Carbon\Carbon ? $t->jatuh_tempo->format('d M Y') : $t->jatuh_tempo }}</td>
                    <td class="px-6 py-4 text-[13px] font-bold text-slate-900 font-mono">Rp {{ number_format($t->jumlah, 0, ',', '.') }}</td>
                    <td class="px-6 py-4">
                        @if($t->status === 'lunas')
                            <span class="bg-[#F0FDF4] text-[#059669] inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border border-[#D1FAE5]"><span class="w-1.5 h-1.5 rounded-full bg-[#10B981]"></span> Lunas</span>
                            @if($t->tanggal_bayar)<span class="block text-[10px] text-slate-400 mt-1">{{ $t->tanggal_bayar instanceof \Carbon\Carbon ? $t->tanggal_bayar->format('d M Y') : $t->tanggal_bayar }}</span>@endif
                        @else
                            <span class="bg-[#FFFBEB] text-[#D97706] inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border border-[#FEF3C7]"><span class="w-1.5 h-1.5 rounded-full bg-[#F59E0B]"></span> Belum Bayar</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            @if($t->status !== 'lunas')
                                <form method="POST" action="/portal/tagihan/{{ $t->id }}/bayar" onsubmit="return confirm('Konfirmasi pembayaran {{ $t->nomor_tagihan }} sebesar Rp {{ number_format($t->jumlah, 0, ',', '.') }}?')">
                                    @csrf
                                    <input type="hidden" name="metode" value="transfer_bank">
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-[#2563EB] hover:bg-blue-700 text-white text-[11px] font-bold transition">💳 Bayar</button>
                                </form>
                            @endif
                            <a href="/portal/tagihan/{{ $t->id }}/cetak" target="_blank" class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-600 text-[11px] font-bold inline-block">🖨️ Invoice</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-12 text-center"><div class="max-w-xs mx-auto space-y-2"><span class="text-3xl">🧾</span><p class="text-sm font-semibold text-slate-600">Belum ada tagihan</p><p class="text-xs text-slate-400">Tagihan bulanan akan muncul di sini setiap periode berjalan.</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($tagihan->hasPages())
        <div class="p-4 border-t border-slate-100">{{ $tagihan->links() }}</div>
    @endif
</div>
@endsection
