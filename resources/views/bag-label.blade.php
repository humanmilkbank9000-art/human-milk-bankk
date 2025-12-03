<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bag #{{ $bagNumber }} - Label</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        
        .label-container {
            background: white;
            border: 3px solid #000;
            border-radius: 12px;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .label-header {
            background: linear-gradient(135deg, #ff93c1, #ff7fb3);
            color: white;
            padding: 25px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        
        .label-header h1 {
            font-size: 36px;
            font-weight: bold;
            margin: 0;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        
        .label-header p {
            margin: 8px 0 0 0;
            font-size: 16px;
            opacity: 0.95;
        }
        
        .label-body {
            padding: 30px;
        }
        
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .info-table tr {
            border-bottom: 1px solid #ddd;
        }
        
        .info-table tr:last-child {
            border-bottom: none;
        }
        
        .info-table td {
            padding: 12px 8px;
        }
        
        .info-table td:first-child {
            font-weight: bold;
            color: #333;
            width: 35%;
        }
        
        .info-table td:last-child {
            color: #555;
        }
        
        .volume-highlight {
            font-size: 22px;
            color: #0d6efd;
            font-weight: bold;
        }
        
        .label-footer {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 0 0 8px 8px;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
        
        .label-footer strong {
            color: #333;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .label-container {
                box-shadow: none;
                max-width: 100%;
            }
        }
        
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
            
            .label-header h1 {
                font-size: 28px;
            }
            
            .label-body {
                padding: 20px;
            }
            
            .info-table td {
                padding: 10px 5px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="label-container">
        <div class="label-header">
            <h1>BAG #{{ $bagNumber }}</h1>
            <p>Human Milk Bank</p>
        </div>
        
        <div class="label-body">
            <table class="info-table">
                <tr>
                    <td>Donor:</td>
                    <td>{{ $donorName }}</td>
                </tr>
                <tr>
                    <td>Date:</td>
                    <td>{{ $date ? \Carbon\Carbon::parse($date)->format('M d, Y') : 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Time:</td>
                    <td>{{ $time }}</td>
                </tr>
                <tr>
                    <td>Volume:</td>
                    <td class="volume-highlight">{{ $volume }} ml</td>
                </tr>
                <tr>
                    <td>Storage:</td>
                    <td>{{ $storage }}</td>
                </tr>
                <tr>
                    <td>Temp:</td>
                    <td>{{ $temp }}°C</td>
                </tr>
                <tr>
                    <td>Method:</td>
                    <td>{{ $method }}</td>
                </tr>
            </table>
        </div>
        
        <div class="label-footer">
            <strong>Donation ID:</strong> {{ $donationId }}<br>
            <strong>Generated:</strong> {{ now()->format('M d, Y g:i A') }}
        </div>
    </div>
</body>
</html>
