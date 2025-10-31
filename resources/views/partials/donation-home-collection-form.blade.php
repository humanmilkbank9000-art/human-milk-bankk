<style>
    /* Pink-themed table wrapper matching the provided style */
    .hc-wrapper { border-radius: 10px; overflow: visible; border: 1px solid #e6e6e9; }
    .hc-table { margin-bottom: 0; border-collapse: separate; border-spacing: 0; }
    .hc-table thead th { background: #ffd7e6; color: #222; font-weight: 700; padding: 12px 16px; border-bottom: 1px solid rgba(0,0,0,0.05); }
    .hc-table thead th:first-child { border-top-left-radius: 8px; }
    .hc-table thead th:last-child { border-top-right-radius: 8px; }
    .hc-table tbody tr { background: #fff; transition: background 0.2s ease; }
    .hc-table tbody tr:nth-child(even) { background: #f8f9fb; }
    .hc-table tbody tr.filled { background: #ffe6ef; }
    .hc-table tbody tr:hover { background: #f8f9fb; }
    .hc-table td, .hc-table th { vertical-align: middle; padding: 10px 12px; border-top: 1px solid #f1f1f2; }
    /* Compact controls inside table cells */
    .hc-table td .form-control-sm, .hc-table td .form-select-sm { min-width: 60px; padding: 6px 8px; box-sizing: border-box; }
    /* Make time and date columns narrower so the table fits better */
    .hc-table td:nth-child(1) .form-control-sm { width: 90px; max-width: 90px; }
    .hc-table td:nth-child(2) .form-control-sm { width: 120px; max-width: 120px; }
    .hc-table td:nth-child(3) .hc-bag-label { white-space: nowrap; }
    /* Make selects slightly narrower, but allow the collection-method dropdown to show longer labels */
    .hc-table td:nth-child(5) .form-select-sm { width: 110px; max-width: 140px; }
    /* Make collection method show full text and fit inside modal */
    /* Ensure the 7th column has enough minimum width and the select fills the cell */
    .hc-table td:nth-child(7) { min-width: 260px; }
    .hc-table td:nth-child(7) .form-select-sm {
        display: block;
        width: 100%;
        max-width: none;
        white-space: normal;
    }
    /* For very small screens, allow controls to shrink and the table to scroll horizontally */
    @media (max-width: 480px) {
        .hc-table td:nth-child(1) .form-control-sm { width: 72px; }
        .hc-table td:nth-child(2) .form-control-sm { width: 96px; }
        .hc-table td .form-control-sm, .hc-table td .form-select-sm { min-width: 56px; }
        .hc-table td:nth-child(7) { min-width: 180px; }
        .hc-table td:nth-child(7) .form-select-sm { width: 100%; max-width: none; }
    }
    .hc-badge { display: inline-block; padding: 0.35rem 0.75rem; background: #ffd1e1; color: #111; border-radius: 999px; min-width: 2.5rem; text-align: center; font-weight: 700; font-size: 0.95rem; }
    .hc-pill { background: #ffb3cf; border-radius: 999px; padding: 0.15rem 0.6rem; display: inline-flex; align-items: center; font-weight: 700; }
    .hc-actions .btn { padding: 0.25rem 0.5rem; }
    /* Add-row button (pink) and Next button (green) styles */
    #hc-add-row {
        background: linear-gradient(135deg,#ff7bb0,#ff5aa8);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 0.5rem 1rem;
        box-shadow: 0 6px 14px rgba(255,90,168,0.12);
        font-weight: 600;
    }
    #hc-add-row:hover { 
        transform: translateY(-2px); 
        background: linear-gradient(135deg,#ff5aa8,#ff2b86);
    }

    #home-collection-submit-btn {
        background: linear-gradient(135deg,#28a745,#20c046);
        color: #fff !important;
        border: none;
        border-radius: 24px;
        padding: 0.5rem 1.5rem;
        box-shadow: 0 6px 18px rgba(32,192,70,0.12);
        font-weight: 600;
    }
    #home-collection-submit-btn:hover {
        background: linear-gradient(135deg,#20c046,#1ea03d);
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
    .modal-body .hc-wrapper { padding: 0; }
    /* Specific modal sizing and controls for Home Collection modal */
    #homeCollectionModal .modal-dialog { max-width: 1100px; }
    @media (max-width: 768px) {
        #homeCollectionModal .modal-dialog { max-width: calc(100% - 32px); margin: 1rem; }
    }
    #homeCollectionModal .modal-body { max-height: calc(100vh - 180px); overflow-y: auto; padding: 1.25rem 1.5rem; }
    #homeCollectionModal .modal-header { border-bottom: 0; padding: 0.75rem 1.25rem; }
    #homeCollectionModal .btn-close { border: 1px solid #e9ecef; border-radius: 8px; background: #fff; width: 36px; height: 36px; color: #000 !important; opacity: 1; }
    /* Ensure table wrapper horizontally scrolls when needed */
    .table-responsive.hc-wrapper { overflow-x: auto; }
    /* Make Next button full width on very small screens so it's always reachable */
    @media (max-width: 480px) {
        .hc-footer .btn { width: 100%; }
    }
    /* Date input styling */
    .hc-date-row { margin-bottom: 1.5rem; }
    .hc-date-row label { font-weight: 600; color: #333; margin-bottom: 0.5rem; }
    .hc-date-row input[type="date"] { border: 1px solid #dee2e6; border-radius: 6px; padding: 0.5rem 0.75rem; }
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
</style>

<form action="{{ route('donation.store') }}" method="POST" id="homeCollectionForm">
    @csrf
    <input type="hidden" name="donation_method" value="home_collection">
    <input type="hidden" name="latitude" id="latitude">
    <input type="hidden" name="longitude" id="longitude">

    <!-- Date of expression fields -->
    <div class="row g-3 hc-date-row">
        <div class="col-md-6">
            <label class="form-label">Date of first expression:</label>
            <input type="date" class="form-control" name="first_expression_date" id="hc-first-expression" placeholder="dd/mm/yyyy" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Date of last expression:</label>
            <input type="date" class="form-control" name="last_expression_date" id="hc-last-expression" placeholder="dd/mm/yyyy" required>
        </div>
    </div>

    <!-- Number of Bags Input -->
    <div class="row g-3 mb-3">
        <div class="col-md-12">
            <label class="form-label">Number of Bags:</label>
            <input type="number" class="form-control" id="hc-bags-input" min="1" max="20" placeholder="Enter number of bags (1-20)" required>
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
        <button type="button" id="home-collection-submit-btn" onclick="submitHomeCollection()" disabled>Submit Home Collection</button>
    </div>
</form>

<script>
    (function(){
        const rowsTbodyId = 'hc-rows';
        let inited = false;

        function el(tag, attrs = {}, children = []){
            const e = document.createElement(tag);
            Object.entries(attrs).forEach(([k,v]) => {
                if (k === 'class') e.className = v; else if (k === 'text') e.textContent = v; else e.setAttribute(k,v);
            });
            children.forEach(c => e.appendChild(c));
            return e;
        }

        function generateRows(numBags){
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

        function addRow(bagNumber){
            const tbody = document.getElementById(rowsTbodyId);
            if (!tbody) {
                console.error('Tbody not found!');
                return;
            }

            const tr = el('tr');

            const time = el('input', { type:'time', name:'bag_time[]', class:'form-control form-control-sm', required: true });
            const date = el('input', { type:'date', name:'bag_date[]', class:'form-control form-control-sm', required: true });
            
            const bagsLabel = el('div', { class: 'hc-bag-label fw-bold', text: 'Bag ' + bagNumber });
            const bags = el('input', { type:'hidden', name:'bag_number[]', value: String(bagNumber) });
            
            const volume = el('input', { type:'number', name:'bag_volume[]', class:'form-control form-control-sm', min:'1', step:'0.01', placeholder:'ml', required: true });
            
            const storage = el('select', { name:'bag_storage[]', class:'form-select form-select-sm', required: true }, [
                el('option', { value:'', text:'Select' }),
                el('option', { value:'REF', text:'REF' }),
                el('option', { value:'FRZ', text:'FRZ' })
            ]);
            
            const temp = el('input', { type:'number', name:'bag_temp[]', class:'form-control form-control-sm', step:'0.1', placeholder:'°C', required: true });
            
            const method = el('select', { name:'bag_method[]', class:'form-select form-select-sm', required: true });
            const m0 = el('option', { value:'', text:'Select method' });
            const m1 = el('option', { value:'Manual hands expression', text:'Manual hands expression' });
            const m2 = el('option', { value:'Manual breast pump', text:'Manual breast pump' });
            const m3 = el('option', { value:'Electric breast pump', text:'Electric breast pump' });
            method.appendChild(m0); method.appendChild(m1); method.appendChild(m2); method.appendChild(m3);

            const inputs = [time, date, volume, storage, temp, method];
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

        function markRow(tr){
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

        function recalcTotals(){
            const rows = Array.from(document.querySelectorAll('#'+rowsTbodyId+' tr'));
            const totalBags = rows.length;
            let totalVol = 0;
            
            rows.forEach(r => {
                const v = parseFloat(r.querySelector('input[name="bag_volume[]"]')?.value || '0');
                totalVol += isNaN(v) ? 0 : v;
            });
            
            const bagsEl = document.getElementById('hc-total-bags');
            const volEl = document.getElementById('hc-total-volume');
            
            if (bagsEl) bagsEl.textContent = String(totalBags);
            if (volEl) volEl.textContent = totalVol.toFixed(2);
        }

        function enableSubmitCheck(){
            const submitBtn = document.getElementById('home-collection-submit-btn');
            if (!submitBtn) {
                console.log('Submit button not found');
                return;
            }

            const rows = Array.from(document.querySelectorAll('#'+rowsTbodyId+' tr'));
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
                console.log(`Row ${index + 1}:`, {vol, date, time, storage, temp, method, isComplete});
                
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

        function requestLocation(){
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
            }, { enableHighAccuracy:true, timeout:10000, maximumAge:0 });
        }

        function init(){
            if (inited) return; 
            inited = true;
            console.log('Home Collection Modal Initialized');
            
            // Add event listener for bags input
            const bagsInput = document.getElementById('hc-bags-input');
            console.log('Bags input element:', bagsInput);
            if (bagsInput) {
                console.log('Adding input listener to bags input');
                bagsInput.addEventListener('input', function() {
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

        // Simple submit function - uses AJAX and SweetAlert
        window.submitHomeCollection = function() {
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
            const rows = Array.from(document.querySelectorAll('#'+rowsTbodyId+' tr'));
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
                    console.log(`Row ${index + 1} is incomplete:`, {vol, date, time, storage, temp, method});
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

            console.log('✓ All validation passed, submitting form...');
            console.log('Submitting to:', form.action);
            
            // Show confirmation dialog
            Swal.fire({
                title: 'Confirm Home Collection Request?',
                html: `Are you sure you want to submit this home collection request with <strong>${rows.length}</strong> bag(s)?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-check-circle me-1"></i> Yes, Submit',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state
                    Swal.fire({
                        title: 'Submitting...',
                        text: 'Please wait while we process your donation request.',
                        icon: 'info',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Submit via AJAX
                    const formData = new FormData(form);
                    
                    fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Success!',
                                html: data.message || 'Your home collection request has been submitted successfully!',
                                icon: 'success',
                                confirmButtonColor: '#28a745',
                                confirmButtonText: 'View Pending Donations'
                            }).then(() => {
                                window.location.href = '/user/pending-donation';
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: data.message || 'Failed to submit donation request.',
                                icon: 'error',
                                confirmButtonColor: '#d33'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error',
                            text: 'An unexpected error occurred. Please try again.',
                            icon: 'error',
                            confirmButtonColor: '#d33'
                        });
                    });
                }
            });
        };

        function reset(){
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
            document.addEventListener('DOMContentLoaded', function() {
                console.log('DOMContentLoaded - trying to attach listeners');
                const bagsInput = document.getElementById('hc-bags-input');
                if (bagsInput) {
                    console.log('Found bags input on DOMContentLoaded, attaching listener');
                    bagsInput.addEventListener('input', function() {
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