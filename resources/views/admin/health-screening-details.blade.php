@extends('layouts.admin-layout')

@section('title', 'Health Screening Details')
@section('pageTitle', 'Health Screening Details')

@section('styles')
    <style>
        .detail-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            margin-bottom: 0.75rem;
        }

        .detail-card-header {
            background: linear-gradient(180deg, #ff93c1 0%, #ff7fb3 100%);
            color: white;
            padding: 0.75rem 1rem;
            border-radius: 12px 12px 0 0;
            font-size: 0.95rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .detail-card-body {
            padding: 1rem;
        }

        .info-row {
            display: flex;
            padding: 0.4rem 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #374151;
            min-width: 140px;
            flex-shrink: 0;
            font-size: 0.9rem;
        }

        .info-value {
            color: #6b7280;
            flex: 1;
            font-size: 0.9rem;
        }

        .question-card {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 0.75rem;
            margin-bottom: 0.75rem;
        }

        .question-number {
            font-weight: 700;
            color: #e83e8c;
            margin-right: 0.4rem;
            font-size: 0.9rem;
        }

        .question-text {
            font-weight: 600;
            color: #111827;
            margin-bottom: 0.3rem;
            font-size: 0.9rem;
        }

        .question-translation {
            font-style: italic;
            color: #6b7280;
            font-size: 0.85rem;
            margin-bottom: 0.5rem;
        }

        .answer-badge {
            display: inline-block;
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .answer-yes {
            background: #ffd4e3;
            color: #c2185b;
        }

        .answer-no {
            background: #f3f4f6;
            color: #6b7280;
        }

        .answer-details {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 0.5rem;
            margin-top: 0.5rem;
            font-size: 0.85rem;
        }

        .section-title {
            color: #e83e8c;
            font-weight: 700;
            font-size: 1rem;
            margin-bottom: 0.75rem;
            padding-bottom: 0.35rem;
            border-bottom: 2px solid #ffd4e3;
        }

        .status-badge-large {
            padding: 0.35rem 0.875rem;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-top: 1rem;
        }

        .btn-action {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: all 0.2s;
            font-size: 0.9rem;
        }

        .btn-action:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            background: #e83e8c;
            color: white;
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 1rem;
            transition: all 0.2s;
            font-size: 0.9rem;
            padding: 0.6rem 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(232, 62, 140, 0.2);
        }

        .back-link i {
            font-size: 1rem;
        }

        .back-link:hover {
            background: #c2185b;
            color: white;
            box-shadow: 0 4px 12px rgba(232, 62, 140, 0.3);
            transform: translateY(-1px);
        }

        .btn-print {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            background: white;
            color: #e83e8c;
            border: 2px solid #e83e8c;
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 1rem;
            margin-left: 0.5rem;
            transition: all 0.2s;
            font-size: 0.9rem;
            padding: 0.6rem 1rem;
            border-radius: 8px;
            cursor: pointer;
        }

        .btn-print:hover {
            background: #e83e8c;
            color: white;
            transform: translateY(-1px);
        }

        .btn-print i {
            font-size: 1rem;
        }

        .header-actions {
            display: flex;
            align-items: center;
            margin-bottom: 0;
        }

        .btn-print {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            background: white;
            color: #e83e8c;
            border: 2px solid #e83e8c;
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 1rem;
            margin-left: 0.5rem;
            transition: all 0.2s;
            font-size: 0.9rem;
            padding: 0.6rem 1rem;
            border-radius: 8px;
            cursor: pointer;
        }

        .btn-print:hover {
            background: #e83e8c;
            color: white;
            transform: translateY(-1px);
        }

        .btn-print i {
            font-size: 1rem;
        }

        .header-actions {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .nav-tabs {
            display: none;
        }

        .tab-content {
            padding-top: 0.25rem;
        }

        .tab-pane {
            display: block !important;
            opacity: 1 !important;
        }

        .btn-print {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            background: white;
            color: #e83e8c;
            border: 2px solid #e83e8c;
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 1rem;
            margin-left: 0.5rem;
            transition: all 0.2s;
            font-size: 0.9rem;
            padding: 0.6rem 1rem;
            border-radius: 8px;
            cursor: pointer;
        }

        .btn-print:hover {
            background: #e83e8c;
            color: white;
            transform: translateY(-1px);
        }

        .btn-print i {
            font-size: 1rem;
        }

        .header-actions {
            display: flex;
            align-items: center;
            margin-bottom: 0;
        }

        @media (max-width: 768px) {
            .back-link {
                width: 40px;
                height: 40px;
                padding: 0;
                border-radius: 50%;
                margin-bottom: 0.75rem;
                gap: 0;
            }

            .back-link i {
                font-size: 1.2rem;
            }

            .back-link-text {
                display: none;
            }

            .btn-print {
                width: 40px;
                height: 40px;
                padding: 0;
                border-radius: 50%;
                margin-left: 0.5rem;
                gap: 0;
            }

            .btn-print i {
                font-size: 1.2rem;
            }

            .btn-print-text {
                display: none;
            }

            .info-row {
                flex-direction: column;
            }

            .info-label {
                min-width: auto;
                margin-bottom: 0.25rem;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn-action {
                width: 100%;
                justify-content: center;
            }

            .nav-tabs .nav-link {
                font-size: 0.8rem;
                padding: 0.4rem 0.6rem;
            }

            .detail-card {
                margin-bottom: 0.5rem;
            }

            .detail-card-header {
                padding: 0.6rem 0.75rem;
                font-size: 0.9rem;
            }

            .detail-card-body {
                padding: 0.75rem;
            }
        }

        @media print {
            @page {
                size: 8.5in 13in;
                margin: 0.5in 0.5in 1in 0.5in;
                
                @bottom-center {
                    content: "Page " counter(page) " of " counter(pages);
                    font-size: 9px;
                    color: #666;
                }
            }

            /* Hide all screen elements and system headers */
            .back-link,
            .btn-print,
            .nav-tabs,
            .action-buttons,
            .header-actions,
            #status,
            header,
            .admin-header,
            .page-header,
            .sidebar,
            .overlay,
            .menu-toggle,
            .hamburger,
            nav,
            .navbar,
            .navigation {
                display: none !important;
            }

            /* Reset body and container */
            body {
                background: white !important;
                margin: 0 !important;
                padding: 0 !important;
                font-family: Arial, sans-serif;
                font-size: 11px;
                line-height: 1.4;
                color: #000;
            }

            .container-fluid {
                padding: 0 !important;
                max-width: 100% !important;
                margin: 0 !important;
                width: 100% !important;
                display: block !important;
                position: relative !important;
                /* Ensure content doesn't run under fixed footer */
                padding-bottom: 1.1in !important;
            }

            .content {
                display: block !important;
                padding: 0 !important;
            }

            /* Show all tabs in sequence */
            .tab-content {
                display: block !important;
                break-before: avoid !important;
                page-break-before: avoid !important;
                margin-top: 0 !important;
                padding-top: 0 !important;
            }

            .tab-pane {
                display: block !important;
                opacity: 1 !important;
                visibility: visible !important;
                page-break-after: avoid;
                break-before: avoid !important;
                page-break-before: avoid !important;
            }

            .tab-pane.fade {
                display: block !important;
                opacity: 1 !important;
            }

            /* Ensure content inside containers shows */
            * {
                visibility: visible !important;
            }

            /* Clean layout - no cards, just sections */
            .detail-card {
                page-break-inside: avoid;
                box-shadow: none;
                border: none;
                margin-bottom: 20px;
                border-radius: 0;
                background: transparent;
            }

            .detail-card-header {
                background: white !important;
                color: #000 !important;
                border-bottom: 2px solid #000;
                padding: 6px 0 4px 0 !important;
                font-size: 11px;
                font-weight: 700;
                text-transform: uppercase;
                border-radius: 0;
                letter-spacing: 0.5px;
                margin-bottom: 10px;
            }

            .detail-card-header i {
                display: none;
            }

            .detail-card-body {
                padding: 0 !important;
            }

            /* Clean info rows */
            .info-row {
                border: none;
                padding: 4px 0;
                display: table;
                width: 100%;
                border-bottom: none;
            }

            .info-row:last-child {
                border-bottom: none;
            }

            .info-label {
                font-weight: 600;
                color: #000;
                display: table-cell;
                width: 140px;
                padding-right: 10px;
                font-size: 10px;
            }

            .info-label::after {
                content: ":";
            }

            .info-value {
                color: #000;
                display: table-cell;
                font-size: 10px;
            }

            /* Question cards - clean style */
            .question-card {
                background: white;
                border: none;
                border-radius: 0;
                padding: 8px 0;
                margin-bottom: 8px;
                page-break-inside: avoid;
                break-inside: avoid-page !important;
            }

            .question-number {
                font-weight: 700;
                color: #000;
                font-size: 10px;
                display: inline;
            }

            .question-text {
                font-size: 10px;
                color: #000;
                display: inline;
            }

            .question-translation {
                font-size: 9px;
                color: #333;
                font-style: italic;
                display: block;
                margin-top: 2px;
            }

            .answer-badge {
                background: white !important;
                color: #000 !important;
                border: 1px solid #000;
                padding: 2px 8px;
                font-weight: 600;
                font-size: 9px;
                display: inline-block;
                margin-top: 3px;
            }

            .answer-badge.bg-success {
                border-color: #000;
                color: #000 !important;
                background: white !important;
            }

            .answer-badge.bg-danger {
                border-color: #000;
                color: #000 !important;
                background: white !important;
            }

            .answer-details {
                margin-top: 5px;
                padding: 5px 0 5px 10px;
                background: transparent;
                border-left: 2px solid #000;
                font-size: 9px;
                color: #000;
            }

            .section-title {
                font-size: 11px;
                font-weight: 700;
                text-transform: uppercase;
                margin-top: 20px;
                margin-bottom: 10px;
                padding-bottom: 4px;
                border-bottom: 2px solid #000;
                color: #000;
                letter-spacing: 0.3px;
            }

            /* Hide row styling */
            .row {
                display: block !important;
            }

            .col-md-6 {
                display: block !important;
                width: 100% !important;
            }

            /* Status badge */
            .status-badge-large {
                border: 1px solid #000 !important;
                background: white !important;
                color: #000 !important;
            }

            /* Show print header only when printing */
            .print-header {
                display: block !important;
                margin: 0 0 16px 0 !important;
                page-break-inside: avoid !important;
                page-break-after: avoid !important;
                break-after: avoid !important;
            }

            /* Force Question 5 of the first section to start on a new page */
            #answers .detail-card-body > .mb-2:first-of-type .question-card:nth-of-type(5) {
                page-break-before: always !important;
                break-before: page !important;
            }

            .print-header table {
                border-collapse: collapse;
                width: 100%;
            }

            .print-header td {
                vertical-align: middle;
            }

            .print-header img {
                display: block;
            }

            .print-header hr {
                margin: 15px 0 20px 0;
                border: none;
                border-top: 2px solid #e5e7eb;
            }

            /* Footer with page number and metadata */
            .print-footer {
                display: block !important;
                position: fixed;
                bottom: 20px;
                left: 0.5in;
                right: 0.5in;
                padding: 10px 0;
                border-top: 1px solid #ddd;
                font-size: 9px;
                color: #666;
                background: white;
                z-index: 2147483647;
            }

            .print-footer-content {
                display: table;
                width: 100%;
                table-layout: fixed;
            }

            .print-footer-left {
                display: table-cell;
                text-align: left;
                width: 50%;
                vertical-align: middle;
            }

            .print-footer-right {
                display: table-cell;
                text-align: right;
                width: 50%;
                vertical-align: middle;
            }
        }
    </style>
@endsection

@section('content')
    <!-- Print Header (hidden on screen) -->
    <div class="print-header" style="display: none;">
        <table style="width: 100%; margin-bottom: 20px;">
            <tr>
                <td style="width: 100px; text-align: left; vertical-align: middle;">
                    <img src="{{ asset('hmblsc-logo.png') }}" style="max-height: 70px; max-width: 70px;" alt="HMBLSC Logo">
                </td>
                <td style="text-align: center; vertical-align: middle;">
                    <div style="font-weight: 700; font-size: 13px; margin-bottom: 3px;">
                        Cagayan de Oro City - Human Milk Bank & Lactation Support Center
                    </div>
                    <div style="font-size: 10px; color: #374151; margin-bottom: 8px;">
                        J.V. Serina St. Carmen, Cagayan de Oro, Philippines
                    </div>
                    <div style="font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 8px;">
                        HEALTH SCREENING DETAILS
                    </div>
                </td>
                <td style="width: 100px; text-align: right; vertical-align: middle;">
                    <img src="{{ asset('jrbgh-logo.png') }}" style="max-height: 70px; max-width: 70px;" alt="JRBGH Logo">
                </td>
            </tr>
        </table>
        <hr style="border: none; border-top: 2px solid #e5e7eb; margin-bottom: 20px;">
    </div>

    <div class="container-fluid px-2 px-md-4">
        <div class="header-actions">
            <a href="{{ route('admin.health-screening', ['status' => $screening->status]) }}" class="back-link">
                <i class="bi bi-arrow-left"></i><span class="back-link-text"> Back to Health Screening List</span>
            </a>
            <button onclick="printScreening()" class="btn-print">
                <i class="bi bi-printer-fill"></i><span class="btn-print-text"> Print</span>
            </button>
        </div>

        {{-- User and Infant Information Side by Side --}}
        <div class="row g-3">
            <div class="col-md-6">
                <div class="detail-card" style="height: 100%;">
                    <div class="detail-card-header">
                        <i class="bi bi-person-fill"></i>
                        <span>User Information</span>
                    </div>
                    <div class="detail-card-body">
                        @if($screening->user)
                            <div class="info-row">
                                <span class="info-label">Name:</span>
                                <span class="info-value">{{ $screening->user->first_name }} {{ $screening->user->last_name }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Contact Number:</span>
                                <span class="info-value">{{ $screening->user->contact_number }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Date of Birth:</span>
                                <span class="info-value">
                                    {{ $screening->user->date_of_birth ? \Carbon\Carbon::parse($screening->user->date_of_birth)->format('M d, Y') : '-' }}
                                </span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Age:</span>
                                <span class="info-value">{{ $screening->user->age }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Sex:</span>
                                <span class="info-value">{{ ucfirst($screening->user->sex) }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Civil Status:</span>
                                <span class="info-value">{{ ucfirst($screening->civil_status ?? 'N/A') }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Occupation:</span>
                                <span class="info-value">{{ $screening->occupation ?? 'N/A' }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Type of Donor:</span>
                                <span class="info-value">{{ ucwords(str_replace('_', ' ', $screening->type_of_donor ?? 'N/A')) }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Address:</span>
                                <span class="info-value">{{ $screening->user->address }}</span>
                            </div>
                        @else
                            <p class="text-muted">No user data found.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="detail-card" style="height: 100%;">
                    <div class="detail-card-header">
                        <i class="bi bi-heart-fill"></i>
                        <span>Infant Information</span>
                    </div>
                    <div class="detail-card-body">
                        @if($screening->infant)
                            <div class="info-row">
                                <span class="info-label">Name:</span>
                                <span class="info-value">
                                    {{ $screening->infant->first_name }} {{ $screening->infant->last_name }}{{ $screening->infant->suffix ? ' ' . $screening->infant->suffix : '' }}
                                </span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Sex:</span>
                                <span class="info-value">{{ ucfirst($screening->infant->sex) }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Date of Birth:</span>
                                <span class="info-value">
                                    {{ $screening->infant->date_of_birth ? \Carbon\Carbon::parse($screening->infant->date_of_birth)->format('M d, Y') : '-' }}
                                </span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Age:</span>
                                <span class="info-value">{{ $screening->infant->getFormattedAge() }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Birth Weight:</span>
                                <span class="info-value">{{ rtrim(rtrim(number_format($screening->infant->birth_weight, 2, '.', ''), '0'), '.') }} kg</span>
                            </div>
                        @else
                            <p class="text-muted">No infant data found.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="detail-card">
            <div class="detail-card-header">
                <i class="bi bi-clipboard-check-fill"></i>
                <span>Screening Answers</span>
            </div>
            <div class="detail-card-body">
                @foreach($sections as $sectionKey => $questions)
                    <div class="mb-2">
                        <h5 class="section-title">{{ ucwords(str_replace('_', ' ', $sectionKey)) }}</h5>
                        @php $qNum = 1; @endphp
                        @foreach($questions as $q)
                            @php
                                $field = $sectionKey . '_' . str_pad($qNum, 2, '0', STR_PAD_LEFT);
                                $value = $screening->{$field} ?? '';
                                $details = $screening->{$field . '_details'} ?? '';
                            @endphp
                            <div class="question-card">
                                <div class="question-text">
                                    <span class="question-number">{{ $qNum }}.</span>{{ $q[0] }}
                                </div>
                                <div class="question-translation">
                                    {{ $q[1] }}
                                </div>
                                <div>
                                    <span class="answer-badge {{ $value == 'yes' ? 'answer-yes' : 'answer-no' }}">
                                        {{ $value ? ucfirst($value) : 'N/A' }}
                                    </span>
                                    @if($details)
                                        <div class="answer-details">
                                            <strong>Details:</strong> {{ $details }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @php $qNum++; @endphp
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>

        <div class="detail-card">
            <div class="detail-card-header">
                <i class="bi bi-info-circle-fill"></i>
                <span>Submission Status</span>
            </div>
            <div class="detail-card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-row">
                            <span class="info-label">Status:</span>
                            <span class="info-value">
                                <span class="status-badge-large badge bg-{{ $screening->status == 'accepted' ? 'success' : ($screening->status == 'declined' ? 'danger' : 'warning text-dark') }}">
                                    {{ ucfirst($screening->status) }}
                                </span>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-row">
                            <span class="info-label">Submitted:</span>
                            <span class="info-value">{{ optional($screening->created_at)->setTimezone('Asia/Manila')->format('M d, Y h:i A') }}</span>
                        </div>
                    </div>
                    @if($screening->status == 'accepted' && $screening->date_accepted)
                        <div class="col-md-6">
                            <div class="info-row">
                                <span class="info-label">Accepted At:</span>
                                <span class="info-value">{{ optional($screening->date_accepted)->setTimezone('Asia/Manila')->format('M d, Y h:i A') }}</span>
                            </div>
                        </div>
                    @endif
                    @if($screening->status == 'declined' && $screening->date_declined)
                        <div class="col-md-6">
                            <div class="info-row">
                                <span class="info-label">Declined At:</span>
                                <span class="info-value">{{ optional($screening->date_declined)->setTimezone('Asia/Manila')->format('M d, Y h:i A') }}</span>
                            </div>
                        </div>
                    @endif
                    @if(!empty($screening->admin_notes))
                        <div class="col-12">
                            <div class="info-row" style="flex-direction: column; align-items: flex-start;">
                                <span class="info-label mb-2">Admin Comments:</span>
                                <div class="answer-details" style="width: 100%; margin-top: 0;">
                                    {{ $screening->admin_notes }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Admin Actions for Pending Status --}}
                @if($screening->status == 'pending')
                    <div class="mt-3 pt-3 border-top">
                        <h6 class="mb-2" style="color: #e83e8c;"><i class="bi bi-chat-left-text-fill me-2"></i>Admin Action</h6>
                        <div class="row">
                            <div class="col-12">
                                <textarea class="form-control rounded" id="adminComments" name="comments" rows="3"
                                    placeholder="Enter comments or notes (required for declining, optional for accepting)"></textarea>
                            </div>
                        </div>
                        <div class="action-buttons">
                            <button type="button" class="btn btn-danger btn-action" onclick="declineScreening({{ $screening->health_screening_id }})">
                                <i class="bi bi-x-circle"></i> Decline
                            </button>
                            <button type="button" class="btn btn-success btn-action" onclick="acceptScreening({{ $screening->health_screening_id }})">
                                <i class="bi bi-check-circle"></i> Accept
                            </button>
                        </div>
                    </div>
                @elseif($screening->status == 'declined')
                    <div class="action-buttons">
                        <button type="button" class="btn btn-success btn-action" onclick="undoDeclineScreening({{ $screening->health_screening_id }})">
                            <i class="bi bi-arrow-counterclockwise"></i> Undo & Accept
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function acceptScreening(id) {
            const comments = document.getElementById('adminComments')?.value || '';
            
            Swal.fire({
                title: 'Accept Health Screening?',
                text: 'This will accept the health screening submission.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Accept',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/admin/health-screening/${id}/accept`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ comments: comments })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Accepted!',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = '{{ route("admin.health-screening", ["status" => "accepted"]) }}';
                            });
                        } else {
                            Swal.fire('Error', data.error || 'Failed to accept screening', 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('Error', 'An error occurred', 'error');
                    });
                }
            });
        }

        function declineScreening(id) {
            const comments = document.getElementById('adminComments')?.value || '';
            
            if (!comments.trim()) {
                Swal.fire('Required', 'Comments are required when declining.', 'warning');
                return;
            }

            Swal.fire({
                title: 'Decline Health Screening?',
                text: 'This will decline the health screening submission.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Decline',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/admin/health-screening/${id}/reject`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ comments: comments })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Declined!',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = '{{ route("admin.health-screening", ["status" => "declined"]) }}';
                            });
                        } else {
                            Swal.fire('Error', data.error || 'Failed to decline screening', 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('Error', 'An error occurred', 'error');
                    });
                }
            });
        }

        function undoDeclineScreening(id) {
            Swal.fire({
                title: 'Undo Decline & Accept?',
                text: 'This will accept the previously declined screening.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Accept',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/admin/health-screening/${id}/undo-decline`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Accepted!',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = '{{ route("admin.health-screening", ["status" => "accepted"]) }}';
                            });
                        } else {
                            Swal.fire('Error', data.error || 'Failed to accept screening', 'error');
                        }
                })
                .catch(error => {
                    Swal.fire('Error', 'An error occurred', 'error');
                });
            }
        });
    }

    function printScreening() {
        window.print();
    }
</script>

<!-- Print Footer (hidden on screen) -->
<div class="print-footer" style="display: none;">
    <div class="print-footer-content">
        <div class="print-footer-left">
            Development of Web App for Breastmilk Request and Donation
        </div>
        <div class="print-footer-right">
            Generated: {{ now()->setTimezone('Asia/Manila')->format('M d, Y h:i A') }}
        </div>
    </div>
</div>

@endsection