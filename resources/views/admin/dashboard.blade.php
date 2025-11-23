@extends('layouts.admin-layout')

@section('title', 'Admin Dashboard')

@section('styles')
  <style>
    /* Modal fixes for visibility, z-index, centering, and responsiveness */
    .modal {
      z-index: 1055 !important;
      /* Above header/sidebar */
      display: flex !important;
      align-items: center;
      justify-content: center;
    }

    .modal-dialog {
      max-width: 700px;
      width: 95vw;
      margin: 2rem auto;
      position: relative;
      z-index: 1060;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .modal-content {
      max-height: 90vh;
      overflow-y: auto;
      border-radius: 1rem;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.18);
      position: relative;
      z-index: 1061;
      background: #fff;
      display: flex;
      flex-direction: column;
      align-items: stretch;
    }

    .modal-header,
    .modal-footer {
      z-index: 1062;
      background: #f8f9fa;
      border-radius: 1rem 1rem 0 0;
    }

    .modal-footer {
      border-radius: 0 0 1rem 1rem;
    }

    /* Modal backdrop z-index */
    .modal-backdrop {
      z-index: 1050 !important;
    }

    /* SweetAlert z-index fix to appear above modal and backdrop */
    .swal2-container {
      z-index: 9999 !important;
    }

    .swal2-popup {
      z-index: 10000 !important;
    }

    /* Ensure SweetAlert overlay is also above everything */
    div:where(.swal2-container) {
      z-index: 9999 !important;
    }

    div:where(.swal2-container).swal2-backdrop-show {
      z-index: 9998 !important;
    }

    /* Custom high z-index class for SweetAlert */
    .swal-high-zindex {
      z-index: 10000 !important;
    }

    .swal-high-zindex .swal2-popup {
      z-index: 10001 !important;
    }

    @media (max-width: 600px) {
      .modal-dialog {
        max-width: 98vw;
        width: 98vw;
        margin: 0.5rem auto;
      }

      .modal-content {
        max-height: 98vh;
        border-radius: 0.5rem;
      }

      .calendar-grid {
        font-size: 0.9em;
      }
    }

    /* Calendar and time slot section responsive tweaks */
    #calendarContainer {
      width: 100%;
      max-width: 480px;
      margin: 0 auto;
      background: #fff;
      border-radius: 0.5rem;
      box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
      padding: 12px 0 18px 0;
    }

    .calendar-grid {
      border: 1px solid #dee2e6;
      border-radius: 0.375rem;
      overflow: visible;
      background: #fff;
      margin-bottom: 0;
    }

    .calendar-header {
      display: grid;
      grid-template-columns: repeat(7, 1fr);
      background-color: #f8f9fa;
    }

    .calendar-days {
      display: grid;
      grid-template-columns: repeat(7, 1fr);
      min-height: 280px;
    }

    .calendar-day {
      aspect-ratio: 1;
      min-width: 36px;
      min-height: 36px;
      font-size: 1em;
    }

    /* Time slots section responsive */
    #timeSlotsContainer {
      display: grid;
      grid-template-columns: repeat(5, 1fr);
      gap: 10px;
      justify-items: center;
      align-items: center;
      width: 100%;
      margin-bottom: 10px;
    }

    /* On narrow screens make slots wrap into fewer columns, and below 480px make them full-width stacked */
    @media (max-width: 768px) {
      #timeSlotsContainer {
        grid-template-columns: repeat(3, 1fr);
      }
    }

    @media (max-width: 600px) {
      #timeSlotsContainer {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    @media (max-width: 480px) {

      /* Keep two columns on small/mobile screens so time slots appear as pairs (8 9, 10 11) */
      #timeSlotsContainer {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    .time-slot-checkbox {
      width: 100%;
      padding: 10px;
      border-radius: 0.5rem;
      margin-bottom: 0;
      box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
      background: #f8f9fa;
      box-sizing: border-box;
    }

    @media (max-width: 600px) {
      .time-slot-checkbox {
        min-height: 36px;
        padding: 6px 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
      }
    }

    .calendar-grid {
      border: 1px solid #dee2e6;
      border-radius: 0.375rem;
      overflow: hidden;
    }

    .calendar-header {
      display: grid;
      grid-template-columns: repeat(7, 1fr);
      background-color: #f8f9fa;
    }

    .calendar-day-header {
      padding: 10px;
      text-align: center;
      font-weight: bold;
      border-right: 1px solid #dee2e6;
      font-size: 0.875rem;
    }

    .calendar-day-header:last-child {
      border-right: none;
    }

    .calendar-days {
      display: grid;
      grid-template-columns: repeat(7, 1fr);
    }

    .calendar-day {
      aspect-ratio: 1;
      border-right: 1px solid #dee2e6;
      border-bottom: 1px solid #dee2e6;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: background-color 0.2s;
      min-height: 45px;
    }

    .calendar-day:last-child {
      border-right: none;
    }

    .calendar-day:hover:not(.disabled):not(.other-month) {
      background-color: #e9ecef;
    }

    /* Pink theme selected day */
    .calendar-day.selected {
      background-color: #e83e8c; /* pink */
      color: white;
      font-weight: bold;
    }

    /* Pink theme for days with availability */
    .calendar-day.has-availability {
      background-color: #ff6fa8 !important; /* lighter pink */
      border: 3px solid #b21f66 !important; /* deep pink border */
      color: white !important;
      font-weight: 700 !important;
      position: relative;
    }

    .calendar-day.has-availability::after {
      content: '\2665'; /* heart symbol */
      position: absolute;
      top: 2px;
      right: 6px;
      font-size: 14px;
      color: white;
      font-weight: bold;
    }

    .calendar-day.has-availability.selected {
      background-color: #e83e8c !important;
      border-color: #e83e8c !important;
      color: white !important;
    }

    .calendar-day.has-availability.selected::after {
      color: white;
    }

    .calendar-day.other-month {
      color: #6c757d;
      background-color: #f8f9fa;
      cursor: not-allowed;
    }

    .calendar-day.disabled {
      color: #6c757d;
      background-color: #f8f9fa;
      cursor: not-allowed;
    }

    .time-slot-checkbox {
      border: 1px solid #dee2e6;
      border-radius: 0.375rem;
      padding: 15px;
      transition: all 0.2s;
    }

    /* Pink hover/checked state for time slots */
    .time-slot-checkbox:hover {
      border-color: #e83e8c;
      background-color: #fff0f6;
    }

    .time-slot-checkbox input:checked+label {
      color: #e83e8c;
      font-weight: bold;
    }

    /* Dashboard Statistics Cards */
    .stats-container {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1rem;
      margin-bottom: 1.25rem;
      padding: 0 0.75rem;
      /* Add horizontal padding to align with user dashboard */
    }

    .stat-card {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border-radius: 0.75rem;
      padding: 1rem;
      color: white;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      min-height: 100px;
    }

    .stat-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.12);
    }

    .stat-card.donations {
      background: #2563eb;
    }

    .stat-card.requests {
      background: #e83e8c;
    }

    .stat-card.screenings {
      background: #16a34a;
    }

    .stat-card-icon {
      font-size: 1.8rem;
      margin-bottom: 0.25rem;
      opacity: 0.9;
    }

    .stat-card-title {
      font-size: 0.8rem;
      font-weight: 500;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 0.25rem;
      opacity: 0.95;
    }

    .stat-card-value {
      font-size: 2rem;
      font-weight: 700;
      line-height: 1;
      margin-bottom: 0.15rem;
    }

    .stat-card-subtitle {
      font-size: 0.75rem;
      opacity: 0.85;
    }

    /* Responsive adjustments - maintain horizontal layout */
    @media (max-width: 768px) {
      .stats-container {
        gap: 0.75rem;
      }

      .stat-card {
        min-height: 110px;
        padding: 1rem;
      }

      .stat-card-icon {
        font-size: 1.5rem;
        margin-bottom: 0.25rem;
      }

      .stat-card-title {
        font-size: 0.65rem;
        margin-bottom: 0.25rem;
      }

      .stat-card-value {
        font-size: 1.5rem;
      }

      .stat-card-subtitle {
        font-size: 0.65rem;
      }
    }

    @media (min-width: 769px) and (max-width: 1024px) {
      .stats-container {
        gap: 1rem;
      }

      .stat-card {
        min-height: 120px;
        padding: 1.25rem;
      }

      .stat-card-icon {
        font-size: 2rem;
      }

      .stat-card-title {
        font-size: 0.75rem;
      }

      .stat-card-value {
        font-size: 2rem;
      }

      .stat-card-subtitle {
        font-size: 0.75rem;
      }
    }

    /* Year Timeline Chart Styles */
    .chart-container {
      background: white;
      border-radius: 0.75rem;
      padding: 1.25rem;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
      margin-bottom: 1.25rem;
      margin-left: 0.75rem;
      /* Align with user dashboard */
      margin-right: 0.75rem;
      /* Align with user dashboard */
    }

    .chart-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;
      flex-wrap: wrap;
      gap: 0.75rem;
    }

    .chart-title {
      font-size: 1.1rem;
      font-weight: 700;
      color: #333;
      margin: 0;
    }

    /* Year Selector Dropdown Styles */
    .year-selector {
      padding: 0.4rem 0.8rem;
      font-size: 0.95rem;
      font-weight: 600;
      color: #667eea;
      background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
      border: 2px solid #667eea;
      border-radius: 0.5rem;
      cursor: pointer;
      transition: all 0.3s ease;
      outline: none;
    }

    .year-selector:hover {
      background: linear-gradient(135deg, rgba(102, 126, 234, 0.2) 0%, rgba(118, 75, 162, 0.2) 100%);
      border-color: #764ba2;
      transform: translateY(-1px);
      box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
    }

    .year-selector:focus {
      border-color: #764ba2;
      box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
    }

    .chart-legend {
      display: flex;
      gap: 1rem;
      flex-wrap: wrap;
    }

    .legend-item {
      display: flex;
      align-items: center;
      gap: 0.4rem;
      font-size: 0.8rem;
    }

    .legend-color {
      width: 16px;
      height: 16px;
      border-radius: 3px;
    }

    .legend-color.donations {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .legend-color.requests {
      background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    #yearTimelineChart {
      width: 100% !important;
      height: 100% !important;
    }

    /* Three Panel Layout */
    .three-panel-container {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1rem;
      margin-bottom: 1.25rem;
      padding: 0 0.75rem;
      /* Align with user dashboard */
    }

    .panel-card {
      background: white;
      border-radius: 0.75rem;
      padding: 1.25rem;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
      transition: all 0.3s ease;
      display: flex;
      flex-direction: column;
      min-height: 280px;
    }

    .panel-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.12);
    }

    .panel-header {
      margin-bottom: 1rem;
      padding-bottom: 0.75rem;
      border-bottom: 2px solid #f0f0f0;
    }

    .panel-title {
      font-size: 1rem;
      font-weight: 700;
      color: #333;
      margin: 0;
      display: flex;
      align-items: center;
      gap: 0.4rem;
    }

    .panel-icon {
      font-size: 1.2rem;
      color: #667eea;
    }

    .panel-body {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
    }

    .chart-wrapper {
      width: 100%;
      height: 100%;
      min-height: 180px;
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    /* Admin Availability Button Panel */
    .action-panel {
      background: linear-gradient(135deg, #ff6fa8 0%, #e83e8c 100%);
      border-radius: 0.75rem;
      padding: 1.25rem;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      min-height: 280px;
      color: white;
      border: none;
      width: 100%;
    }

    .action-panel:hover {
      transform: translateY(-3px);
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
    }

    .action-panel-icon {
      font-size: 2.5rem;
      margin-bottom: 0.75rem;
      opacity: 0.95;
    }

    .action-panel-title {
      font-size: 1.2rem;
      font-weight: 700;
      margin-bottom: 0.4rem;
    }

    .action-panel-description {
      font-size: 0.85rem;
      opacity: 0.9;
      text-align: center;
    }

    @media (max-width: 768px) {
      .stats-container {
        gap: 0.6rem;
        margin-bottom: 1rem;
      }

      .stat-card {
        min-height: 85px;
        padding: 0.75rem;
      }

      .stat-card-icon {
        font-size: 1.3rem;
        margin-bottom: 0.15rem;
      }

      .stat-card-title {
        font-size: 0.65rem;
        margin-bottom: 0.15rem;
      }

      .stat-card-value {
        font-size: 1.4rem;
      }

      .stat-card-subtitle {
        font-size: 0.65rem;
      }

      .chart-container {
        padding: 0.6rem;
        margin-bottom: 0.75rem;
        margin-left: 0.5rem;
        margin-right: 0.5rem;
      }

      .chart-header {
        flex-direction: column;
        align-items: flex-start;
        margin-bottom: 0.4rem;
        gap: 0.4rem;
      }

      .chart-header > div:first-child {
        width: 100%;
        justify-content: space-between;
      }

      .chart-legend {
        gap: 0.6rem;
        font-size: 0.7rem;
      }

      .legend-item {
        font-size: 0.7rem;
      }

      .legend-color {
        width: 10px;
        height: 10px;
      }

      .chart-title {
        font-size: 0.85rem;
      }

      .year-selector {
        padding: 0.3rem 0.5rem;
        font-size: 0.8rem;
      }

      .chart-container > div[style*="position: relative"] {
        height: 160px !important;
      }

      /* Three panels stack vertically on mobile */
      .three-panel-container {
        grid-template-columns: 1fr;
        gap: 0.75rem;
        margin-bottom: 1rem;
      }

      .panel-card {
        min-height: 220px;
        padding: 1rem;
      }

      .panel-title {
        font-size: 0.95rem;
      }

      .chart-wrapper {
        min-height: 160px;
      }

      .action-panel {
        min-height: 130px;
        padding: 1rem;
      }

      .action-panel-icon {
        font-size: 2rem;
        margin-bottom: 0.5rem;
      }

      .action-panel-title {
        font-size: 1.1rem;
        margin-bottom: 0.3rem;
      }

      .action-panel-description {
        font-size: 0.8rem;
      }
    }
  </style>
@endsection

@section('content')

  <!-- Dashboard Statistics Cards -->
  <div class="stats-container">
    <div class="stat-card donations">
      <div>
        <div class="stat-card-icon"><i class="fas fa-hand-holding-heart"></i></div>
        <div class="stat-card-title">Total Donations</div>
      </div>
      <div>
        <div class="stat-card-value">{{ $totalDonations ?? 0 }}</div>
        <div class="stat-card-subtitle">Successful donations received</div>
      </div>
    </div>

    <div class="stat-card requests">
      <div>
        <div class="stat-card-icon"><i class="fas fa-check-circle"></i></div>
        <div class="stat-card-title">Approved Requests</div>
      </div>
      <div>
        <div class="stat-card-value">{{ $approvedRequests ?? 0 }}</div>
        <div class="stat-card-subtitle">Approved and dispensed requests</div>
      </div>
    </div>

    <div class="stat-card screenings">
      <div>
        <div class="stat-card-icon"><i class="fas fa-clipboard-check"></i></div>
        <div class="stat-card-title">Health Screenings</div>
      </div>
      <div>
        <div class="stat-card-value">{{ $totalHealthScreenings ?? 0 }}</div>
        <div class="stat-card-subtitle">Total health screenings submitted</div>
      </div>
    </div>
  </div>

  <!-- Year Timeline Chart -->
  <div class="chart-container">
    <div class="chart-header">
      <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
        <h3 class="chart-title" style="margin-bottom: 0;">Monthly Overview</h3>
        <select id="yearSelector" class="year-selector" onchange="changeYear(this.value)">
          @php
            $startYear = 2020;
            $endYear = date('Y');
            $selectedYear = $currentYear ?? $endYear;
          @endphp
          @for($year = $endYear; $year >= $startYear; $year--)
            <option value="{{ $year }}" {{ $year == $selectedYear ? 'selected' : '' }}>
              {{ $year }}
            </option>
          @endfor
        </select>
      </div>
      <div class="chart-legend">
        <div class="legend-item">
          <div class="legend-color donations"></div>
          <span>Donations</span>
        </div>
        <div class="legend-item">
          <div class="legend-color requests"></div>
          <span>Approved Requests</span>
        </div>
      </div>
    </div>
    <div style="position: relative; height: 200px;">
      <canvas id="yearTimelineChart"></canvas>
    </div>
  </div>

  <!-- Three Panel Layout: Bar Chart, Pie Chart, Admin Availability -->
  <div class="three-panel-container">

    <!-- Left Panel: Bar Chart - Donation Methods -->
    <div class="panel-card">
      <div class="panel-header">
        <h4 class="panel-title">
          <i class="fas fa-chart-bar panel-icon"></i>
          Donation Methods
        </h4>
      </div>
      <div class="panel-body">
        <div class="chart-wrapper">
          <canvas id="donationMethodChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Middle Panel: Pie Chart - Health Screening Status -->
    <div class="panel-card">
      <div class="panel-header">
        <h4 class="panel-title">
          <i class="fas fa-chart-pie panel-icon"></i>
          Health Screening Status
        </h4>
      </div>
      <div class="panel-body">
        <div class="chart-wrapper">
          <canvas id="screeningStatusChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Right Panel: Admin Availability Button -->
    <button class="action-panel" data-bs-toggle="modal" data-bs-target="#availabilityModal">
      <div class="action-panel-icon">
        <i class="fas fa-calendar-check"></i>
      </div>
      <div class="action-panel-title">Set Availability</div>
      <div class="action-panel-description">Manage your appointment schedule and availability</div>
    </button>

  </div>

  <!-- Modal -->
  <div class="modal fade" id="availabilityModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Set Availability</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">

          <!-- Calendar Display -->
          <div class="mb-4">
            <h6 class="mb-3">Select Date</h6>
            <div id="calendarContainer">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <button type="button" id="prevMonth" class="btn btn-outline-secondary btn-sm">&lt;</button>
                <div style="display:flex; align-items:center; gap:8px;">
                  <h5 id="currentMonth" class="mb-0"></h5>
                </div>
                <button type="button" id="nextMonth" class="btn btn-outline-secondary btn-sm">&gt;</button>
              </div>
              <div class="calendar-grid">
                <div class="calendar-header">
                  <div class="calendar-day-header">Sun</div>
                  <div class="calendar-day-header">Mon</div>
                  <div class="calendar-day-header">Tue</div>
                  <div class="calendar-day-header">Wed</div>
                  <div class="calendar-day-header">Thu</div>
                  <div class="calendar-day-header">Fri</div>
                  <div class="calendar-day-header">Sat</div>
                </div>
                <div id="calendarDays" class="calendar-days"></div>
              </div>
            </div>
          </div>

          <!-- Selected Date Display -->
          <div id="selectedDateDisplay" class="alert alert-info" style="display: none;">
            <strong>Selected Date:</strong> <span id="selectedDateText"></span>
          </div>

            <!-- Form to save availability -->
          <form id="availabilityForm" action="{{ route('admin.availability.store') }}" method="POST">
            @csrf

            <!-- Hidden date input for form submission -->
            <input type="hidden" id="formDate" name="date" required>

            <!-- No time slots: admin sets date-only availability -->
            <div id="timeSlotsSection">
                <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="deleteBtn" class="btn btn-danger me-2" style="display:none;">Delete Availability</button>
                <button type="submit" id="saveBtn" class="btn btn-primary" disabled>Save Availability</button>
              </div>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // ============ YEAR TIMELINE CHART ============
      const monthlyDonations = @json($monthlyDonations ?? []);
      const monthlyRequests = @json($monthlyRequests ?? []);
      const currentYear = {{ $currentYear ?? date('Y') }};

      const ctx = document.getElementById('yearTimelineChart').getContext('2d');
      const yearTimelineChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
          datasets: [
            {
              label: 'Donations',
              data: monthlyDonations,
              borderColor: '#667eea',
              backgroundColor: 'rgba(102, 126, 234, 0.1)',
              borderWidth: window.innerWidth < 768 ? 2 : 3,
              fill: true,
              tension: 0.4,
              pointRadius: window.innerWidth < 768 ? 3 : 5,
              pointHoverRadius: window.innerWidth < 768 ? 5 : 7,
              pointBackgroundColor: '#667eea',
              pointBorderColor: '#fff',
              pointBorderWidth: 2,
              pointHoverBackgroundColor: '#667eea',
              pointHoverBorderColor: '#fff',
            },
            {
              label: 'Approved Requests',
              data: monthlyRequests,
              borderColor: '#f5576c',
              backgroundColor: 'rgba(245, 87, 108, 0.1)',
              borderWidth: window.innerWidth < 768 ? 2 : 3,
              fill: true,
              tension: 0.4,
              pointRadius: window.innerWidth < 768 ? 3 : 5,
              pointHoverRadius: window.innerWidth < 768 ? 5 : 7,
              pointBackgroundColor: '#f5576c',
              pointBorderColor: '#fff',
              pointBorderWidth: 2,
              pointHoverBackgroundColor: '#f5576c',
              pointHoverBorderColor: '#fff',
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          interaction: {
            mode: 'index',
            intersect: false,
          },
          plugins: {
            legend: {
              display: false // We have custom legend
            },
            tooltip: {
              backgroundColor: 'rgba(0, 0, 0, 0.8)',
              padding: window.innerWidth < 768 ? 8 : 12,
              titleFont: {
                size: window.innerWidth < 768 ? 12 : 14,
                weight: 'bold'
              },
              bodyFont: {
                size: window.innerWidth < 768 ? 11 : 13
              },
              callbacks: {
                title: function (context) {
                  return context[0].label + ' ' + currentYear;
                },
                label: function (context) {
                  return context.dataset.label + ': ' + context.parsed.y;
                }
              }
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                stepSize: 1,
                font: {
                  size: window.innerWidth < 768 ? 10 : 12
                },
                padding: window.innerWidth < 768 ? 5 : 10
              },
              grid: {
                color: 'rgba(0, 0, 0, 0.05)',
              }
            },
            x: {
              ticks: {
                font: {
                  size: window.innerWidth < 768 ? 9 : 12
                },
                maxRotation: 0,
                minRotation: 0,
                padding: window.innerWidth < 768 ? 5 : 10,
                autoSkip: false
              },
              grid: {
                display: false
              }
            }
          }
        }
      });

      // ============ DONATION METHOD BAR CHART ============
      const walkInDonations = {{ $walkInDonations ?? 0 }};
      const homeCollectionDonations = {{ $homeCollectionDonations ?? 0 }};

      const donationMethodCtx = document.getElementById('donationMethodChart').getContext('2d');
      const donationMethodChart = new Chart(donationMethodCtx, {
        type: 'bar',
        data: {
          labels: ['Walk-in', 'Home Collection'],
          datasets: [{
            label: 'Donations',
            data: [walkInDonations, homeCollectionDonations],
            backgroundColor: [
              'rgba(102, 126, 234, 0.8)',
              'rgba(118, 75, 162, 0.8)'
            ],
            borderColor: [
              'rgba(102, 126, 234, 1)',
              'rgba(118, 75, 162, 1)'
            ],
            borderWidth: 2,
            borderRadius: 8,
            barPercentage: 0.6
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            },
            tooltip: {
              backgroundColor: 'rgba(0, 0, 0, 0.8)',
              padding: 12,
              titleFont: {
                size: 14,
                weight: 'bold'
              },
              bodyFont: {
                size: 13
              },
              callbacks: {
                label: function (context) {
                  return context.parsed.y + ' donations';
                }
              }
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                stepSize: 1,
                font: {
                  size: 12
                }
              },
              grid: {
                color: 'rgba(0, 0, 0, 0.05)',
              }
            },
            x: {
              ticks: {
                font: {
                  size: 12,
                  weight: 'bold'
                }
              },
              grid: {
                display: false
              }
            }
          }
        }
      });

      // ============ HEALTH SCREENING STATUS PIE CHART ============
      const pendingScreenings = {{ $pendingScreenings ?? 0 }};
      const acceptedScreenings = {{ $acceptedScreenings ?? 0 }};
      const declinedScreenings = {{ $declinedScreenings ?? 0 }};

      const screeningStatusCtx = document.getElementById('screeningStatusChart').getContext('2d');
      const screeningStatusChart = new Chart(screeningStatusCtx, {
        type: 'pie',
        data: {
          labels: ['Pending', 'Accepted', 'Declined'],
          datasets: [{
            data: [pendingScreenings, acceptedScreenings, declinedScreenings],
            backgroundColor: [
              'rgba(255, 193, 7, 0.8)',
              'rgba(40, 167, 69, 0.8)',
              'rgba(220, 53, 69, 0.8)'
            ],
            borderColor: [
              'rgba(255, 193, 7, 1)',
              'rgba(40, 167, 69, 1)',
              'rgba(220, 53, 69, 1)'
            ],
            borderWidth: 2
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: true,
              position: 'bottom',
              labels: {
                padding: 15,
                font: {
                  size: 12,
                  weight: 'bold'
                },
                usePointStyle: true,
                pointStyle: 'circle'
              }
            },
            tooltip: {
              backgroundColor: 'rgba(0, 0, 0, 0.8)',
              padding: 12,
              titleFont: {
                size: 14,
                weight: 'bold'
              },
              bodyFont: {
                size: 13
              },
              callbacks: {
                label: function (context) {
                  const total = context.dataset.data.reduce((a, b) => a + b, 0);
                  const value = context.parsed;
                  const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                  return context.label + ': ' + value + ' (' + percentage + '%)';
                }
              }
            }
          }
        }
      });

      // Make charts responsive on window resize
      window.addEventListener('resize', function () {
        donationMethodChart.resize();
        screeningStatusChart.resize();
      });

      // ============ AVAILABILITY CALENDAR ============
      // Get available dates from backend
      const availableDates = @json($availableDates ?? []);
      console.log('Available Dates:', availableDates);

      const currentMonthDisplay = document.getElementById('currentMonth');
      const calendarDays = document.getElementById('calendarDays');
      const prevMonthBtn = document.getElementById('prevMonth');
      const nextMonthBtn = document.getElementById('nextMonth');
      const selectedDateDisplay = document.getElementById('selectedDateDisplay');
      const selectedDateText = document.getElementById('selectedDateText');
      const formDate = document.getElementById('formDate');
      const saveBtn = document.getElementById('saveBtn');

    let currentDate = new Date();
    // Multi-date selection always enabled (toggle removed for simplicity)
    let selectedDates = []; // array of 'YYYY-MM-DD' strings
    let selectedAvailabilityIds = {}; // map dateString -> availabilityId (if exists)

      // Format date in local timezone to avoid UTC offset issues
      function formatDate(date) {
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
      }

      function formatDisplayDate(date) {
        return date.toLocaleDateString('en-US', {
          weekday: 'long',
          year: 'numeric',
          month: 'long',
          day: 'numeric'
        });
      }

      function renderCalendar() {
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();

        // Update month display
        currentMonthDisplay.textContent = new Date(year, month).toLocaleDateString('en-US', {
          month: 'long',
          year: 'numeric'
        });

        // Clear calendar
        calendarDays.innerHTML = '';

        // Debug: Log available dates array
        console.log('=== Rendering Calendar ===');
        console.log('Available Dates Array:', availableDates);
        console.log('Type of first element:', typeof availableDates[0]);

        // Get first day of month and number of days
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const startDate = new Date(firstDay);
        startDate.setDate(startDate.getDate() - firstDay.getDay());

        // Generate calendar days (6 weeks)
        for (let i = 0; i < 42; i++) {
          const date = new Date(startDate);
          date.setDate(startDate.getDate() + i);

          const dayElement = document.createElement('div');
          dayElement.className = 'calendar-day';
          dayElement.textContent = date.getDate();

          const today = new Date();
          const isCurrentMonth = date.getMonth() === month;
          // Compare at local start-of-day to prevent timezone-related off-by-one
          const dayStart = new Date(date.getFullYear(), date.getMonth(), date.getDate()).getTime();
          const todayStart = new Date(today.getFullYear(), today.getMonth(), today.getDate()).getTime();
          const isPastDate = dayStart < todayStart;
          const dateString = formatDate(date);
          const hasAvailability = availableDates.includes(dateString);

          // Debug logging for every date in current month
          if (isCurrentMonth) {
            console.log(`Date: ${dateString}, hasAvailability: ${hasAvailability}, isPastDate: ${isPastDate}`);
          }

          if (!isCurrentMonth) {
            dayElement.classList.add('other-month');
          } else if (isPastDate) {
            dayElement.classList.add('disabled');
          } else {
            dayElement.addEventListener('click', () => selectDate(date));
          }

          // Mark days with availability
          if (hasAvailability && isCurrentMonth && !isPastDate) {
            dayElement.classList.add('has-availability');
            console.log('✅ HIGHLIGHTED:', dateString);
          }

          // Mark selected date(s)
          if (selectedDates.includes(dateString)) {
            dayElement.classList.add('selected');
          }

          calendarDays.appendChild(dayElement);
        }
        console.log('=== Calendar Render Complete ===');
      }

      function selectDate(date) {
        const dateStr = formatDate(new Date(date));

        // Toggle selection for this date (multi-date always enabled)
        const idx = selectedDates.indexOf(dateStr);
        if (idx !== -1) {
          // Deselect
          selectedDates.splice(idx, 1);
          delete selectedAvailabilityIds[dateStr];
        } else {
          // Select
          selectedDates.push(dateStr);
          // Fetch availability id for this date if exists (for delete support)
          fetch(`{{ route('admin.availability.slots') }}?date=${encodeURIComponent(dateStr)}`)
            .then(r => r.json())
            .then(data => {
              if (data && Array.isArray(data.available_slots) && data.available_slots.length > 0) {
                selectedAvailabilityIds[dateStr] = data.available_slots[0].id;
                // Update delete button visibility after async load
                const deleteBtnEl = document.getElementById('deleteBtn');
                if (deleteBtnEl) {
                  const hasAnyId = Object.keys(selectedAvailabilityIds).length > 0;
                  deleteBtnEl.style.display = hasAnyId ? 'inline-block' : 'none';
                  deleteBtnEl.disabled = !hasAnyId;
                }
              }
            })
            .catch(err => console.error('Error checking availability slots:', err));
        }

        // Update display (show raw list of YYYY-MM-DD for clarity; could be improved to formatted list)
        selectedDateText.textContent = selectedDates.length ? selectedDates.join(', ') : '';
        selectedDateDisplay.style.display = selectedDates.length ? 'block' : 'none';
        if (saveBtn) saveBtn.disabled = selectedDates.length === 0;

        // Update delete button
        const deleteBtnEl = document.getElementById('deleteBtn');
        if (deleteBtnEl) {
          const hasAnyId = Object.keys(selectedAvailabilityIds).length > 0;
          deleteBtnEl.style.display = hasAnyId ? 'inline-block' : 'none';
          deleteBtnEl.disabled = !hasAnyId;
        }
        renderCalendar();
        console.log('Date toggled:', dateStr, 'Selected set:', selectedDates);
      }

      // No time slots to render - admin selects date only

      // Navigation buttons
      prevMonthBtn.addEventListener('click', function () {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar();
      });

      nextMonthBtn.addEventListener('click', function () {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar();
      });

      // Multi-select toggle removed; multi-date selection always active

      // Form submission (date-only availability)
      document.getElementById('availabilityForm').addEventListener('submit', function (e) {
        e.preventDefault();
        // Determine which dates to save
        const datesToSave = selectedDates.slice();
        if (!datesToSave.length) {
          // Close modal first
          const modalEl = document.getElementById('availabilityModal');
          const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
          try { modal.hide(); } catch (e) { /* ignore */ }
          
          // Wait for modal to close before showing alert
          setTimeout(() => {
            Swal.fire({ icon: 'warning', title: 'No Date Selected', text: 'Please select at least one date.', confirmButtonColor: '#ff6fa8' });
          }, 300);
          return;
        }

        // Close modal before showing loading SweetAlert
        const modalEl = document.getElementById('availabilityModal');
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        try { modal.hide(); } catch (e) { /* ignore */ }
        
        // Wait for modal to close, then show loading
        setTimeout(() => {
          Swal.fire({
            title: 'Saving Availability...',
            text: 'Please wait while we save your availability settings.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => { Swal.showLoading(); }
          });
        }, 300);

        // Post each date sequentially and collect results
        const csrf = document.querySelector('input[name="_token"]').value;
        const savePromises = datesToSave.map(d => {
          // Skip if already present
          if (availableDates.includes(d)) return Promise.resolve({ success: true, skipped: true, date: d });
          const fd = new FormData(); fd.append('date', d);
          return fetch(this.action, { method: 'POST', body: fd, headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(resp => resp.ok ? resp.json() : resp.text().then(t => { let j=null; try{j=JSON.parse(t)}catch(e){}; throw new Error((j && j.message) ? j.message : t || 'Save failed'); }))
            .then(data => ({ success: true, data, date: d }))
            .catch(err => ({ success: false, error: err.message || String(err), date: d }));
        });

        Promise.all(savePromises).then(results => {
          const saved = results.filter(r => r.success && !r.skipped).map(r => r.date);
          const skipped = results.filter(r => r.skipped).map(r => r.date);
          const failed = results.filter(r => !r.success).map(r => ({ date: r.date, error: r.error }));

          // Update availableDates array
          saved.forEach(d => { if (!availableDates.includes(d)) availableDates.push(d); });

          // Build message
          let msg = '';
          if (saved.length) msg += `${saved.length} date(s) saved successfully.`;
          if (skipped.length) msg += (msg ? ' ' : '') + `${skipped.length} date(s) were already set.`;
          if (failed.length) msg += (msg ? ' ' : '') + `${failed.length} date(s) failed to save.`;

          Swal.fire({ icon: failed.length ? 'warning' : 'success', title: failed.length ? 'Partial Success' : 'Success', text: msg || 'No changes made.', confirmButtonColor: '#e83e8c', customClass: { container: 'swal-high-zindex' } })
            .then(() => { window.location.reload(); });
        }).catch(err => {
          console.error('Save error:', err);
          Swal.fire({ icon: 'error', title: 'Error', text: 'An unexpected error occurred while saving availability.', confirmButtonColor: '#b21f66', customClass: { container: 'swal-high-zindex' } });
        });
      });

      // Delete availability button handler (supports multiple delete)
      const deleteBtn = document.getElementById('deleteBtn');
      if (deleteBtn) {
        deleteBtn.addEventListener('click', function () {
          // Collect availability ids for selected dates
          const ids = Object.values(selectedAvailabilityIds).filter(Boolean);
          if (!ids.length) {
            // Close modal first
            const modalEl = document.getElementById('availabilityModal');
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            try { modal.hide(); } catch (e) { /* ignore */ }
            
            // Wait for modal to close before showing alert
            setTimeout(() => {
              Swal.fire({ icon: 'warning', title: 'Nothing to delete', text: 'There is no availability record for the selected date(s).', confirmButtonColor: '#e83e8c' });
            }, 300);
            return;
          }

          // Close modal before showing delete confirmation
          const modalEl = document.getElementById('availabilityModal');
          const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
          try { modal.hide(); } catch (e) { /* ignore */ }
          
          // Wait for modal to close, then show confirmation
          setTimeout(() => {
            Swal.fire({
              title: 'Delete availability?',
              text: `This will remove the availability for ${ids.length} selected date(s). This action cannot be undone.`,
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#d33',
              cancelButtonColor: '#6c757d',
              confirmButtonText: 'Yes, delete them',
            }).then((result) => {
            if (!result.isConfirmed) return;

            const csrf = document.querySelector('input[name="_token"]').value;
            // Send DELETE requests sequentially
            const deletePromises = ids.map(id => fetch(`{{ url('/admin/availability') }}/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
              .then(resp => resp.text().then(text => { let data=null; try{data= text ? JSON.parse(text) : null;}catch(e){data=null;} if (resp.ok) return data||{success:true}; const msg=(data&&data.message)?data.message:(text||'Delete failed'); throw new Error(msg);}))
            );

            Promise.allSettled(deletePromises).then(results => {
              const succeeded = [];
              const failed = [];
              results.forEach((r, idx) => {
                if (r.status === 'fulfilled') succeeded.push(ids[idx]);
                else failed.push({ id: ids[idx], reason: r.reason && r.reason.message ? r.reason.message : String(r.reason) });
              });

              // Remove succeeded dates from availableDates and selected arrays
              succeeded.forEach(id => {
                // find date(s) that matched this id and remove
                for (const [dateStr, availId] of Object.entries(selectedAvailabilityIds)) {
                  if (String(availId) === String(id)) {
                    const idx = availableDates.indexOf(dateStr);
                    if (idx !== -1) availableDates.splice(idx, 1);
                    // remove from selectedDates
                    const sdIdx = selectedDates.indexOf(dateStr);
                    if (sdIdx !== -1) selectedDates.splice(sdIdx, 1);
                    delete selectedAvailabilityIds[dateStr];
                  }
                }
              });

              // Show result
              let msg = `${succeeded.length} date(s) deleted.`;
              if (failed.length) msg += ` ${failed.length} failed.`;
              Swal.fire({ icon: failed.length ? 'warning' : 'success', title: failed.length ? 'Partial Result' : 'Deleted', text: msg, confirmButtonColor: '#e83e8c', customClass: { container: 'swal-high-zindex' } }).then(() => { window.location.reload(); });
            }).catch(err => {
              console.error('Delete batch error:', err);
              Swal.fire({ icon: 'error', title: 'Error', text: 'An unexpected error occurred while deleting availability.', confirmButtonColor: '#b21f66', customClass: { container: 'swal-high-zindex' } });
            });
          });
          }, 300); // End setTimeout for delete confirmation
        });
      }

      // Initialize calendar
      renderCalendar();
      console.log('Admin availability calendar loaded');
    });

    // Function to handle year change
    function changeYear(year) {
      // Redirect to dashboard with year parameter
      window.location.href = "{{ route('admin.dashboard') }}?year=" + year;
    }
  </script>
@endsection