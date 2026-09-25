<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $certificate->certificate_number }} - {{ $certificate->recipient_name }}</title>
    <style>
        @page {
            size: a4 landscape;
            margin: 8mm 10mm;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: "DejaVu Serif", "Times New Roman", Georgia, serif;
            background: #ffffff;
            color: #0f172a;
            margin: 0;
            padding: 0;
            width: 100%;
        }

@php
    $template = $certificate->template ?? 'formal';
    $palette = match($template) {
        'modern' => [
            'outer' => '#3730a3',
            'inner' => '#0891b2',
            'accent' => '#0284c7',
            'title' => '#1e1b4b',
            'header' => '#312e81',
            'muted' => '#475569',
            'soft' => '#ecfeff',
        ],
        'achievement' => [
            'outer' => '#78350f',
            'inner' => '#d97706',
            'accent' => '#b45309',
            'title' => '#451a03',
            'header' => '#78350f',
            'muted' => '#57534e',
            'soft' => '#fffbeb',
        ],
        'seminar' => [
            'outer' => '#4c1d95',
            'inner' => '#7c3aed',
            'accent' => '#6d28d9',
            'title' => '#2e1065',
            'header' => '#4c1d95',
            'muted' => '#52525b',
            'soft' => '#f5f3ff',
        ],
        default => [
            'outer' => '#0f2b48',
            'inner' => '#b45309',
            'accent' => '#b45309',
            'title' => '#0f2b48',
            'header' => '#0f2b48',
            'muted' => '#475569',
            'soft' => '#fffbeb',
        ],
    };
