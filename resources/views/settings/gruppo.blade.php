@extends('layouts.app')
@section('title', 'Impostazioni — '.($gruppiLabel[$gruppo] ?? ucfirst($gruppo)))
@section('content')
<div style="display:grid;grid-template-columns:180px 1fr;gap:16px;align-items:start">
  <div style="background:#111827;border-radius:var(--radius-lg);padding:6px;border:1px solid rgba(255,255,255,.06)">
    <div style="font-size:10px;font-weight:600;color:rgba(255,255,255,.25);letter-spacing:.1em;text-transform:uppercase;padding:8px 10px 4px">Sezioni</div>
    @foreach($gruppiLabel as $key => $label)
    <a href="{{ route('settings.gruppo', $key) }}" class="nav-item {{ $gruppo == $key ? 'active' : '' }}" style="border-radius:var(--radius);margin:2px 0;font-size:12px">{{ $label }}</a>
    @endforeach
    @if(auth()->user()->isAdmin())
    <div style="border-top:1px solid rgba(255,255,255,.06);margin:8px 0"></div>
    <a href="{{ route('settings.gruppo', 'permessi') }}" class="nav-item {{ $gruppo == 'permessi' ? 'active' : '' }}" style="border-radius:var(--radius);margin:2px 0;font-size:12px">Permessi operatori</a>
    <a href="{{ route('documenti-catalogo.index') }}" class="nav-item" style="border-radius:var(--radius);margin:2px 0;font-size:12px">Catalogo documenti</a>
    @endif
  </div>

  <div>
    @if($gruppo === 'permessi')
    <div class="card">
      <div class="card-title">Permessi operatori</div>
      @php $tutti = \App\Models\User::where('tenant_id', auth()->user()->tenant_id)->where('role','!=','admin')->get(); @endphp
      @forelse($tutti as $op)
      <form method="POST" action="{{ route('settings.permessi.aggiorna') }}" style="margin-bottom:20px;padding-bottom:20px;border-bottom:1px solid var(--border2)">
        @csrf
        <input type="hidden" name="user_id" value="{{ $op->id }}">
        <div style="font-weight:600;font-size:13px;margin-bottom:10px">{{ $op->name }} <span class="badge badge-gray">{{ ucfirst($op->role) }}</span></div>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:12px">
          @foreach($gruppiLabel as $gKey => $gLabel)
          @php $hasPerm = \DB::table('setting_permissions')->where('tenant_id', auth()->user()->tenant_id)->where('user_id', $op->id)->where('gruppo', $gKey)->where('can_edit', true)->exists(); @endphp
          <label style="display:flex;align-items:center;gap:6px;font-size:12px;cursor:pointer">
            <input type="checkbox" name="permessi[{{ $gKey }}]" value="1" {{ $hasPerm ? 'checked' : '' }}> {{ $gLabel }}
          </label>
          @endforeach
        </div>
        <button type="submit" class="btn btn-ghost btn-sm">Salva permessi</button>
      </form>
      @empty
      <div style="text-align:center;color:var(--text3);padding:20px">Nessun operatore trovato</div>
      @endforelse
    </div>

    @elseif($gruppo === 'sito_web')
    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- EDITOR SITO WEB                                            --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    @php
      $sw = $settings; // shorthand
      $v  = fn($k,$d='') => $sw[$k]->valore ?? \App\Models\Setting::defaultPerGruppo()['sito_web'][$k] ?? $d;
    @endphp

    {{-- Tabs --}}
    <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:16px" id="sito-tabs">
      @foreach([
        'seo'       => '🔍 SEO',
        'hero'      => '🏠 Hero',
        'testi'     => '✏️ Testi',
        'contatti'  => '📞 Contatti',
        'media'     => '🖼️ Foto & Media',
        'colori'    => '🎨 Colori',
        'analytics' => '📊 Analytics',
      ] as $tab => $label)
      <button type="button" onclick="sitoTab('{{ $tab }}')" id="tab-btn-{{ $tab }}"
        class="btn btn-ghost btn-sm" style="font-size:12px">{{ $label }}</button>
      @endforeach
    </div>

    {{-- ─── TAB: SEO ─── --}}
    <form method="POST" action="{{ route('settings.salva', 'sito_web') }}" enctype="multipart/form-data" id="form-sito_web">
    @csrf
    <div id="tab-seo" class="sito-tab-panel">
      <div class="card" style="margin-bottom:14px">
        <div class="card-title">🔍 SEO — Ottimizzazione motori di ricerca</div>

        <div class="form-group">
          <label class="form-label">Title tag globale <span style="color:var(--text3)">(tag &lt;title&gt;)</span></label>
          <input name="seo_site_title" class="form-input" value="{{ $v('seo_site_title') }}" placeholder="AleCar S.r.l. - Vendita Auto e Noleggio Torino">
          <div style="font-size:11px;color:var(--text3);margin-top:3px">Idealmente 50-60 caratteri. Attuale: <span id="title-count">{{ strlen($v('seo_site_title')) }}</span></div>
        </div>

        <div class="form-group">
          <label class="form-label">Meta description <span style="color:var(--text3)">(tag &lt;meta description&gt;)</span></label>
          <textarea name="seo_site_description" class="form-textarea" style="min-height:70px" placeholder="Descrizione breve del sito per Google..." oninput="document.getElementById('desc-count').textContent=this.value.length">{{ $v('seo_site_description') }}</textarea>
          <div style="font-size:11px;color:var(--text3);margin-top:3px">Idealmente 150-160 caratteri. Attuale: <span id="desc-count">{{ strlen($v('seo_site_description')) }}</span></div>
        </div>

        <div class="form-group">
          <label class="form-label">Parole chiave <span style="color:var(--text3)">(keywords, separate da virgola)</span></label>
          <input name="seo_keywords" class="form-input" value="{{ $v('seo_keywords') }}" placeholder="auto usate torino, noleggio auto, alecar">
        </div>

        <div class="form-group">
          <label class="form-label">Immagine Open Graph <span style="color:var(--text3)">(condivisione social, 1200×630px)</span></label>
          @if($v('seo_og_image'))
            <div style="margin-bottom:8px"><img src="{{ $v('seo_og_image') }}" style="max-height:80px;border-radius:6px;border:1px solid var(--border2)"></div>
          @endif
          <input type="file" name="seo_og_image" class="form-input" accept="image/*" style="padding:6px">
        </div>

        <div style="background:var(--bg3);border:1px solid var(--border2);border-radius:8px;padding:14px;margin-top:8px">
          <div style="font-size:11px;font-weight:700;color:var(--text3);margin-bottom:10px;text-transform:uppercase;letter-spacing:.05em">📋 Anteprima Google</div>
          <div style="font-size:18px;color:#8ab4f8;font-weight:500;margin-bottom:2px" id="preview-title">{{ $v('seo_site_title') }}</div>
          <div style="font-size:12px;color:#4caf50;margin-bottom:4px">https://alecar.it</div>
          <div style="font-size:13px;color:#bdc1c6;line-height:1.5" id="preview-desc">{{ $v('seo_site_description') }}</div>
        </div>

        {{-- Per-pagina SEO --}}
        <div style="border-top:1px solid var(--border2);margin-top:20px;padding-top:16px">
          <div style="font-size:12px;font-weight:700;color:var(--text2);margin-bottom:12px">📄 SEO per pagina</div>
          @foreach([
            ['home',          'Home',           'H1 / Title / Description'],
            ['auto_vendita',  'Auto in vendita','H1 / Title / Description'],
            ['noleggio',      'Noleggio',        'H1 / Title / Description'],
            ['servizi',       'Servizi',         'H1 / Title / Description'],
            ['chi_siamo',     'Chi siamo',       'H1 / Title / Description'],
            ['contatti',      'Contatti',        'H1 / Title / Description'],
          ] as [$slug, $nome, $hint])
          <div style="background:var(--bg3);border:1px solid var(--border2);border-radius:8px;padding:12px;margin-bottom:10px">
            <div style="font-size:12px;font-weight:700;color:var(--orange);margin-bottom:8px">📄 {{ $nome }}</div>
            <div class="two-col" style="gap:10px">
              <div class="form-group" style="margin-bottom:8px">
                <label class="form-label">H1 — Titolo principale</label>
                <input name="page_{{ $slug }}_h1" class="form-input" value="{{ $v('page_'.$slug.'_h1') }}" placeholder="Titolo H1 pagina {{ $nome }}">
              </div>
              <div class="form-group" style="margin-bottom:8px">
                <label class="form-label">H2 — Sottotitolo</label>
                <input name="page_{{ $slug }}_h2" class="form-input" value="{{ $v('page_'.$slug.'_h2') }}" placeholder="Sottotitolo H2">
              </div>
            </div>
            <div class="form-group" style="margin-bottom:8px">
              <label class="form-label">Meta title (lascia vuoto per usare il globale)</label>
              <input name="page_{{ $slug }}_title" class="form-input" value="{{ $v('page_'.$slug.'_title') }}" placeholder="{{ $v('seo_site_title') }}">
            </div>
            <div class="form-group" style="margin-bottom:0">
              <label class="form-label">Meta description</label>
              <textarea name="page_{{ $slug }}_description" class="form-textarea" style="min-height:50px" placeholder="Descrizione pagina {{ $nome }}">{{ $v('page_'.$slug.'_description') }}</textarea>
            </div>
          </div>
          @endforeach
        </div>

        <div style="margin-top:14px"><button type="submit" class="btn btn-primary">✓ Salva SEO</button></div>
      </div>
    </div>

    {{-- ─── TAB: HERO ─── --}}
    <div id="tab-hero" class="sito-tab-panel" style="display:none">
      <div class="card" style="margin-bottom:14px">
        <div class="card-title">🏠 Sezione Hero (prima schermata)</div>
        <div class="form-group">
          <label class="form-label">Badge sopra il titolo</label>
          <input name="hero_badge" class="form-input" value="{{ $v('hero_badge') }}" placeholder="TORINO — DAL 2018">
        </div>
        <div class="form-group">
          <label class="form-label">Titolo Hero (H1) — puoi usare HTML per colorare parole</label>
          <textarea name="hero_titolo" class="form-textarea" style="min-height:80px">{{ $v('hero_titolo') }}</textarea>
          <div style="font-size:11px;color:var(--text3);margin-top:3px">Esempio: Auto <span style="color:var(--orange)">&lt;span style="color:var(--orange)"&gt;selezionate&lt;/span&gt;</span> e noleggio</div>
        </div>
        <div class="form-group">
          <label class="form-label">Sottotitolo Hero</label>
          <textarea name="hero_sottotitolo" class="form-textarea" style="min-height:70px">{{ $v('hero_sottotitolo') }}</textarea>
        </div>
        <div class="two-col" style="gap:10px">
          <div class="form-group">
            <label class="form-label">Testo CTA principale</label>
            <input name="hero_cta1_testo" class="form-input" value="{{ $v('hero_cta1_testo') }}" placeholder="Vedi auto in vendita">
          </div>
          <div class="form-group">
            <label class="form-label">Testo CTA secondario</label>
            <input name="hero_cta2_testo" class="form-input" value="{{ $v('hero_cta2_testo') }}" placeholder="Noleggio veicoli">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Immagine Hero / Sfondo <span style="color:var(--text3)">(opzionale)</span></label>
          @if($v('hero_immagine'))
            <div style="margin-bottom:8px"><img src="{{ $v('hero_immagine') }}" style="max-height:100px;border-radius:6px;border:1px solid var(--border2)"></div>
          @endif
          <input type="file" name="hero_immagine" class="form-input" accept="image/*" style="padding:6px">
        </div>

        {{-- Vantaggi --}}
        <div style="border-top:1px solid var(--border2);margin-top:20px;padding-top:16px">
          <div style="font-size:12px;font-weight:700;color:var(--text2);margin-bottom:12px">⭐ Vantaggi (4 box sotto l'hero)</div>
          @for($i=1;$i<=4;$i++)
          <div style="background:var(--bg3);border:1px solid var(--border2);border-radius:8px;padding:12px;margin-bottom:10px">
            <div style="font-size:11px;font-weight:700;color:var(--text3);margin-bottom:8px">VANTAGGIO {{ $i }}</div>
            <div style="display:grid;grid-template-columns:60px 1fr 1fr;gap:10px">
              <div class="form-group" style="margin-bottom:0">
                <label class="form-label">Icona</label>
                <input name="vantaggio_{{ $i }}_icon" class="form-input" value="{{ $v('vantaggio_'.$i.'_icon') }}" placeholder="🔍">
              </div>
              <div class="form-group" style="margin-bottom:0">
                <label class="form-label">Titolo</label>
                <input name="vantaggio_{{ $i }}_titolo" class="form-input" value="{{ $v('vantaggio_'.$i.'_titolo') }}" placeholder="Titolo">
              </div>
              <div class="form-group" style="margin-bottom:0">
                <label class="form-label">Descrizione</label>
                <input name="vantaggio_{{ $i }}_desc" class="form-input" value="{{ $v('vantaggio_'.$i.'_desc') }}" placeholder="Descrizione breve">
              </div>
            </div>
          </div>
          @endfor
        </div>
        <div style="margin-top:14px"><button type="submit" class="btn btn-primary">✓ Salva Hero</button></div>
      </div>
    </div>

    {{-- ─── TAB: TESTI ─── --}}
    <div id="tab-testi" class="sito-tab-panel" style="display:none">
      <div class="card" style="margin-bottom:14px">
        <div class="card-title">✏️ Testi delle pagine</div>

        {{-- Chi siamo --}}
        <div style="background:var(--bg3);border:1px solid var(--border2);border-radius:8px;padding:14px;margin-bottom:14px">
          <div style="font-size:12px;font-weight:700;color:var(--orange);margin-bottom:10px">👤 Chi siamo</div>
          <div class="form-group">
            <label class="form-label">Titolo pagina (H1)</label>
            <input name="chi_siamo_h1" class="form-input" value="{{ $v('chi_siamo_h1') }}">
          </div>
          <div class="form-group">
            <label class="form-label">Titolo sezione (H2)</label>
            <input name="chi_siamo_h2" class="form-input" value="{{ $v('chi_siamo_h2') }}">
          </div>
          <div class="form-group">
            <label class="form-label">Testo principale</label>
            <textarea name="chi_siamo_testo" class="form-textarea" style="min-height:100px">{{ $v('chi_siamo_testo') }}</textarea>
          </div>
          <div class="two-col" style="gap:10px">
            <div class="form-group">
              <label class="form-label">La nostra missione</label>
              <textarea name="chi_siamo_missione" class="form-textarea" style="min-height:80px">{{ $v('chi_siamo_missione') }}</textarea>
            </div>
            <div class="form-group">
              <label class="form-label">La nostra visione</label>
              <textarea name="chi_siamo_visione" class="form-textarea" style="min-height:80px">{{ $v('chi_siamo_visione') }}</textarea>
            </div>
          </div>
        </div>

        {{-- Servizi --}}
        <div style="background:var(--bg3);border:1px solid var(--border2);border-radius:8px;padding:14px;margin-bottom:14px">
          <div style="font-size:12px;font-weight:700;color:var(--orange);margin-bottom:10px">🛠️ Servizi</div>
          <div class="form-group">
            <label class="form-label">Titolo pagina (H1)</label>
            <input name="servizi_h1" class="form-input" value="{{ $v('servizi_h1') }}">
          </div>
          <div class="form-group">
            <label class="form-label">Titolo sezione (H2)</label>
            <input name="servizi_h2" class="form-input" value="{{ $v('servizi_h2') }}">
          </div>
          <div class="form-group">
            <label class="form-label">Testo introduttivo</label>
            <textarea name="servizi_intro" class="form-textarea" style="min-height:70px">{{ $v('servizi_intro') }}</textarea>
          </div>
        </div>

        {{-- Azienda / Footer --}}
        <div style="background:var(--bg3);border:1px solid var(--border2);border-radius:8px;padding:14px;margin-bottom:14px">
          <div style="font-size:12px;font-weight:700;color:var(--orange);margin-bottom:10px">🏢 Azienda & Footer</div>
          <div class="form-group">
            <label class="form-label">Slogan azienda</label>
            <input name="azienda_slogan" class="form-input" value="{{ $v('azienda_slogan') }}">
          </div>
          <div class="form-group">
            <label class="form-label">Descrizione azienda <span style="color:var(--text3)">(usata nel footer e chi siamo)</span></label>
            <textarea name="azienda_descrizione" class="form-textarea" style="min-height:80px">{{ $v('azienda_descrizione') }}</textarea>
          </div>
          <div class="form-group">
            <label class="form-label">Testo footer</label>
            <textarea name="footer_descrizione" class="form-textarea" style="min-height:70px">{{ $v('footer_descrizione') }}</textarea>
          </div>
          <div class="two-col" style="gap:10px">
            <div class="form-group">
              <label class="form-label">Anno fondazione</label>
              <input name="azienda_anno" class="form-input" value="{{ $v('azienda_anno') }}" placeholder="2018">
            </div>
            <div class="form-group">
              <label class="form-label">P.IVA</label>
              <input name="azienda_piva" class="form-input" value="{{ $v('azienda_piva') }}">
            </div>
          </div>
        </div>

        <div style="margin-top:14px"><button type="submit" class="btn btn-primary">✓ Salva testi</button></div>
      </div>
    </div>

    {{-- ─── TAB: CONTATTI ─── --}}
    <div id="tab-contatti" class="sito-tab-panel" style="display:none">
      <div class="card" style="margin-bottom:14px">
        <div class="card-title">📞 Contatti & Recapiti</div>
        <div class="two-col" style="gap:10px">
          <div class="form-group">
            <label class="form-label">Telefono</label>
            <input name="azienda_telefono" class="form-input" value="{{ $v('azienda_telefono') }}" placeholder="+39 327 807 2650">
          </div>
          <div class="form-group">
            <label class="form-label">WhatsApp <span style="color:var(--text3)">(solo numeri, con prefisso, es. 393278072650)</span></label>
            <input name="azienda_whatsapp" class="form-input" value="{{ $v('azienda_whatsapp') }}" placeholder="393278072650">
          </div>
        </div>
        <div class="two-col" style="gap:10px">
          <div class="form-group">
            <label class="form-label">Email</label>
            <input name="azienda_email" class="form-input" value="{{ $v('azienda_email') }}" placeholder="alecarto7@gmail.com">
          </div>
          <div class="form-group">
            <label class="form-label">Indirizzo</label>
            <input name="azienda_indirizzo" class="form-input" value="{{ $v('azienda_indirizzo') }}" placeholder="Via Ignazio Collino 29, Torino">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Titolo pagina Contatti (H1)</label>
          <input name="contatti_h1" class="form-input" value="{{ $v('contatti_h1') }}">
        </div>
        <div class="form-group">
          <label class="form-label">Sottotitolo (H2)</label>
          <input name="contatti_h2" class="form-input" value="{{ $v('contatti_h2') }}">
        </div>
        <div class="form-group">
          <label class="form-label">Testo introduttivo</label>
          <textarea name="contatti_intro" class="form-textarea" style="min-height:70px">{{ $v('contatti_intro') }}</textarea>
        </div>
        <div class="form-group">
          <label class="form-label">Embed Google Maps <span style="color:var(--text3)">(incolla il codice iframe da Google Maps → Condividi → Incorpora)</span></label>
          <textarea name="contatti_maps_embed" class="form-textarea" style="min-height:80px;font-family:monospace;font-size:11px" placeholder='&lt;iframe src="https://www.google.com/maps/embed?..."&gt;&lt;/iframe&gt;'>{{ $v('contatti_maps_embed') }}</textarea>
        </div>
        {{-- Social --}}
        <div style="border-top:1px solid var(--border2);margin-top:14px;padding-top:14px">
          <div style="font-size:12px;font-weight:700;color:var(--text2);margin-bottom:10px">📱 Social Media</div>
          <div class="two-col" style="gap:10px">
            <div class="form-group">
              <label class="form-label">Facebook URL</label>
              <input name="social_facebook" class="form-input" value="{{ $v('social_facebook') }}" placeholder="https://facebook.com/alecar">
            </div>
            <div class="form-group">
              <label class="form-label">Instagram URL</label>
              <input name="social_instagram" class="form-input" value="{{ $v('social_instagram') }}" placeholder="https://instagram.com/alecar">
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">TikTok URL</label>
            <input name="social_tiktok" class="form-input" value="{{ $v('social_tiktok') }}" placeholder="https://tiktok.com/@alecar">
          </div>
        </div>
        <div style="margin-top:14px"><button type="submit" class="btn btn-primary">✓ Salva contatti</button></div>
      </div>
    </div>

    {{-- ─── TAB: MEDIA ─── --}}
    <div id="tab-media" class="sito-tab-panel" style="display:none">
      <div class="card" style="margin-bottom:14px">
        <div class="card-title">🖼️ Foto & Media</div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
          <div style="background:var(--bg3);border:1px solid var(--border2);border-radius:8px;padding:14px">
            <div style="font-size:12px;font-weight:700;color:var(--text2);margin-bottom:10px">Logo principale</div>
            @if($v('logo_url'))
              <img src="{{ $v('logo_url') }}" style="max-height:50px;margin-bottom:10px;display:block">
              <input type="hidden" name="logo_url_attuale" value="{{ $v('logo_url') }}">
            @endif
            <input type="file" name="logo_url" class="form-input" accept="image/*" style="padding:6px">
            <div style="font-size:10px;color:var(--text3);margin-top:4px">Formato PNG con sfondo trasparente consigliato</div>
          </div>
          <div style="background:var(--bg3);border:1px solid var(--border2);border-radius:8px;padding:14px">
            <div style="font-size:12px;font-weight:700;color:var(--text2);margin-bottom:10px">Favicon <span style="color:var(--text3)">(32×32px)</span></div>
            @if($v('logo_favicon'))
              <img src="{{ $v('logo_favicon') }}" style="width:32px;height:32px;margin-bottom:10px;display:block">
            @endif
            <input type="file" name="logo_favicon" class="form-input" accept="image/png,image/x-icon,image/svg+xml" style="padding:6px">
          </div>
        </div>

        {{-- Foto Chi siamo --}}
        <div style="background:var(--bg3);border:1px solid var(--border2);border-radius:8px;padding:14px;margin-bottom:14px">
          <div style="font-size:12px;font-weight:700;color:var(--text2);margin-bottom:10px">📸 Foto "Chi siamo"</div>
          @if($v('chi_siamo_foto'))
            <img src="{{ $v('chi_siamo_foto') }}" style="max-height:120px;border-radius:6px;margin-bottom:10px;display:block;border:1px solid var(--border2)">
          @endif
          <input type="file" name="chi_siamo_foto" class="form-input" accept="image/*" style="padding:6px">
        </div>

        {{-- Galleria --}}
        <div style="background:var(--bg3);border:1px solid var(--border2);border-radius:8px;padding:14px">
          <div style="font-size:12px;font-weight:700;color:var(--text2);margin-bottom:10px">🎞️ Galleria sito</div>
          @php
            $galleria = json_decode($v('galleria_foto','[]'), true) ?? [];
          @endphp
          @if(count($galleria) > 0)
          <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(100px,1fr));gap:8px;margin-bottom:12px">
            @foreach($galleria as $fotoUrl)
            <div style="position:relative">
              <img src="{{ $fotoUrl }}" style="width:100%;height:80px;object-fit:cover;border-radius:6px;border:1px solid var(--border2)">
              <label style="position:absolute;top:4px;right:4px;background:rgba(239,68,68,.9);border-radius:4px;padding:2px 5px;cursor:pointer;font-size:10px;color:#fff">
                <input type="checkbox" name="galleria_rimuovi[]" value="{{ $fotoUrl }}" style="display:none"> ✕
              </label>
            </div>
            @endforeach
          </div>
          <div style="font-size:11px;color:var(--text3);margin-bottom:10px">Seleziona le foto da rimuovere (spunta ✕) e salva</div>
          @else
          <div style="font-size:12px;color:var(--text3);margin-bottom:12px">Nessuna foto in galleria</div>
          @endif
          <div class="form-group">
            <label class="form-label">Aggiungi foto (puoi selezionarne più di una)</label>
            <input type="file" name="galleria_nuove[]" class="form-input" accept="image/*" multiple style="padding:6px">
          </div>
        </div>

        <div style="margin-top:14px"><button type="submit" class="btn btn-primary">✓ Salva media</button></div>
      </div>
    </div>

    {{-- ─── TAB: COLORI ─── --}}
    <div id="tab-colori" class="sito-tab-panel" style="display:none">
      <div class="card" style="margin-bottom:14px">
        <div class="card-title">🎨 Colori del sito</div>
        <div style="background:var(--bg3);border:1px solid var(--amber);border-radius:8px;padding:12px;margin-bottom:16px">
          <div style="font-size:12px;color:var(--amber)">⚠️ Modifica i colori con attenzione — influenzano tutta la grafica del sito</div>
        </div>
        <div class="two-col" style="gap:16px">
          <div class="form-group">
            <label class="form-label">Colore primario (arancio)</label>
            <div style="display:flex;gap:8px;align-items:center">
              <input type="color" name="colore_primario" value="{{ $v('colore_primario','#ff6b00') }}" style="width:48px;height:38px;border:none;border-radius:6px;cursor:pointer;background:transparent">
              <input type="text" id="colore_primario_txt" class="form-input" value="{{ $v('colore_primario','#ff6b00') }}" style="font-family:monospace" placeholder="#ff6b00"
                oninput="document.querySelector('[name=colore_primario]').value=this.value">
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Colore sfondo</label>
            <div style="display:flex;gap:8px;align-items:center">
              <input type="color" name="colore_sfondo" value="{{ $v('colore_sfondo','#0a0a0a') }}" style="width:48px;height:38px;border:none;border-radius:6px;cursor:pointer;background:transparent">
              <input type="text" id="colore_sfondo_txt" class="form-input" value="{{ $v('colore_sfondo','#0a0a0a') }}" style="font-family:monospace" placeholder="#0a0a0a"
                oninput="document.querySelector('[name=colore_sfondo]').value=this.value">
            </div>
          </div>
        </div>
        {{-- Preview colori --}}
        <div style="margin-top:12px;border-radius:8px;overflow:hidden;border:1px solid var(--border2)">
          <div id="color-preview" style="background:#0a0a0a;padding:20px;text-align:center">
            <div id="cp-title" style="font-size:20px;font-weight:800;color:#f0f0f0;margin-bottom:8px">Anteprima sito AleCar</div>
            <div id="cp-btn" style="display:inline-block;background:#ff6b00;color:#000;padding:10px 24px;border-radius:8px;font-weight:700;font-size:14px">Bottone primario</div>
          </div>
        </div>
        <div style="margin-top:14px"><button type="submit" class="btn btn-primary">✓ Salva colori</button></div>
      </div>
    </div>

    {{-- ─── TAB: ANALYTICS ─── --}}
    <div id="tab-analytics" class="sito-tab-panel" style="display:none">
      <div class="card" style="margin-bottom:14px">
        <div class="card-title">📊 Analytics & Tracking</div>
        <div class="form-group">
          <label class="form-label">Google Analytics 4 — ID misurazione <span style="color:var(--text3)">(es. G-XXXXXXXXXX)</span></label>
          <input name="google_analytics_id" class="form-input" value="{{ $v('google_analytics_id') }}" placeholder="G-XXXXXXXXXX">
          <div style="font-size:11px;color:var(--text3);margin-top:3px">Trova l'ID in Analytics → Amministrazione → Flussi di dati</div>
        </div>
        <div class="form-group">
          <label class="form-label">Google Tag Manager — ID contenitore <span style="color:var(--text3)">(es. GTM-XXXXXXX)</span></label>
          <input name="google_tag_manager" class="form-input" value="{{ $v('google_tag_manager') }}" placeholder="GTM-XXXXXXX">
        </div>
        <div style="background:var(--bg3);border:1px solid var(--border2);border-radius:8px;padding:12px;margin-top:8px">
          <div style="font-size:11px;font-weight:700;color:var(--text3);margin-bottom:6px">Stato attuale</div>
          <div style="font-size:12px">
            GA4: <strong style="color:{{ $v('google_analytics_id') ? 'var(--green)' : 'var(--text3)' }}">{{ $v('google_analytics_id') ?: 'Non configurato' }}</strong><br>
            GTM: <strong style="color:{{ $v('google_tag_manager') ? 'var(--green)' : 'var(--text3)' }}">{{ $v('google_tag_manager') ?: 'Non configurato' }}</strong>
          </div>
        </div>
        <div style="margin-top:14px"><button type="submit" class="btn btn-primary">✓ Salva Analytics</button></div>
      </div>
    </div>

    </form>

    <script>
    function sitoTab(tab) {
      document.querySelectorAll('.sito-tab-panel').forEach(p => p.style.display = 'none');
      document.getElementById('tab-' + tab).style.display = 'block';
      document.querySelectorAll('#sito-tabs button').forEach(b => {
        b.style.background = 'transparent';
        b.style.borderColor = 'var(--border2)';
        b.style.color = 'var(--text2)';
      });
      const btn = document.getElementById('tab-btn-' + tab);
      if (btn) {
        btn.style.background = 'var(--orange)';
        btn.style.borderColor = 'var(--orange)';
        btn.style.color = '#000';
      }
    }
    // Apri primo tab di default
    sitoTab('seo');

    // Anteprima Google
    const titleInput = document.querySelector('[name=seo_site_title]');
    const descInput  = document.querySelector('[name=seo_site_description]');
    if (titleInput) titleInput.addEventListener('input', function(){
      document.getElementById('preview-title').textContent = this.value;
      document.getElementById('title-count').textContent = this.value.length;
    });
    if (descInput) descInput.addEventListener('input', function(){
      document.getElementById('preview-desc').textContent = this.value;
    });

    // Anteprima colori
    const colPrimario = document.querySelector('[name=colore_primario]');
    const colSfondo   = document.querySelector('[name=colore_sfondo]');
    function aggiornaPrev(){
      const p = colPrimario?.value || '#ff6b00';
      const s = colSfondo?.value || '#0a0a0a';
      const prev = document.getElementById('color-preview');
      const btn  = document.getElementById('cp-btn');
      if (prev) prev.style.background = s;
      if (btn)  btn.style.background  = p;
      document.getElementById('colore_primario_txt').value = p;
      document.getElementById('colore_sfondo_txt').value = s;
    }
    colPrimario?.addEventListener('input', aggiornaPrev);
    colSfondo?.addEventListener('input', aggiornaPrev);

    // Highlight foto galleria da rimuovere
    document.querySelectorAll('[name="galleria_rimuovi[]"]').forEach(cb => {
      cb.closest('div[style*="position:relative"]').addEventListener('click', function(){
        cb.checked = !cb.checked;
        this.querySelector('img').style.opacity = cb.checked ? '0.3' : '1';
        this.querySelector('label').style.background = cb.checked ? 'rgba(239,68,68,1)' : 'rgba(239,68,68,.9)';
      });
    });
    </script>

    @else
    <form method="POST" action="{{ route('settings.salva', $gruppo) }}">
      @csrf
      <div class="card">
        <div class="card-title">{{ $gruppiLabel[$gruppo] ?? ucfirst($gruppo) }}</div>
        @php $defaults = \App\Models\Setting::defaultPerGruppo()[$gruppo] ?? []; @endphp

        @if($gruppo === 'mail')
        <div class="two-col" style="gap:10px">
          <div class="form-group"><label class="form-label">Driver</label>
            <select name="mail_driver" class="form-select">
              <option value="smtp" {{ ($settings['mail_driver']->valore ?? 'smtp') === 'smtp' ? 'selected' : '' }}>SMTP</option>
              <option value="sendmail" {{ ($settings['mail_driver']->valore ?? '') === 'sendmail' ? 'selected' : '' }}>Sendmail</option>
            </select>
          </div>
          <div class="form-group"><label class="form-label">Cifratura</label>
            <select name="mail_encryption" class="form-select">
              <option value="tls" {{ ($settings['mail_encryption']->valore ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS</option>
              <option value="ssl" {{ ($settings['mail_encryption']->valore ?? '') === 'ssl' ? 'selected' : '' }}>SSL</option>
              <option value="" {{ ($settings['mail_encryption']->valore ?? '') === '' ? 'selected' : '' }}>Nessuna</option>
            </select>
          </div>
        </div>
        <div class="two-col" style="gap:10px">
          <div class="form-group"><label class="form-label">Host SMTP</label><input name="mail_host" class="form-input" value="{{ $settings['mail_host']->valore ?? 'smtp.legalmail.it' }}" placeholder="smtp.legalmail.it"></div>
          <div class="form-group"><label class="form-label">Porta</label><input name="mail_port" class="form-input" value="{{ $settings['mail_port']->valore ?? '587' }}" placeholder="587"></div>
        </div>
        <div class="two-col" style="gap:10px">
          <div class="form-group"><label class="form-label">Username (PEC)</label><input name="mail_username" class="form-input" value="{{ $settings['mail_username']->valore ?? '' }}" placeholder="tuamail@legalmail.it"></div>
          <div class="form-group"><label class="form-label">Password</label><input type="password" name="mail_password" class="form-input" value="{{ $settings['mail_password']->valore ?? '' }}" autocomplete="new-password"></div>
        </div>
        <div class="two-col" style="gap:10px">
          <div class="form-group"><label class="form-label">Nome mittente</label><input name="mail_from_name" class="form-input" value="{{ $settings['mail_from_name']->valore ?? '' }}" placeholder="AleCar S.r.l."></div>
          <div class="form-group"><label class="form-label">Email mittente</label><input name="mail_from_address" class="form-input" value="{{ $settings['mail_from_address']->valore ?? '' }}" placeholder="tuamail@legalmail.it"></div>
        </div>
        <div style="margin-top:12px;display:flex;gap:8px">
          <button type="submit" class="btn btn-primary">✓ Salva</button>
          <a href="{{ route('settings.mail.test') }}" class="btn btn-ghost">📨 Invia mail di test</a>
        </div>

        @elseif($gruppo === 'ai')
        @php
          $savedKey   = $settings['ai_api_key']->valore ?? '';
          $savedModel = $settings['ai_model']->valore   ?? '';
          // Rileva provider dalla chiave salvata
          $detected = 'non rilevato';
          $detectedSlug = '';
          if (str_starts_with($savedKey, 'sk-ant-')) { $detected = 'Anthropic (Claude)'; $detectedSlug = 'anthropic'; }
          elseif (str_starts_with($savedKey, 'AIza')) { $detected = 'Google (Gemini)';    $detectedSlug = 'google'; }
          $defaultModel = $detectedSlug === 'google' ? 'gemini-2.0-flash' : 'claude-3-5-sonnet-20240620';
        @endphp
        <div style="background:var(--bg3);border:1px solid var(--border2);border-radius:var(--radius);padding:12px;margin-bottom:16px">
          <div style="font-size:12px;font-weight:600;margin-bottom:4px">🤖 Intelligenza Artificiale</div>
          <div style="font-size:11px;color:var(--text3)">Incolla la chiave API: il provider viene rilevato automaticamente. Supportati: <strong>Anthropic Claude</strong> (chiavi <code>sk-ant-...</code>) e <strong>Google Gemini</strong> (chiavi <code>AIza...</code>, free tier su <a href="https://aistudio.google.com/apikey" target="_blank" style="color:var(--green)">aistudio.google.com</a>).</div>
        </div>
        <div class="form-group">
          <label class="form-label">Chiave API</label>
          <input type="password" name="ai_api_key" id="ai_api_key_input" class="form-input"
            value="{{ $savedKey }}"
            autocomplete="new-password"
            placeholder="sk-ant-... oppure AIza...">
          <div style="font-size:11px;margin-top:6px;display:flex;align-items:center;gap:6px" id="provider_badge">
            <span style="color:var(--text3)">Provider rilevato:</span>
            <span id="provider_label" style="font-weight:600;color:{{ $detectedSlug ? 'var(--green)' : 'var(--amber)' }}">{{ $detected }}</span>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Modello AI <span style="color:var(--text3);font-weight:400">(opzionale)</span></label>
          <input type="text" name="ai_model" id="ai_model_input" class="form-input"
            value="{{ $savedModel }}"
            placeholder="Lascia vuoto per usare il default: {{ $defaultModel }}">
          <div style="font-size:11px;color:var(--text3);margin-top:3px">
            Lascia vuoto per il default automatico. Esempi: <code>claude-3-5-sonnet-20240620</code>, <code>claude-haiku-4-5-20251001</code>, <code>gemini-2.0-flash</code>, <code>gemini-1.5-flash</code>
          </div>
        </div>
        {{-- Manteniamo ai_provider sincronizzato lato server in fase di salvataggio --}}
        <input type="hidden" name="ai_provider" id="ai_provider_hidden" value="{{ $detectedSlug ?: 'anthropic' }}">
        <script>
        (function(){
          const inp   = document.getElementById('ai_api_key_input');
          const lbl   = document.getElementById('provider_label');
          const hid   = document.getElementById('ai_provider_hidden');
          if (!inp || !lbl || !hid) return;

          function detect() {
            const v = (inp.value || '').trim();
            let provider = '', label = 'non rilevato', color = 'var(--amber)';
            if (v.startsWith('sk-ant-')) { provider = 'anthropic'; label = 'Anthropic (Claude)'; color = 'var(--green)'; }
            else if (v.startsWith('AIza')) { provider = 'google';  label = 'Google (Gemini)';    color = 'var(--green)'; }
            lbl.textContent = label;
            lbl.style.color = color;
            hid.value = provider || 'anthropic';
          }
          inp.addEventListener('input', detect);
        })();
        </script>
        @if(auth()->user()->isAdmin())
        <div style="background:var(--bg3);border:1px solid var(--border2);border-radius:var(--radius);padding:10px;margin-top:4px">
          <div style="font-size:10px;color:var(--text3);font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px">Info tecniche</div>
          <div style="font-size:11px;color:var(--text3)">
            Provider attivo: <strong style="color:var(--text2)">{{ $settings['ai_provider']->valore ?? 'anthropic' }}</strong><br>
            Stato: <strong style="color:{{ isset($settings['ai_api_key']) && $settings['ai_api_key']->valore ? 'var(--green)' : 'var(--red)' }}">{{ (isset($settings['ai_api_key']) && $settings['ai_api_key']->valore) ? '✓ Configurato' : '✗ Non configurato' }}</strong>
          </div>
        </div>
        @endif
        <div style="margin-top:16px">
          <button type="submit" class="btn btn-primary">✓ Salva configurazione AI</button>
        </div>

        @else
        @foreach($defaults as $chiave => $default)
        @php 
            $settingObj = $settings[$chiave] ?? null;
            $valore = $settingObj ? $settingObj->valore : $default; 
            $isSecret = $settingObj ? $settingObj->is_secret : false; 
            $label = ucwords(str_replace('_',' ',$chiave)); 
        @endphp
        <div class="form-group">
          <label class="form-label">{{ $label }}</label>
          @if(str_contains($chiave,'testo') || str_contains($chiave,'template'))
            <textarea name="{{ $chiave }}" class="form-textarea" style="min-height:120px">{{ $valore }}</textarea>
          @elseif(in_array($chiave,['notifica_campanellina','notifica_email','notifica_email_admin','firma_cartacea_attiva']))
            <div style="display:flex;gap:12px">
              <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px"><input type="radio" name="{{ $chiave }}" value="1" {{ $valore=='1'?'checked':'' }}> Abilitato</label>
              <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px"><input type="radio" name="{{ $chiave }}" value="0" {{ $valore!='1'?'checked':'' }}> Disabilitato</label>
            </div>
          @elseif($chiave==='sms_provider')
            <select name="{{ $chiave }}" class="form-select">
              @foreach(['self_hosted'=>'Self-hosted','twilio'=>'Twilio','esendex'=>'eSendex','smshosting'=>'SMSHOSTING','vonage'=>'Vonage'] as $v=>$l)
              <option value="{{ $v }}" {{ $valore==$v?'selected':'' }}>{{ $l }}</option>
              @endforeach
            </select>
          @elseif($chiave==='firma_modalita')
            <select name="{{ $chiave }}" class="form-select">
              <option value="self_hosted" {{ $valore=='self_hosted'?'selected':'' }}>Self-hosted</option>
              <option value="provider_esterno" {{ $valore=='provider_esterno'?'selected':'' }}>Provider esterno</option>
            </select>
          @elseif($chiave==='firma_provider')
            <select name="{{ $chiave }}" class="form-select">
              <option value="" {{ !$valore?'selected':'' }}>Nessuno</option>
              <option value="yousign" {{ $valore=='yousign'?'selected':'' }}>Yousign</option>
              <option value="namirial" {{ $valore=='namirial'?'selected':'' }}>Namirial</option>
              <option value="docusign" {{ $valore=='docusign'?'selected':'' }}>DocuSign</option>
            </select>
          @elseif($isSecret)
            <input type="password" name="{{ $chiave }}" class="form-input" value="{{ $valore }}" autocomplete="new-password">
          @elseif(in_array($chiave,['otp_timeout_minuti','otp_lunghezza','link_scadenza_giorni','upload_max_mb','km_alert_soglia','revisione_alert_giorni']))
            <input type="number" name="{{ $chiave }}" class="form-input" value="{{ $valore }}">
          @else
            <input type="text" name="{{ $chiave }}" class="form-input" value="{{ $valore }}">
          @endif
        </div>
        @endforeach
        <div style="margin-top:8px"><button type="submit" class="btn btn-primary">✓ Salva impostazioni</button></div>
        @endif
      </div>
    </form>
    @endif
  </div>
</div>
@endsection