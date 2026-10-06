<?php

use App\Models\Pelanggan;
use App\Models\Paket;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherProfile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

// Helper check apakah user adalah staff/admin
$isStaffUser = function ($user) {
    if (!$user) return false;
    return app(\App\Tenancy\TenantContext::class)->run(
        $user->tenant_id,
        fn () => $user->hasPermissionTo('dashboard.view')
    );
};

Route::get('/login', function () use ($isStaffUser) {
    if (session('user_id')) {
        $u = User::find(session('user_id'));
        return $isStaffUser($u) ? redirect('/dashboard') : redirect('/portal');
    }
    return view('auth.login');
})->name('login');

Route::post('/login', function (Request $request) use ($isStaffUser) {
    $role = $request->input('role', 'pelanggan');

    if ($role === 'admin') {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->username)
            ->orWhere('name', $request->username)
            ->first();
    } else {
        $request->validate([
            'phone' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('phone', $request->phone)
            ->orWhere('email', $request->phone)
            ->first();
    }

    if (!$user || !Hash::check($request->password, $user->password)) {
        return back()->withInput()->withErrors([
            $role === 'admin' ? 'username' : 'phone' => 'Kredensial atau password salah.'
        ]);
    }

    if (!$user->is_active) {
        return back()->withInput()->withErrors([
            $role === 'admin' ? 'username' : 'phone' => 'Akun Anda sedang menunggu persetujuan dari Admin. Silakan hubungi admin.'
        ]);
    }

    $isAdmin = $isStaffUser($user);

    // Jika mencoba login lewat form admin tapi bukan admin, tolak
    if ($role === 'admin' && !$isAdmin) {
        return back()->withInput()->withErrors([
            'username' => 'Akun ini bukan akun Administrator/Staf.'
        ]);
    }

    Auth::login($user);

    $request->session()->put('user_id', $user->id);
    $request->session()->put('user_email', $user->email);
    $request->session()->put('user_name', $user->name);
    $request->session()->put('tenant_id', $user->tenant_id);

    // Redirect sesuai role: admin ke dashboard, pelanggan ke portal
    return $isAdmin ? redirect('/dashboard') : redirect('/portal');
});

Route::get('/register', function () use ($isStaffUser) {
    if (session('user_id')) {
        $u = User::find(session('user_id'));
        return $isStaffUser($u) ? redirect('/dashboard') : redirect('/portal');
    }
    return view('auth.register');
})->name('register');

Route::post('/register', function (Request $request) {
    $request->validate([
        'name' => 'required|string|max:255',
        'phone' => 'required|string|max:30|unique:users,phone',
        'email' => 'required|email|max:255|unique:users,email',
        'alamat' => 'required|string|max:500',
        'password' => 'required|min:6|confirmed',
    ]);

    $tenant = \App\Models\Tenant::where('is_active', true)->first();
    if (!$tenant) {
        $tenant = \App\Models\Tenant::first();
    }

    abort_unless($tenant, 500, 'Tenant aktif belum tersedia.');

    // 1. Buat User baru (status nonaktif menunggu persetujuan admin)
    $user = User::create([
        'tenant_id' => $tenant->id,
        'name' => $request->name,
        'email' => $request->email,
        'phone' => $request->phone,
        'password' => $request->password,
        'is_active' => false,
    ]);

    // 2. Buat Data Pelanggan otomatis status 'menunggu'
    app(\App\Tenancy\TenantContext::class)->run($tenant->id, function () use ($tenant, $user, $request) {
        Pelanggan::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'nama' => $request->name,
            'telepon' => $request->phone,
            'email' => $request->email,
            'alamat' => $request->alamat,
            'status' => 'menunggu',
            'tanggal_aktif' => null,
        ]);
    });

    return redirect('/login')->with('success', 'Pendaftaran berhasil! Akun Anda telah masuk ke antrean verifikasi admin dan akan diaktifkan setelah diverifikasi.');
});

Route::get('/admin/login', function () use ($isStaffUser) {
    if (session('user_id')) {
        $u = User::find(session('user_id'));
        return $isStaffUser($u) ? redirect('/dashboard') : redirect('/portal');
    }
    return view('auth.admin_login');
})->name('admin.login');

