@extends('layouts.app')
@section('title', 'Lead — Marketplace')

@section('content')
<div class="min-h-screen bg-gray-50/50 px-4 py-6 md:px-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Lead ricevuti</h1>
        <form action="{{ route('marketplace.sync.leads') }}" method="POST">
            @csrf
            <button class="px-4 py-2 text-sm border border-gray-200 rounded-xl bg-white hover:bg-gray-50 text-gray-600">↻ Aggiorna lead</button>
        </form>
    </div>

    <form method="GET" class="flex flex-wrap gap-3 mb-6">
        <select name="status" onchange="this.form.submit()" class="px-4 py-2 text-sm rounded-xl border border-gray-200 bg-white outline-none">
            <option value="">Tutti gli stati</option>
            @foreach(['nuovo','contattato','appuntamento','trattativa','vinto','perso'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <select name="platform" onchange="this.form.submit()" class="px-4 py-2 text-sm rounded-xl border border-gray-200 bg-white outline-none">
            <option value="">Tutte le piattaforme</option>
            @foreach(['autoscout24','automobile_it','ebay_motors','subito_it','facebook_marketplace'] as $p)
                <option value="{{ $p }}" {{ request('platform') === $p ? 'selected' : '' }}>{{ ucwords(str_replace('_',' ',$p)) }}</option>
            @endforeach
        </select>
    </form>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-xs font-medium text-gray-500 px-5 py-3 text-left uppercase tracking-wider">Contatto</th>
                    <th class="text-xs font-medium text-gray-500 px-5 py-3 text-left uppercase tracking-wider">Veicolo</th>
                    <th class="text-xs font-medium text-gray-500 px-5 py-3 text-left uppercase tracking-wider">Piattaforma</th>
                    <th class="text-xs font-medium text-gray-500 px-5 py-3 text-left uppercase tracking-wider">Stato</th>
                    <th class="text-xs font-medium text-gray-500 px-5 py-3 text-left uppercase tracking-wider">Ricevuto</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($leads as $lead)
                @php
                    $statusBadge = match($lead->status) {
                        'nuovo'        => 'bg-emerald-50 text-emerald-700',
                        'contattato'   => 'bg-blue-50 text-blue-700',
                        'appuntamento' => 'bg-purple-50 text-purple-700',
                        'trattativa'   => 'bg-yellow-50 text-yellow-700',
                        'vinto'        => 'bg-emerald-100 text-emerald-800',
                        default        => 'bg-gray-100 text-gray-400',
                    };
                @endphp
                <tr class="hover:bg-gray-50/60 transition-colors">
                    <td class="px-5 py-4">
                        <div class="font-medium text-sm text-gray-900">{{ $lead->lead_name ?? 'Anonimo' }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">
                            @if($lead->lead_email)<a href="mailto:{{ $lead->lead_email }}" class="text-blue-500 hover:underline">{{ $lead->lead_email }}</a>@endif
                            @if($lead->lead_phone) · <a href="tel:{{ $lead->lead_phone }}" class="text-blue-500 hover:underline">{{ $lead->lead_phone }}</a>@endif
                        </div>
                        @if($lead->lead_message)<p class="text-xs text-gray-400 mt-1 max-w-xs truncate">{{ $lead->lead_message }}</p>@endif
                    </td>
                    <td class="px-5 py-4 text-sm text-gray-700">
                        @if($lead->saleVehicle)
                            <a href="{{ route('marketplace.vehicles.show', $lead->sale_vehicle_id) }}" class="hover:text-blue-600 hover:underline">{{ $lead->saleVehicle->full_name }}</a>
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-4">@include('marketplace.partials._platform_badge', ['platform' => $lead->platform])</td>
                    <td class="px-5 py-4">
                        <form action="{{ route('marketplace.leads.update', $lead) }}" method="POST">
                            @csrf @method('PATCH')
                            <select name="status" onchange="this.form.submit()" class="text-xs px-2 py-1 rounded-full border-0 font-medium cursor-pointer outline-none {{ $statusBadge }}">
                                @foreach(['nuovo','contattato','appuntamento','trattativa','vinto','perso'] as $s)
                                    <option value="{{ $s }}" {{ $lead->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </form>
                    </td>
                    <td class="px-5 py-4 text-xs text-gray-400 whitespace-nowrap">
                        {{ $lead->created_at->format('d/m/Y H:i') }}<br>
                        <span class="text-gray-300">{{ $lead->created_at->diffForHumans() }}</span>
                    </td>
                    <td class="px-5 py-4 text-right">
                        @if($lead->lead_email)<a href="mailto:{{ $lead->lead_email }}" class="text-xs text-blue-600 hover:underline">Scrivi</a>
                        @elseif($lead->lead_phone)<a href="tel:{{ $lead->lead_phone }}" class="text-xs text-blue-600 hover:underline">Chiama</a>@endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-16 text-sm text-gray-400">Nessun lead trovato.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $leads->links() }}</div>
</div>
@endsection