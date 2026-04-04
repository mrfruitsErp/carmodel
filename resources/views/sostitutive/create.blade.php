@extends('layouts.app')
@section('title', 'Assegna Auto Sostitutiva')
@section('content')
<div style="margin-bottom:16px"><a href="{{ route('sostitutive.index') }}" style="color:var(--text3);text-decoration:none;font-size:13px">← Sostitutive</a></div>
<div class="card"><div style="color:var(--text3);padding:20px;font-size:13px">Usa <a href="{{ route('noleggio.create') }}?tipo=sostitutiva" style="color:var(--green)">Nuovo Contratto Noleggio</a> con tipo "sostitutiva".</div></div>
@endsection
