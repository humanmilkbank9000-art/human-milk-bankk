<!DOCTYPE html>
<html>
<head>
    <title>EXACT User Calendar Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .grid { display: grid; grid-template-columns: repeat(7, 60px); gap: 2px; margin: 20px 0; }
        .day { 
            aspect-ratio: 1; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            border: 1px solid #ddd;
            background: white;
        }
        .day.green { background: #d4edda; color: #155724; font-weight: bold; }
        .day.gray { background: #f8f9fa; color: #adb5bd; }
        .day.header { background: #e9ecef; font-weight: bold; }
        .console-output { background: #1e1e1e; color: #d4d4d4; padding: 15px; font-family: monospace; font-size: 12px; white-space: pre; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>User Walk-in Calendar - EXACT Simulation</h1>
    
    <?php
    require __DIR__.'/vendor/autoload.php';
    $app = require_once __DIR__.'/bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    $service = app(\App\Services\AvailabilityService::class);
    $availableDates = $service->listAvailableDates();
    ?>
    
    <h2>1. Backend Data</h2>
    <p><strong>Available dates from service:</strong></p>
    <div style="background: #f5f5f5; padding: 15px; font-family: monospace;">
        <?php echo implode(', ', array_slice($availableDates, 0, 20)); ?>
    </div>
    
    <h2>2. JavaScript Receives</h2>
    <div id="jsOutput" class="console-output"></div>
    
    <h2>3. November 2025 Calendar (EXACT USER CODE)</h2>
    <div class="grid" id="calendar"></div>
    
    <h2>4. Detailed Date Checks</h2>
    <div id="dateChecks" class="console-output"></div>
    
    <script>
        // EXACT SAME CODE AS USER CALENDAR
        const availableDates = <?php echo json_encode($availableDates); ?>;
        let currentMonth = 10; // November
        let currentYear = 2025;
        
        // Log what JS receives
        let jsLog = '';
        jsLog += 'availableDates type: ' + typeof availableDates + '\n';
        jsLog += 'availableDates length: ' + availableDates.length + '\n';
        jsLog += 'First 5 dates:\n';
        availableDates.slice(0, 5).forEach((d, i) => {
            jsLog += `  [${i}]: "${d}" (type: ${typeof d}, length: ${d.length})\n`;
        });
        document.getElementById('jsOutput').textContent = jsLog;
        
        // EXACT toLocalYMD function from user calendar
        function toLocalYMD(date) {
            const y = date.getFullYear();
            const m = String(date.getMonth() + 1).padStart(2, '0');
            const d = String(date.getDate()).padStart(2, '0');
            return `${y}-${m}-${d}`;
        }
        
        // EXACT calendar generation from user calendar
        const firstDay = new Date(currentYear, currentMonth, 1);
        const now = new Date();
        const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
        
        const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        let calendarHTML = '';
        
        dayNames.forEach(day => {
            calendarHTML += `<div class="day header">${day}</div>`;
        });
        
        const startDate = new Date(firstDay);
        startDate.setDate(startDate.getDate() - firstDay.getDay());
        
        let detailedLog = '';
        
        for (let i = 0; i < 42; i++) {
            const date = new Date(startDate.getTime());
            date.setDate(date.getDate() + i);
            const day = date.getDate();
            const dateString = toLocalYMD(date);
            
            const dayStart = new Date(date.getFullYear(), date.getMonth(), date.getDate()).getTime();
            const todayStart = new Date(now.getFullYear(), now.getMonth(), now.getDate()).getTime();
            const isPast = dayStart < todayStart;
            
            const isCurrentMonth = date.getMonth() === currentMonth;
            const isAvailable = availableDates.includes(dateString);
            
            let dayClass = 'day';
            if (!isCurrentMonth) {
                dayClass += ' other-month';
            } else if (isPast) {
                dayClass += ' gray';
            } else if (isAvailable) {
                dayClass += ' green';
            }
            
            calendarHTML += `<div class="${dayClass}">${day}</div>`;
            
            // Log November 1-5
            if (dateString >= '2025-11-01' && dateString <= '2025-11-05') {
                detailedLog += `${dateString}:\n`;
                detailedLog += `  Date obj: ${date.toDateString()}\n`;
                detailedLog += `  toLocalYMD: ${dateString}\n`;
                detailedLog += `  isAvailable: ${isAvailable}\n`;
                detailedLog += `  includes check: ${availableDates.includes(dateString)}\n`;
                detailedLog += `  Result: ${isAvailable ? '✅ GREEN' : '⚪ WHITE'}\n`;
                detailedLog += '\n';
            }
        }
        
        document.getElementById('calendar').innerHTML = calendarHTML;
        document.getElementById('dateChecks').textContent = detailedLog;
    </script>
    
    <h2>5. Expected vs Actual</h2>
    <table border="1" cellpadding="10" style="border-collapse: collapse;">
        <tr>
            <th>Date</th>
            <th>Expected (Admin)</th>
            <th>Actual (User)</th>
            <th>Status</th>
        </tr>
        <tr>
            <td>Nov 1</td>
            <td>🟢 GREEN</td>
            <td id="nov1">...</td>
            <td id="nov1status">...</td>
        </tr>
        <tr>
            <td>Nov 2</td>
            <td>🟢 GREEN</td>
            <td id="nov2">...</td>
            <td id="nov2status">...</td>
        </tr>
        <tr>
            <td>Nov 14</td>
            <td>🟢 GREEN</td>
            <td id="nov14">...</td>
            <td id="nov14status">...</td>
        </tr>
        <tr>
            <td>Nov 15</td>
            <td>⚪ WHITE</td>
            <td id="nov15">...</td>
            <td id="nov15status">...</td>
        </tr>
    </table>
    
    <script>
        function checkDate(date, cellId, statusId) {
            const isIn = availableDates.includes(date);
            document.getElementById(cellId).textContent = isIn ? '🟢 GREEN' : '⚪ WHITE';
            document.getElementById(statusId).textContent = isIn ? '✅ CORRECT' : '❌ WRONG';
            document.getElementById(statusId).style.color = isIn ? 'green' : 'red';
            document.getElementById(statusId).style.fontWeight = 'bold';
        }
        
        checkDate('2025-11-01', 'nov1', 'nov1status');
        checkDate('2025-11-02', 'nov2', 'nov2status');
        checkDate('2025-11-14', 'nov14', 'nov14status');
        checkDate('2025-11-15', 'nov15', 'nov15status');
    </script>
</body>
</html>