@endphp
        /* Certificate outer ornate border */
        .cert-outer-border {
            border: 3.5pt solid {{ $palette['outer'] }};
            padding: 3pt;
            background: #ffffff;
            width: 100%;
            height: 525pt;
            position: relative;
        }

        /* Certificate inner gold border */
        .cert-inner-border {
            border: 1.5pt solid {{ $palette['inner'] }};
            padding: 10pt 18pt 8pt 18pt;
            background: #ffffff;
            height: 100%;
            position: relative;
        }

        /* Decorative Corner Flourishes */
        .corner-flourish {
            position: absolute;
            width: 20pt;
            height: 20pt;
        }
        .corner-tl { top: 3pt; left: 3pt; border-top: 2.5pt solid {{ $palette['accent'] }}; border-left: 2.5pt solid {{ $palette['accent'] }}; }
        .corner-tr { top: 3pt; right: 3pt; border-top: 2.5pt solid {{ $palette['accent'] }}; border-right: 2.5pt solid {{ $palette['accent'] }}; }
        .corner-bl { bottom: 3pt; left: 3pt; border-bottom: 2.5pt solid {{ $palette['accent'] }}; border-left: 2.5pt solid {{ $palette['accent'] }}; }
        .corner-br { bottom: 3pt; right: 3pt; border-bottom: 2.5pt solid {{ $palette['accent'] }}; border-right: 2.5pt solid {{ $palette['accent'] }}; }

        /* Revoked Watermark Stamp */
        .watermark-revoked {
            position: absolute;
            top: 155pt;
            left: 70pt;
            width: 580pt;
            text-align: center;
            color: #dc2626;
            font-size: 42pt;
            font-weight: bold;
            font-family: "DejaVu Sans", Arial, sans-serif;
            letter-spacing: 5pt;
            border: 4pt dashed #dc2626;
            padding: 14pt 10pt;
            transform: rotate(-16deg);
            z-index: 999;
            opacity: 0.55;
            line-height: 1.1;
        }
        .watermark-sub {
            font-size: 10pt;
            letter-spacing: 2pt;
            font-weight: normal;
            margin-top: 5pt;
            text-transform: uppercase;
        }

        /* Institutional Header */
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .logo-cell {
            width: 65pt;
            vertical-align: middle;
            text-align: left;
        }
        .logo-img {
            width: 54pt;
            height: 54pt;
            display: block;
        }
        .inst-info-cell {
            vertical-align: middle;
            text-align: center;
            padding: 0 8pt;
        }
        .inst-title {
            font-size: 16pt;
            font-weight: bold;
            color: {{ $palette['title'] }};
            letter-spacing: 1.5pt;
            text-transform: uppercase;
        }
        .inst-subtitle {
            font-size: 8pt;
            color: {{ $palette['muted'] }};
            letter-spacing: 1.2pt;
            text-transform: uppercase;
            margin-top: 2pt;
            font-family: "DejaVu Sans", Arial, sans-serif;
        }
        .inst-faculty {
            font-size: 9pt;
            font-weight: bold;
            color: {{ $palette['accent'] }};
            letter-spacing: 1pt;
            text-transform: uppercase;
            margin-top: 2pt;
            font-family: "DejaVu Sans", Arial, sans-serif;
        }
        .serial-cell {
            width: 155pt;
            vertical-align: middle;
            text-align: right;
        }
        .serial-container {
            border-left: 2pt solid {{ $palette['accent'] }};
            padding-left: 8pt;
            text-align: right;
            display: inline-block;
        }
        .serial-lbl {
            font-size: 7.5pt;
            font-weight: bold;
            color: {{ $palette['muted'] }};
            text-transform: uppercase;
            font-family: "DejaVu Sans", Arial, sans-serif;
        }
        .serial-val {
            font-family: "Courier New", Courier, monospace;
            font-size: 9.5pt;
            font-weight: bold;
            color: {{ $palette['title'] }};
            margin-top: 1pt;
        }
        .serial-badge {
            display: inline-block;
            background: #ecfdf5;
            color: #047857;
            border: 1pt solid #a7f3d0;
            font-size: 6.5pt;
            font-weight: bold;
            padding: 1pt 4pt;
            border-radius: 2pt;
            margin-top: 2pt;
            font-family: "DejaVu Sans", Arial, sans-serif;
        }

        /* Gold Line Separator */
        .gold-divider {
            width: 100%;
            height: 1.5pt;
            background: {{ $palette['accent'] }};
            margin: 4pt 0 5pt 0;
        }

        /* Certificate Title */
        .award-title-section {
            text-align: center;
            margin: 2pt 0 3pt 0;
        }
        .award-title {
            font-size: 21pt;
            font-weight: bold;
            color: {{ $palette['title'] }};
            letter-spacing: 3pt;
            text-transform: uppercase;
        }
        .award-subtitle {
            font-size: 9.5pt;
            font-style: italic;
            color: {{ $palette['muted'] }};
            margin-top: 3pt;
        }

        /* Recipient Name Section */
        .recipient-section {
            text-align: center;
            margin: 4pt 0 3pt 0;
        }
        .recipient-name {
            font-size: 22pt;
            font-weight: bold;
            color: {{ $palette['title'] }};
            display: inline-block;
            border-bottom: 2pt solid {{ $palette['accent'] }};
            padding: 0 16pt 2pt 16pt;
            letter-spacing: 0.5pt;
        }
        .recipient-nim {
            font-size: 9pt;
            color: {{ $palette['muted'] }};
            margin-top: 2pt;
            font-family: "DejaVu Sans", Arial, sans-serif;
        }

        /* Academic Body Statement */
        .statement-section {
            text-align: center;
            font-size: 10pt;
            line-height: 1.3;
            color: {{ $palette['muted'] }};
            max-width: 88%;
            margin: 3pt auto 5pt auto;
        }
        .major-title {
            font-size: 13.5pt;
            font-weight: bold;
            color: {{ $palette['title'] }};
            margin-top: 2pt;
            letter-spacing: 0.5pt;
        }
        .major-desc {
            font-size: 8.5pt;
            color: {{ $palette['muted'] }};
            font-style: italic;
            margin-top: 1pt;
        }

        /* Bottom Section Table */
        .bottom-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6pt;
        }
        .crypto-cell {
            width: 44%;
            vertical-align: bottom;
            text-align: left;
        }
        .crypto-box {
            border: 1pt solid #cbd5e1;
            background: {{ $palette['soft'] }};
            border-left: 3pt solid {{ $palette['header'] }};
            border-radius: 3pt;
            padding: 4pt 6pt;
        }
        .crypto-inner-table {
            width: 100%;
            border-collapse: collapse;
        }
        .qr-col {
            width: 54pt;
            text-align: center;
            vertical-align: middle;
        }
        .crypto-info-col {
            padding-left: 6pt;
            vertical-align: middle;
            font-family: "DejaVu Sans", Arial, sans-serif;
            font-size: 7pt;
            color: {{ $palette['muted'] }};
            line-height: 1.25;
        }
        .crypto-badge {
            background: {{ $palette['header'] }};
            color: #ffffff;
            font-size: 6.5pt;
            font-weight: bold;
            padding: 1pt 3pt;
            border-radius: 2pt;
        }
        .mono-val {
            font-family: "Courier New", Courier, monospace;
            font-weight: bold;
            color: {{ $palette['title'] }};
        }

        /* Official Gold Security Seal */
        .seal-col {
            width: 18%;
            vertical-align: bottom;
            text-align: center;
        }
        .seal-circle {
            width: 56pt;
            height: 56pt;
            border-radius: 50%;
            border: 1.5pt dashed {{ $palette['accent'] }};
            background: {{ $palette['soft'] }};
            margin: 0 auto;
            text-align: center;
            padding-top: 6pt;
        }
        .seal-text-top {
            font-size: 5.5pt;
            font-weight: bold;
            color: {{ $palette['header'] }};
            font-family: "DejaVu Sans", Arial, sans-serif;
            letter-spacing: 0.5pt;
        }
        .seal-text-mid {
            font-size: 8pt;
            font-weight: bold;
            color: {{ $palette['accent'] }};
            letter-spacing: 1pt;
            margin: 1pt 0;
        }
        .seal-text-bot {
            font-size: 4.5pt;
            font-weight: bold;
            color: {{ $palette['header'] }};
            font-family: "DejaVu Sans", Arial, sans-serif;
            letter-spacing: 0.3pt;
        }

        /* Signatory Cell */
        .signatory-cell {
            width: 38%;
            vertical-align: bottom;
            text-align: center;
        }
        .digital-signature {
            display: inline-block;
            border: 1pt solid {{ $palette['accent'] }};
            background: {{ $palette['soft'] }};
            padding: 3pt 7pt;
            margin-bottom: 4pt;
            text-align: left;
            min-width: 190pt;
        }
        .digital-signature-title {
            color: {{ $palette['header'] }};
            font-size: 7pt;
            font-weight: bold;
            font-family: "DejaVu Sans", Arial, sans-serif;
            letter-spacing: 0.35pt;
        }
        .digital-signature-meta {
            color: {{ $palette['muted'] }};
            font-size: 5.5pt;
            margin-top: 1pt;
            font-family: "DejaVu Sans", Arial, sans-serif;
        }
        .issue-date {
            font-size: 9pt;
            color: {{ $palette['muted'] }};
            margin-bottom: 14pt;
        }
        .sign-name {
            font-size: 11pt;
            font-weight: bold;
            color: {{ $palette['title'] }};
            border-bottom: 1pt solid #94a3b8;
            display: inline-block;
            padding-bottom: 2pt;
            min-width: 160pt;
        }
        .sign-title {
            font-size: 7.5pt;
            color: {{ $palette['muted'] }};
            margin-top: 2pt;
            text-transform: uppercase;
            font-family: "DejaVu Sans", Arial, sans-serif;
        }
        .sign-legal {
            font-size: 5.5pt;
            color: #94a3b8;
            margin-top: 1pt;
            font-family: "DejaVu Sans", Arial, sans-serif;
        }
    </style>
