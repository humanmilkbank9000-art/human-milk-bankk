<style>
    /* Pink-themed table wrapper matching the provided style */
    .hc-wrapper {
        border-radius: 10px;
        overflow: visible;
        border: 1px solid #e6e6e9;
    }

    .hc-table {
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }

    .hc-table thead th {
        background: #ffd7e6;
        color: #222;
        font-weight: 700;
        padding: 12px 16px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .hc-table thead th:first-child {
        border-top-left-radius: 8px;
    }

    .hc-table thead th:last-child {
        border-top-right-radius: 8px;
    }

    .hc-table tbody tr {
        background: #fff;
        transition: background 0.2s ease;
    }

    .hc-table tbody tr:nth-child(even) {
        background: #f8f9fb;
    }

    .hc-table tbody tr.filled {
        background: #ffe6ef;
    }

    .hc-table tbody tr:hover {
        background: #f8f9fb;
    }

    .hc-table td,
    .hc-table th {
        vertical-align: middle;
        padding: 10px 12px;
        border-top: 1px solid #f1f1f2;
    }

    /* Compact controls inside table cells */
    .hc-table td .form-control-sm,
    .hc-table td .form-select-sm {
        min-width: 60px;
        padding: 6px 8px;
        box-sizing: border-box;
    }

    /* Make time and date columns narrower so the table fits better */
    .hc-table td:nth-child(1) .form-control-sm {
        width: 90px;
        max-width: 90px;
    }

    .hc-table td:nth-child(2) .form-control-sm {
        width: 120px;
        max-width: 120px;
    }

    .hc-table td:nth-child(3) .hc-bag-label {
        white-space: nowrap;
    }

    /* Make selects slightly narrower, but allow the collection-method dropdown to show longer labels */
    .hc-table td:nth-child(5) .form-select-sm {
        width: 110px;
        max-width: 140px;
    }

    /* Make collection method show full text and fit inside modal */
    /* Ensure the 7th column has enough minimum width and the select fills the cell */
    .hc-table td:nth-child(7) {
        min-width: 260px;
    }

    .hc-table td:nth-child(7) .form-select-sm {
        display: block;
        width: 100%;
        max-width: none;
        white-space: normal;
    }

    /* For very small screens, allow controls to shrink and the table to scroll horizontally */
    @media (max-width: 480px) {
        .hc-table td:nth-child(1) .form-control-sm {
            width: 72px;
        }

        .hc-table td:nth-child(2) .form-control-sm {
            width: 96px;
        }

        .hc-table td .form-control-sm,
        .hc-table td .form-select-sm {
            min-width: 56px;
        }

        .hc-table td:nth-child(7) {
            min-width: 180px;
        }

        .hc-table td:nth-child(7) .form-select-sm {
            width: 100%;
            max-width: none;
        }
    }

    .hc-badge {
        display: inline-block;
        padding: 0.35rem 0.75rem;
        background: #ffd1e1;
        color: #111;
        border-radius: 999px;
        min-width: 2.5rem;
        text-align: center;
        font-weight: 700;
        font-size: 0.95rem;
    }

    .hc-pill {
        background: #ffb3cf;
        border-radius: 999px;
        padding: 0.15rem 0.6rem;
        display: inline-flex;
        align-items: center;
        font-weight: 700;
    }

    .hc-actions .btn {
        padding: 0.25rem 0.5rem;
    }

    /* Add-row button (pink) and Next button (green) styles */
    #hc-add-row {
        background: linear-gradient(135deg, #ff7bb0, #ff5aa8);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 0.5rem 1rem;
        box-shadow: 0 6px 14px rgba(255, 90, 168, 0.12);
        font-weight: 600;
    }

    #hc-add-row:hover {
        transform: translateY(-2px);
        background: linear-gradient(135deg, #ff5aa8, #ff2b86);
    }

    #home-collection-submit-btn {
        background: linear-gradient(135deg, #28a745, #20c046);
        color: #fff !important;
        border: none;
        border-radius: 24px;
        padding: 0.5rem 1.5rem;
        box-shadow: 0 6px 18px rgba(32, 192, 70, 0.12);
        font-weight: 600;
    }

    #home-collection-submit-btn:hover {
        background: linear-gradient(135deg, #20c046, #1ea03d);
        transform: translateY(-2px);
        color: #fff !important;
    }

    #home-collection-submit-btn:disabled {
        opacity: 0.6;
        filter: grayscale(10%);
        cursor: not-allowed;
        transform: none;
    }

    /* Improve modal body spacing so table fits nicely */
    .modal-body .hc-wrapper {
        padding: 0;
    }

    /* Specific modal sizing and controls for Home Collection modal */
    #homeCollectionModal .modal-dialog {
        max-width: 1100px;
    }

    @media (max-width: 768px) {
        #homeCollectionModal .modal-dialog {
            max-width: calc(100% - 32px);
            margin: 1rem;
        }
    }

    #homeCollectionModal .modal-body {
        max-height: calc(100vh - 180px);
        overflow-y: auto;
        padding: 1.25rem 1.5rem;
    }

    #homeCollectionModal .modal-header {
        border-bottom: 0;
        padding: 0.75rem 1.25rem;
    }

    #homeCollectionModal .btn-close {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        background: #fff;
        width: 36px;
        height: 36px;
        color: #000 !important;
        opacity: 1;
    }

    /* Ensure table wrapper horizontally scrolls when needed */
    .table-responsive.hc-wrapper {
        overflow-x: auto;
    }

    /* Make Next button full width on very small screens so it's always reachable */
    @media (max-width: 480px) {
        .hc-footer .btn {
            width: 100%;
        }
    }

    /* Date input styling */
    .hc-date-row {
        margin-bottom: 1.5rem;
    }

    .hc-date-row label {
        font-weight: 600;
        color: #333;
        margin-bottom: 0.5rem;
    }

    .hc-date-row input[type="date"] {
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 0.5rem 0.75rem;
    }

    /* Counter section */
    .hc-counters {
        display: flex;
        justify-content: space-between;
        margin-bottom: 1rem;
        padding: 0.75rem;
        background: #f8f9fa;
        border-radius: 8px;
    }

    .hc-counter-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 600;
        color: #333;
    }

    /* Button row styling */
    .hc-button-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 1.5rem;
        padding-top: 1rem;
        border-top: 1px solid #e9ecef;
    }

    /* Responsive Leaflet map container */
    #hcMap {
        height: 60vh;
        min-height: 320px;
        width: 100%;
        border-radius: 8px;
    }

    @media (max-width: 576px) {
        #hcMap {
            height: 55vh;
            min-height: 280px;
        }
    }
