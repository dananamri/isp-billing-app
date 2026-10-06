<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Portal Pelanggan') | SATAK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#F1F5F9] text-slate-800 antialiased flex h-screen overflow-hidden">

    <!-- Mobile Drawer Overlay -->
    <div id="portal-drawer-overlay" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40 hidden md:hidden" onclick="togglePortalDrawer()"></div>

    <!-- Sidebar (Desktop + Mobile Drawer) -->
    <aside id="portal-sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white flex flex-col h-full border-r border-slate-200 shrink-0 transform -translate-x-full md:translate-x-0 md:static transition-transform duration-200">
        <!-- Logo -->
        <div class="h-16 flex items-center justify-between px-5 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-[#2563EB] rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path></svg>
                </div>
                <div>
                    <p class="text-[11px] text-slate-400 font-medium">Portal Pelanggan</p>
                </div>
            </div>
            <button onclick="togglePortalDrawer()" class="md:hidden text-slate-400 hover:text-slate-600 p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Menu Navigasi -->
        <div class="px-4 py-5 flex-1 overflow-y-auto">
            <p class="px-3 text-[10px] font-bold text-slate-400 mb-3 tracking-widest uppercase">Menu</p>
            <nav class="space-y-1">
                @php
                    $currentRoute = request()->path();
                @endphp
                <a href="/portal" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold text-sm transition {{ $currentRoute === 'portal' ? 'bg-[#2563EB] text-white shadow-sm' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-100' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    Dashboard
                </a>
                <a href="/portal/tagihan" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold text-sm transition {{ str_starts_with($currentRoute, 'portal/tagihan') ? 'bg-[#2563EB] text-white shadow-sm' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-100' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Tagihan Saya
                </a>
                <a href="/portal/pembayaran" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold text-sm transition {{ str_starts_with($currentRoute, 'portal/pembayaran') ? 'bg-[#2563EB] text-white shadow-sm' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-100' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    Pembayaran
                </a>
                <a href="/portal/paket" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold text-sm transition {{ str_starts_with($currentRoute, 'portal/paket') ? 'bg-[#2563EB] text-white shadow-sm' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-100' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path></svg>
                    Paket Internet
                </a>
                <a href="/portal/pengaturan" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold text-sm transition {{ str_starts_with($currentRoute, 'portal/pengaturan') ? 'bg-[#2563EB] text-white shadow-sm' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-100' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Pengaturan
                </a>
            </nav>
        </div>

        <!-- Bawah: Keluar -->
        <div class="p-4 border-t border-slate-100">
            <a href="/logout" class="flex items-center gap-3 bg-slate-900 hover:bg-slate-800 text-white px-3 py-2.5 rounded-xl font-semibold text-sm transition">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                Keluar
            </a>
        </div>
    </aside>

    <!-- Main Layout -->
    <main class="flex-1 flex flex-col h-full overflow-hidden">
        <!-- Header Top -->
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 lg:px-8 shrink-0">
            <div class="flex items-center gap-3">
                <button onclick="togglePortalDrawer()" class="md:hidden p-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <div>
                    <h1 class="text-base sm:text-lg font-bold text-slate-900 leading-tight">@yield('page_title', 'Dashboard')</h1>
                    <p class="text-[11px] text-slate-400 font-medium hidden sm:block">@yield('page_subtitle', 'Pantau paket, tagihan, dan pembayaran kamu')</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <a href="/portal/tagihan?status=belum_bayar" title="Notifikasi Tagihan" class="relative text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-50 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    @if(isset($tagihanBelumBayar) && $tagihanBelumBayar > 0)
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white"></span>
                    @endif
                </a>
                <div class="flex items-center gap-3 pl-4 border-l border-slate-200">
                    <div class="text-right hidden sm:block">
                        <p class="text-[13px] font-bold text-slate-900 leading-tight">{{ $pelanggan->nama ?? session('user_name', 'Pelanggan') }}</p>
                        <p class="text-[11px] text-slate-400">Pelanggan</p>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-[#2563EB] text-white flex items-center justify-center font-bold text-sm shadow-sm">
                        {{ strtoupper(mb_substr($pelanggan->nama ?? session('user_name', 'P'), 0, 1)) }}
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <div class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
            <div class="max-w-7xl mx-auto space-y-6 pb-8">
                @if(session('success'))
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm px-4 py-3 rounded-xl flex items-center gap-2 shadow-sm">
                        <span class="text-base">✅</span>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-rose-50 border border-rose-200 text-rose-800 text-sm px-4 py-3 rounded-xl flex items-center gap-2 shadow-sm">
                        <span class="text-base">⚠️</span>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if(session('info'))
                    <div class="bg-blue-50 border border-blue-200 text-blue-800 text-sm px-4 py-3 rounded-xl flex items-center gap-2 shadow-sm">
                        <span class="text-base">ℹ️</span>
                        <span>{{ session('info') }}</span>
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

                @yield('content')
            </div>
        </div>
    </main>

    <script>
        function togglePortalDrawer() {
            const sidebar = document.getElementById('portal-sidebar');
            const overlay = document.getElementById('portal-drawer-overlay');
            if (sidebar && overlay) {
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
            }
        }
    </script>
</body>
</html>