</head>
<body>
    <div class="cert-outer-border">
        <div class="cert-inner-border">
            <!-- Decorative Corners -->
            <div class="corner-flourish corner-tl"></div>
            <div class="corner-flourish corner-tr"></div>
            <div class="corner-flourish corner-bl"></div>
            <div class="corner-flourish corner-br"></div>

            @if($certificate->status === 'revoked')
                <div class="watermark-revoked">
                    DICABUT / REVOKED
                    <div class="watermark-sub">Sertifikat ini telah dinyatakan tidak berlaku oleh otoritas universitas</div>
                </div>
            @endif

            <!-- Header -->
            <table class="header-table">
                <tr>
                    <td class="logo-cell">
                        @if(!empty($logoData))
                            <img src="data:{{ $logoData['mime'] }};base64,{{ $logoData['base64'] }}" class="logo-img" alt="Logo" />
                        @elseif(!empty($logoBase64))
                            <img src="data:image/png;base64,{{ $logoBase64 }}" class="logo-img" alt="Logo" />
                        @endif
                    </td>
                    <td class="inst-info-cell">
                        <div class="inst-title">{{ $certificate->institution_name ?? 'UNIVERSITAS BINA SARANA INFORMATIKA' }}</div>
                        <div class="inst-subtitle">LEMBAGA SERTIFIKASI PROFESI & DEWAN SENAT AKADEMIK</div>
                        <div class="inst-faculty">{{ strtoupper($certificate->department ?? 'FAKULTAS TEKNIK & INFORMATIKA') }}</div>
                    </td>
                    <td class="serial-cell">
                        <div class="serial-container">
                            <div class="serial-lbl">NO. REGISTRASI</div>
                            <div class="serial-val">{{ $certificate->certificate_number }}</div>
                            <div class="serial-badge">TERVERIFIKASI RESMI</div>
                        </div>
                    </td>
                </tr>
            </table>

            <div class="gold-divider"></div>

            <!-- Title -->
            <div class="award-title-section">
                <div class="award-title">{{ $certificate->category ?? 'IJAZAH & SERTIFIKAT KELULUSAN' }}</div>
                <div class="award-subtitle">Diberikan secara sah dan tervalidasi kepada:</div>
            </div>

            <!-- Recipient -->
            <div class="recipient-section">
                <div class="recipient-name">{{ $certificate->recipient_name }}</div>
                @if($certificate->recipient_identifier)
                    <div class="recipient-nim">Nomor Induk Mahasiswa (NIM): <strong>{{ $certificate->recipient_identifier }}</strong></div>
                @endif
            </div>

            <!-- Statement -->
            <div class="statement-section">
                @if($certificate->category === 'Piagam Penghargaan Akademik')
                    <div>Atas dedikasi, prestasi luar biasa, dan pencapaian akademik terbaik dalam:</div>
                @elseif($certificate->category === 'Sertifikat Kompetensi Profesi')
                    <div>Atas keberhasilan memenuhi seluruh standar uji kompetensi dan keahlian profesi pada bidang:</div>
                @elseif($certificate->category === 'Sertifikat Pelatihan & Workshop')
                    <div>Atas partisipasi aktif dan keberhasilan menyelesaikan seluruh rangkaian kegiatan:</div>
                @else
                    <div>Atas keberhasilan menyelesaikan seluruh program pendidikan dan memenuhi seluruh standar kompetensi kelulusan pada program studi:</div>
                @endif
                <div class="major-title">{{ $certificate->title }}</div>
                @if($certificate->description)
                    <div class="major-desc">"{{ $certificate->description }}"</div>
                @endif
            </div>

            <!-- Bottom: Crypto Seal, Official Seal, Signatory -->
            <table class="bottom-table">
                <tr>
                    <td class="crypto-cell">
                        <div class="crypto-box">
                            <table class="crypto-inner-table">
                                <tr>
                                    <td class="qr-col">
                                        <img src="data:image/svg+xml;base64,{{ $qrBase64 }}" width="50" height="50" alt="QR Code" />
                                    </td>
                                    <td class="crypto-info-col">
                                        <div><span class="crypto-badge">VERIFIKASI RESMI</span></div>
                                        <div style="font-weight: bold; color: {{ $palette['title'] }}; font-size: 8pt; margin-top: 2pt;">Keaslian Dokumen Terjamin</div>
                                        <div style="color: {{ $palette['muted'] }}; font-size: 6.5pt; line-height: 1.3; margin-top: 2pt;">
                                            Ijazah ini dilindungi pengaman digital universitas dan terdaftar dalam pangkalan data resmi.
                                        </div>
                                        <div style="color: #047857; font-size: 6.5pt; font-weight: bold; margin-top: 2pt;">
                                            Pindai QR Code untuk memvalidasi keabsahan data.
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </td>

                    <td class="seal-col">
                        <div class="seal-circle">
                            <div class="seal-text-top">UBSI RESMI</div>
                            <div class="seal-text-mid">SAH</div>
                            <div class="seal-text-bot">TERVALIDASI</div>
                        </div>
                    </td>

                    <td class="signatory-cell">
                        <div class="issue-date">Jakarta, {{ \Carbon\Carbon::parse($certificate->issued_date)->translatedFormat('d F Y') }}</div>
                        <div class="digital-signature">
                            <div class="digital-signature-title">✓ DITANDATANGANI SECARA DIGITAL</div>
                            <div class="digital-signature-meta">
                                {{ $cryptoKey->algorithm ?? 'RSA-2048' }} / {{ $cryptoKey->signature_scheme ?? 'RSA-PSS' }} / {{ $cryptoKey->hash_algorithm ?? 'SHA-256' }}
                            </div>
                            <div class="digital-signature-meta">
                                Kunci: {{ $cryptoKey->key_id ?? 'CERTIVA-ACTIVE-KEY' }}
                            </div>
                        </div>
                        <div class="sign-name">{{ $certificate->signatory_name ?? 'Prof. Dr. Ir. H. Budi Rahardjo, M.Sc.' }}</div>
                        <div class="sign-title">{{ $certificate->signatory_title ?? 'Rektor Universitas Bina Sarana Informatika' }}</div>
                        <div class="sign-legal">Tanda tangan elektronik bersertifikasi sah sesuai UU ITE No. 11/2008</div>
                    </td>
                </tr>
            </table>

        </div>
    </div>
</body>
</html>
