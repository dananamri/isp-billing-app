@extends('layouts.app')

@section('title', 'Rekap Hotspot')

@section('content')
<header class="bg-white shadow-sm py-4 md:py-5 px-4 md:px-8 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
    <div>
        <h2 class="text-xl md:text-2xl font-bold text-gray-800">Rekap Hotspot</h2>
        <p class="text-gray-500 text-xs md:text-sm">Rekap penggunaan dan pendapatan hotspot</p>
    </div>
</header>
<div class="p-4 md:p-8">
    <div class="bg-white rounded-2xl shadow-sm p-8 text-center text-gray-400">
        Belum ada data rekap
    </div>
</div>
@endsection
