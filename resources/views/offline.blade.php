@extends('layouts.app')

@section('title', 'Çevrimdışı Mod - Antik Nümizmatik')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4 py-16">
    <div class="max-w-md w-full bg-slate-900 border border-slate-800 rounded-3xl p-8 text-center shadow-2xl relative overflow-hidden">
        <!-- Arka Plan Efekti -->
        <div class="absolute -top-24 -left-24 w-48 h-48 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -right-24 w-48 h-48 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="w-16 h-16 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center mx-auto mb-6 text-amber-400">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-1.414-1.414L3 3m8.293 8.293l1.414 1.414" />
            </svg>
        </div>

        <span class="inline-block bg-slate-800 border border-slate-700 text-slate-300 text-[10px] font-mono font-bold px-3 py-1 rounded-full uppercase tracking-wider mb-3">
            Müze & Arazi Çevrimdışı Modu
        </span>

        <h1 class="font-numis text-2xl font-bold text-slate-100 mb-3 tracking-wide">
            İnternet Bağlantısı Yok
        </h1>

        <p class="text-xs text-slate-400 leading-relaxed mb-6">
            Cihazınız şu anda çevrimdışı. Ancak daha önce ziyaret ettiğiniz sikke sayfalarına ve yüksek çözünürlüklü fotoğraflara yerel önbellek üzerinden erişebilirsiniz.
        </p>

        <div class="space-y-3">
            <button onclick="window.location.reload()" 
                    class="w-full bg-gradient-to-r from-amber-600 to-amber-500 hover:from-amber-500 hover:to-amber-400 text-slate-950 font-bold py-3 px-4 rounded-xl text-xs shadow-lg shadow-amber-500/20 transition flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                <span>Yeniden Bağlanmayı Dene</span>
            </button>

            <a href="{{ route('coins.index') }}" 
               class="block w-full bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold py-2.5 px-4 rounded-xl text-xs border border-slate-700 transition">
                Önbellekteki Kataloğa Git
            </a>
        </div>

        <div class="mt-6 pt-4 border-t border-slate-800 text-[11px] text-slate-500 font-mono">
            PWA Yerel Depolama (Cache Storage API) Aktif
        </div>
    </div>
</div>
@endsection

