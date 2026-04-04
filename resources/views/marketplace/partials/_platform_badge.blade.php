@php
$platforms = [
    'autoscout24'          => ['label' => 'AutoScout24',   'color' => 'bg-blue-100 text-blue-800',    'dot' => 'bg-blue-500'],
    'automobile_it'        => ['label' => 'Automobile.it', 'color' => 'bg-red-100 text-red-800',      'dot' => 'bg-red-500'],
    'ebay_motors'          => ['label' => 'eBay Motors',   'color' => 'bg-yellow-100 text-yellow-800','dot' => 'bg-yellow-500'],
    'subito_it'            => ['label' => 'Subito.it',     'color' => 'bg-orange-100 text-orange-800','dot' => 'bg-orange-500'],
    'facebook_marketplace' => ['label' => 'Facebook',      'color' => 'bg-indigo-100 text-indigo-800','dot' => 'bg-indigo-500'],
    'mobile_de'            => ['label' => 'mobile.de',     'color' => 'bg-teal-100 text-teal-800',    'dot' => 'bg-teal-500'],
    'olx'                  => ['label' => 'OLX',           'color' => 'bg-purple-100 text-purple-800','dot' => 'bg-purple-500'],
    'instagram'            => ['label' => 'Instagram',     'color' => 'bg-pink-100 text-pink-800',    'dot' => 'bg-pink-500'],
];
$p = $platforms[$platform] ?? ['label' => ucfirst($platform), 'color' => 'bg-gray-100 text-gray-700', 'dot' => 'bg-gray-400'];
@endphp
<span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium {{ $p['color'] }}">
    <span class="w-1.5 h-1.5 rounded-full {{ $p['dot'] }}"></span>
    {{ $p['label'] }}
</span>