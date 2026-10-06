@extends('layouts.app')

@section('title', 'Pengaturan')

@section('content')
<header class="bg-white shadow-sm py-4 md:py-5 px-4 md:px-8 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
    <div>
        <h2 class="text-xl md:text-2xl font-bold text-gray-800">Pengaturan</h2>
        <p class="text-gray-500 text-xs md:text-sm">Konfigurasi profil ISP, billing, WhatsApp gateway, dan pengguna</p>
    </div>
</header>

<div class="p-4 md:p-8 space-y-6">
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm px-4 py-3 rounded-xl flex items-center gap-2">
            <span>✅</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-800 text-sm px-4 py-3 rounded-xl">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- TAB NAVIGATION -->
    <div class="flex border-b border-slate-200 space-x-2 overflow-x-auto text-sm font-semibold">
        <button onclick="switchTab('profil')" id="tab-btn-profil" class="tab-btn pb-3 px-4 border-b-2 border-[#0066FF] text-[#0066FF] whitespace-nowrap">
            🏢 Profil Perusahaan
        </button>
        <button onclick="switchTab('billing')" id="tab-btn-billing" class="tab-btn pb-3 px-4 border-b-2 border-transparent text-slate-500 hover:text-slate-700 whitespace-nowrap">
            ⚙️ Sistem & Billing
        </button>
        <button onclick="switchTab('whatsapp')" id="tab-btn-whatsapp" class="tab-btn pb-3 px-4 border-b-2 border-transparent text-slate-500 hover:text-slate-700 whitespace-nowrap">
            💬 WhatsApp Gateway
        </button>
        <button onclick="switchTab('template')" id="tab-btn-template" class="tab-btn pb-3 px-4 border-b-2 border-transparent text-slate-500 hover:text-slate-700 whitespace-nowrap">
            📝 Template Pesan
        </button>
        <button onclick="switchTab('users')" id="tab-btn-users" class="tab-btn pb-3 px-4 border-b-2 border-transparent text-slate-500 hover:text-slate-700 whitespace-nowrap">
            👥 Pengguna ({{ count($users ?? []) }})
        </button>
    </div>

    <!-- TAB 1: PROFIL PERUSAHAAN -->
    <div id="tab-profil" class="tab-content">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 max-w-3xl">
            <h3 class="text-base font-bold text-slate-800 mb-1">Identitas ISP / Perusahaan</h3>
            <p class="text-xs text-slate-500 mb-6">Informasi yang tampil di faktur, portal, dan pesan pelanggan</p>

            <form method="POST" action="/pengaturan/profil" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Perusahaan / ISP <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $tenant->name ?? '') }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-[#0066FF]">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Email Resmi</label>
                        <input type="email" name="email" value="{{ old('email', $tenant->email ?? '') }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-[#0066FF]">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Telepon / CS</label>
                        <input type="text" name="phone" value="{{ old('phone', $tenant->phone ?? '') }}" placeholder="08xxxxxxxxxx" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-[#0066FF]">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Kantor</label>
                    <textarea name="address" rows="3" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-[#0066FF]">{{ old('address', $tenant->address ?? '') }}</textarea>
                </div>

                <div class="pt-2">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-[#00D2B4] via-[#0066FF] to-[#0052FF] text-white text-sm font-semibold shadow-md shadow-blue-500/20 hover:opacity-90 transition">
                        Simpan Profil
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- TAB 2: SISTEM & BILLING -->
    <div id="tab-billing" class="tab-content hidden">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 max-w-3xl">
            <h3 class="text-base font-bold text-slate-800 mb-1">Pengaturan Penagihan & Isolir Otomatis</h3>
            <p class="text-xs text-slate-500 mb-6">Toleransi jatuh tempo dan jadwal pengingat tagihan ke pelanggan</p>

            <form method="POST" action="/pengaturan/billing" class="space-y-5">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Masa Tenggang Isolir (Hari)</label>
                        <input type="number" name="grace_days" min="0" max="30" value="{{ old('grace_days', $settings['billing.suspension_grace_days'] ?? 3) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-[#0066FF]">
                        <p class="text-[11px] text-slate-400 mt-1">Hari toleransi setelah jatuh tempo sebelum layanan diisolir otomatis.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Pengingat Tagihan (Hari Sebelum)</label>
                        <input type="number" name="reminder_days" min="1" max="30" value="{{ old('reminder_days', $settings['billing.reminder_days_before'] ?? 3) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-[#0066FF]">
                        <p class="text-[11px] text-slate-400 mt-1">Berapa hari sebelum jatuh tempo pesan pengingat WA dikirimkan.</p>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-[#00D2B4] via-[#0066FF] to-[#0052FF] text-white text-sm font-semibold shadow-md shadow-blue-500/20 hover:opacity-90 transition">
                        Simpan Pengaturan Billing
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- TAB 3: WHATSAPP GATEWAY -->
    <div id="tab-whatsapp" class="tab-content hidden">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 max-w-3xl">
            <h3 class="text-base font-bold text-slate-800 mb-1">Penyedia Layanan WhatsApp</h3>
            <p class="text-xs text-slate-500 mb-6">Pilih provider untuk pengiriman notifikasi tagihan dan isolir otomatis</p>

            <form method="POST" action="/pengaturan/whatsapp" class="space-y-5">
                @csrf
                @php($currentProvider = $settings['whatsapp.provider'] ?? 'disabled')
                <div class="space-y-3">
                    <label class="flex items-start gap-3 p-3.5 rounded-xl border cursor-pointer {{ $currentProvider === 'fonnte' ? 'border-[#0066FF] bg-blue-50/30' : 'border-slate-200 hover:bg-slate-50' }}">
                        <input type="radio" name="wa_provider" value="fonnte" {{ $currentProvider === 'fonnte' ? 'checked' : '' }} class="mt-1 text-[#0066FF] focus:ring-0">
                        <div>
                            <div class="font-bold text-sm text-slate-800">Fonnte</div>
                            <div class="text-xs text-slate-500">API gateway stabil berbasis token. Cocok untuk broadcast & webhook instan.</div>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 p-3.5 rounded-xl border cursor-pointer {{ $currentProvider === 'wablas' ? 'border-[#0066FF] bg-blue-50/30' : 'border-slate-200 hover:bg-slate-50' }}">
                        <input type="radio" name="wa_provider" value="wablas" {{ $currentProvider === 'wablas' ? 'checked' : '' }} class="mt-1 text-[#0066FF] focus:ring-0">
                        <div>
                            <div class="font-bold text-sm text-slate-800">Wablas</div>
                            <div class="text-xs text-slate-500">Mendukung multi-device, multi-nomor, dan reporting delivery status via ref_id.</div>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 p-3.5 rounded-xl border cursor-pointer {{ $currentProvider === 'disabled' ? 'border-[#0066FF] bg-blue-50/30' : 'border-slate-200 hover:bg-slate-50' }}">
                        <input type="radio" name="wa_provider" value="disabled" {{ $currentProvider === 'disabled' ? 'checked' : '' }} class="mt-1 text-[#0066FF] focus:ring-0">
                        <div>
                            <div class="font-bold text-sm text-slate-800">Nonaktifkan (Simulasi / Catat Saja)</div>
                            <div class="text-xs text-slate-500">Notifikasi tetap dicatat di riwayat pengiriman tetapi tidak dikirim ke nomor pelanggan.</div>
                        </div>
                    </label>
                </div>

                <div class="pt-2">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-[#00D2B4] via-[#0066FF] to-[#0052FF] text-white text-sm font-semibold shadow-md shadow-blue-500/20 hover:opacity-90 transition">
                        Simpan Provider WhatsApp
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- TAB 4: TEMPLATE PESAN -->
    <div id="tab-template" class="tab-content hidden">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 max-w-3xl">
            <h3 class="text-base font-bold text-slate-800 mb-1">Format Pesan Otomatis</h3>
            <p class="text-xs text-slate-500 mb-4">Gunakan placeholder: <code class="bg-slate-100 px-1 py-0.5 rounded text-[11px]">{customer}</code>, <code class="bg-slate-100 px-1 py-0.5 rounded text-[11px]">{tenant}</code>, <code class="bg-slate-100 px-1 py-0.5 rounded text-[11px]">{amount}</code>, <code class="bg-slate-100 px-1 py-0.5 rounded text-[11px]">{due_date}</code>, <code class="bg-slate-100 px-1 py-0.5 rounded text-[11px]">{invoice_number}</code></p>

            <form method="POST" action="/pengaturan/template" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Pengingat Tagihan (Due Reminder)</label>
                    <textarea name="tpl_due_reminder" rows="3" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-[#0066FF]">{{ old('tpl_due_reminder', $settings['whatsapp.templates.due_reminder'] ?? config('whatsapp.templates.due_reminder')) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Pemberitahuan Isolir (Suspend)</label>
                    <textarea name="tpl_suspend" rows="3" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-[#0066FF]">{{ old('tpl_suspend', $settings['whatsapp.templates.suspend'] ?? config('whatsapp.templates.suspend')) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Pemberitahuan Reaktivasi (Lunas)</label>
                    <textarea name="tpl_reactivate" rows="3" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-[#0066FF]">{{ old('tpl_reactivate', $settings['whatsapp.templates.reactivate'] ?? config('whatsapp.templates.reactivate')) }}</textarea>
                </div>

                <div class="pt-2">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-[#00D2B4] via-[#0066FF] to-[#0052FF] text-white text-sm font-semibold shadow-md shadow-blue-500/20 hover:opacity-90 transition">
                        Simpan Template Pesan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- TAB 5: PENGGUNA -->
    <div id="tab-users" class="tab-content hidden">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-800 text-base">Daftar Pengguna / Staf</h3>
                    <p class="text-xs text-slate-400">Pengguna yang memiliki hak akses ke portal admin ini</p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-500 text-xs">
                        <tr>
                            <th class="text-left p-3.5">Nama</th>
                            <th class="text-left p-3.5">Email</th>
                            <th class="text-left p-3.5">No. Telepon</th>
                            <th class="text-left p-3.5">Status</th>
                            <th class="text-left p-3.5">Terdaftar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($users as $u)
                        <tr class="hover:bg-slate-50/50">
                            <td class="p-3.5 font-bold text-slate-800">{{ $u->name }}</td>
                            <td class="p-3.5 text-slate-600">{{ $u->email }}</td>
                            <td class="p-3.5 text-slate-600 font-mono">{{ $u->phone ?? '-' }}</td>
                            <td class="p-3.5">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $u->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                    {{ $u->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="p-3.5 text-slate-400 text-xs">{{ $u->created_at?->format('d/m/Y') ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-slate-400">Belum ada user lain</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function switchTab(tabId) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.tab-btn').forEach(el => {
            el.classList.remove('border-[#0066FF]', 'text-[#0066FF]');
            el.classList.add('border-transparent', 'text-slate-500');
        });

        const activeContent = document.getElementById('tab-' + tabId);
        const activeBtn = document.getElementById('tab-btn-' + tabId);
        if (activeContent) activeContent.classList.remove('hidden');
        if (activeBtn) {
            activeBtn.classList.add('border-[#0066FF]', 'text-[#0066FF]');
            activeBtn.classList.remove('border-transparent', 'text-slate-500');
        }
    }
</script>
@endsection
