@extends('layouts.portal')

@section('title', 'Riwayat Pembayaran')
@section('page_title', 'Pembayaran')
@section('page_subtitle', 'Riwayat seluruh transaksi pembayaran yang pernah kamu lakukan')

@section('content')
<!-- Ringkasan Total Terbayar -->
<div class="bg-gradient-to-r from-emerald-500 to-teal-600 rounded-2xl p-6 text-white shadow-lg shadow-emerald-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <p class="text-xs font-medium text-white/80">Akumulasi Pembayaran Sukses</p>
        <p class="text-2xl sm:text-3xl font-extrabold font-mono mt-1">Rp {{ number_format($totalTerbayar, 0, ',', '.') }}</p>
        <p class="text-[11px] text-white/80 mt-1">Total seluruh pembayaran yang tercatat dalam sistem</p>
    </div>
    <a href="/portal/tagihan" class="shrink-0 bg-white hover:bg-slate-50 text-emerald-700 font-bold px-4 py-2.5 rounded-xl text-xs transition shadow-sm">
        Cek Tagihan Aktif &rarr;
    </a>
</div>

<!-- Tabel Riwayat Pembayaran -->
<div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
        <h3 class="font-bold text-base text-slate-900">Riwayat Transaksi</h3>
        <span class="text-xs text-slate-400">Total: {{ $pembayaran->total() }} transaksi</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="text-[11px] text-slate-400 uppercase tracking-wider font-bold border-b border-slate-100 bg-slate-50">
                    <th class="px-6 py-3">No. Referensi</th>
                    <th class="px-6 py-3">Tagihan Terkait</th>
                    <th class="px-6 py-3">Tanggal Bayar</th>
                    <th class="px-6 py-3">Metode</th>
                    <th class="px-6 py-3">Nominal</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3 text-center">Bukti</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pembayaran as $p)
                <tr class="border-b border-slate-50 hover:bg-slate-50/60 transition text-xs">
                    <td class="px-6 py-4 font-mono font-bold text-slate-900">{{ $p->referensi ?? '-' }}</td>
                    <td class="px-6 py-4 font-semibold text-slate-700">{{ $p->tagihan->nomor_tagihan ?? '-' }}</td>
                    <td class="px-6 py-4 text-slate-500">{{ $p->tanggal_bayar instanceof \Carbon\Carbon ? $p->tanggal_bayar->format('d M Y') : $p->tanggal_bayar }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-block px-2.5 py-1 rounded-md bg-slate-100 text-slate-700 font-semibold uppercase text-[10px]">
                            {{ str_replace('_', ' ', $p->metode_pembayaran) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 font-bold font-mono text-slate-900">Rp {{ number_format($p->jumlah, 0, ',', '.') }}</td>
                    <td class="px-6 py-4">
                        @if($p->status === 'berhasil' || $p->status === 'lunas')
                            <span class="bg-emerald-50 text-emerald-700 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Sukses
                            </span>
                        @elseif($p->status === 'menunggu')
                            <span class="bg-amber-50 text-amber-700 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border border-amber-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Menunggu
                            </span>
                        @else
                            <span class="bg-rose-50 text-rose-700 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border border-rose-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Gagal
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($p->tagihan_id)
                            <a href="/portal/tagihan/{{ $p->tagihan_id }}/cetak" target="_blank" class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-600 text-[11px] font-bold inline-block">
                                🖨️ Cetak
                            </a>
                        @else
                            <span class="text-slate-300">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                        <div class="max-w-xs mx-auto space-y-2">
                            <span class="text-3xl">💳</span>
                            <p class="text-sm font-semibold text-slate-600">Belum ada riwayat pembayaran</p>
                            <p class="text-xs text-slate-400">Setiap pembayaran tagihan yang berhasil akan tercatat di sini.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pembayaran->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $pembayaran->links() }}
        </div>
    @endif
</div>
@endsection