Route::post('/admin/login', function (Request $request) use ($isStaffUser) {
    $request->validate(['email' => 'required', 'password' => 'required']);

    $user = User::where('email', $request->email)
        ->orWhere('name', $request->email)
        ->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return back()->withErrors(['email' => 'Email/Username atau password salah.']);
    }

    if (!$user->is_active) {
        return back()->withErrors(['email' => 'Akun admin nonaktif.']);
    }

    if (!$isStaffUser($user)) {
        return back()->withErrors(['email' => 'Akun ini bukan Administrator/Staf.']);
    }

    Auth::login($user);

    $request->session()->put('user_id', $user->id);
    $request->session()->put('user_email', $user->email);
    $request->session()->put('user_name', $user->name);
    $request->session()->put('tenant_id', $user->tenant_id);

    return redirect('/dashboard');
});

Route::get('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->forget(['user_id', 'user_email', 'user_name', 'tenant_id']);
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// Portal pelanggan (butuh auth, data milik user sendiri)
Route::middleware(['auth', 'tenant'])->group(function () {
    Route::get('/portal', function () {
        $pelanggan = Pelanggan::where('user_id', session('user_id'))->with('paket')->first();
        $tagihan = collect();
        $tagihanBelumBayar = 0;
        if ($pelanggan) {
            $tagihan = Tagihan::where('tenant_id', session('tenant_id'))
                ->where('pelanggan_id', $pelanggan->id)
                ->latest()
                ->take(5)
                ->get();
            $tagihanBelumBayar = Tagihan::where('tenant_id', session('tenant_id'))
                ->where('pelanggan_id', $pelanggan->id)
                ->where('status', 'belum_bayar')
                ->count();
        }
        return view('portal.index', compact('pelanggan', 'tagihan', 'tagihanBelumBayar'));
    })->name('portal');

    Route::get('/portal/tagihan', function (Request $request) {
        $pelanggan = Pelanggan::where('user_id', session('user_id'))->with('paket')->first();
        if (!$pelanggan) {
            return redirect('/portal')->with('error', 'Data pelanggan belum terdaftar.');
        }

        $query = Tagihan::where('tenant_id', session('tenant_id'))
            ->where('pelanggan_id', $pelanggan->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tagihan = $query->latest('jatuh_tempo')->paginate(10)->withQueryString();
        $totalBelumBayar = Tagihan::where('tenant_id', session('tenant_id'))
            ->where('pelanggan_id', $pelanggan->id)
            ->where('status', 'belum_bayar')
            ->sum('jumlah');

        return view('portal.tagihan', compact('pelanggan', 'tagihan', 'totalBelumBayar'));
    })->name('portal.tagihan');

    Route::post('/portal/tagihan/{id}/bayar', function ($id, Request $request) {
        $pelanggan = Pelanggan::where('user_id', session('user_id'))->firstOrFail();
        $t = Tagihan::where('tenant_id', session('tenant_id'))
            ->where('pelanggan_id', $pelanggan->id)
            ->where('id', $id)
            ->firstOrFail();

        if ($t->status === 'lunas') {
            return back()->with('info', 'Tagihan ini sudah lunas.');
        }

        $metode = $request->input('metode', 'transfer_bank');
        $referensi = 'PAY-' . strtoupper(Str::random(8));

        // Buat record pembayaran
        Pembayaran::create([
            'tenant_id' => session('tenant_id'),
            'tagihan_id' => $t->id,
            'pelanggan_id' => $pelanggan->id,
            'jumlah' => $t->jumlah,
            'tanggal_bayar' => now()->toDateString(),
            'metode_pembayaran' => $metode,
            'referensi' => $referensi,
            'status' => 'berhasil',
            'keterangan' => 'Pembayaran via Portal Pelanggan (' . strtoupper($metode) . ')',
        ]);

        // Update status tagihan
        $t->update([
            'status' => 'lunas',
            'tanggal_bayar' => now()->toDateString(),
        ]);

        return redirect('/portal/tagihan')->with('success', "Pembayaran tagihan {$t->nomor_tagihan} sebesar Rp " . number_format($t->jumlah, 0, ',', '.') . " berhasil dikonfirmasi!");
    })->name('portal.tagihan.bayar');

    Route::get('/portal/tagihan/{id}/cetak', function ($id) {
        $pelanggan = Pelanggan::where('user_id', session('user_id'))->with('paket')->firstOrFail();
        $tagihan = Tagihan::where('tenant_id', session('tenant_id'))
            ->where('pelanggan_id', $pelanggan->id)
            ->where('id', $id)
            ->firstOrFail();
        $tenant = \App\Models\Tenant::find(session('tenant_id'));
        $pembayaran = Pembayaran::where('tagihan_id', $tagihan->id)->latest()->first();

        return view('portal.invoice', compact('pelanggan', 'tagihan', 'tenant', 'pembayaran'));
    })->name('portal.tagihan.cetak');

    Route::get('/portal/pembayaran', function () {
        $pelanggan = Pelanggan::where('user_id', session('user_id'))->first();
        if (!$pelanggan) {
            return redirect('/portal')->with('error', 'Data pelanggan belum terdaftar.');
        }

        $pembayaran = Pembayaran::where('tenant_id', session('tenant_id'))
            ->where('pelanggan_id', $pelanggan->id)
            ->with('tagihan')
            ->latest('tanggal_bayar')
            ->paginate(15);

        $totalTerbayar = Pembayaran::where('tenant_id', session('tenant_id'))
            ->where('pelanggan_id', $pelanggan->id)
            ->where('status', 'berhasil')
            ->sum('jumlah');

        return view('portal.pembayaran', compact('pelanggan', 'pembayaran', 'totalTerbayar'));
    })->name('portal.pembayaran');

    Route::get('/portal/paket', function () {
        $pelanggan = Pelanggan::where('user_id', session('user_id'))->with('paket')->first();
        $pakets = Paket::where('tenant_id', session('tenant_id'))
            ->where('status', 'aktif')
            ->get();

        return view('portal.paket', compact('pelanggan', 'pakets'));
    })->name('portal.paket');

    Route::post('/portal/paket/pilih', function (Request $request) {
        $request->validate([
            'paket_id' => 'required|exists:pakets,id',
        ]);

        $pelanggan = Pelanggan::where('user_id', session('user_id'))->firstOrFail();
        $paketBaru = Paket::where('tenant_id', session('tenant_id'))
            ->where('id', $request->paket_id)
            ->firstOrFail();

        $paketLamaId = $pelanggan->paket_id;
        $pelanggan->update([
            'paket_id' => $paketBaru->id,
        ]);

        $msg = $paketLamaId
            ? "Paket internet berhasil diubah ke {$paketBaru->nama_paket}."
            : "Paket {$paketBaru->nama_paket} berhasil dipilih.";

        return redirect('/portal/paket')->with('success', $msg);
    })->name('portal.paket.pilih');

    Route::get('/portal/pengaturan', function () {
        $user = User::find(session('user_id'));
        $pelanggan = Pelanggan::where('user_id', session('user_id'))->with('paket')->first();

        return view('portal.pengaturan', compact('user', 'pelanggan'));
    })->name('portal.pengaturan');

    Route::post('/portal/pengaturan', function (Request $request) {
        $user = User::findOrFail(session('user_id'));
        $pelanggan = Pelanggan::where('user_id', $user->id)->first();

        $request->validate([
            'nama' => 'required|string|max:255',
            'telepon' => 'required|string|max:30|unique:users,phone,' . $user->id,
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'alamat' => 'nullable|string|max:500',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:6|confirmed',
        ]);

        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Password saat ini salah.']);
            }
            $user->password = $request->new_password;
        }

        $user->name = $request->nama;
        $user->phone = $request->telepon;
        $user->email = $request->email;
        $user->save();

        if ($pelanggan) {
            $pelanggan->update([
                'nama' => $request->nama,
                'telepon' => $request->telepon,
                'email' => $request->email,
                'alamat' => $request->alamat,
            ]);
        }

        session([
            'user_name' => $user->name,
            'user_email' => $user->email,
        ]);

        return redirect('/portal/pengaturan')->with('success', 'Data profil berhasil diperbarui.');
    })->name('portal.pengaturan.simpan');
});

