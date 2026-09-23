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

        /* Certificate outer ornate border */
        .cert-outer-border {
            border: 3.5pt solid #0f2b48;
            padding: 3pt;
            background: #ffffff;
            width: 100%;
            height: 525pt;
            position: relative;
        }

        /* Certificate inner gold border */
        .cert-inner-border {
            border: 1.5pt solid #b45309;
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
        .corner-tl { top: 3pt; left: 3pt; border-top: 2.5pt solid #b45309; border-left: 2.5pt solid #b45309; }
        .corner-tr { top: 3pt; right: 3pt; border-top: 2.5pt solid #b45309; border-right: 2.5pt solid #b45309; }
        .corner-bl { bottom: 3pt; left: 3pt; border-bottom: 2.5pt solid #b45309; border-left: 2.5pt solid #b45309; }
        .corner-br { bottom: 3pt; right: 3pt; border-bottom: 2.5pt solid #b45309; border-right: 2.5pt solid #b45309; }

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
            color: #0f2b48;
            letter-spacing: 1.5pt;
            text-transform: uppercase;
        }
        .inst-subtitle {
            font-size: 8pt;
            color: #64748b;
            letter-spacing: 1.2pt;
            text-transform: uppercase;
            margin-top: 2pt;
            font-family: "DejaVu Sans", Arial, sans-serif;
        }
        .inst-faculty {
            font-size: 9pt;
            font-weight: bold;
            color: #b45309;
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
            border-left: 2pt solid #b45309;
            padding-left: 8pt;
            text-align: right;
            display: inline-block;
        }
        .serial-lbl {
            font-size: 7.5pt;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            font-family: "DejaVu Sans", Arial, sans-serif;
        }
        .serial-val {
            font-family: "Courier New", Courier, monospace;
            font-size: 9.5pt;
            font-weight: bold;
            color: #0f2b48;
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
            background: #b45309;
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
            color: #0f2b48;
            letter-spacing: 3pt;
            text-transform: uppercase;
        }
        .award-subtitle {
            font-size: 9.5pt;
            font-style: italic;
            color: #475569;
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
            color: #0f2b48;
            display: inline-block;
            border-bottom: 2pt solid #b45309;
            padding: 0 16pt 2pt 16pt;
            letter-spacing: 0.5pt;
        }
        .recipient-nim {
            font-size: 9pt;
            color: #475569;
            margin-top: 2pt;
            font-family: "DejaVu Sans", Arial, sans-serif;
        }

        /* Academic Body Statement */
        .statement-section {
            text-align: center;
            font-size: 10pt;
            line-height: 1.3;
            color: #334155;
            max-width: 88%;
            margin: 3pt auto 5pt auto;
        }
        .major-title {
            font-size: 13.5pt;
            font-weight: bold;
            color: #0f2b48;
            margin-top: 2pt;
            letter-spacing: 0.5pt;
        }
        .major-desc {
            font-size: 8.5pt;
            color: #64748b;
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
            background: #f8fafc;
            border-left: 3pt solid #0f2b48;
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
            color: #334155;
            line-height: 1.25;
        }
        .crypto-badge {
            background: #0f2b48;
            color: #ffffff;
            font-size: 6.5pt;
            font-weight: bold;
            padding: 1pt 3pt;
            border-radius: 2pt;
        }
        .mono-val {
            font-family: "Courier New", Courier, monospace;
            font-weight: bold;
            color: #0f2b48;
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
            border: 1.5pt dashed #b45309;
            background: #fffbeb;
            margin: 0 auto;
            text-align: center;
            padding-top: 6pt;
        }
        .seal-text-top {
            font-size: 5.5pt;
            font-weight: bold;
            color: #92400e;
            font-family: "DejaVu Sans", Arial, sans-serif;
            letter-spacing: 0.5pt;
        }
        .seal-text-mid {
            font-size: 8pt;
            font-weight: bold;
            color: #b45309;
            letter-spacing: 1pt;
            margin: 1pt 0;
        }
        .seal-text-bot {
            font-size: 4.5pt;
            font-weight: bold;
            color: #78350f;
            font-family: "DejaVu Sans", Arial, sans-serif;
            letter-spacing: 0.3pt;
        }

        /* Signatory Cell */
        .signatory-cell {
            width: 38%;
            vertical-align: bottom;
            text-align: center;
        }
        .issue-date {
            font-size: 9pt;
            color: #475569;
            margin-bottom: 14pt;
        }
        .sign-name {
            font-size: 11pt;
            font-weight: bold;
            color: #0f2b48;
            border-bottom: 1pt solid #94a3b8;
            display: inline-block;
            padding-bottom: 2pt;
            min-width: 160pt;
        }
        .sign-title {
            font-size: 7.5pt;
            color: #64748b;
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
                        @if(!empty($logoBase64))
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
                                        <div style="font-weight: bold; color: #0f2b48; font-size: 8pt; margin-top: 2pt;">Keaslian Dokumen Terjamin</div>
                                        <div style="color: #475569; font-size: 6.5pt; line-height: 1.3; margin-top: 2pt;">
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
