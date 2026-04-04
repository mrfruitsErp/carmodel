@extends('layouts.app')
@section('title', $saleVehicle->full_name)

@section('content')
<div class="min-h-screen bg-gray-50/50 px-4 py-6 md:px-8">

    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
                <a href="{{ route('marketplace.vehicles.index') }}" class="hover:text-gray-800">Veicoli</a>
                <span>/</span><span class="text-gray-800">{{ $saleVehicle->brand }} {{ $saleVehicle->model }}</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">{{ $saleVehicle->full_name }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ number_format($saleVehicle->mileage, 0, ',', '.') }} km · {{ ucfirst($saleVehicle->fuel_type) }} · {{ ucfirst($saleVehicle->transmission) }}</p>
        </div>
        <div class="flex gap-2 flex-wrap">
            @if($saleVehicle->status !== 'venduto')
                <a href="{{ route('marketplace.vehicles.edit', $saleVehicle) }}" class="px-4 py-2 text-sm border border-gray-200 rounded-xl bg-white hover:bg-gray-50 text-gray-700">Modifica</a>
                <form action="{{ route('marketplace.vehicles.sold', $saleVehicle) }}" method="POST" onsubmit="return confirm('Segnare come venduto?')">
                    @csrf
                    <input type="hidden" name="sold_price" value="{{ $saleVehicle->asking_price }}">
                    <button type="submit" class="px-4 py-2 text-sm bg-blue-600 text-white rounded-xl hover:bg-blue-700">✓ Segna venduto</button>
                </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

        {{-- Sinistra --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Foto --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-800">Foto</h3>
                    <label for="foto-upload" class="text-xs text-blue-600 hover:underline cursor-pointer">+ Aggiungi</label>
                    <input id="foto-upload" type="file" accept="image/*" multiple class="hidden" onchange="uploadFotos(this)">
                </div>
                <div id="foto-grid" class="grid grid-cols-3 gap-2">
                    @forelse($saleVehicle->getMedia('sale_photos') as $i => $media)
                    <div class="relative group aspect-video rounded-lg overflow-hidden bg-gray-100">
                        <img src="{{ $media->getUrl('thumb') }}" class="w-full h-full object-cover" alt="Foto {{ $i+1 }}">
                        @if($i === 0)<div class="absolute top-1 left-1 bg-black/60 text-white text-[10px] px-1.5 py-0.5 rounded">Cover</div>@endif
                        <button onclick="deleteFoto({{ $media->id }}, this)" class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 text-xs opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">×</button>
                    </div>
                    @empty
                    <div class="col-span-3 py-8 text-center border-2 border-dashed border-gray-200 rounded-xl">
                        <p class="text-xs text-gray-400">Nessuna foto. Aggiungine almeno una per pubblicare.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- Dati veicolo --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="text-sm font-semibold text-gray-800 mb-4">Dati veicolo</h3>
                <div class="grid grid-cols-2 gap-4">
                    @foreach([
                        ['Marca',       $saleVehicle->brand],
                        ['Modello',     $saleVehicle->model],
                        ['Anno',        $saleVehicle->year],
                        ['Km',          number_format($saleVehicle->mileage,0,',','.').' km'],
                        ['Carburante',  ucfirst($saleVehicle->fuel_type)],
                        ['Cambio',      ucfirst($saleVehicle->transmission)],
                        ['Colore',      $saleVehicle->color ?? '—'],
                        ['Potenza',     $saleVehicle->power_hp ? $saleVehicle->power_hp.' CV' : '—'],
                        ['Condizione',  ucfirst($saleVehicle->condition)],
                        ['Proprietari', $saleVehicle->previous_owners ?? '—'],
                    ] as [$label, $value])
                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">{{ $label }}</p>
                        <p class="text-sm font-medium text-gray-800 mt-0.5">{{ $value }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Prezzi --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="text-sm font-semibold text-gray-800 mb-4">Prezzi</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Prezzo richiesta</span>
                        <span class="text-sm font-bold text-gray-900">€ {{ number_format($saleVehicle->asking_price,0,',','.') }}</span>
                    </div>
                    @if($saleVehicle->purchase_price)
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Prezzo acquisto</span>
                        <span class="text-sm text-gray-700">€ {{ number_format($saleVehicle->purchase_price,0,',','.') }}</span>
                    </div>
                    <div class="flex justify-between border-t border-gray-100 pt-2">
                        <span class="text-sm text-gray-500">Margine</span>
                        <span class="text-sm font-semibold text-emerald-600">€ {{ number_format($saleVehicle->margin,0,',','.') }} ({{ $saleVehicle->margin_percent }}%)</span>
                    </div>
                    @endif
                    <form action="{{ route('marketplace.update-price', $saleVehicle) }}" method="POST" class="flex gap-2 pt-2 border-t border-gray-100">
                        @csrf
                        <input type="number" name="price" value="{{ $saleVehicle->asking_price }}" step="100" class="flex-1 px-3 py-1.5 text-sm border border-gray-200 rounded-lg outline-none focus:ring-2 focus:ring-gray-900">
                        <button type="submit" class="px-3 py-1.5 text-xs bg-gray-800 text-white rounded-lg hover:bg-gray-700">Aggiorna</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Destra --}}
        <div class="lg:col-span-3 space-y-5">

            {{-- Piattaforme --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-800">Piattaforme di vendita</h3>
                    <span class="text-xs text-gray-400">{{ $saleVehicle->listings->where('status','published')->count() }} live</span>
                </div>
                @php
                    $allPlatforms   = ['autoscout24','automobile_it','ebay_motors','subito_it','facebook_marketplace'];
                    $platformLabels = ['autoscout24'=>'AutoScout24','automobile_it'=>'Automobile.it','ebay_motors'=>'eBay Motors','subito_it'=>'Subito.it','facebook_marketplace'=>'Facebook Marketplace'];
                    $listingsByPlatform = $saleVehicle->listings->keyBy('platform');
                @endphp
                <form action="{{ route('marketplace.publish', $saleVehicle) }}" method="POST">
                    @csrf
                    <div class="space-y-2 mb-4">
                        @foreach($allPlatforms as $p)
                        @php
                            $listing       = $listingsByPlatform->get($p);
                            $isEnabled     = $enabledPlatforms->contains($p);
                            $listingStatus = $listing?->status ?? 'not_configured';
                            $validation    = $validations[$p] ?? ['valid' => true, 'errors' => []];
                            $rowBg = match($listingStatus) { 'published'=>'border-emerald-200 bg-emerald-50/50', 'error'=>'border-red-200 bg-red-50/50', 'publishing'=>'border-blue-200 bg-blue-50/50', default=>'border-gray-100 bg-gray-50/50' };
                        @endphp
                        <div class="flex items-center justify-between p-4 rounded-xl border {{ $rowBg }} transition-all">
                            <div class="flex items-center gap-3">
                                @if($isEnabled && $listingStatus !== 'published')
                                    <input type="checkbox" name="platforms[]" value="{{ $p }}" id="p_{{ $p }}" {{ !$validation['valid'] ? 'disabled' : '' }} class="w-4 h-4 rounded border-gray-300 text-gray-900">
                                @elseif($listingStatus === 'published')
                                    <div class="w-4 h-4 rounded-full bg-emerald-500 flex items-center justify-center">
                                        <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    </div>
                                @else
                                    <div class="w-4 h-4 rounded border-2 border-gray-200"></div>
                                @endif
                                <label for="p_{{ $p }}" class="text-sm font-medium text-gray-700 cursor-pointer">{{ $platformLabels[$p] }}</label>
                                @if(!$isEnabled)<span class="text-xs text-gray-400 italic">Non configurata</span>@endif
                                @if($isEnabled && !$validation['valid'])<span class="text-xs text-amber-600" title="{{ implode(', ', $validation['errors']) }}">⚠ Dati mancanti</span>@endif
                            </div>
                            <div class="flex items-center gap-3">
                                @if($listing?->views > 0)<span class="text-xs text-gray-500">{{ number_format($listing->views,0,',','.') }} views</span>@endif
                                @if($listingStatus === 'published' && $listing?->external_url)<a href="{{ $listing->external_url }}" target="_blank" class="text-xs text-blue-600 hover:underline">Vedi →</a>@endif
                                @if(in_array($listingStatus, ['published','paused','error']))
                                    <form action="{{ route('marketplace.unpublish', $listing) }}" method="POST" class="inline">@csrf @method('DELETE')
                                        <button type="submit" class="text-xs text-red-500 hover:text-red-700">Rimuovi</button>
                                    </form>
                                @endif
                                @if($listingStatus === 'publishing')<span class="text-xs text-blue-500 italic">In corso...</span>@endif
                                @if($listingStatus === 'error')<span class="text-xs text-red-500 truncate max-w-[120px]" title="{{ $listing?->last_error_message }}">Errore</span>@endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="flex items-center gap-3 pt-2 border-t border-gray-100">
                        <div class="flex-1">
                            <label class="text-xs text-gray-500">Prezzo annunci (opzionale)</label>
                            <input type="number" name="price" placeholder="{{ $saleVehicle->asking_price }}" step="100" class="mt-1 w-full px-3 py-1.5 text-sm border border-gray-200 rounded-lg outline-none focus:ring-2 focus:ring-gray-900">
                        </div>
                        <button type="submit" class="mt-4 px-5 py-2 bg-gray-900 text-white text-sm rounded-xl hover:bg-gray-700 font-medium whitespace-nowrap">Pubblica selezionati</button>
                    </div>
                </form>
            </div>

            {{-- Lead --}}
            @if($saleVehicle->leads->isNotEmpty())
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="text-sm font-semibold text-gray-800 mb-4">Lead ricevuti <span class="text-xs font-normal text-gray-400">({{ $saleVehicle->leads->count() }})</span></h3>
                <div class="divide-y divide-gray-50">
                    @foreach($saleVehicle->leads as $lead)
                    <div class="py-3 flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-sm font-medium text-gray-900">{{ $lead->lead_name ?? 'Anonimo' }}</span>
                                @include('marketplace.partials._platform_badge', ['platform' => $lead->platform])
                                <span class="text-xs text-gray-400">{{ $lead->created_at->diffForHumans() }}</span>
                            </div>
                            @if($lead->lead_message)<p class="text-xs text-gray-600 mt-1 line-clamp-2">{{ $lead->lead_message }}</p>@endif
                            <div class="flex gap-3 mt-1 text-xs">
                                @if($lead->lead_email)<a href="mailto:{{ $lead->lead_email }}" class="text-blue-500 hover:underline">{{ $lead->lead_email }}</a>@endif
                                @if($lead->lead_phone)<a href="tel:{{ $lead->lead_phone }}" class="text-blue-500 hover:underline">{{ $lead->lead_phone }}</a>@endif
                            </div>
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-500 whitespace-nowrap">{{ ucfirst($lead->status) }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function deleteFoto(mediaId, btn) {
    if (!confirm('Eliminare questa foto?')) return;
    fetch(`/marketplace/vehicles/{{ $saleVehicle->id }}/foto/${mediaId}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
    }).then(r => r.json()).then(d => { if (d.ok) btn.closest('.relative').remove(); });
}
function uploadFotos(input) {
    const form = new FormData();
    form.append('photo', input.files[0]);
    form.append('_token', '{{ csrf_token() }}');
    fetch(`/marketplace/vehicles/{{ $saleVehicle->id }}/foto`, { method: 'POST', body: form })
        .then(r => r.json()).then(d => {
            const div = document.createElement('div');
            div.className = 'relative group aspect-video rounded-lg overflow-hidden bg-gray-100';
            div.innerHTML = `<img src="${d.thumb_url}" class="w-full h-full object-cover"><button onclick="deleteFoto(${d.id}, this)" class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 text-xs opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">×</button>`;
            document.getElementById('foto-grid').appendChild(div);
        });
}
</script>
@endpush
@endsection