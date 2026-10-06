<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SATAK Keuangan - @yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased">
    <header class="md:hidden sticky top-0 z-40 bg-white border-b border-slate-200 flex items-center justify-between px-4 py-3">
        <div class="flex items-center gap-3">
            <img src="{{ asset('satak.jpeg') }}" alt="Logo SATAK" class="w-8 h-8 rounded-lg object-contain border border-slate-100">
        </div>
        <button id="btn-open" class="w-10 h-10 grid place-items-center rounded-xl border border-slate-200 active:bg-slate-100" aria-label="Menu">☰</button>
    </header>

    <div id="backdrop" class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40 md:hidden"></div>
    <aside id="drawer" class="fixed inset-y-0 left-0 w-[84%] max-w-[300px] bg-white z-50 -translate-x-full transition-transform duration-300 md:hidden flex flex-col">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ asset('satak.jpeg') }}" alt="Logo SATAK" class="w-9 h-9 rounded-xl object-contain border border-slate-100">
            </div>
            <button id="btn-close" class="w-9 h-9 grid place-items-center rounded-xl bg-slate-100">✕</button>
        </div>
        <nav class="p-4 space-y-1.5 text-sm font-medium flex-1 overflow-y-auto">
            <a href="/dashboard" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('dashboard*') ? 'bg-gradient-to-r from-[#00D2B4] via-[#0066FF] to-[#0052FF] text-white shadow-md font-semibold' : 'text-slate-600 hover:bg-slate-50' }}"><span>📊</span> Dashboard</a>
            <a href="/pelanggan" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('pelanggan*') ? 'bg-gradient-to-r from-[#00D2B4] via-[#0066FF] to-[#0052FF] text-white shadow-md font-semibold' : 'text-slate-600 hover:bg-slate-50' }}"><span>👥</span> Pelanggan</a>
            <a href="/pembayaran" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('pembayaran*') ? 'bg-gradient-to-r from-[#00D2B4] via-[#0066FF] to-[#0052FF] text-white shadow-md font-semibold' : 'text-slate-600 hover:bg-slate-50' }}"><span>💳</span> Pembayaran</a>
            <a href="/tagihan" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('tagihan*') ? 'bg-gradient-to-r from-[#00D2B4] via-[#0066FF] to-[#0052FF] text-white shadow-md font-semibold' : 'text-slate-600 hover:bg-slate-50' }}"><span>🧾</span> Tagihan</a>
            @php($paketOpenM = request()->is('paket*') || request()->is('hotspot*') || request()->is('pppoe*'))
            @php($hotspotOpenM = request()->is('hotspot*'))
            <div class="rounded-xl {{ $paketOpenM ? 'bg-slate-50' : '' }}">
                <button onclick="toggleSub('paket-mobile','chev-paket-mobile')" class="w-full flex items-center justify-between px-4 py-3 rounded-xl {{ $paketOpenM ? 'text-[#0066FF] font-semibold' : 'text-slate-600 hover:bg-slate-50' }}"><span class="flex items-center gap-3"><span>📦</span> Paket Internet</span><span id="chev-paket-mobile" class="{{ $paketOpenM ? 'rotate-180' : '' }} transition-transform text-xs">▼</span></button>
                <div id="paket-mobile" class="{{ $paketOpenM ? '' : 'hidden' }} pb-1">
                    <a href="/paket" class="flex items-center gap-3 pl-11 pr-4 py-2.5 rounded-xl text-[13px] {{ request()->is('paket*') ? 'bg-gradient-to-r from-[#00D2B4] via-[#0066FF] to-[#0052FF] text-white shadow-md font-semibold' : 'text-slate-600 hover:bg-white' }}"><span>📦</span> Semua Paket</a>
                    <div class="mx-2 rounded-xl">
                        <button onclick="toggleSub('hotspot-mobile','chev-hotspot-mobile')" class="w-full flex items-center justify-between pl-11 pr-4 py-2.5 rounded-xl text-[13px] {{ $hotspotOpenM ? 'text-[#0066FF] font-semibold' : 'text-slate-600 hover:bg-white' }}"><span class="flex items-center gap-3"><span>🔥</span> Hotspot</span><span id="chev-hotspot-mobile" class="{{ $hotspotOpenM ? 'rotate-180' : '' }} transition-transform text-[10px]">▼</span></button>
                        <div id="hotspot-mobile" class="{{ $hotspotOpenM ? '' : 'hidden' }}">
                            <a href="/hotspot/voucher" class="flex items-center gap-3 pl-[3.25rem] pr-4 py-2 rounded-xl text-[13px] {{ request()->is('hotspot/voucher*') ? 'bg-gradient-to-r from-[#00D2B4] via-[#0066FF] to-[#0052FF] text-white shadow-md font-semibold' : 'text-slate-600 hover:bg-white' }}">Daftar Voucher</a>
                            <a href="/hotspot/profil" class="flex items-center gap-3 pl-[3.25rem] pr-4 py-2 rounded-xl text-[13px] {{ request()->is('hotspot/profil*') ? 'bg-gradient-to-r from-[#00D2B4] via-[#0066FF] to-[#0052FF] text-white shadow-md font-semibold' : 'text-slate-600 hover:bg-white' }}">Profil Voucher</a>
                            <a href="/hotspot/member" class="flex items-center gap-3 pl-[3.25rem] pr-4 py-2 rounded-xl text-[13px] {{ request()->is('hotspot/member*') ? 'bg-gradient-to-r from-[#00D2B4] via-[#0066FF] to-[#0052FF] text-white shadow-md font-semibold' : 'text-slate-600 hover:bg-white' }}">Member Hotspot</a>
                            <a href="/hotspot/rekap" class="flex items-center gap-3 pl-[3.25rem] pr-4 py-2 rounded-xl text-[13px] {{ request()->is('hotspot/rekap*') ? 'bg-gradient-to-r from-[#00D2B4] via-[#0066FF] to-[#0052FF] text-white shadow-md font-semibold' : 'text-slate-600 hover:bg-white' }}">Rekap</a>
                        </div>
                    </div>
                    <a href="/pppoe" class="flex items-center gap-3 pl-11 pr-4 py-2.5 rounded-xl text-[13px] {{ request()->is('pppoe*') ? 'bg-gradient-to-r from-[#00D2B4] via-[#0066FF] to-[#0052FF] text-white shadow-md font-semibold' : 'text-slate-600 hover:bg-white' }}"><span>🔌</span> PPPoE</a>
                </div>
            </div>
            <a href="/laporan" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('laporan*') ? 'bg-gradient-to-r from-[#00D2B4] via-[#0066FF] to-[#0052FF] text-white shadow-md font-semibold' : 'text-slate-600 hover:bg-slate-50' }}"><span>📈</span> Laporan</a>
            <a href="/pengaturan" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('pengaturan*') ? 'bg-gradient-to-r from-[#00D2B4] via-[#0066FF] to-[#0052FF] text-white shadow-md font-semibold' : 'text-slate-600 hover:bg-slate-50' }}"><span>⚙️</span> Pengaturan</a>
        </nav>
        <div class="p-4 border-t border-slate-100">
            <a href="/logout" class="flex items-center gap-3 px-4 py-3 rounded-xl text-rose-600 hover:bg-rose-50 text-sm font-medium"><span>🚪</span> Keluar</a>
        </div>
    </aside>

    <div class="flex min-h-screen">
        <aside class="hidden md:flex w-64 bg-white border-r border-slate-200 fixed inset-y-0 left-0 flex-col justify-between z-20">
            <div>
                <div class="p-6 border-b border-slate-100 flex items-center gap-3">
                    <img src="{{ asset('satak.jpeg') }}" alt="Logo SATAK" class="w-10 h-10 rounded-xl object-contain shadow-sm border border-slate-100">
                </div>
                <nav class="p-4 space-y-1.5 text-sm font-medium">
                    <a href="/dashboard" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->is('dashboard*') ? 'bg-gradient-to-r from-[#00D2B4] via-[#0066FF] to-[#0052FF] text-white shadow-md shadow-blue-500/20 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-[#0066FF]' }}"><span class="text-base">📊</span> Dashboard</a>
                    <a href="/pelanggan" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->is('pelanggan*') ? 'bg-gradient-to-r from-[#00D2B4] via-[#0066FF] to-[#0052FF] text-white shadow-md shadow-blue-500/20 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-[#0066FF]' }}"><span class="text-base">👥</span> Pelanggan</a>
                    <a href="/pembayaran" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->is('pembayaran*') ? 'bg-gradient-to-r from-[#00D2B4] via-[#0066FF] to-[#0052FF] text-white shadow-md shadow-blue-500/20 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-[#0066FF]' }}"><span class="text-base">💳</span> Pembayaran</a>
                    <a href="/tagihan" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->is('tagihan*') ? 'bg-gradient-to-r from-[#00D2B4] via-[#0066FF] to-[#0052FF] text-white shadow-md shadow-blue-500/20 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-[#0066FF]' }}"><span class="text-base">🧾</span> Tagihan</a>
                    @php($paketOpen = request()->is('paket*') || request()->is('hotspot*') || request()->is('pppoe*'))
                    @php($hotspotOpen = request()->is('hotspot*'))
                    <div class="rounded-xl {{ $paketOpen ? 'bg-slate-50/80' : '' }}">
                        <button onclick="toggleSub('paket-desktop','chev-paket-desktop')" class="w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-200 {{ $paketOpen ? 'text-[#0066FF] font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-[#0066FF]' }}"><span class="flex items-center gap-3"><span class="text-base">📦</span> Paket Internet</span><span id="chev-paket-desktop" class="{{ $paketOpen ? 'rotate-180' : '' }} transition-transform duration-200 text-xs">▼</span></button>
                        <div id="paket-desktop" class="{{ $paketOpen ? '' : 'hidden' }} space-y-1 pb-1">
                            <a href="/paket" class="flex items-center gap-3 pl-11 pr-4 py-2 rounded-xl text-xs font-medium transition-all duration-200 {{ request()->is('paket*') ? 'bg-gradient-to-r from-[#00D2B4] via-[#0066FF] to-[#0052FF] text-white shadow-md shadow-blue-500/20 font-semibold' : 'text-slate-600 hover:bg-white hover:text-[#0066FF]' }}"><span>📦</span> Semua Paket</a>
                            <div>
                                <button onclick="toggleSub('hotspot-desktop','chev-hotspot-desktop')" class="w-full flex items-center justify-between pl-11 pr-4 py-2 rounded-xl text-xs font-medium transition-all duration-200 {{ $hotspotOpen ? 'text-[#0066FF] font-semibold' : 'text-slate-600 hover:bg-white hover:text-[#0066FF]' }}"><span class="flex items-center gap-2"><span>🔥</span> Hotspot</span><span id="chev-hotspot-desktop" class="{{ $hotspotOpen ? 'rotate-180' : '' }} transition-transform duration-200 text-[10px]">▼</span></button>
                                <div id="hotspot-desktop" class="{{ $hotspotOpen ? '' : 'hidden' }} space-y-1 mt-1 pl-4">
                                    <a href="/hotspot/voucher" class="block pl-9 pr-4 py-1.5 rounded-xl text-xs transition-all duration-200 {{ request()->is('hotspot/voucher*') ? 'bg-gradient-to-r from-[#00D2B4] via-[#0066FF] to-[#0052FF] text-white shadow-sm font-semibold' : 'text-slate-500 hover:text-[#0066FF] hover:bg-white' }}">Daftar Voucher</a>
                                    <a href="/hotspot/profil" class="block pl-9 pr-4 py-1.5 rounded-xl text-xs transition-all duration-200 {{ request()->is('hotspot/profil*') ? 'bg-gradient-to-r from-[#00D2B4] via-[#0066FF] to-[#0052FF] text-white shadow-sm font-semibold' : 'text-slate-500 hover:text-[#0066FF] hover:bg-white' }}">Profil Voucher</a>
                                    <a href="/hotspot/member" class="block pl-9 pr-4 py-1.5 rounded-xl text-xs transition-all duration-200 {{ request()->is('hotspot/member*') ? 'bg-gradient-to-r from-[#00D2B4] via-[#0066FF] to-[#0052FF] text-white shadow-sm font-semibold' : 'text-slate-500 hover:text-[#0066FF] hover:bg-white' }}">Member Hotspot</a>
                                    <a href="/hotspot/rekap" class="block pl-9 pr-4 py-1.5 rounded-xl text-xs transition-all duration-200 {{ request()->is('hotspot/rekap*') ? 'bg-gradient-to-r from-[#00D2B4] via-[#0066FF] to-[#0052FF] text-white shadow-sm font-semibold' : 'text-slate-500 hover:text-[#0066FF] hover:bg-white' }}">Rekap</a>
                                </div>
                            </div>
                            <a href="/pppoe" class="flex items-center gap-3 pl-11 pr-4 py-2 rounded-xl text-xs font-medium transition-all duration-200 {{ request()->is('pppoe*') ? 'bg-gradient-to-r from-[#00D2B4] via-[#0066FF] to-[#0052FF] text-white shadow-md shadow-blue-500/20 font-semibold' : 'text-slate-600 hover:bg-white hover:text-[#0066FF]' }}"><span>🔌</span> PPPoE</a>
                        </div>
                    </div>
                    <a href="/laporan" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->is('laporan*') ? 'bg-gradient-to-r from-[#00D2B4] via-[#0066FF] to-[#0052FF] text-white shadow-md shadow-blue-500/20 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-[#0066FF]' }}"><span class="text-base">📈</span> Laporan</a>
                    <a href="/pengaturan" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->is('pengaturan*') ? 'bg-gradient-to-r from-[#00D2B4] via-[#0066FF] to-[#0052FF] text-white shadow-md shadow-blue-500/20 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-[#0066FF]' }}"><span class="text-base">⚙️</span> Pengaturan</a>
                </nav>
            </div>
            <div class="p-4 border-t border-slate-100">
                <a href="/logout" class="flex items-center gap-3 px-4 py-3 rounded-xl text-rose-600 hover:bg-rose-50 transition-colors text-sm font-medium"><span class="text-base">🚪</span> Keluar</a>
            </div>
        </aside>

        <main class="flex-1 min-w-0 md:ml-64">
            @yield('content')
        </main>
    </div>

    <script>
        const drawer=document.getElementById('drawer');
        const backdrop=document.getElementById('backdrop');
        const openBtn=document.getElementById('btn-open');
        const closeBtn=document.getElementById('btn-close');
        function openDrawer(){drawer.classList.remove('-translate-x-full');backdrop.classList.remove('hidden');document.body.style.overflow='hidden';}
        function closeDrawer(){drawer.classList.add('-translate-x-full');backdrop.classList.add('hidden');document.body.style.overflow='';}
        openBtn?.addEventListener('click',openDrawer);
        closeBtn?.addEventListener('click',closeDrawer);
        backdrop?.addEventListener('click',closeDrawer);

        function toggleSub(id, chevId) {
            const el = document.getElementById(id);
            const chev = document.getElementById(chevId);
            if (!el) return;
            el.classList.toggle('hidden');
            if (chev) chev.classList.toggle('rotate-180');
        }
    </script>
</body>
</html>