</style>

<form action="{{ route('donation.store') }}" method="POST" id="homeCollectionForm">
    @csrf
    <input type="hidden" name="donation_method" value="home_collection">
    <input type="hidden" name="latitude" id="latitude">
    <input type="hidden" name="longitude" id="longitude">
    <!-- Consent handled via modal; no questionnaire hidden fields needed -->

    <!-- Date of expression fields -->
    <div class="row g-3 hc-date-row">
        <div class="col-md-6">
            <label class="form-label">Date of first expression:</label>
            <input type="date" class="form-control" name="first_expression_date" id="hc-first-expression"
                placeholder="dd/mm/yyyy" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Date of last expression:</label>
            <input type="date" class="form-control" name="last_expression_date" id="hc-last-expression"
                placeholder="dd/mm/yyyy" required>
        </div>
    </div>

    <!-- Number of Bags Input -->
    <div class="row g-3 mb-3">
        <div class="col-md-12">
            <label class="form-label">Number of Bags:</label>
            <input type="number" class="form-control" id="hc-bags-input" min="1" max="20"
                placeholder="Enter number of bags" required>
            <small class="text-muted">Enter the number of bags to create input rows below</small>
        </div>
    </div>

    <!-- Counters -->
    <div class="hc-counters">
        <div class="hc-counter-item">
            <span>No. of Bags:</span>
            <span id="hc-total-bags" class="hc-badge">0</span>
        </div>
        <div class="hc-counter-item">
            <span>Total Volume:</span>
            <span id="hc-total-volume" class="hc-badge">0</span>
        </div>
    </div>

    <!-- Table -->
    <div class="table-responsive hc-wrapper" id="hc-table-container" style="display: none;">
        <table class="table hc-table align-middle">
            <thead>
                <tr>
                    <th style="width:12%">Time</th>
                    <th style="width:14%">Date</th>
                    <th style="width:10%">Bags</th>
                    <th style="width:12%">Volume</th>
                    <th style="width:16%">Storage Location</th>
                    <th style="width:12%">TEMP (°C)</th>
                    <th style="width:24%">Milk Collection Method</th>
                </tr>
            </thead>
            <tbody id="hc-rows"></tbody>
        </table>
    </div>

    <!-- Submit Button -->
    <div class="hc-button-row">
        <button type="button" id="home-collection-submit-btn" onclick="submitHomeCollection()" disabled>Submit Home
            Collection</button>
    </div>
</form>

