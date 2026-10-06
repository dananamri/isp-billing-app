@extends('layouts.app')

@section('title', 'Daftar Voucher Hotspot')

@section('content')
<header class="bg-white shadow-sm py-4 md:py-5 px-4 md:px-8 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
    <div>
        <h2 class="text-xl md:text-2xl font-bold text-gray-800">Daftar Voucher Hotspot</h2>
        <p class="text-gray-500 text-xs md:text-sm">Kelola, generate, dan cetak voucher internet hotspot</p>
    </div>
    <div class="flex items-center gap-2">
        @if(request('batch'))
            <a href="/hotspot/voucher/print?batch={{ request('batch') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-blue-200 bg-blue-50 text-blue-700 hover:bg-blue-100 text-xs md:text-sm font-semibold transition shadow-sm">
                <span></span> Cetak Batch Ini ({{ request('batch') }})
            </a>
        @elseif($batches->isNotEmpty())
            <a href="/hotspot/voucher/print?batch={{ $batches->first() }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs md:text-sm font-semibold transition shadow-sm">
                <span></span> Cetak Batch Terbaru
            </a>
        @endif
        <button onclick="openModal('modal-generate')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-[#00D2B4] via-[#0066FF] to-[#0052FF] text-white text-xs md:text-sm font-semibold shadow-md shadow-blue-500/20 hover:opacity-90 transition">
            <span></span> Generate Voucher
        </button>
    </div>
</header>

