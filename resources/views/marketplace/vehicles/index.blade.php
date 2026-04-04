@extends('layouts.app')
@section('title', 'Veicoli in vendita')

@section('content')
<div class="min-h-screen bg-gray-50/50 px-4 py-6 md:px-8">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Veicoli in vendita</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $vehicles->total() }} veicoli in gestione</p>
        </div>
        <a href="{{ route('marketplace.vehicles.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 text-white text-sm rounded-xl hover:bg-gray-700 font-medium">
            + Aggiungi veicolo
        </a>
    </div>

    {{-- Filtri --}}
    <form method="GET" class="flex flex-wrap gap-3 mb-6">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cerca marca, modello, targa..."
               class="flex-1 min-w-[200px] px-4 py-2 text-sm rounded-xl border border-gray-200 bg-white focus:ring-2 focus:ring-gray-900 outline-none">
        <select name="status" onchange="this.form.submit()" class="px-4 py-2 text-sm rounded-xl border border-gray-200 bg-white outline-none">
            <option value="">Tutti gli stati</option>
            @foreach(['bozza','attivo','venduto','sospeso'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        @if(request()->hasAny(['search','status']))
            <a href="{{ route('marketplace.vehicles.index') }}" class="px-4 py-2 text-sm rounded-xl border border-gray-200 bg-white text-gray-500 hover:bg-gray-50">Azzera</a>
        @endif
    </form>

    @if($vehicles->isEmpty())
        <div class="text-center py-24 bg-white rounded-2xl border border-gray-100">
            <p class="text-lg font-semibold text-gray-700 mb-1">Nessun veicolo</p>
            <p class="text-sm text-gray-400 mb-4">Aggiungi il primo veicolo per iniziare.</p>
            <a href="{{ route('marketplace.vehicles.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 text-white text-sm rounded-xl">+ Aggiungi veicolo</a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            @foreach($vehicles as $vehicle)
            @php
                $sc = match($vehicle->status) {
                    'attivo'    => ['badge' => 'bg-emerald-50 text-emerald-700', 'dot' => 'bg-emerald-500', 'label' => 'Attivo'],
                    'venduto'   => ['badge' => 'bg-blue-50 text-blue-700',       'dot' => 'bg-blue-500',    'label' => 'Venduto'],
                    'sospeso'   => ['badge' => 'bg-yellow-50 text-yellow-700',   'dot' => 'bg-yellow-500',  'label' => 'Sospeso'],
                    default     => ['badge' => 'bg-gray-100 text-gray-600',      'dot' => 'bg-gray-400',    'label' => 'Bozza'],
                };
            @endphp
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                <a href="{{ route('marketplace.vehicles.show', $vehicle) }}" class="block">
                    <div class="h-44 bg-gray-100 relative overflow-hidden">
                        @if($vehicle->primary_photo_url)
                            <img src="{{ $vehicle->primary_photo_url }}" alt="{{ $vehicle->full_name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                        @endif
                        <div class="absolute top-3 left-3">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold {{ $sc['badge'] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $sc['dot'] }}"></span>{{ $sc['label'] }}
                            </span>
                        </div>
                        @php $photoCount = $vehicle->getMedia('sale_photos')->count(); @endphp
                        @if($photoCount > 0)
                            <div class="absolute bottom-3 right-3 bg-black/60 text-white text-xs px-2 py-0.5 rounded-full">{{ $photoCount }} foto</div>
                        @endif
                    </div>
                </a>
                <div class="p-4">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <div class="min-w-0">
                            <h3 class="font-semibold text-gray-900 truncate text-sm">{{ $vehicle->full_name }}</h3>
                            @if($vehicle->version)<p class="text-xs text-gray-500 truncate">{{ $vehicle->version }}</p>@endif
                        </div>
                        <div class="text-right shrink-0">
                            <p class="font-bold text-gray-900 text-sm">€ {{ number_format($vehicle->asking_price, 0, ',', '.') }}</p>
                            @if($vehicle->purchase_price)<p class="text-xs text-emerald-600">+{{ $vehicle->margin_percent }}%</p>@endif
                        </div>
                    </div>
                    <div class="flex items-center gap-3 text-xs text-gray-500 mb-3">
                        <span>{{ number_format($vehicle->mileage, 0, ',', '.') }} km</span>
                        <span>·</span><span>{{ ucfirst($vehicle->fuel_type) }}</span>
                        <span>·</span><span>{{ ucfirst($vehicle->transmission) }}</span>
                    </div>
                    <div class="flex flex-wrap gap-1 mb-3 min-h-[20px]">
                        @foreach($vehicle->listings->where('status','published') as $listing)
                            @include('marketplace.partials._platform_badge', ['platform' => $listing->platform])
                        @endforeach
                        @if($vehicle->listings->where('status','published')->isEmpty())
                            <p class="text-xs text-gray-400 italic">Non ancora pubblicato</p>
                        @endif
                    </div>
                    <div class="flex items-center justify-between pt-3 border-t border-gray-50">
                        <div class="flex gap-4 text-xs text-gray-500">
                            @if($vehicle->listings->sum('views') > 0)<span>👁 {{ number_format($vehicle->listings->sum('views'), 0, ',', '.') }}</span>@endif
                            @if($vehicle->leads()->count() > 0)<span class="text-emerald-600 font-medium">✉ {{ $vehicle->leads()->count() }}</span>@endif
                        </div>
                        <a href="{{ route('marketplace.vehicles.show', $vehicle) }}" class="text-xs font-medium text-gray-700 hover:text-gray-900">Gestisci →</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $vehicles->links() }}</div>
    @endif
</div>
@endsection