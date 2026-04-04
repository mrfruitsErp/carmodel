@extends('layouts.app')
@section('title', 'Marketplace — Dashboard')

@section('content')
<div class="min-h-screen bg-gray-50/50 px-4 py-6 md:px-8">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Marketplace</h1>
            <p class="text-sm text-gray-500 mt-0.5">Gestione annunci multi-piattaforma</p>
        </div>
        <div class="flex items-center gap-2">
            <form action="{{ route('marketplace.sync.leads') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 text-sm border border-gray-200 rounded-xl bg-white hover:bg-gray-50 text-gray-600 transition-colors">
                    ↻ Sync lead
                </button>
            </form>
            <a href="{{ route('marketplace.vehicles.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm bg-gray-900 text-white rounded-xl hover:bg-gray-700 font-medium">
                + Nuovo veicolo
            </a>
        </div>
    </div>

    {{-- KPI --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 font-medium">Annunci live</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['listings_published'] }}</p>
            <p class="text-xs text-gray-400 mt-1">su {{ $stats['vehicles_active'] }} veicoli attivi</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 font-medium">Visualizzazioni totali</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_views'], 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">su tutte le piattaforme</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 font-medium">Lead nuovi</p>
            <p class="text-3xl font-bold text-emerald-600 mt-1">{{ $stats['leads_new'] }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $stats['leads_total'] }} totali</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            @if($stats['listings_error'] > 0)
                <p class="text-xs text-red-400 font-medium">Errori piattaforme</p>
                <p class="text-3xl font-bold text-red-500 mt-1">{{ $stats['listings_error'] }}</p>
                <p class="text-xs text-red-400 mt-1">annunci con problemi</p>
            @else
                <p class="text-xs text-gray-400 font-medium">Venduti</p>
                <p class="text-3xl font-bold text-blue-600 mt-1">{{ $stats['vehicles_sold'] }}</p>
                <p class="text-xs text-gray-400 mt-1">questo periodo</p>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

        {{-- Performance piattaforme --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 lg:col-span-2">
            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-4">Performance piattaforme</p>
            <div class="space-y-3">
                @forelse($stats['by_platform'] as $platform => $data)
                    @php $maxViews = collect($stats['by_platform'])->max('views') ?: 1; $pct = min(100, round(($data['views'] / $maxViews) * 100)); @endphp
                    <div class="flex items-center gap-4">
                        <div class="w-32 shrink-0">@include('marketplace.partials._platform_badge', ['platform' => $platform])</div>
                        <div class="flex-1">
                            <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-2 bg-gray-800 rounded-full" style="width:{{ $pct }}%"></div>
                            </div>
                        </div>
                        <div class="text-xs text-gray-500 w-44 text-right flex gap-3 justify-end">
                            <span>{{ number_format($data['views'], 0, ',', '.') }} views</span>
                            <span class="text-emerald-600">{{ $data['contacts'] }} contatti</span>
                        </div>
                        <span class="text-xs font-semibold text-gray-700 w-10 text-right">{{ $data['cnt'] }}</span>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <p class="text-sm text-gray-400">Nessun annuncio pubblicato.</p>
                        <a href="{{ route('marketplace.settings') }}" class="text-sm text-blue-600 hover:underline mt-1 inline-block">Configura le piattaforme →</a>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Stato stock --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-4">Stato stock</p>
            <div class="space-y-3">
                @foreach([
                    ['Attivi',        $stats['vehicles_active'],    'bg-emerald-500', 'bg-emerald-50',  'text-emerald-700'],
                    ['Bozze',         $stats['vehicles_draft'],     'bg-gray-400',    'bg-gray-50',     'text-gray-600'],
                    ['Venduti',       $stats['vehicles_sold'],      'bg-blue-500',    'bg-blue-50',     'text-blue-700'],
                    ['Errori annunci',$stats['listings_error'],     'bg-red-500',     'bg-red-50',      'text-red-600'],
                ] as [$label, $count, $dot, $bg, $text])
                <div class="flex items-center justify-between p-3 {{ $bg }} rounded-xl">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full {{ $dot }}"></span>
                        <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
                    </div>
                    <span class="text-lg font-bold {{ $text }}">{{ $count }}</span>
                </div>
                @endforeach
                <div class="pt-2 border-t border-gray-100">
                    <a href="{{ route('marketplace.vehicles.index') }}" class="block text-center text-sm text-gray-500 hover:text-gray-900 py-1">Vedi tutti →</a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Lead recenti --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 pt-6 pb-4 flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">Lead recenti</p>
                @if($stats['leads_new'] > 0)
                    <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">{{ $stats['leads_new'] }} nuovi</span>
                @endif
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($leads as $lead)
                <div class="px-6 py-3.5 flex items-start gap-3 hover:bg-gray-50/60 transition-colors">
                    <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-semibold text-gray-600 shrink-0 mt-0.5">
                        {{ strtoupper(substr($lead->lead_name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-sm font-medium text-gray-900">{{ $lead->lead_name ?? 'Anonimo' }}</span>
                            @include('marketplace.partials._platform_badge', ['platform' => $lead->platform])
                        </div>
                        @if($lead->saleVehicle)
                            <p class="text-xs text-blue-600 mt-0.5 truncate">{{ $lead->saleVehicle->full_name }}</p>
                        @endif
                        @if($lead->lead_message)
                            <p class="text-xs text-gray-500 mt-0.5 truncate">{{ $lead->lead_message }}</p>
                        @endif
                    </div>
                    <span class="text-xs text-gray-400 shrink-0">{{ $lead->created_at->diffForHumans() }}</span>
                </div>
                @empty
                <div class="px-6 py-10 text-center text-sm text-gray-400">Nessun lead ricevuto ancora.</div>
                @endforelse
            </div>
            @if($leads->count() > 0)
            <div class="px-6 py-3 border-t border-gray-50">
                <a href="{{ route('marketplace.leads.index') }}" class="text-sm text-gray-500 hover:text-gray-900">Tutti i lead →</a>
            </div>
            @endif
        </div>

        {{-- Errori --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 pt-6 pb-4">
                <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">Annunci con problemi</p>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($errors as $errListing)
                <div class="px-6 py-3.5 flex items-start gap-3">
                    <div class="w-2 h-2 rounded-full bg-red-500 shrink-0 mt-2"></div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-sm font-medium text-gray-900 truncate">{{ $errListing->saleVehicle?->full_name ?? 'Veicolo #'.$errListing->sale_vehicle_id }}</span>
                            @include('marketplace.partials._platform_badge', ['platform' => $errListing->platform])
                        </div>
                        <p class="text-xs text-red-500 mt-0.5 truncate">{{ $errListing->last_error_message }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $errListing->last_error_at?->diffForHumans() }}</p>
                    </div>
                    <a href="{{ route('marketplace.vehicles.show', $errListing->sale_vehicle_id) }}" class="text-xs text-blue-600 hover:underline shrink-0">Risolvi</a>
                </div>
                @empty
                <div class="px-6 py-10 text-center">
                    <div class="w-10 h-10 rounded-full bg-emerald-50 flex items-center justify-center mx-auto mb-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <p class="text-sm text-gray-400">Nessun errore!</p>
                </div>
                @endforelse
            </div>
            @if($errors->count() > 0)
            <div class="px-6 py-3 border-t border-gray-50">
                <form action="{{ route('marketplace.sync.stats') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-sm text-gray-500 hover:text-gray-900">Forza ri-sincronizzazione →</button>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection