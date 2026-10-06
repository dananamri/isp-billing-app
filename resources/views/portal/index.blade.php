@extends('layouts.portal')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'Pantau paket, tagihan, dan pembayaran kamu')

@section('content')
<!-- Banner Sambutan -->
<div class="bg-gradient-to-r from-[#1D4ED8] via-[#2563EB] to-[#3B82F6] rounded-2xl px-7 py-6 text-white relative overflow-hidden shadow-lg shadow-blue-600/20">
    <div class="absolute -right-10 -top-16 w-56 h-56 bg-white/10 rounded-full"></div>
    <div class="absolute right-24 -bottom-20 w-40 h-40 bg-white/10 rounded-full"></div>
    <div class="relative">
        <h2 class="text-xl font-bold">Selamat Datang{{ $pelanggan ? ', ' . explode(' ', $pelanggan->nama)[0] : '' }} 👋</h2>
        <p class="text-blue-100 text-sm mt-1">Kelola paket, tagihan, dan riwayat pembayaran kamu melalui satu portal.</p>
    </div>
</div>

@if(!$pelanggan)
    <div class="bg-amber-50 border border-amber-200 text-amber-800 text-sm px-4 py-3 rounded-xl">Data pelanggan belum terdaftar. Hubungi admin.</div>
@else
    @if($pelanggan->status === 'menunggu')
        <div class="bg-amber-50 border border-amber-200 text-amber-800 text-sm px-4 py-4 rounded-xl flex gap-3">
            <span class="text-lg">⏳</span>
            <div>
                <p class="font-bold">Akun menunggu verifikasi admin</p>
                <p class="text-xs mt-0.5">Pendaftaran kamu sedang ditinjau. Layanan akan aktif setelah disetujui.</p>
            </div>
        </div>
    @endif

    @if($tagihanBelumBayar > 0)
        <div class="bg-rose-50 border border-rose-200 text-rose-800 text-sm px-4 py-3 rounded-xl flex items-center justify-between gap-2">
            <span>⚠️ Kamu punya <strong>{{ $tagihanBelumBayar }} tagihan belum dibayar</strong>. Segera lunasi agar layanan tidak terganggu.</span>
            <a href="/portal/tagihan?status=belum_bayar" class="shrink-0 bg-rose-600 hover:bg-rose-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold">Bayar Sekarang</a>
        </div>
    @endif
@endif

<!-- Statistik: 4 Kartu -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">
    <div class="bg-white rounded-2xl border border-slate-200/70 p-5 shadow-sm">
        <p class="text-xs font-medium text-slate-400">Paket Aktif</p>
        <p class="text-[18px] font-extrabold text-slate-900 tracking-tight mt-1.5 truncate">{{ $pelanggan?->paket->nama_paket ?? 'Belum ada paket' }}</p>
        <p class="text-[11px] font-semibold mt-2 flex items-center gap-1.5">
            @php $st = $pelanggan->status ?? 'menunggu'; @endphp
            @if($st === 'aktif')
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span><span class="text-emerald-600">Status: Aktif</span>
            @elseif($st === 'menunggu')
                <span class="w-2 h-2 rounded-full bg-amber-500"></span><span class="text-amber-600">Status: Menunggu</span>
            @else
                <span class="w-2 h-2 rounded-full bg-rose-500"></span><span class="text-rose-600">Status: {{ ucfirst($st) }}</span>
            @endif
        </p>
    </div>

    @php $tagihanBulanIni = $tagihan->first(); @endphp
    <div class="bg-white rounded-2xl border border-slate-200/70 p-5 shadow-sm">
        <p class="text-xs font-medium text-slate-400">Tagihan Terakhir</p>
        <p class="text-[22px] font-extrabold text-slate-900 tracking-tight mt-1.5 font-mono">{{ $tagihanBulanIni ? 'Rp ' . number_format($tagihanBulanIni->jumlah, 0, ',', '.') : 'Rp 0' }}</p>
        <p class="text-[11px] font-semibold text-slate-400 mt-2 flex items-center gap-1 truncate">{{ $tagihanBulanIni ? 'Jatuh tempo ' . ($tagihanBulanIni->jatuh_tempo instanceof \Carbon\Carbon ? $tagihanBulanIni->jatuh_tempo->format('d M Y') : $tagihanBulanIni->jatuh_tempo) : 'Belum ada tagihan' }}</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/70 p-5 shadow-sm">
        <p class="text-xs font-medium text-slate-400">Tagihan Belum Dibayar</p>
        <p class="text-[26px] font-extrabold {{ $tagihanBelumBayar > 0 ? 'text-rose-500' : 'text-slate-900' }} tracking-tight mt-1.5">{{ $tagihanBelumBayar }}</p>
        <p class="text-[11px] font-semibold {{ $tagihanBelumBayar > 0 ? 'text-rose-500' : 'text-emerald-600' }} mt-2">{{ $tagihanBelumBayar > 0 ? 'Perlu diselesaikan' : 'Semua tagihan lunas' }}</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/70 p-5 shadow-sm">
        <p class="text-xs font-medium text-slate-400">Kecepatan</p>
        <p class="text-[20px] font-extrabold text-slate-900 tracking-tight mt-1.5 truncate">{{ $pelanggan?->paket->kecepatan ?? '-' }}</p>
        <p class="text-[11px] font-semibold text-emerald-600 mt-2">Unlimited kuota</p>
    </div>
</div>

