<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Voucher Hotspot - {{ $tenant->name ?? 'SATAK' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                padding: 0 !important;
            }
            .voucher-card {
                break-inside: avoid;
                page-break-inside: avoid;
            }
        }
        @page {
            size: auto;
            margin: 8mm;
        }
    </style>
</head>
<body class="bg-slate-100 p-4 sm:p-6 font-sans text-slate-800 antialiased">

    <!-- Top Action Bar (Hanya tampil di layar) -->
    <div class="no-print max-w-5xl mx-auto mb-6 bg-white p-4 rounded-2xl shadow-sm border border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3">
        <div>
            <h1 class="font-bold text-slate-800 text-sm md:text-base">Pratinjau Cetak Voucher ({{ count($vouchers) }} Kartu)</h1>
            <p class="text-xs text-slate-400">Siap dicetak ke printer standar A4 / thermal</p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.close()" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold transition">
                Tutup
            </button>
            <button onclick="window.print()" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-md shadow-blue-500/20 transition flex items-center gap-1.5">
                <span>🖨️</span> Cetak Sekarang
            </button>
        </div>
    </div>

    <!-- Grid Kartu Voucher -->
    <div class="max-w-5xl mx-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
        @forelse($vouchers as $v)
        <div class="voucher-card bg-white rounded-xl border-2 border-dashed border-slate-300 p-3 flex flex-col justify-between shadow-sm relative overflow-hidden">
            <!-- Header Kartu -->
            <div class="border-b border-slate-100 pb-2 mb-2 flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-black tracking-wider text-blue-600 leading-tight uppercase">{{ $tenant->name ?? 'SATAK HOTSPOT' }}</p>
                    <p class="text-[9px] text-slate-400 font-medium">INTERNET CEPAT & STABIL</p>
                </div>
                <span class="text-xs font-bold px-2 py-0.5 rounded bg-blue-50 text-blue-700 text-[10px]">
                    {{ $v->profile->validity ?? 'WiFi' }}
                </span>
            </div>

            <!-- Kode Voucher -->
            <div class="my-2 text-center bg-slate-50 border border-slate-200 rounded-lg p-2.5">
                <span class="text-[9px] uppercase tracking-wider text-slate-400 block font-semibold">Kode Voucher</span>
                <span class="text-base font-black font-mono tracking-widest text-slate-900 block select-all">
                    {{ $v->code }}
                </span>
                @if($v->password && $v->password !== $v->code)
                    <div class="mt-1 pt-1 border-t border-slate-200 text-[10px] text-slate-600 font-mono">
                        Password: <strong class="text-slate-900">{{ $v->password }}</strong>
                    </div>
                @endif
            </div>

            <!-- Info Paket & Harga -->
            <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[10px]">
                <div class="text-slate-500">
                    <span class="font-bold text-slate-800 block text-[11px]">{{ $v->profile->name ?? 'Hotspot' }}</span>
                    <span>{{ $v->profile->rate_limit ?? 'Up to speed' }}</span>
                </div>
                <div class="text-right">
                    <span class="text-[9px] text-slate-400 block">Tarif</span>
                    <span class="font-extrabold text-blue-700 text-xs font-mono">
                        Rp {{ number_format($v->profile->price ?? 0, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <!-- Footer Login Info -->
            <div class="mt-2 pt-1 border-t border-slate-50 text-center">
                <p class="text-[8px] text-slate-400">Hubungkan WiFi &rarr; Buka browser &rarr; Masukkan kode</p>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white p-8 rounded-2xl text-center text-slate-400">
            Tidak ada voucher untuk dicetak
        </div>
        @endforelse
    </div>

</body>
</html>
