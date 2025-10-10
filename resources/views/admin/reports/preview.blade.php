<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $meta['title'] ?? 'Monthly Report' }} - {{ $meta['periodLabel'] ?? '' }}</title>
    <style>
        @page {
            size: 8.5in 13in;
            margin: 1in 0.75in;
        }

        html,
        body {
            height: 100%;
        }

        body {
            margin: 0;
            font-family: "DejaVu Sans", Arial, sans-serif;
            color: #1f2937;
            font-size: 12px;
            line-height: 1.4;
            background: #ffffff;
        }

        body.screen-preview {
            background: linear-gradient(180deg, #d1d5db 0%, #eceff4 40%, #d1d5db 100%);
            padding: 40px 0;
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
            width: auto;
            margin: 0;
        }

        header.report-header {
            position: fixed;
            top: -1in;
            left: 0.75in;
            right: 0.75in;
            text-align: center;
            padding: 0.25in 0 0.15in 0;
        }

        body.screen-preview header.report-header {
            position: absolute;
            top: 0;
            left: 50%;
            right: auto;
            transform: translateX(-50%);
            width: calc(8.5in - 1.5in);
            padding-top: 0.4in;
        }

        .header-grid {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            gap: 20px;
        }

        .header-cell {
            flex: 0 0 auto;
        }

        .header-cell.logo-left,
        .header-cell.logo-right {
            width: 80px;
        }

        .header-cell.center {
            flex: 1;
            text-align: center;
        }

        .header-cell img {
            max-height: 80px;
            max-width: 80px;
            width: auto;
            height: auto;
        }

        .unit-name {
            font-weight: 700;
            font-size: 14px;
            color: #111827;
            margin-bottom: 2px;
            line-height: 1.3;
        }

        .unit-address {
            font-size: 11px;
            color: #111827;
            margin-bottom: 8px;
        }

        .report-heading {
            margin-top: 6px;
            margin-bottom: 2px;
            font-size: 13px;
            font-weight: 700;
            color: #111827;
        }

        footer.report-footer {
            position: fixed;
            bottom: -1in;
            left: 0.75in;
            right: 0.75in;
            padding-top: 10px;
            padding-bottom: 0.25in;
            font-size: 10px;
            color: #4b5563;
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
        }

        footer.report-footer .footer-grid {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
        }

        .page-number:after {
            content: counter(page);
        }

        main {
            padding: 0.75in;
            margin-top: 0.25in;
        }

        body.screen-preview main {
            padding-top: 1.2in;
            padding-bottom: 1in;
            padding-left: 0.75in;
            padding-right: 0.75in;
            min-height: calc(13in - 2in);
        }

        body.pdf-output main {
            padding-top: 0.75in;
            padding-bottom: 0.75in;
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
            font-size: 15px;
            font-weight: 600;
            margin: 16px 0 12px 0;
            color: #111827;
            text-transform: uppercase;
        }

        .meta-row {
            display: flex;
            gap: 24px;
            flex-wrap: wrap;
            margin-bottom: 20px;
            margin-top: 8px;
        }

        .meta-item {
            min-width: 220px;
            font-size: 11px;
        }

        .meta-item span {
            font-weight: 600;
            color: #111827;
        }

        table.report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-bottom: 30px;
            font-size: 11px;
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
            padding: 10px 8px;
            text-align: center;
            line-height: 1.3;
        }

        table.report-table tbody td {
            border: 1px solid #d1d5db;
            padding: 8px;
            line-height: 1.3;
        }

        table.report-table tbody tr:nth-child(even) td {
            background: #f9fafb;
        }

        table.report-table tfoot td {
            font-weight: 700;
            border: 1px solid #9ca3af;
            background: #f3f4f6;
            padding: 10px 8px;
        }

        .text-end {
            text-align: right;
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

        @media print {
            .btn-download {
                display: none !important;
            }
        }
    </style>
</head>

<body class="{{ $isPdf ? 'pdf-output' : 'screen-preview' }}">
    @php
        $leftLogoPath = public_path('hmblsc-logo.jpg');
        $rightLogoPath = public_path('jrbgh-logo.png');
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
                    @if (file_exists($leftLogoPath))
                        <img src="{{ asset('hmblsc-logo.jpg') }}" alt="HMBLSC Logo">
                    @endif
                </div>
                <div class="header-cell center">
                    <div class="unit-name">Cagayan de Oro City - Human Milk Bank & Lactation Support Center</div>
                    <div class="unit-address">J.V. Serina St. Carmen, Cagayan de Oro, Philippines</div>
                    <div class="report-heading">{{ $meta['title'] ?? 'Breastmilk Donation Report' }}</div>
                </div>
                <div class="header-cell logo-right">
                    @if (file_exists($rightLogoPath))
                        <img src="{{ asset('jrbgh-logo.png') }}" alt="JRBGH Logo">
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