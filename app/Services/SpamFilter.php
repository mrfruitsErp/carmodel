<?php

namespace App\Services;

/**
 * Filtro anti-spam multilivello per i form pubblici.
 *
 * Ritorna l'oggetto con esito:
 *  - isSpam (bool)
 *  - reason (string|null)  → motivo se è spam
 *
 * Uso:
 *   $result = SpamFilter::analyze($request->all(), $request);
 *   if ($result->isSpam) {
 *       // marca come spam, salva comunque per audit, NON inviare notifica
 *   }
 */
class SpamFilter
{
    /**
     * Pattern regex e parole chiave classiche dei bot spam.
     * Aggiungi qui ogni nuovo pattern che noti dal log.
     */
    private const SPAM_PATTERNS = [
        // BBCode link (classico bot spam: [url=...]testo[/url])
        '/\[url=.+?\]/i',
        '/\[link=.+?\]/i',
        '/\[\/url\]/i',
        // Markdown link sospetti (più di 2 link nel messaggio)
        '/\[[^\]]+\]\(https?:\/\/[^)]+\).*\[[^\]]+\]\(https?:\/\/[^)]+\)/s',
        // HTML tag (chi compila un form con HTML è bot)
        '/<\s*(a|script|iframe|img|object|embed|form|input)[\s>]/i',
        // Più di 3 URL nel messaggio
        '/(https?:\/\/[^\s]+){4,}/i',
    ];

    /** Parole chiave spam tipiche (case-insensitive). */
    private const SPAM_KEYWORDS = [
        'casino', 'кaзино', 'казино', 'gambling', 'betting',
        'viagra', 'cialis', 'pharmacy',
        'porn', 'xxx', 'escort',
        'crypto', 'bitcoin', 'forex', 'investment opportunity',
        'seo service', 'backlink', 'link building',
        'vpn', 'впн', 'proxy',
        'cheap loan', 'fast loan',
        'replica watch', 'rolex replica',
        'work from home', 'make money fast', 'earn $',
    ];

    /** TLD/domini email tipicamente usati dai bot. */
    private const SUSPICIOUS_EMAIL_DOMAINS = [
        'budgetthailandtravel.com',
        'mailinator.com', 'tempmail', 'guerrillamail',
        'yopmail', 'maildrop.cc',
        '.ru', '.cn', '.tk', '.ml', '.ga', '.cf',  // TLD sospetti
    ];

    /** Numeri di telefono che iniziano con codici di paesi non rilevanti (Cina, ecc.) */
    private const SUSPICIOUS_PHONE_PREFIXES = [
        '86', '+86',   // Cina
        '7', '+7',     // Russia
        '380', '+380', // Ucraina (NB: a volte legittimo)
        '234', '+234', // Nigeria
    ];

    /**
     * Analizza i dati del form e ritorna un risultato.
     *
     * @param  array  $data  Dati form (name, email, phone, message, ecc.)
     * @param  ?\Illuminate\Http\Request  $request  Per timestamp/honeypot/IP
     */
    public static function analyze(array $data, $request = null): SpamResult
    {
        // ─── 1. Honeypot — campo nascosto solo i bot lo riempiono ───
        if (!empty($data['website']) || !empty($data['url']) || !empty($data['homepage'])) {
            return new SpamResult(true, 'honeypot');
        }

        // ─── 2. Time-check — il form è stato inviato in <2s? È un bot ───
        if ($request && $request->filled('_form_ts')) {
            $ts = (int) $request->input('_form_ts');
            if ($ts > 0) {
                $elapsed = time() - $ts;
                if ($elapsed < 2) {
                    return new SpamResult(true, 'submit_too_fast');
                }
            }
        }

        $message = (string) ($data['message'] ?? '');
        $name    = (string) ($data['name'] ?? '');
        $email   = strtolower((string) ($data['email'] ?? ''));
        $phone   = preg_replace('/\s+/', '', (string) ($data['phone'] ?? ''));
        $combined = strtolower($name . ' ' . $message);

        // ─── 3. Pattern regex (BBCode, HTML, link multipli) ───
        foreach (self::SPAM_PATTERNS as $pattern) {
            if (preg_match($pattern, $message) || preg_match($pattern, $name)) {
                return new SpamResult(true, 'pattern_match');
            }
        }

        // ─── 4. Parole chiave spam ───
        foreach (self::SPAM_KEYWORDS as $kw) {
            if (mb_stripos($combined, $kw) !== false) {
                return new SpamResult(true, 'spam_keyword:' . $kw);
            }
        }

        // ─── 5. Cirillico/cinese/arabo nel testo (target italiano) ───
        // Se il messaggio contiene caratteri cirillici, cinesi o arabi è spam
        // (un cliente italiano scrive in italiano/inglese al massimo)
        if (preg_match('/[\x{0400}-\x{04FF}]/u', $combined)) {
            return new SpamResult(true, 'cyrillic_chars');
        }
        if (preg_match('/[\x{4E00}-\x{9FFF}]/u', $combined)) {
            return new SpamResult(true, 'chinese_chars');
        }
        if (preg_match('/[\x{0600}-\x{06FF}]/u', $combined)) {
            return new SpamResult(true, 'arabic_chars');
        }

        // ─── 6. Email da dominio sospetto ───
        if ($email) {
            $emailDomain = substr(strrchr($email, '@'), 1);
            foreach (self::SUSPICIOUS_EMAIL_DOMAINS as $sus) {
                // Match esatto o suffix (es. ".ru" come TLD)
                if ($sus[0] === '.' && str_ends_with($emailDomain, $sus)) {
                    return new SpamResult(true, 'suspicious_tld:' . $sus);
                }
                if ($sus[0] !== '.' && str_contains($emailDomain, $sus)) {
                    return new SpamResult(true, 'suspicious_domain:' . $sus);
                }
            }
        }

        // ─── 7. Telefono con prefisso sospetto ───
        if ($phone) {
            $phoneClean = ltrim($phone, '+');
            foreach (self::SUSPICIOUS_PHONE_PREFIXES as $prefix) {
                $prefixClean = ltrim($prefix, '+');
                if (str_starts_with($phoneClean, $prefixClean) && !str_starts_with($phoneClean, '39')) {
                    // 39 = Italia (38 = Ucraina ma non collide perché 380 inizia con 380)
                    return new SpamResult(true, 'suspicious_phone:' . $prefix);
                }
            }
        }

        // ─── 8. Messaggio troppo corto + presenza link ───
        if (strlen(trim($message)) < 30 && preg_match('/https?:\/\//i', $message)) {
            return new SpamResult(true, 'short_msg_with_link');
        }

        // ─── 9. Nome con caratteri non latini ───
        if ($name && !preg_match('/^[\p{Latin}\p{Common}\s.\'-]+$/u', $name)) {
            return new SpamResult(true, 'non_latin_name');
        }

        return new SpamResult(false, null);
    }
}

/**
 * Oggetto risultato semplice.
 */
class SpamResult
{
    public function __construct(
        public readonly bool $isSpam,
        public readonly ?string $reason
    ) {}
}