<div class="p-4 md:p-8 space-y-6">

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm px-4 py-3 rounded-xl flex items-center justify-between gap-2 shadow-sm">
            <div class="flex items-center gap-2">
                <span class="text-base">✅</span>
                <span>{{ session('success') }}</span>
            </div>
            @if(session('batch_code') || request('batch'))
                <a href="/hotspot/voucher/print?batch={{ session('batch_code') ?? request('batch') }}" target="_blank" class="text-xs font-bold underline hover:text-emerald-950">
                    Cetak Sekarang &rarr;
                </a>
            @endif
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-800 text-sm px-4 py-3 rounded-xl flex items-center gap-2 shadow-sm">
            <span class="text-base">⚠️</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-800 text-sm px-4 py-3 rounded-xl shadow-sm">
            <ul class="list-disc list-inside space-y-1 text-xs">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- 4 KARTU STATISTIK -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 md:gap-5">
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500">Total Voucher</span>
                <span class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 grid place-items-center text-sm"></span>
            </div>
            <p class="text-2xl font-black text-slate-800 mt-2">{{ number_format($totalVouchers) }}</p>
            <p class="text-[11px] text-slate-400 mt-1">Seluruh voucher dibuat</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500">Tersedia (Belum Dipakai)</span>
                <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 grid place-items-center text-sm"></span>
            </div>
            <p class="text-2xl font-black text-emerald-600 mt-2">{{ number_format($unusedCount) }}</p>
            <p class="text-[11px] text-slate-400 mt-1">Siap dijual ke pelanggan</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500">Terpakai (Aktif/Used)</span>
                <span class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 grid place-items-center text-sm"></span>
            </div>
            <p class="text-2xl font-black text-indigo-600 mt-2">{{ number_format($usedCount) }}</p>
            <p class="text-[11px] text-slate-400 mt-1">Sedang/pernah digunakan</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500">Omzet Voucher Terpakai</span>
                <span class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 grid place-items-center text-sm"></span>
            </div>
            <p class="text-xl font-black text-slate-800 mt-2 font-mono">Rp {{ number_format($potentialRevenue, 0, ',', '.') }}</p>
            <p class="text-[11px] text-slate-400 mt-1">Akumulasi penjualan</p>
        </div>
    </div>

    <!-- FILTER & TOOLBAR -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-sm">
        <form method="GET" action="/hotspot/voucher" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari kode / batch..." class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-[#0066FF]">
            </div>

            <div>
                <select name="profile_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-[#0066FF] text-slate-600 bg-white">
                    <option value="">Semua Profil</option>
                    @foreach($profiles as $prof)
                        <option value="{{ $prof->id }}" {{ request('profile_id') == $prof->id ? 'selected' : '' }}>
                            {{ $prof->name }} (Rp {{ number_format($prof->price, 0, ',', '.') }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-[#0066FF] text-slate-600 bg-white">
                    <option value="">Semua Status</option>
                    <option value="unused" {{ request('status') === 'unused' ? 'selected' : '' }}>Tersedia (Unused)</option>
                    <option value="used" {{ request('status') === 'used' ? 'selected' : '' }}>Terpakai (Used)</option>
                    <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Kedaluwarsa (Expired)</option>
                </select>
            </div>

            <div>
                <select name="batch" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-[#0066FF] text-slate-600 bg-white">
                    <option value="">Semua Batch</option>
                    @foreach($batches as $b)
                        <option value="{{ $b }}" {{ request('batch') === $b ? 'selected' : '' }}>{{ $b }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold transition">
                    Filter
                </button>
                @if(request()->hasAny(['q', 'profile_id', 'status', 'batch']))
                    <a href="/hotspot/voucher" class="px-3 py-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 text-xs font-medium">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- TABEL VOUCHER -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="font-bold text-slate-800 text-base">Daftar Voucher</h3>
                <p class="text-xs text-slate-400">Menampilkan {{ $vouchers->total() }} total data</p>
            </div>

            @if($unusedCount > 0)
                <div class="flex items-center gap-2">
                    <form method="POST" action="/hotspot/voucher/delete-batch" onsubmit="return confirm('Yakin ingin membersihkan seluruh voucher yang BELUM dipakai?')">
                        @csrf
                        <input type="hidden" name="delete_all_unused" value="1">
                        <button type="submit" class="px-3 py-1.5 rounded-lg border border-rose-200 text-rose-600 hover:bg-rose-50 text-xs font-semibold transition">
                            🗑️ Bersihkan Unused
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider">
                    <tr>
                        <th class="text-left p-3.5 pl-5">Kode / Username</th>
                        <th class="text-left p-3.5">Password</th>
                        <th class="text-left p-3.5">Profil Paket</th>
                        <th class="text-left p-3.5">Masa Aktif</th>
                        <th class="text-left p-3.5">Harga</th>
                        <th class="text-left p-3.5">Status</th>
                        <th class="text-left p-3.5">Batch</th>
                        <th class="text-center p-3.5 pr-5 w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($vouchers as $v)
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="p-3.5 pl-5 font-mono font-bold text-slate-900 text-[13px]">
                            <span class="bg-slate-100 px-2.5 py-1 rounded-md tracking-wider border border-slate-200">{{ $v->code }}</span>
                        </td>
                        <td class="p-3.5 font-mono text-slate-600">
                            {{ $v->password ?? '-' }}
                        </td>
                        <td class="p-3.5 font-semibold text-slate-800">
                            {{ $v->profile->name ?? 'Default' }}
                            @if($v->profile && $v->profile->rate_limit)
                                <span class="block text-[10px] text-slate-400 font-normal">{{ $v->profile->rate_limit }}</span>
                            @endif
                        </td>
                        <td class="p-3.5 text-slate-600">
                            {{ $v->profile->validity ?? '-' }}
                        </td>
                        <td class="p-3.5 font-bold font-mono text-slate-900">
                            Rp {{ number_format($v->profile->price ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="p-3.5">
                            @if($v->status === 'unused')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Tersedia
                                </span>
                            @elseif($v->status === 'used')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> Terpakai
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Expired
                                </span>
                            @endif
                        </td>
                        <td class="p-3.5 text-slate-400 text-[11px] font-mono">
                            {{ $v->batch_code ?? '-' }}
                        </td>
                        <td class="p-3.5 pr-5 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="/hotspot/voucher/print?id={{ $v->id }}" target="_blank" title="Cetak Voucher Satuan" class="p-1.5 rounded-lg border border-slate-200 hover:bg-slate-100 text-slate-600 transition"></a>
                                <form method="POST" action="/hotspot/voucher/{{ $v->id }}" onsubmit="return confirm('Hapus voucher {{ $v->code }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Hapus Voucher" class="p-1.5 rounded-lg border border-rose-200 hover:bg-rose-50 text-rose-600 transition"></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-12 text-center text-slate-400">
                            <div class="max-w-xs mx-auto space-y-2">
                                <span class="text-3xl"></span>
                                <p class="text-sm font-semibold text-slate-600">Belum ada voucher hotspot</p>
                                <p class="text-xs text-slate-400">Klik tombol "Generate Voucher" di atas untuk membuat voucher baru secara massal.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($vouchers->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $vouchers->links() }}
            </div>
        @endif
    </div>

</div>

<!-- MODAL GENERATE VOUCHER MASSAL -->
<div id="modal-generate" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-lg overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900 text-base">Generate Voucher Massal</h3>
                <p class="text-xs text-slate-400">Buat voucher hotspot baru secara otomatis</p>
            </div>
            <button onclick="closeModal('modal-generate')" class="w-8 h-8 rounded-lg hover:bg-slate-100 grid place-items-center text-slate-500 font-bold">✕</button>
        </div>

        <form method="POST" action="/hotspot/voucher/generate" class="p-6 space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Profil Voucher / Paket <span class="text-rose-500">*</span></label>
                <select name="profile_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-[#0066FF] bg-white text-slate-800">
                    @forelse($profiles as $prof)
                        <option value="{{ $prof->id }}">
                            {{ $prof->name }} — Rp {{ number_format($prof->price, 0, ',', '.') }} ({{ $prof->validity ?? 'unlimited' }})
                        </option>
                    @empty
                        <option disabled selected>Belum ada profil voucher, tambahkan dulu di Profil Voucher</option>
                    @endforelse
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Jumlah Voucher <span class="text-rose-500">*</span></label>
                    <input type="number" name="quantity" min="1" max="500" value="20" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-[#0066FF]">
                    <p class="text-[10px] text-slate-400 mt-1">Maksimal 500 voucher sekali buat</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Panjang Kode (Karakter) <span class="text-rose-500">*</span></label>
                    <input type="number" name="char_length" min="4" max="12" value="6" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-[#0066FF]">
                    <p class="text-[10px] text-slate-400 mt-1">Contoh: 6 karakter</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tipe Karakter</label>
                    <select name="char_type" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-[#0066FF] bg-white text-slate-800">
                        <option value="mixed" selected>Campuran Huruf Kapital & Angka</option>
                        <option value="numeric">Angka Saja (123456)</option>
                        <option value="alpha_upper">Huruf Kapital Saja (ABCDEF)</option>
                        <option value="alpha_lower">Huruf Kecil Saja (abcdef)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Format Login</label>
                    <select name="mode" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-[#0066FF] bg-white text-slate-800">
                        <option value="same" selected>Username = Password (1 Field)</option>
                        <option value="different">Username Berbeda Password</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Awalan / Prefix Kode (Opsional)</label>
                <input type="text" name="prefix" placeholder="Misal: SK- atau H-" maxlength="8" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-[#0066FF] uppercase">
                <p class="text-[10px] text-slate-400 mt-1">Akan diletakkan di depan kode, contoh: SK-7A9B2C</p>
            </div>

            <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="closeModal('modal-generate')" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-[#00D2B4] via-[#0066FF] to-[#0052FF] text-white text-xs font-semibold shadow-md shadow-blue-500/20 hover:opacity-90 transition">
                    Proses Generate
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) {
        const el = document.getElementById(id);
        if (el) el.classList.remove('hidden');
    }
    function closeModal(id) {
        const el = document.getElementById(id);
        if (el) el.classList.add('hidden');
    }
</script>
@endsection
