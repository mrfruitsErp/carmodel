@extends('layouts.app')
@section('title', $vehicle->exists ? 'Modifica '.$vehicle->display_name : 'Nuovo veicolo in vendita')

@section('content')
<div class="min-h-screen bg-gray-50/50 px-4 py-6 md:px-8">
<div class="max-w-4xl mx-auto">

    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
            <a href="{{ route('marketplace.vehicles.index') }}" class="hover:text-gray-800">Veicoli</a>
            <span>/</span><span class="text-gray-800">{{ $vehicle->exists ? 'Modifica' : 'Nuovo veicolo' }}</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">{{ $vehicle->exists ? 'Modifica '.$vehicle->display_name : 'Aggiungi veicolo in vendita' }}</h1>
    </div>

    <form action="{{ $vehicle->exists ? route('marketplace.vehicles.update', $vehicle) : route('marketplace.vehicles.store') }}"
          method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @if($vehicle->exists) @method('PUT') @endif

        {{-- Dati tecnici --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-800 mb-5 pb-3 border-b border-gray-100">Dati tecnici</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach([
                    ['brand','Marca','text','es. BMW',true],
                    ['model','Modello','text','es. 320d',true],
                    ['version','Versione','text','es. Sport Line',false],
                    ['plate','Targa','text','AB123CD',false],
                    ['vin','VIN','text','Numero telaio',false],
                    ['year','Anno','number','2021',true],
                ] as [$name, $label, $type, $placeholder, $required])
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">{{ $label }} @if($required)<span class="text-red-500">*</span>@endif</label>
                    <input type="{{ $type }}" name="{{ $name }}" value="{{ old($name, $vehicle->$name) }}" placeholder="{{ $placeholder }}" {{ $required ? 'required' : '' }}
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none @error($name) border-red-400 @enderror">
                    @error($name)<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                @endforeach

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Km <span class="text-red-500">*</span></label>
                    <input type="number" name="mileage" value="{{ old('mileage', $vehicle->mileage) }}" min="0" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-gray-900 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Carburante <span class="text-red-500">*</span></label>
                    <select name="fuel_type" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-gray-900 outline-none">
                        @foreach(['benzina','diesel','gpl','metano','elettrico','ibrido_benzina','ibrido_diesel','altro'] as $f)
                            <option value="{{ $f }}" {{ old('fuel_type', $vehicle->fuel_type) === $f ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$f)) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Cambio <span class="text-red-500">*</span></label>
                    <select name="transmission" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-gray-900 outline-none">
                        @foreach(['manuale','automatico','semiautomatico'] as $t)
                            <option value="{{ $t }}" {{ old('transmission', $vehicle->transmission) === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Carrozzeria</label>
                    <select name="body_type" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-gray-900 outline-none">
                        <option value="">â€” Seleziona â€”</option>
                        @foreach(['berlina','station_wagon','suv','coupÃ©','cabriolet','monovolume','van','pickup','altro'] as $b)
                            <option value="{{ $b }}" {{ old('body_type', $vehicle->body_type) === $b ? 'selected' : '' }}>{{ ucfirst($b) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Condizione</label>
                    <select name="condition" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-gray-900 outline-none">
                        @foreach(['eccellente','ottimo','buono','discreto','da_riparare'] as $c)
                            <option value="{{ $c }}" {{ old('condition', $vehicle->condition) === $c ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$c)) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Prezzi --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-800 mb-5 pb-3 border-b border-gray-100">Prezzi</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Prezzo richiesta (â‚¬) <span class="text-red-500">*</span></label>
                    <input type="number" name="asking_price" value="{{ old('asking_price', $vehicle->asking_price) }}" min="0" step="100" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-gray-900 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Prezzo acquisto (â‚¬) <span class="text-xs font-normal text-gray-400">interno</span></label>
                    <input type="number" name="purchase_price" value="{{ old('purchase_price', $vehicle->purchase_price) }}" min="0" step="100" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-gray-900 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Prezzo minimo (â‚¬) <span class="text-xs font-normal text-gray-400">interno</span></label>
                    <input type="number" name="min_price" value="{{ old('min_price', $vehicle->min_price) }}" min="0" step="100" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-gray-900 outline-none">
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="price_negotiable" value="1" id="negotiable" {{ old('price_negotiable', $vehicle->price_negotiable ?? true) ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300 text-gray-900">
                    <label for="negotiable" class="text-sm text-gray-600">Prezzo trattabile</label>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="vat_deductible" value="1" id="vat" {{ old('vat_deductible', $vehicle->vat_deductible) ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300 text-gray-900">
                    <label for="vat" class="text-sm text-gray-600">IVA detraibile</label>
                </div>
            </div>
        </div>

        {{-- Descrizione --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-800 mb-5 pb-3 border-b border-gray-100">Descrizione annuncio</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Titolo personalizzato <span class="text-xs font-normal text-gray-400">opzionale</span></label>
                    <input type="text" name="title" value="{{ old('title', $vehicle->title) }}" maxlength="200" placeholder="Lascia vuoto per generazione automatica" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-gray-900 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Descrizione</label>
                    <textarea name="description" rows="5" placeholder="Descrizione dettagliata del veicolo..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-gray-900 outline-none resize-none">{{ old('description', $vehicle->description) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Foto (solo creazione) --}}
        @if(!$vehicle->exists)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-800 mb-4">Foto</h2>
            <label class="flex flex-col items-center justify-center h-32 border-2 border-dashed border-gray-200 rounded-xl cursor-pointer hover:border-gray-400 hover:bg-gray-50 transition-colors">
                <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                <span class="text-sm text-gray-500">Clicca per caricare foto</span>
                <span class="text-xs text-gray-400 mt-0.5">JPG, PNG, WebP â€” max 10MB</span>
                <input type="file" name="photos[]" multiple accept="image/*" class="hidden" onchange="previewFotos(this)">
            </label>
            <div id="foto-preview" class="grid grid-cols-4 gap-2 mt-3"></div>
        </div>
        @endif

        {{-- Azioni --}}
        <div class="flex items-center justify-between pt-2">
            <a href="{{ route('marketplace.vehicles.index') }}" class="text-sm text-gray-500 hover:text-gray-800">â† Annulla</a>
            <div class="flex gap-2">
                <button type="submit" class="px-5 py-2.5 text-sm border border-gray-200 bg-white rounded-xl hover:bg-gray-50 text-gray-700">Salva bozza</button>
                <button type="submit" class="px-5 py-2.5 text-sm bg-gray-900 text-white rounded-xl hover:bg-gray-700 font-medium">{{ $vehicle->exists ? 'Salva modifiche' : 'Crea veicolo' }}</button>
            </div>
        </div>
    </form>
</div>
</div>

@push('scripts')
<script>
function previewFotos(input) {
    const preview = document.getElementById('foto-preview');
    preview.innerHTML = '';
    [...input.files].forEach(file => {
        const reader = new FileReader();
        reader.onload = e => {
            const div = document.createElement('div');
            div.className = 'aspect-video rounded-lg overflow-hidden bg-gray-100';
            div.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
            preview.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
}
</script>
@endpush
@endsection