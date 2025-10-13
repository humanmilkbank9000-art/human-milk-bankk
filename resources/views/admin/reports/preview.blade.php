<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $meta['title'] ?? 'Monthly Report' }} - {{ $meta['periodLabel'] ?? '' }}</title>
    <style>
        @page {
            size: 8.5in 13in;
            margin: 0.75in 0.75in 0.85in 0.75in;
        }

        html,
        body {
            height: 100%;
        }

        body {
            margin: 0;
            font-family: "DejaVu Sans", Arial, sans-serif;
            color: #1f2937;
            font-size: 11px;
            line-height: 1.5;
            background: #ffffff;
        }

        body.screen-preview {
            background: linear-gradient(180deg, #d1d5db 0%, #eceff4 40%, #d1d5db 100%);
            padding: 40px 0;
        }

        body.pdf-output {
            font-size: 10px;
        }

        .page-canvas {
            position: relative;
            min-height: calc(13in - 2in);
        }

        body.screen-preview .page-canvas {
            width: 8.5in;
            min-height: 13in;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #d4d4d8;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.14), 0 8px 18px rgba(15, 23, 42, 0.1);
        }

        body.pdf-output .page-canvas {
            width: 100%;
            margin: 0;
            padding: 0;
        }

        header.report-header {
            text-align: center;
            margin-bottom: 0.3in;
        }

        body.screen-preview header.report-header {
            position: absolute;
            top: 0;
            left: 50%;
            right: auto;
            transform: translateX(-50%);
            width: calc(8.5in - 1.5in);
            padding-top: 0.4in;
            margin-bottom: 0;
        }

        body.pdf-output header.report-header {
            position: relative;
            margin-top: 0;
            padding-top: 0;
        }

        .header-grid {
            display: table;
            width: 100%;
            table-layout: fixed;
        }

        body.screen-preview .header-grid {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .header-cell {
            display: table-cell;
            vertical-align: middle;
        }

        body.screen-preview .header-cell {
            display: block;
            flex: 0 0 auto;
        }

        .header-cell.logo-left,
        .header-cell.logo-right {
            width: 100px;
        }

        .header-cell.logo-left {
            text-align: left;
            padding-left: 0;
        }

        .header-cell.logo-right {
            text-align: right;
            padding-right: 0;
        }

        body.screen-preview .header-cell.logo-left,
        body.screen-preview .header-cell.logo-right {
            width: 80px;
            text-align: center;
        }

        .header-cell.center {
            text-align: center;
        }

        body.screen-preview .header-cell.center {
            flex: 1;
        }

        .header-cell img {
            max-height: 70px;
            max-width: 70px;
            width: auto;
            height: auto;
        }

        body.screen-preview .header-cell img {
            max-height: 80px;
            max-width: 80px;
        }

        .unit-name {
            font-weight: 700;
            font-size: 13px;
            color: #111827;
            margin-bottom: 3px;
            line-height: 1.4;
        }

        body.pdf-output .unit-name {
            font-size: 12px;
        }

        .unit-address {
            font-size: 10px;
            color: #374151;
            margin-bottom: 8px;
            line-height: 1.4;
        }

        body.pdf-output .unit-address {
            font-size: 9px;
        }

        .report-heading {
            margin-top: 8px;
            margin-bottom: 0;
            font-size: 12px;
            font-weight: 700;
            color: #111827;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        body.pdf-output .report-heading {
            font-size: 11px;
        }

        footer.report-footer {
            margin-top: 0.3in;
            padding-top: 0.15in;
            padding-bottom: 0;
            font-size: 9px;
            color: #6b7280;
            border-top: 1px solid #d1d5db;
        }

        body.screen-preview footer.report-footer {
            position: absolute;
            bottom: 0;
            left: 50%;
            right: auto;
            transform: translateX(-50%);
            width: calc(8.5in - 1.5in);
            padding-bottom: 0.4in;
            margin-top: 0;
        }

        body.pdf-output footer.report-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            width: 100%;
            padding-left: 0;
            padding-right: 0;
            padding-bottom: 0.2in;
            background: white;
        }

        footer.report-footer .footer-grid {
            width: 100%;
        }

        body.screen-preview footer.report-footer .footer-grid {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
        }

        body.pdf-output footer.report-footer .footer-grid {
            display: block;
            padding-left: 0;
            text-align: left;
        }

        footer.report-footer .footer-grid>span {
            vertical-align: middle;
        }

        body.screen-preview footer.report-footer .footer-grid>span {
            display: inline;
        }

        body.pdf-output footer.report-footer .footer-grid>span {
            display: inline;
            margin-right: 15px;
        }

        body.pdf-output footer.report-footer .footer-grid>span:first-child {
            text-align: left;
        }

        body.pdf-output footer.report-footer .footer-grid>span:nth-child(2) {
            text-align: left;
        }

        body.pdf-output footer.report-footer .footer-grid>span:last-child {
            text-align: left;
        }

        .page-number:after {
            content: counter(page);
        }

        main {
            padding: 0;
            margin: 0;
        }

        body.screen-preview main {
            padding-top: 1.2in;
            padding-bottom: 1in;
            padding-left: 0.75in;
            padding-right: 0.75in;
            min-height: calc(13in - 2in);
        }

        body.pdf-output main {
            padding: 0;
            margin-bottom: 0.5in;
        }

        .btn-download {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 18px;
            background: #2563eb;
            color: #ffffff;
            border-radius: 999px;
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
            margin-bottom: 16px;
            box-shadow: 0 8px 16px rgba(37, 99, 235, 0.25);
        }

        .btn-download:hover {
            background: #1d4ed8;
        }

        h2.section-title {
            font-size: 13px;
            font-weight: 700;
            margin: 0.2in 0 0.15in 0;
            color: #111827;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        body.pdf-output h2.section-title {
            font-size: 11px;
            margin: 0.15in 0 0.1in 0;
        }

        .meta-row {
            margin-bottom: 0.25in;
            margin-top: 0.15in;
            line-height: 1.6;
        }

        body.screen-preview .meta-row {
            display: flex;
            gap: 24px;
            flex-wrap: wrap;
        }

        .meta-item {
            font-size: 10px;
            margin-bottom: 5px;
        }

        body.screen-preview .meta-item {
            min-width: 220px;
            font-size: 11px;
        }

        body.pdf-output .meta-item {
            font-size: 9px;
        }

        .meta-item span {
            font-weight: 700;
            color: #111827;
        }

        table.report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0.2in;
            margin-bottom: 0.25in;
            font-size: 10px;
        }

        body.pdf-output table.report-table {
            font-size: 9px;
        }

        table.report-table thead {
            display: table-header-group;
        }

        table.report-table tfoot {
            display: table-footer-group;
        }

        table.report-table thead th {
            background: #f3f4f6;
            color: #111827;
            font-weight: 700;
            border: 1px solid #9ca3af;
            padding: 8px 6px;
            text-align: center;
            line-height: 1.4;
            vertical-align: middle;
        }

        body.pdf-output table.report-table thead th {
            padding: 6px 4px;
            font-size: 9px;
        }

        table.report-table tbody td {
            border: 1px solid #d1d5db;
            padding: 7px 6px;
            line-height: 1.4;
            vertical-align: middle;
        }

        body.pdf-output table.report-table tbody td {
            padding: 5px 4px;
        }

        table.report-table tbody tr:nth-child(even) td {
            background: #f9fafb;
        }

        table.report-table tfoot td {
            font-weight: 700;
            border: 1px solid #9ca3af;
            background: #f3f4f6;
            padding: 8px 6px;
        }

        body.pdf-output table.report-table tfoot td {
            padding: 6px 4px;
        }

        .text-end {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        /* PDF-specific improvements */
        body.pdf-output * {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        body.pdf-output table.report-table {
            page-break-inside: auto;
        }

        body.pdf-output table.report-table tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        body.pdf-output table.report-table thead {
            display: table-header-group;
        }

        body.pdf-output table.report-table tfoot {
            display: table-footer-group;
        }

        /* Ensure proper text wrapping */
        body.pdf-output td,
        body.pdf-output th {
            word-wrap: break-word;
            word-break: break-word;
        }

        @media print {
            .btn-download {
                display: none !important;
            }

            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>

<body class="{{ $isPdf ? 'pdf-output' : 'screen-preview' }}">
    @php
        $leftLogoPath = public_path('hmblsc-logo.jpg');
        $rightLogoPath = public_path('jrbgh-logo.png');

        // For PDF, encode images as base64 to embed them
        $leftLogoSrc = '';
        $rightLogoSrc = '';

        if ($isPdf) {
            if (file_exists($leftLogoPath)) {
                $leftLogoData = base64_encode(file_get_contents($leftLogoPath));
                $leftLogoSrc = 'data:image/jpeg;base64,' . $leftLogoData;
            }
            if (file_exists($rightLogoPath)) {
                $rightLogoData = base64_encode(file_get_contents($rightLogoPath));
                $rightLogoSrc = 'data:image/png;base64,' . $rightLogoData;
            }
        } else {
            $leftLogoSrc = file_exists($leftLogoPath) ? asset('hmblsc-logo.jpg') : '';
            $rightLogoSrc = file_exists($rightLogoPath) ? asset('jrbgh-logo.png') : '';
        }

        $timezoneName = $meta['timezoneName'] ?? config('app.timezone', 'Asia/Manila');
        $generatedSource = $meta['generatedAt'] ?? now();

        if ($generatedSource instanceof \DateTimeInterface) {
            $generatedAt = \Illuminate\Support\Carbon::instance($generatedSource)->setTimezone($timezoneName);
        } else {
            $generatedAt = \Illuminate\Support\Carbon::parse($generatedSource, $timezoneName)->setTimezone($timezoneName);
        }

        $timezoneAbbr = $meta['timezoneAbbr'] ?? $generatedAt->format('T');
    @endphp

    <div class="page-canvas">
        <header class="report-header">
            <div class="header-grid">
                <div class="header-cell logo-left">
                    @if ($leftLogoSrc)
                        <img src="{{ $leftLogoSrc }}" alt="HMBLSC Logo">
                    @endif
                </div>
                <div class="header-cell center">
                    <div class="unit-name">Cagayan de Oro City - Human Milk Bank & Lactation Support Center</div>
                    <div class="unit-address">J.V. Serina St. Carmen, Cagayan de Oro, Philippines</div>
                    <div class="report-heading">{{ $meta['title'] ?? 'Breastmilk Donation Report' }}</div>
                </div>
                <div class="header-cell logo-right">
                    @if ($rightLogoSrc)
                        <img src="{{ $rightLogoSrc }}" alt="JRBGH Logo">
                    @endif
                </div>
            </div>
        </header>

        <main>
            @unless($isPdf)
                @php
                    $downloadUrl = route('admin.reports.download', [
                        'type' => $type,
                        'year' => $meta['year'] ?? now()->year,
                        'month' => $meta['month'] ?? now()->month,
                    ]);
                @endphp
                <a class="btn-download" href="{{ $downloadUrl }}" target="_blank" rel="noopener">
                    <span aria-hidden="true">⬇</span>
                    <span>Download PDF Report</span>
                    <span class="sr-only">for {{ $meta['title'] ?? '' }}</span>
                </a>
            @endunless

            <section>
                <div class="meta-row">
                    <div class="meta-item"><span>Covered Period:</span> {{ $meta['periodLabel'] ?? '' }}</div>
                    <div class="meta-item"><span>Generated On:</span>
                        {{ $generatedAt->format('M d, Y h:i A') }} {{ $timezoneAbbr }}
                    </div>
                </div>
                @include($view, $data ?? [])
            </section>
        </main>

        <footer class="report-footer">
            <div class="footer-grid">
                <span>City Human Milk Bank – Monthly Report Portal</span>
                <span>Page <span class="page-number"></span></span>
                <span>Generated:
                    {{ $generatedAt->format('M d, Y h:i A') }} {{ $timezoneAbbr }}</span>
            </div>
        </footer>
    </div>
</body>

</html>