<!-- Layout Bawah (Tabel & Status) -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm lg:col-span-2 overflow-hidden flex flex-col">
        <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center">
            <div>
                <h3 class="font-bold text-base text-slate-900">Transaksi Terbaru</h3>
                <p class="text-xs text-slate-400 mt-0.5">Riwayat pembayaran terakhir kamu</p>
            </div>
            <a href="/portal/tagihan" class="text-xs font-bold text-[#2563EB] hover:underline">Lihat Semua</a>
        </div>
        <div class="flex-1 overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[11px] text-slate-400 uppercase tracking-wider font-bold border-b border-slate-100">
                        <th class="px-6 py-3">Tagihan</th>
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
                        <td class="px-6 py-4 text-[13px] text-slate-500">{{ $t->jatuh_tempo instanceof \Carbon\Carbon ? $t->jatuh_tempo->format('d M Y') : $t->jatuh_tempo }}</td>
                        <td class="px-6 py-4 text-[13px] font-bold text-slate-900 font-mono">Rp {{ number_format($t->jumlah, 0, ',', '.') }}</td>
                        <td class="px-6 py-4">
                            @if($t->status === 'lunas')
                                <span class="bg-[#F0FDF4] text-[#059669] inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border border-[#D1FAE5]"><span class="w-1.5 h-1.5 rounded-full bg-[#10B981]"></span> Lunas</span>
                            @else
                                <span class="bg-[#FFFBEB] text-[#D97706] inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border border-[#FEF3C7]"><span class="w-1.5 h-1.5 rounded-full bg-[#F59E0B]"></span> Belum Bayar</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($t->status !== 'lunas')
                                <form method="POST" action="/portal/tagihan/{{ $t->id }}/bayar" onsubmit="return confirm('Konfirmasi pembayaran {{ $t->nomor_tagihan }} sebesar Rp {{ number_format($t->jumlah, 0, ',', '.') }}?')">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-[#2563EB] hover:bg-blue-700 text-white text-[11px] font-bold">Bayar</button>
                                </form>
                            @else
                                <a href="/portal/tagihan/{{ $t->id }}/cetak" target="_blank" class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-600 text-[11px] font-bold inline-block">Invoice</a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-6 py-10 text-center text-sm text-slate-400">Belum ada tagihan</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @php
        $kecepatanRaw = $pelanggan?->paket->kecepatan ?? '-';
        $down = $kecepatanRaw; $up = '-';
        if (str_contains($kecepatanRaw, '/')) { [$down, $up] = array_map('trim', explode('/', $kecepatanRaw, 2)); }
        elseif ($kecepatanRaw !== '-' && $kecepatanRaw !== '') { $up = $kecepatanRaw; }
        $isAktif = ($pelanggan->status ?? '') === 'aktif';
    @endphp
    <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-6 flex flex-col">
        <div class="mb-6">
            <h3 class="font-bold text-base text-slate-900">Status Layanan</h3>
            <p class="text-xs text-slate-400 mt-0.5">Kondisi layanan kamu saat ini</p>
        </div>
        <div class="space-y-6 flex-1">
            <div>
                <div class="flex justify-between items-center text-xs mb-2"><span class="font-semibold text-slate-600">Download</span><span class="font-bold text-slate-900">{{ $down }}</span></div>
                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden"><div class="bg-[#2563EB] h-full rounded-full" style="width: {{ $isAktif ? '100' : '0' }}%"></div></div>
            </div>
            <div>
                <div class="flex justify-between items-center text-xs mb-2"><span class="font-semibold text-slate-600">Upload</span><span class="font-bold text-slate-900">{{ $up }}</span></div>
                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden"><div class="bg-[#60A5FA] h-full rounded-full" style="width: {{ $isAktif ? '50' : '0' }}%"></div></div>
            </div>
            <div>
                <div class="flex justify-between items-center text-xs mb-2"><span class="font-semibold text-slate-600">Uptime</span><span class="font-bold text-slate-900">{{ $isAktif ? '99,8%' : '-' }}</span></div>
                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden"><div class="bg-[#10B981] h-full rounded-full" style="width: {{ $isAktif ? '99' : '0' }}%"></div></div>
            </div>
        </div>
        @if($isAktif)
            <div class="mt-6 bg-[#F0FDF4] border border-[#D1FAE5] rounded-xl px-4 py-3 flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-[#10B981] flex items-center justify-center shrink-0"><svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg></div>
                <div><p class="text-xs font-bold text-[#047857]">Layanan Aktif</p><p class="text-[11px] text-[#059669]">Koneksi berjalan normal</p></div>
            </div>
        @elseif(($pelanggan->status ?? '') === 'menunggu')
            <div class="mt-6 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-amber-500 flex items-center justify-center shrink-0 text-white text-sm">⏳</div>
                <div><p class="text-xs font-bold text-amber-700">Menunggu Aktivasi</p><p class="text-[11px] text-amber-600">Admin akan mengaktifkan layanan kamu</p></div>
            </div>
        @else
            <div class="mt-6 bg-rose-50 border border-rose-200 rounded-xl px-4 py-3 flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-rose-500 flex items-center justify-center shrink-0 text-white font-bold text-sm">!</div>
                <div><p class="text-xs font-bold text-rose-700">Layanan Nonaktif</p><p class="text-[11px] text-rose-600">Hubungi admin untuk bantuan</p></div>
            </div>
        @endif
    </div>
</div>
@endsection
