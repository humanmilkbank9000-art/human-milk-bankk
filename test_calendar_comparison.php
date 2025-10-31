<!DOCTYPE html>
<html>
<head>
    <title>Calendar Comparison Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .container { display: flex; gap: 40px; }
        .calendar-section { flex: 1; }
        .calendar-section h2 { margin-bottom: 20px; }
        .info { background: #e3f2fd; padding: 15px; margin-bottom: 20px; border-radius: 8px; }
        .calendar-grid { display: grid; grid-template-columns: repeat(7, 50px); gap: 2px; }
        .calendar-day { 
            aspect-ratio: 1; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            border: 1px solid #ddd; 
            background: white;
        }
        .calendar-day.available { background: #d4edda; color: #155724; font-weight: bold; }
        .calendar-day.past { background: #f8f9fa; color: #adb5bd; }
        .calendar-day-header { font-weight: bold; padding: 10px; text-align: center; }
    </style>
</head>
<body>
    <h1>Calendar Comparison: Admin vs User Walk-in</h1>
    
    <div class="info">
        <strong>Test Date:</strong> <?= date('Y-m-d H:i:s') ?><br>
        <strong>Purpose:</strong> Verify both calendars highlight the same dates
    </div>

    <?php
    require __DIR__.'/vendor/autoload.php';
    $app = require_once __DIR__.'/bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

    $service = app(\App\Services\AvailabilityService::class);
    $availableDates = $service->listAvailableDates();
    ?>

    <div class="container">
        <div class="calendar-section">
            <h2>Available Dates from Service</h2>
            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <strong>Count:</strong> <?= count($availableDates) ?><br>
                <strong>Dates:</strong><br>
                <?php foreach ($availableDates as $date): ?>
                    <div style="padding: 4px 0;">• <?= $date ?></div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="calendar-section">
            <h2>November 2025 Calendar</h2>
            <div class="calendar-grid">
                <?php
                $monthNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                foreach ($monthNames as $name) {
                    echo "<div class='calendar-day-header'>$name</div>";
                }

                $firstDay = new DateTime('2025-11-01');
                $startDay = clone $firstDay;
                $startDay->modify('-' . $firstDay->format('w') . ' days');
                
                $today = new DateTime('today');

                for ($i = 0; $i < 42; $i++) {
                    $currentDay = clone $startDay;
                    $currentDay->modify("+$i days");
                    $dateStr = $currentDay->format('Y-m-d');
                    $isNovember = $currentDay->format('m') === '11';
                    $isPast = $currentDay < $today;
                    $isAvailable = in_array($dateStr, $availableDates);
                    
                    $class = 'calendar-day';
                    if ($isPast) $class .= ' past';
                    elseif ($isAvailable && $isNovember) $class .= ' available';
                    
                    echo "<div class='$class'>" . $currentDay->format('j') . "</div>";
                }
                ?>
            </div>
            <div style="margin-top: 20px; padding: 15px; background: #fff3cd; border-radius: 8px;">
                <strong>Legend:</strong><br>
                <div style="padding: 5px 0;">🟢 Green = Available dates (should match admin calendar)</div>
                <div style="padding: 5px 0;">⚪ Gray = Past dates (not selectable)</div>
                <div style="padding: 5px 0;">⬜ White = Future but not available</div>
            </div>
        </div>
    </div>

    <script>
        console.log('Available dates:', <?= json_encode($availableDates) ?>);
    </script>
</body>
</html>
