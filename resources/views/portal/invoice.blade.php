<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $tagihan->nomor_tagihan }} | {{ $tenant->name ?? 'SATAK' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 p-4 sm:p-8 text-slate-800 antialiased">

    <!-- Top Action Bar -->
    <div class="no-print max-w-3xl mx-auto mb-4 flex items-center justify-between">
        <a href="/portal/tagihan" class="text-xs font-bold text-slate-600 hover:text-slate-900">&larr; Kembali ke Tagihan</a>
        <div class="flex items-center gap-2">
            <button onclick="window.close()" class="px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-700">Tutup</button>
            <button onclick="window.print()" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-500/20">🖨️ Cetak Invoice</button>
        </div>
    </div>

    <!-- Invoice Sheet -->
    <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-sm border border-slate-200 p-8 sm:p-12 space-y-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white font-black text-lg">S</div>
                <div>
                    <h1 class="text-lg font-black text-slate-900 leading-tight uppercase">{{ $tenant->name ?? 'SATAK Solusi Internet' }}</h1>
                    <p class="text-xs text-slate-400">{{ $tenant->address ?? 'Penyedia Layanan Internet Terpercaya' }}</p>
                    @if($tenant->phone)<p class="text-[11px] text-slate-400">Telp: {{ $tenant->phone }}</p>@endif
                </div>
            </div>
            <div class="text-left sm:text-right">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-widest block">INVOICE</span>
                <span class="text-base font-mono font-bold text-slate-900 block">#{{ $tagihan->nomor_tagihan }}</span>
                <span class="inline-block mt-2 px-3 py-1 rounded-full text-xs font-bold {{ $tagihan->status === 'lunas' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                    {{ strtoupper($tagihan->status === 'lunas' ? 'LUNAS' : 'BELUM DIBAYAR') }}
                </span>
            </div>
        </div>

        <!-- Info Penerima & Tanggal -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
            <div>
                <p class="font-bold text-slate-400 uppercase tracking-wider text-[10px] mb-1">Ditagihkan Kepada:</p>
                <p class="font-extrabold text-sm text-slate-900">{{ $pelanggan->nama }}</p>
                <p class="text-slate-600 mt-0.5">{{ $pelanggan->telepon ?? '-' }}</p>
                <p class="text-slate-500 mt-0.5">{{ $pelanggan->alamat ?? 'Alamat tidak tercatat' }}</p>
            </div>
            <div class="space-y-1.5 sm:text-right">
                <div><span class="text-slate-400">Tanggal Terbit:</span> <strong class="text-slate-700">{{ $tagihan->tanggal_terbit instanceof \Carbon\Carbon ? $tagihan->tanggal_terbit->format('d M Y') : $tagihan->tanggal_terbit }}</strong></div>
                <div><span class="text-slate-400">Jatuh Tempo:</span> <strong class="text-slate-700">{{ $tagihan->jatuh_tempo instanceof \Carbon\Carbon ? $tagihan->jatuh_tempo->format('d M Y') : $tagihan->jatuh_tempo }}</strong></div>
                @if($tagihan->tanggal_bayar)
                    <div><span class="text-slate-400">Tanggal Bayar:</span> <strong class="text-emerald-700">{{ $tagihan->tanggal_bayar instanceof \Carbon\Carbon ? $tagihan->tanggal_bayar->format('d M Y') : $tagihan->tanggal_bayar }}</strong></div>
                @endif
                @if($pembayaran)
                    <div><span class="text-slate-400">Ref Transaksi:</span> <strong class="text-slate-700 font-mono">{{ $pembayaran->referensi ?? '-' }}</strong></div>
                @endif
            </div>
        </div>

        <!-- Rincian Layanan -->
        <div class="border border-slate-200 rounded-xl overflow-hidden">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-600 font-bold uppercase tracking-wider text-[10px] border-b border-slate-200">
                    <tr>
                        <th class="p-3 pl-4">Deskripsi Layanan</th>
                        <th class="p-3 text-center">Kecepatan</th>
                        <th class="p-3 text-right pr-4">Jumlah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr>
                        <td class="p-4 pl-4 font-semibold text-slate-800">
                            Langganan Internet: {{ $pelanggan->paket->nama_paket ?? 'Paket Dedicated' }}
                            <span class="block text-[11px] text-slate-400 font-normal mt-0.5">{{ $tagihan->keterangan ?? 'Biaya langganan bulanan' }}</span>
                        </td>
                        <td class="p-4 text-center text-slate-600 font-mono">{{ $pelanggan->paket->kecepatan ?? '-' }}</td>
                        <td class="p-4 text-right pr-4 font-mono font-bold text-slate-900">Rp {{ number_format($tagihan->jumlah, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
                <tfoot class="bg-slate-50 border-t border-slate-200 font-bold">
                    <tr>
                        <td colspan="2" class="p-3 pl-4 text-slate-600 uppercase text-[11px]">Total Tagihan</td>
                        <td class="p-3 text-right pr-4 text-sm font-mono text-blue-700">Rp {{ number_format($tagihan->jumlah, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Footer / Tanda Tangan -->
        <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row justify-between items-end gap-6 text-xs text-slate-400">
            <div>
                <p class="font-bold text-slate-700">Catatan:</p>
                <p class="text-[11px] mt-0.5">Terima kasih atas pembayaran Anda. Simpan bukti invoice ini sebagai tanda terima sah.</p>
            </div>
            <div class="text-center sm:text-right">
                <p class="text-[11px] font-semibold text-slate-600">{{ $tenant->name ?? 'SATAK Solusi Internet' }}</p>
                <div class="h-10"></div>
                <p class="text-[10px] text-slate-400 border-t border-slate-200 pt-1">Finance & Billing Department</p>
            </div>
        </div>
    </div>

</body>
</html>