<script>
    (function () {
        const rowsTbodyId = 'hc-rows';
        let inited = false;

        function el(tag, attrs = {}, children = []) {
            const e = document.createElement(tag);
            Object.entries(attrs).forEach(([k, v]) => {
                if (k === 'class') e.className = v; else if (k === 'text') e.textContent = v; else e.setAttribute(k, v);
            });
            children.forEach(c => e.appendChild(c));
            return e;
        }

        function generateRows(numBags) {
            console.log('generateRows called with:', numBags);
            const tbody = document.getElementById(rowsTbodyId);
            const tableContainer = document.getElementById('hc-table-container');
            console.log('tbody:', tbody);
            console.log('tableContainer:', tableContainer);

            if (!tbody) {
                console.error('Tbody not found!');
                return;
            }

            // Clear existing rows
            tbody.innerHTML = '';
            console.log('Cleared existing rows');

            // Show or hide table
            if (numBags > 0) {
                console.log('Showing table and generating', numBags, 'rows');
                tableContainer.style.display = 'block';
                // Generate the specified number of rows
                for (let i = 1; i <= numBags; i++) {
                    console.log('Adding row', i);
                    addRow(i);
                }
            } else {
                console.log('Hiding table');
                tableContainer.style.display = 'none';
            }

            recalcTotals();
            enableSubmitCheck();
            console.log('generateRows completed');
        }

        function addRow(bagNumber) {
            const tbody = document.getElementById(rowsTbodyId);
            if (!tbody) {
                console.error('Tbody not found!');
                return;
            }

            const tr = el('tr');

            const time = el('input', { type: 'time', name: 'bag_time[]', class: 'form-control form-control-sm', required: true });
            const date = el('input', { type: 'date', name: 'bag_date[]', class: 'form-control form-control-sm', required: true });

            const bagsLabel = el('div', { class: 'hc-bag-label fw-bold', text: 'Bag ' + bagNumber });
            const bags = el('input', { type: 'hidden', name: 'bag_number[]', value: String(bagNumber) });

            const volume = el('input', { 
                type: 'number', 
                name: 'bag_volume[]', 
                class: 'form-control form-control-sm', 
                min: '1', 
                step: '1', 
                placeholder: 'ml', 
                required: true, 
                value: '120' 
            });

            const storage = el('select', { name: 'bag_storage[]', class: 'form-select form-select-sm', required: true }, [
                el('option', { value: '', text: 'Select' }),
                // Keep underlying values for backend compatibility but present friendly labels to users
                el('option', { value: 'REF', text: 'Refrigerator' }),
                el('option', { value: 'FRZ', text: 'Freezer' })
            ]);

            const temp = el('input', { type: 'number', name: 'bag_temp[]', class: 'form-control form-control-sm', step: '0.1', placeholder: '°C', required: true });

            const method = el('select', { name: 'bag_method[]', class: 'form-select form-select-sm', required: true });
            const m0 = el('option', { value: '', text: 'Select method' });
            const m1 = el('option', { value: 'Manual hands expression', text: 'Manual hands expression' });
            const m2 = el('option', { value: 'Manual breast pump', text: 'Manual breast pump' });
            const m3 = el('option', { value: 'Electric breast pump', text: 'Electric breast pump' });
            method.appendChild(m0); method.appendChild(m1); method.appendChild(m2); method.appendChild(m3);

            // Rounding helper: rounds to nearest 10ml for values >= 10
            function snapVolumeIfNeeded(inputEl) {
                const raw = inputEl.value.trim();
                if (!raw) return;
                const num = parseFloat(raw);
                if (isNaN(num) || num <= 0) return;
                
                // For values less than 10, keep as whole numbers
                if (num < 10) {
                    const rounded = Math.round(num);
                    inputEl.value = String(rounded);
                } else {
                    // For values >= 10, round to nearest 10
                    const rounded = Math.round(num / 10) * 10;
                    inputEl.value = String(rounded);
                }
            }

            // Attach snapping on blur (when user exits the field) for volume field
            // On creation, apply rounding to default value
            setTimeout(() => { snapVolumeIfNeeded(volume); recalcTotals(); enableSubmitCheck(); }, 0);
            volume.addEventListener('blur', () => { snapVolumeIfNeeded(volume); markRow(tr); recalcTotals(); enableSubmitCheck(); });
            volume.addEventListener('change', () => { snapVolumeIfNeeded(volume); markRow(tr); recalcTotals(); enableSubmitCheck(); });

            const inputs = [time, date, storage, temp, method];
            inputs.forEach(inp => inp.addEventListener('input', () => { markRow(tr); recalcTotals(); enableSubmitCheck(); }));
            inputs.forEach(inp => inp.addEventListener('change', () => { markRow(tr); recalcTotals(); enableSubmitCheck(); }));

            const timeTd = el('td', {}, [time]);
            const dateTd = el('td', {}, [date]);
            const bagsTd = el('td', {}, []);
            bagsTd.appendChild(bagsLabel);
            bagsTd.appendChild(bags);
            const volumeTd = el('td', {}, [volume]);
            const storageTd = el('td', {}, [storage]);
            const tempTd = el('td', {}, [temp]);
            const methodTd = el('td', {}, [method]);
            [timeTd, dateTd, bagsTd, volumeTd, storageTd, tempTd, methodTd].forEach(td => tr.appendChild(td));

            tbody.appendChild(tr);
            markRow(tr);
        }

        function markRow(tr) {
            const vol = tr.querySelector('input[name="bag_volume[]"]')?.value;
            const date = tr.querySelector('input[name="bag_date[]"]')?.value;
            const time = tr.querySelector('input[name="bag_time[]"]')?.value;
            const storage = tr.querySelector('select[name="bag_storage[]"]')?.value;
            const temp = tr.querySelector('input[name="bag_temp[]"]')?.value;
            const method = tr.querySelector('select[name="bag_method[]"]')?.value;

            if (vol && date && time && storage && temp && method) {
                tr.classList.add('filled');
            } else {
                tr.classList.remove('filled');
            }
        }

        function recalcTotals() {
            const rows = Array.from(document.querySelectorAll('#' + rowsTbodyId + ' tr'));
            const totalBags = rows.length;
            let totalVol = 0;

            rows.forEach(r => {
                const v = parseFloat(r.querySelector('input[name="bag_volume[]"]')?.value || '0');
                totalVol += isNaN(v) ? 0 : v;
            });

            const bagsEl = document.getElementById('hc-total-bags');
            const volEl = document.getElementById('hc-total-volume');

            if (bagsEl) bagsEl.textContent = String(totalBags);
            if (volEl) {
                // If total volume is a whole number, show it without decimals (remove trailing .0)
                // Otherwise show with two decimals for precision
                const rounded = Math.round(totalVol);
                if (Math.abs(totalVol - rounded) < 1e-9) {
                    volEl.textContent = String(rounded);
                } else {
                    volEl.textContent = totalVol.toFixed(2);
                }
            }
        }

        function enableSubmitCheck() {
            const submitBtn = document.getElementById('home-collection-submit-btn');
            if (!submitBtn) {
                console.log('Submit button not found');
                return;
            }

            const rows = Array.from(document.querySelectorAll('#' + rowsTbodyId + ' tr'));
            const firstExpression = document.getElementById('hc-first-expression')?.value;
            const lastExpression = document.getElementById('hc-last-expression')?.value;

            console.log('Checking button state:', {
                rowsCount: rows.length,
                firstExpression: firstExpression,
                lastExpression: lastExpression
            });

            // Check if all rows are completely filled
            let allRowsComplete = rows.length > 0; // Start with true if we have rows
            rows.forEach((r, index) => {
                const vol = r.querySelector('input[name="bag_volume[]"]')?.value;
                const date = r.querySelector('input[name="bag_date[]"]')?.value;
                const time = r.querySelector('input[name="bag_time[]"]')?.value;
                const storage = r.querySelector('select[name="bag_storage[]"]')?.value;
                const temp = r.querySelector('input[name="bag_temp[]"]')?.value;
                const method = r.querySelector('select[name="bag_method[]"]')?.value;

                const isComplete = vol && date && time && storage && temp && method;
                console.log(`Row ${index + 1}:`, { vol, date, time, storage, temp, method, isComplete });

                if (!isComplete) {
                    allRowsComplete = false;
                }
            });

            console.log('All rows complete:', allRowsComplete);

            // Enable Submit button only if dates are set, rows exist, and all rows are complete
            const shouldEnable = rows.length > 0 && firstExpression && lastExpression && allRowsComplete;
            console.log('Should enable button:', shouldEnable);
            submitBtn.disabled = !shouldEnable;
        }

        function requestLocation() {
            // Silently request location in the background
            if (!navigator.geolocation) {
                console.warn('Geolocation not supported');
                return;
            }
            navigator.geolocation.getCurrentPosition((pos) => {
                document.getElementById('latitude').value = pos.coords.latitude;
                document.getElementById('longitude').value = pos.coords.longitude;
                console.log('Location captured successfully');
            }, (error) => {
                console.warn('Location error:', error.message);
            }, { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 });
        }

        function init() {
            if (inited) return;
            inited = true;
            console.log('Home Collection Modal Initialized');

            // Add event listener for bags input
            const bagsInput = document.getElementById('hc-bags-input');
            console.log('Bags input element:', bagsInput);
            if (bagsInput) {
                console.log('Adding input listener to bags input');
                bagsInput.addEventListener('input', function () {
                    const numBags = parseInt(this.value) || 0;
                    console.log('Bags input changed to:', numBags);
                    if (numBags >= 1 && numBags <= 20) {
                        console.log('Generating', numBags, 'rows');
                        generateRows(numBags);
                    } else if (numBags > 20) {
                        this.value = 20;
                        generateRows(20);
                    } else {
                        generateRows(0);
                    }
                });
                console.log('Input listener attached successfully');
            } else {
                console.error('Bags input element not found!');
            }

            // Add listeners for expression dates
            const firstExpr = document.getElementById('hc-first-expression');
            const lastExpr = document.getElementById('hc-last-expression');
            if (firstExpr) firstExpr.addEventListener('change', enableSubmitCheck);
            if (lastExpr) lastExpr.addEventListener('change', enableSubmitCheck);

            console.log('Home collection form initialized');

            // Request location on first open
            requestLocation();
        }

        // Submit flow: validation -> lifestyle Yes/No Q&A -> confirm -> AJAX
        window.submitHomeCollection = function () {
            console.log('=== submitHomeCollection called ===');
            const form = document.getElementById('homeCollectionForm');

            if (!form) {
                console.error('Form not found');
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Form not found. Please refresh the page and try again.',
                    confirmButtonColor: '#d33'
                });
                return;
            }

            console.log('Form found:', form);
            console.log('Form action:', form.action);
            console.log('Form method:', form.method);

            // Validate all fields
            const rows = Array.from(document.querySelectorAll('#' + rowsTbodyId + ' tr'));
            const firstExpression = document.getElementById('hc-first-expression')?.value;
            const lastExpression = document.getElementById('hc-last-expression')?.value;

            console.log('Validation check:', {
                rows: rows.length,
                firstExpression,
                lastExpression
            });

            if (!firstExpression || !lastExpression) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Missing Information',
                    text: 'Please fill in both expression dates.',
                    confirmButtonColor: '#6c757d'
                });
                return;
            }

            if (rows.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Missing Information',
                    text: 'Please add at least one bag.',
                    confirmButtonColor: '#6c757d'
                });
                return;
            }

            // Check if all rows are complete and log data
            let allComplete = true;
            let formDataLog = [];

            rows.forEach((r, index) => {
                const vol = r.querySelector('input[name="bag_volume[]"]')?.value;
                const date = r.querySelector('input[name="bag_date[]"]')?.value;
                const time = r.querySelector('input[name="bag_time[]"]')?.value;
                const storage = r.querySelector('select[name="bag_storage[]"]')?.value;
                const temp = r.querySelector('input[name="bag_temp[]"]')?.value;
                const method = r.querySelector('select[name="bag_method[]"]')?.value;
                const bagNum = r.querySelector('input[name="bag_number[]"]')?.value;

                formDataLog.push({
                    bag: bagNum,
                    time, date, vol, storage, temp, method
                });

                if (!(vol && date && time && storage && temp && method)) {
                    allComplete = false;
                    console.log(`Row ${index + 1} is incomplete:`, { vol, date, time, storage, temp, method });
                }
            });

            console.log('Form data to submit:', formDataLog);

            if (!allComplete) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Incomplete Information',
                    text: 'Please complete all bag fields (time, date, volume, storage, temperature, and collection method).',
                    confirmButtonColor: '#6c757d'
                });
                return;
            }

            console.log('✓ All validation passed, opening lifestyle Q&A modal...');
            showQAModal(rows.length, form);
        };

        function showQAModal(rowsCount, form) {
            let qaEl = document.getElementById('hcQAModal');
            if (!qaEl) {
                qaEl = document.createElement('div');
                qaEl.className = 'modal fade';
                qaEl.id = 'hcQAModal';
                qaEl.setAttribute('tabindex', '-1');
                qaEl.setAttribute('aria-hidden', 'true');
                qaEl.innerHTML = `
                                <style>
                                    /* Scoped HS-like theme for Q&A modal */
                                    #hcQAModal .modal-header {
                                        background: linear-gradient(180deg, #ffd9e8 0%, #ff93c1 100%) !important;
                                        color: #222 !important;
                                    }
                                    #hcQAModal .qa-intro { color:#444; }
                                    #hcQAModal .question-item { background:#fff9fb; border:1px solid rgba(255,111,166,0.12); border-radius:8px; padding:12px; margin-bottom:12px; }
                                    #hcQAModal .question-label { font-weight:600; color:#212529; margin-bottom:8px; }
                                    #hcQAModal .translation { font-style: italic; color:#555; font-size:0.9rem; }
                                    #hcQAModal .radio-group { display:flex; gap:10px; }
                                    #hcQAModal .radio-option { position:relative; flex:1; }
                                    #hcQAModal .radio-option input[type="radio"]{ position:absolute; opacity:0; width:0; height:0; }
                                    #hcQAModal .radio-option label{ display:flex; align-items:center; justify-content:center; padding:10px 16px; background:#fff6fb; border:2px solid rgba(255,111,166,0.2); border-radius:8px; cursor:pointer; transition:all .2s; font-weight:500; min-height:44px; }
                                    #hcQAModal .radio-option label:hover{ border-color:#ff93c1; background:#fff0f6; }
                                    #hcQAModal .radio-option input[type="radio"]:checked + label{ background:#ff93c1; border-color:#ff93c1; color:#fff; box-shadow:0 2px 8px rgba(255,83,140,0.18); }
                                </style>
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Lifestyle Checklist</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p class="qa-intro mb-3">Please answer all questions below. You can proceed regardless of Yes/No answers.</p>
                                            ${[
                        ['q1', 'I am in good health', 'Maayo akong paminaw sa akong kalawasan'],
                        ['q2', 'I do not smoke', 'Dili ako gapangarilyo'],
                        ['q3', 'I am not taking medication or herbal supplements', 'Dili ako gatumar ug mga tambal o supplements'],
                        ['q4', 'I am not consuming alcohol', 'Dili ako gainom ug alkohol'],
                        ['q5', 'I have not had a fever', 'Wala ako naghilanat'],
                        ['q6', 'I have not had cough or colds', 'Wala ako nag-ubo o sip-on'],
                        ['q7', 'I have no breast infections', 'Wala ako impeksyon sa akong totoy'],
                        ['q8', 'I have followed all hygiene instructions', 'Gisunod nako ang tanan mga instruksyon tumong sa kalimpyo'],
                        ['q9', 'I have followed all labeling instructions', 'Gisunod nako ang tanan mga instruksyon tumong sa pagmarka'],
                        ['q10', 'I have followed all storage instructions', 'Gisunod nako ang tanan mga instruksyon tumong sa pagtipig sa gatas']
                    ].map(([code, en, cebu]) => `
                                                <div class="question-item">
                                                    <div class="question-label">${en}</div>
                                                    <div class="translation">(${cebu})</div>
                                                    <div class="radio-group mt-2">
                                                        <div class="radio-option yes">
                                                            <input type="radio" id="${code}_yes" name="hcqa[${code}]" value="yes">
                                                            <label for="${code}_yes">Yes</label>
                                                        </div>
                                                        <div class="radio-option no">
                                                            <input type="radio" id="${code}_no" name="hcqa[${code}]" value="no">
                                                            <label for="${code}_no">No</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            `).join('')}
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Back</button>
                                            <button type="button" class="btn btn-primary" id="hcQAContinueBtn" disabled>Continue</button>
                                        </div>
                                    </div>
                                </div>`;
                document.body.appendChild(qaEl);
            }

            const qaModal = new bootstrap.Modal(qaEl);
            qaModal.show();

            const continueBtn = qaEl.querySelector('#hcQAContinueBtn');
            // Fix selector for radios (must match names starting with hcqa[)
            const radios = Array.from(qaEl.querySelectorAll('input[type="radio"][name^="hcqa["]'));

            function updateContinueState() {
                const names = [...new Set(radios.map(r => r.name))];
                const allAnswered = names.every(n => qaEl.querySelector(`input[name="${n}"]:checked`));
                if (continueBtn) continueBtn.disabled = !allAnswered;
            }

            // Auto-scroll to next question when an answer is selected
            function autoScrollNextQuestion(changedRadio) {
                try {
                    const body = qaEl.querySelector('.modal-body');
                    if (!body) return;
                    const current = changedRadio.closest('.question-item');
                    if (!current) return;
                    // Find the next question-item (skip whitespace/text nodes)
                    let next = current.nextElementSibling;
                    while (next && !next.classList.contains('question-item')) {
                        next = next.nextElementSibling;
                    }
                    if (!next) return; // no next question

                    // Smooth scroll the modal body so the next question is visible and roughly centered
                    const offset = next.offsetTop - 12; // small top padding
                    body.scrollTo({ top: offset, behavior: 'smooth' });

                    // Focus the first interactive control in the next question for accessibility
                    const nextInput = next.querySelector('input, textarea, select, button');
                    if (nextInput) {
                        // small delay to allow scroll to start before focusing
                        setTimeout(() => { try { nextInput.focus(); } catch (e) { } }, 300);
                    }
                } catch (e) {
                    // Non-fatal: if scrolling fails, ignore
                    console.warn('Auto-scroll failed', e);
                }
            }

            radios.forEach(r => {
                r.addEventListener('change', function (ev) {
                    updateContinueState();
                    autoScrollNextQuestion(r);
                });
            });
            updateContinueState();

            if (continueBtn) {
                continueBtn.onclick = function () {
                    // After Q&A -> collect answers and show location review step
                    const names = [...new Set(radios.map(r => r.name))];
                    const anyNo = names.some(n => qaEl.querySelector(`input[name="${n}"]:checked`)?.value === 'no');

                    // Map q1..q10 to donation field names and normalize to YES/NO
                    const codeToField = {
                        'q1': 'good_health',
                        'q2': 'no_smoking',
                        'q3': 'no_medication',
                        'q4': 'no_alcohol',
                        'q5': 'no_fever',
                        'q6': 'no_cough_colds',
                        'q7': 'no_breast_infection',
                        'q8': 'followed_hygiene',
                        'q9': 'followed_labeling',
                        'q10': 'followed_storage'
                    };
                    const answers = {};
                    Object.keys(codeToField).forEach(code => {
                        const sel = qaEl.querySelector(`input[name="hcqa[${code}]"]:checked`);
                        if (sel) {
                            const fieldName = codeToField[code];
                            const value = String(sel.value).toUpperCase() === 'YES' ? 'YES' : 'NO';
                            answers[fieldName] = value;
                            console.log(`Q&A: ${fieldName} = ${value}`);
                        }
                    });
                    console.log('All lifestyle answers collected:', answers);

                    qaModal.hide();
                    showLocationReviewModal(rowsCount, form, anyNo, answers);
                };
            }
        }

        // Location Review Modal with 'View/Edit Map' step
        function showLocationReviewModal(rowsCount, form, anyNo, answers) {
            let locEl = document.getElementById('hcLocationModal');
            const latInput = document.getElementById('latitude');
            const lngInput = document.getElementById('longitude');
            const lat = parseFloat(latInput?.value || '');
            const lng = parseFloat(lngInput?.value || '');

            const hasLocation = !isNaN(lat) && !isNaN(lng);

            if (!locEl) {
                locEl = document.createElement('div');
                locEl.className = 'modal fade';
                locEl.id = 'hcLocationModal';
                locEl.setAttribute('tabindex', '-1');
                locEl.setAttribute('aria-hidden', 'true');
                locEl.innerHTML = `
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title">Home Collection</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div id="hc-location-banner"></div>
                                            <div class="form-check mt-3">
                                                <input class="form-check-input" type="checkbox" value="1" id="hcAllowLocation">
                                                <label class="form-check-label" for="hcAllowLocation">I allow this application to access my location.</label>
                                            </div>
                                        </div>
                                        <div class="modal-footer d-flex justify-content-between flex-wrap">
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-outline-primary" id="hcViewMapBtn"><i class="bi bi-geo-alt"></i> View / Edit Map</button>
                                                <button type="button" class="btn btn-outline-success" id="hcRecaptureBtn"><i class="bi bi-crosshair"></i> Re-capture Location</button>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Back</button>
                                                <button type="button" class="btn btn-success" id="hcLocationContinueBtn" disabled>Continue</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>`;
                document.body.appendChild(locEl);
            }

            const banner = locEl.querySelector('#hc-location-banner');
            if (banner) {
                if (hasLocation) {
                    banner.innerHTML = `
                                            <div class="alert alert-success d-flex align-items-start" role="alert">
                                                <span class="me-2">✅</span>
                                                <div>
                                                    <div class="fw-semibold">Location Captured Successfully!</div>
                                                    <small>Your location has been recorded for home collection service.</small>
                                                </div>
                                            </div>`;
                } else {
                    banner.innerHTML = `
                                            <div class="alert alert-warning d-flex align-items-start" role="alert">
                                                <span class="me-2">⚠️</span>
                                                <div>
                                                    <div class="fw-semibold">Location Not Captured</div>
                                                    <small>We could not read your current location. Click "View / Edit Map" to set it, or re-allow location access.</small>
                                                </div>
                                            </div>`;
                }
            }

            const allowCb = locEl.querySelector('#hcAllowLocation');
            let contBtn = locEl.querySelector('#hcLocationContinueBtn');
            const viewBtn = locEl.querySelector('#hcViewMapBtn');
            const recaptureBtn = locEl.querySelector('#hcRecaptureBtn');

            // Initialize checkbox state based on whether we have a location
            // Helper to perform a full capture flow (used by Recapture button and checkbox)
            function performCaptureFlow(triggerBtn) {
                if (!navigator.geolocation) {
                    Swal.fire({ icon: 'warning', title: 'Geolocation Unavailable', text: 'Your browser does not support location access. Use the map to set it, or allow location access in your browser settings.', confirmButtonColor: '#6c757d' });
                    return;
                }

                if (triggerBtn) {
                    triggerBtn.disabled = true;
                    // show spinner if button supports innerHTML toggle
                    try { triggerBtn.__oldInner = triggerBtn.innerHTML; triggerBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Capturing...'; } catch (e) { }
                }

                navigator.geolocation.getCurrentPosition((pos) => {
                    const latV = pos.coords.latitude;
                    const lngV = pos.coords.longitude;
                    if (latInput) latInput.value = latV.toFixed(6);
                    if (lngInput) lngInput.value = lngV.toFixed(6);
                    if (allowCb) allowCb.checked = true;
                    if (contBtn) contBtn.disabled = false;
                    if (banner) {
                        banner.innerHTML = `
                                            <div class="alert alert-success d-flex align-items-start" role="alert">
                                            <span class="me-2">✅</span>
                                            <div>
                                                <div class="fw-semibold">Location Captured Successfully!</div>
                                                <small>Your location has been recorded for home collection service.</small>
                                            </div>
                                            </div>`;
                    }
                    // If map modal open, update marker + view
                    if (window.hcMapCtx && window.hcMapCtx.marker && window.hcMapCtx.map) {
                        try {
                            window.hcMapCtx.marker.setLatLng([latV, lngV]);
                            window.hcMapCtx.map.setView([latV, lngV], 14);
                        } catch (e) { }
                    }
                }, (err) => {
                    console.warn('Geolocation error:', err);
                    Swal.fire({ icon: 'warning', title: 'Unable to Capture Location', text: 'Please allow location access or set your location on the map.', confirmButtonColor: '#6c757d' });
                }, { enableHighAccuracy: true, timeout: 12000, maximumAge: 0 });

                // restore UI after a small delay
                setTimeout(() => {
                    if (triggerBtn) {
                        try { triggerBtn.disabled = false; triggerBtn.innerHTML = triggerBtn.__oldInner || '<i class="bi bi-crosshair"></i> Re-capture Location'; } catch (e) { }
                    }
                }, 1400);
            }

            if (allowCb) {
                allowCb.checked = hasLocation;
                contBtn.disabled = !allowCb.checked;
                allowCb.onchange = function () {
                    contBtn.disabled = !allowCb.checked;
                    // If checked and we don't have coords, capture immediately (primary action)
                    if (allowCb.checked && (!latInput.value || !lngInput.value)) {
                        performCaptureFlow();
                    }
                };
            }

            if (viewBtn) {
                viewBtn.onclick = function () { showMapModal(); };
            }

            if (recaptureBtn) {
                recaptureBtn.onclick = function () {
                    performCaptureFlow(recaptureBtn);
                };
            }

            // CRITICAL: Always reassign the onclick handler with fresh answers from this invocation
            if (contBtn) {
                // Remove any old handler first
                contBtn.replaceWith(contBtn.cloneNode(true));
                contBtn = locEl.querySelector('#hcLocationContinueBtn');
                
                contBtn.onclick = async function () {
                    const confirm = await Swal.fire({
                        title: 'Confirm Home Collection Request?',
                        html: `Submit this request with <strong>${rowsCount}</strong> bag(s)?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#28a745',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="bi bi-check-circle me-1"></i> Yes, Submit',
                        cancelButtonText: 'Cancel'
                    });
                    if (!confirm.isConfirmed) return;

                    Swal.fire({
                        title: 'Submitting...',
                        text: 'Please wait while we process your donation request.',
                        icon: 'info',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => { Swal.showLoading(); }
                    });

                    // Inject lifestyle answers as hidden inputs before submission
                    try {
                        const fields = ['good_health','no_smoking','no_medication','no_alcohol','no_fever','no_cough_colds','no_breast_infection','followed_hygiene','followed_labeling','followed_storage'];
                        // Remove any previous injected inputs to avoid duplicates
                        fields.forEach(f => {
                            form.querySelectorAll(`input[type="hidden"][name="${f}"]`).forEach(n => n.remove());
                        });
                        if (answers && typeof answers === 'object') {
                            console.log('Injecting lifestyle answers:', answers);
                            fields.forEach(f => {
                                if (answers.hasOwnProperty(f) && answers[f]) {
                                    const inp = document.createElement('input');
                                    inp.type = 'hidden';
                                    inp.name = f;
                                    inp.value = answers[f]; // Already normalized to YES/NO
                                    form.appendChild(inp);
                                    console.log(`Injected ${f} = ${answers[f]}`);
                                }
                            });
                        } else {
                            console.error('Answers object is missing or invalid!', answers);
                        }
                    } catch (e) { console.error('Failed to inject lifestyle answers:', e); }

                    const formData = new FormData(form);
                    console.log('Submitting FormData with entries:');
                    for (let [key, value] of formData.entries()) {
                        if (key.includes('health') || key.includes('smoking') || key.includes('medication') || 
                            key.includes('alcohol') || key.includes('fever') || key.includes('cough') || 
                            key.includes('infection') || key.includes('hygiene') || key.includes('labeling') || 
                            key.includes('storage')) {
                            console.log(`  ${key}: ${value}`);
                        }
                    }
                    
                    fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Success!',
                                    html: data.message || 'Your home collection request has been submitted successfully!',
                                    icon: 'success',
                                    confirmButtonColor: '#28a745',
                                    confirmButtonText: 'View Pending Donations'
                                }).then(() => { window.location.href = '/user/pending-donation'; });
                            } else {
                                Swal.fire({ title: 'Error', text: data.message || 'Failed to submit donation request.', icon: 'error', confirmButtonColor: '#d33' });
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            Swal.fire({ title: 'Error', text: 'An unexpected error occurred. Please try again.', icon: 'error', confirmButtonColor: '#d33' });
                        });
                };
            }

            const locModal = new bootstrap.Modal(locEl);
            locModal.show();
        }

        // Leaflet loader and Map Modal
        function ensureLeafletLoaded(cb) {
            if (window.L && typeof window.L.map === 'function') { cb(); return; }
            const cssId = 'leaflet-css';
            const jsId = 'leaflet-js';
            if (!document.getElementById(cssId)) {
                const link = document.createElement('link');
                link.id = cssId; link.rel = 'stylesheet'; link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                document.head.appendChild(link);
            }
            const done = () => cb();
            if (!document.getElementById(jsId)) {
                const script = document.createElement('script');
                script.id = jsId; script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                script.onload = done; document.body.appendChild(script);
            } else { done(); }
        }

        function showMapModal() {
            let mapEl = document.getElementById('hcMapModal');
            if (!mapEl) {
                mapEl = document.createElement('div');
                mapEl.className = 'modal fade';
                mapEl.id = 'hcMapModal';
                mapEl.setAttribute('tabindex', '-1');
                mapEl.setAttribute('aria-hidden', 'true');
                mapEl.innerHTML = `
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">View / Edit Location</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div id="hcMap"></div>
                                            <div class="mt-2 text-muted small">Drag the pin to adjust your location.</div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Done</button>
                                        </div>
                                    </div>
                                </div>`;
                document.body.appendChild(mapEl);
            }

            const mapModal = new bootstrap.Modal(mapEl);
            mapModal.show();

            const initMap = () => {
                const latInput = document.getElementById('latitude');
                const lngInput = document.getElementById('longitude');
                let lat = parseFloat(latInput?.value || '');
                let lng = parseFloat(lngInput?.value || '');
                if (isNaN(lat) || isNaN(lng)) { lat = 12.8797; lng = 121.7740; }

                // Recreate map fresh each time to avoid stale instances
                const mapContainer = mapEl.querySelector('#hcMap');
                mapContainer.innerHTML = '';

                const map = L.map(mapContainer).setView([lat, lng], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap'
                }).addTo(map);

                const marker = L.marker([lat, lng], { draggable: true }).addTo(map);
                marker.on('dragend', function () {
                    const p = marker.getLatLng();
                    latInput.value = p.lat.toFixed(6);
                    lngInput.value = p.lng.toFixed(6);
                });

                // Give Leaflet a moment to compute sizes inside the shown modal
                setTimeout(() => { try { map.invalidateSize(); } catch (e) { } }, 50);
                window.addEventListener('resize', () => { try { map.invalidateSize(); } catch (e) { } }, { passive: true });

                // If browser provides current location, optionally recenter
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition((pos) => {
                        const p = [pos.coords.latitude, pos.coords.longitude];
                        map.setView(p, 14);
                        marker.setLatLng(p);
                        latInput.value = pos.coords.latitude.toFixed(6);
                        lngInput.value = pos.coords.longitude.toFixed(6);
                    }, () => { }, { maximumAge: 60000 });
                }

                // Expose context so other actions (recapture) can update live
                window.hcMapCtx = { map, marker };
            };

            // Initialize after modal is shown to ensure correct sizing
            setTimeout(() => ensureLeafletLoaded(initMap), 150);
        }

        function reset() {
            const tbody = document.getElementById(rowsTbodyId);
            const tableContainer = document.getElementById('hc-table-container');
            if (tbody) tbody.innerHTML = '';
            if (tableContainer) tableContainer.style.display = 'none';

            const bagsEl = document.getElementById('hc-total-bags');
            if (bagsEl) bagsEl.textContent = '0';
            const volEl = document.getElementById('hc-total-volume');
            if (volEl) volEl.textContent = '0';

            const bagsInput = document.getElementById('hc-bags-input');
            if (bagsInput) bagsInput.value = '';

            const firstExpr = document.getElementById('hc-first-expression');
            if (firstExpr) firstExpr.value = '';
            const lastExpr = document.getElementById('hc-last-expression');
            if (lastExpr) lastExpr.value = '';

            const submitBtn = document.getElementById('home-submit-btn');
            if (submitBtn) submitBtn.disabled = true;

            inited = false;
        }

        // Expose init and reset to page (modal) lifecycle
        window.initHomeCollectionModal = init;
        window.resetHomeCollectionModal = reset;

        // Also try to initialize immediately if elements exist
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function () {
                console.log('DOMContentLoaded - trying to attach listeners');
                const bagsInput = document.getElementById('hc-bags-input');
                if (bagsInput) {
                    console.log('Found bags input on DOMContentLoaded, attaching listener');
                    bagsInput.addEventListener('input', function () {
                        const numBags = parseInt(this.value) || 0;
                        console.log('Direct listener - Bags input changed to:', numBags);
                        if (numBags >= 1 && numBags <= 20) {
                            generateRows(numBags);
                        } else if (numBags > 20) {
                            this.value = 20;
                            generateRows(20);
                        } else {
                            generateRows(0);
                        }
                    });
                }
            });
        }
    })();
</script>