Route::middleware(['auth', 'tenant', 'permission:dashboard.view'])->group(function () {

    Route::get('/dashboard', function () {
        $totalPelanggan = Pelanggan::where('tenant_id', session('tenant_id'))->count();
        $totalPaket = Paket::where('tenant_id', session('tenant_id'))->count();
        $tagihanBelumBayar = Tagihan::where('tenant_id', session('tenant_id'))
            ->where('status', 'belum_bayar')->count();
        $pembayaranBulanIni = Pembayaran::where('tenant_id', session('tenant_id'))
            ->whereMonth('tanggal_bayar', now()->month)
            ->sum('jumlah');

        return view('dashboard.index', compact(
            'totalPelanggan',
            'totalPaket',
            'tagihanBelumBayar',
            'pembayaranBulanIni'
        ));
    })->name('dashboard');

    Route::get('/pelanggan', function () {
        $pelanggan = Pelanggan::where('tenant_id', session('tenant_id'))
            ->with('paket')
            ->orderByRaw("CASE WHEN status = 'menunggu' THEN 0 ELSE 1 END")
            ->latest()
            ->get();
        return view('pelanggan.index', compact('pelanggan'));
    })->name('pelanggan.index');

    Route::post('/pelanggan/{id}/setujui', function ($id) {
        $pelanggan = Pelanggan::where('tenant_id', session('tenant_id'))->where('id', $id)->firstOrFail();
        $pelanggan->update([
            'status' => 'aktif',
            'tanggal_aktif' => now()->toDateString(),
        ]);

        if ($pelanggan->user_id) {
            User::where('id', $pelanggan->user_id)->update(['is_active' => true]);
        }

        return back()->with('success', "Pelanggan {$pelanggan->nama} berhasil disetujui dan diaktifkan.");
    })->name('pelanggan.setujui');

    Route::delete('/pelanggan/{id}/tolak', function ($id) {
        $pelanggan = Pelanggan::where('tenant_id', session('tenant_id'))->where('id', $id)->firstOrFail();
        $nama = $pelanggan->nama;
        $userId = $pelanggan->user_id;

        $pelanggan->delete();

        if ($userId) {
            User::where('id', $userId)->delete();
        }

        return back()->with('success', "Pendaftaran {$nama} berhasil ditolak dan dihapus.");
    })->name('pelanggan.tolak');

    Route::get('/paket', function () {
        $paket = Paket::where('tenant_id', session('tenant_id'))->get();
        return view('paket.index', compact('paket'));
    })->name('paket.index');

    Route::get('/tagihan', function () {
        $tagihan = Tagihan::where('tenant_id', session('tenant_id'))
            ->with('pelanggan')
            ->get();
        return view('tagihan.index', compact('tagihan'));
    })->name('tagihan.index');

    Route::get('/pembayaran', function () {
        $pembayaran = Pembayaran::where('tenant_id', session('tenant_id'))
            ->with(['pelanggan', 'tagihan'])
            ->get();
        return view('pembayaran.index', compact('pembayaran'));
    })->name('pembayaran.index');

    Route::get('/laporan', function () {
        $totalPendapatan = Pembayaran::where('tenant_id', session('tenant_id'))
            ->where('status', 'lunas')->sum('jumlah');
        $totalTagihan = Tagihan::where('tenant_id', session('tenant_id'))
            ->sum('jumlah');
        $tagihanLunas = Tagihan::where('tenant_id', session('tenant_id'))
            ->where('status', 'lunas')->count();
        $tagihanBelumBayar = Tagihan::where('tenant_id', session('tenant_id'))
            ->where('status', 'belum_bayar')->count();

        return view('laporan.index', compact(
            'totalPendapatan',
            'totalTagihan',
            'tagihanLunas',
            'tagihanBelumBayar'
        ));
    })->name('laporan.index');

    Route::get('/pengaturan', function () {
        $tenant = \App\Models\Tenant::find(session('tenant_id'));
        $settings = \App\Models\TenantSetting::where('tenant_id', session('tenant_id'))
            ->get(['key', 'value'])
            ->mapWithKeys(fn ($s) => [$s->key => is_array($s->value) ? ($s->value[0] ?? '') : $s->value])
            ->toArray();
        $users = \App\Models\User::where('tenant_id', session('tenant_id'))->get();
        return view('pengaturan.index', compact('tenant', 'settings', 'users'));
    })->name('pengaturan.index');

    Route::post('/pengaturan/profil', function (Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:500',
        ]);
        \App\Models\Tenant::where('id', session('tenant_id'))->update($request->only('name', 'email', 'phone', 'address'));
        return back()->with('success', 'Profil perusahaan berhasil diperbarui.');
    })->name('pengaturan.profil');

    Route::post('/pengaturan/billing', function (Request $request) {
        $request->validate([
            'grace_days' => 'required|integer|min:0|max:30',
            'reminder_days' => 'required|integer|min:1|max:30',
        ]);
        $tid = session('tenant_id');
        \App\Models\TenantSetting::updateOrCreate(
            ['tenant_id' => $tid, 'key' => 'billing.suspension_grace_days'],
            ['value' => $request->grace_days, 'type' => 'integer']
        );
        \App\Models\TenantSetting::updateOrCreate(
            ['tenant_id' => $tid, 'key' => 'billing.reminder_days_before'],
            ['value' => $request->reminder_days, 'type' => 'integer']
        );
        return back()->with('success', 'Pengaturan billing berhasil diperbarui.');
    })->name('pengaturan.billing');

    Route::post('/pengaturan/whatsapp', function (Request $request) {
        $request->validate([
            'wa_provider' => 'required|in:disabled,fonnte,wablas',
        ]);
        $tid = session('tenant_id');
        \App\Models\TenantSetting::updateOrCreate(
            ['tenant_id' => $tid, 'key' => 'whatsapp.provider'],
            ['value' => $request->wa_provider, 'type' => 'string']
        );
        return back()->with('success', 'Pengaturan WhatsApp berhasil diperbarui.');
    })->name('pengaturan.whatsapp');

    Route::post('/pengaturan/template', function (Request $request) {
        $request->validate([
            'tpl_suspend' => 'nullable|string|max:1000',
            'tpl_reactivate' => 'nullable|string|max:1000',
            'tpl_due_reminder' => 'nullable|string|max:1000',
        ]);
        $tid = session('tenant_id');
        $map = [
            'tpl_suspend' => 'whatsapp.templates.suspend',
            'tpl_reactivate' => 'whatsapp.templates.reactivate',
            'tpl_due_reminder' => 'whatsapp.templates.due_reminder',
        ];
        foreach ($map as $field => $key) {
            if ($request->filled($field)) {
                \App\Models\TenantSetting::updateOrCreate(
                    ['tenant_id' => $tid, 'key' => $key],
                    ['value' => $request->$field, 'type' => 'string']
                );
            }
        }
        return back()->with('success', 'Template pesan berhasil diperbarui.');
    })->name('pengaturan.template');

    // Hotspot Voucher routes
    Route::get('/hotspot/voucher', function (Request $request) {
        $tenantId = session('tenant_id');
        $query = Voucher::where('tenant_id', $tenantId)->with('profile');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('code', 'like', "%{$q}%")
                    ->orWhere('batch_code', 'like', "%{$q}%");
            });
        }

        if ($request->filled('profile_id')) {
            $query->where('profile_id', $request->profile_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('batch')) {
            $query->where('batch_code', $request->batch);
        }

        $vouchers = $query->latest()->paginate(25)->withQueryString();

        $profiles = VoucherProfile::where('tenant_id', $tenantId)->where('is_active', true)->get();
        $batches = Voucher::where('tenant_id', $tenantId)
            ->whereNotNull('batch_code')
            ->select('batch_code')
            ->distinct()
            ->latest('created_at')
            ->pluck('batch_code');

        // Statistik
        $totalVouchers = Voucher::where('tenant_id', $tenantId)->count();
        $unusedCount = Voucher::where('tenant_id', $tenantId)->where('status', 'unused')->count();
        $usedCount = Voucher::where('tenant_id', $tenantId)->where('status', 'used')->count();
        $potentialRevenue = Voucher::withoutGlobalScopes()
            ->where('vouchers.tenant_id', $tenantId)
            ->where('vouchers.status', 'used')
            ->join('voucher_profiles', 'vouchers.profile_id', '=', 'voucher_profiles.id')
            ->sum('voucher_profiles.price');

        return view('hotspot.voucher.index', compact(
            'vouchers',
            'profiles',
            'batches',
            'totalVouchers',
            'unusedCount',
            'usedCount',
            'potentialRevenue'
        ));
    })->name('hotspot.voucher');

    Route::post('/hotspot/voucher/generate', function (Request $request) {
        $request->validate([
            'profile_id' => 'required|exists:voucher_profiles,id',
            'quantity' => 'required|integer|min:1|max:500',
            'char_length' => 'required|integer|min:4|max:12',
            'char_type' => 'required|in:numeric,alpha_lower,alpha_upper,mixed',
            'mode' => 'required|in:same,different',
            'prefix' => 'nullable|string|max:10',
        ]);

        $tenantId = session('tenant_id');
        $batchCode = 'BATCH-' . date('Ymd-His');
        $prefix = strtoupper((string) $request->prefix);
        $qty = (int) $request->quantity;
        $len = (int) $request->char_length;
        $charType = $request->char_type;
        $mode = $request->mode;
        $profileId = $request->profile_id;

        $pool = match ($charType) {
            'numeric' => '23456789',
            'alpha_lower' => 'abcdefghjkmnpqrstuvwxyz',
            'alpha_upper' => 'ABCDEFGHJKLMNPQRSTUVWXYZ',
            default => '23456789ABCDEFGHJKLMNPQRSTUVWXYZ',
        };

        $created = 0;
        $attempts = 0;
        $maxAttempts = $qty * 5;

        while ($created < $qty && $attempts < $maxAttempts) {
            $attempts++;
            $randomStr = '';
            for ($i = 0; $i < $len; $i++) {
                $randomStr .= $pool[random_int(0, strlen($pool) - 1)];
            }
            $code = $prefix . $randomStr;

            // Pastikan unik per tenant
            if (Voucher::withoutGlobalScopes()->where('tenant_id', $tenantId)->where('code', $code)->exists()) {
                continue;
            }

            $password = ($mode === 'same') ? $code : substr(str_shuffle('23456789ABCDEFGHJKMNPQRSTUVWXYZ'), 0, 6);

            Voucher::create([
                'tenant_id' => $tenantId,
                'profile_id' => $profileId,
                'code' => $code,
                'password' => $password,
                'status' => 'unused',
                'batch_code' => $batchCode,
            ]);

            $created++;
        }

        return redirect('/hotspot/voucher?batch=' . $batchCode)
            ->with('success', "Berhasil men-generate {$created} voucher baru (Kode Batch: {$batchCode}).");
    })->name('hotspot.voucher.generate');

    Route::delete('/hotspot/voucher/{id}', function ($id) {
        $v = Voucher::where('tenant_id', session('tenant_id'))->where('id', $id)->firstOrFail();
        $code = $v->code;
        $v->delete();
        return back()->with('success', "Voucher {$code} berhasil dihapus.");
    })->name('hotspot.voucher.delete');

    Route::post('/hotspot/voucher/delete-batch', function (Request $request) {
        $tenantId = session('tenant_id');
        if ($request->filled('batch_code')) {
            $count = Voucher::where('tenant_id', $tenantId)
                ->where('batch_code', $request->batch_code)
                ->where('status', 'unused')
                ->delete();
            return back()->with('success', "Berhasil menghapus {$count} voucher belum dipakai dari batch {$request->batch_code}.");
        }

        if ($request->filled('delete_all_unused')) {
            $count = Voucher::where('tenant_id', $tenantId)->where('status', 'unused')->delete();
            return back()->with('success', "Berhasil membersihkan {$count} seluruh voucher yang belum dipakai.");
        }

        return back()->with('error', 'Tidak ada data yang dihapus.');
    })->name('hotspot.voucher.delete_batch');

    Route::get('/hotspot/voucher/print', function (Request $request) {
        $tenantId = session('tenant_id');
        $tenant = \App\Models\Tenant::find($tenantId);

        $query = Voucher::where('tenant_id', $tenantId)->with('profile');

        if ($request->filled('id')) {
            $query->where('id', $request->id);
        } elseif ($request->filled('batch')) {
            $query->where('batch_code', $request->batch);
        } elseif ($request->filled('ids')) {
            $ids = is_array($request->ids) ? $request->ids : explode(',', $request->ids);
            $query->whereIn('id', $ids);
        } else {
            $query->where('status', 'unused')->latest()->take(50);
        }

        $vouchers = $query->get();

        return view('hotspot.voucher.print', compact('vouchers', 'tenant'));
    })->name('hotspot.voucher.print');

    Route::get('/hotspot/profil', fn() => view('hotspot.profil.index'))->name('hotspot.profil');
    Route::get('/hotspot/member', fn() => view('hotspot.member.index'))->name('hotspot.member');
    Route::get('/hotspot/rekap', fn() => view('hotspot.rekap.index'))->name('hotspot.rekap');

    // PPPoE route
    Route::get('/pppoe', fn() => view('pppoe.index'))->name('pppoe.index');

});
