<!DOCTYPE html>
<html>
<head>
    <title>Live Calendar Debug Test</title>
    <style>
        body { font-family: monospace; padding: 20px; }
        .test-section { background: #f5f5f5; padding: 20px; margin: 20px 0; border-radius: 8px; }
        .error { color: red; font-weight: bold; }
        .success { color: green; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Live Calendar Date Calculation Test</h1>
    
    <div class="test-section">
        <h2>Test 1: Database Dates</h2>
        <pre><?php
        require __DIR__.'/vendor/autoload.php';
        $app = require_once __DIR__.'/bootstrap/app.php';
        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
        
        $service = app(\App\Services\AvailabilityService::class);
        $dates = $service->listAvailableDates();
        echo "Available dates from service:\n";
        foreach (array_slice($dates, 0, 5) as $d) {
            echo "  $d\n";
        }
        ?></pre>
    </div>

    <div class="test-section">
        <h2>Test 2: JavaScript Date Loop Simulation</h2>
        <p>Simulating the EXACT calendar generation logic:</p>
        <div id="result"></div>
    </div>

    <script>
        // Exact dates from PHP
        const availableDates = <?php echo json_encode($dates); ?>;
        
        console.log('Available dates:', availableDates);
        
        // Simulate November 2025 calendar
        const currentYear = 2025;
        const currentMonth = 10; // November (0-indexed)
        
        function toLocalYMD(date) {
            const y = date.getFullYear();
            const m = String(date.getMonth() + 1).padStart(2, '0');
            const d = String(date.getDate()).padStart(2, '0');
            return `${y}-${m}-${d}`;
        }
        
        const firstDay = new Date(currentYear, currentMonth, 1);
        console.log('First day of month:', firstDay.toDateString(), '- Day of week:', firstDay.getDay());
        
        const startDate = new Date(firstDay);
        startDate.setDate(startDate.getDate() - firstDay.getDay());
        console.log('Start date (Sunday before):', startDate.toDateString());
        
        let output = '<table border="1" cellpadding="5"><tr><th>i</th><th>Date Object</th><th>toLocalYMD</th><th>In Array?</th></tr>';
        
        // Test first 14 dates (2 weeks)
        for (let i = 0; i < 14; i++) {
            const date = new Date(startDate.getTime());
            date.setDate(date.getDate() + i);
            
            const dateString = toLocalYMD(date);
            const isAvailable = availableDates.includes(dateString);
            
            const color = isAvailable ? '#d4edda' : '#ffffff';
            const status = isAvailable ? '✓ YES' : '✗ NO';
            
            output += `<tr style="background: ${color}">
                <td>${i}</td>
                <td>${date.toDateString()}</td>
                <td>${dateString}</td>
                <td>${status}</td>
            </tr>`;
            
            if (dateString === '2025-11-01') {
                console.log('🎯 FOUND November 1! At index:', i, 'isAvailable:', isAvailable);
            }
        }
        
        output += '</table>';
        document.getElementById('result').innerHTML = output;
        
        // Additional check
        console.log('Does array include 2025-11-01?', availableDates.includes('2025-11-01'));
        console.log('Does array include 2025-11-02?', availableDates.includes('2025-11-02'));
    </script>
</body>
</html>
