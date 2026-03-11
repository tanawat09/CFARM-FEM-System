<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>พิมพ์ QR Code ถังดับเพลิง (จำนวน {{ count($extinguishers) }} ถัง)</title>
    <!-- Google Fonts: Sarabun -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .controls {
            background: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn {
            background-color: #0d6efd;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-family: 'Sarabun', sans-serif;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .btn-outline {
            background-color: transparent;
            color: #6c757d;
            border: 1px solid #6c757d;
        }
        .btn:hover { opacity: 0.9; }
        
        /* Grid Layout for QR Codes */
        .qr-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .qr-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            text-align: center;
            border: 1px solid #dee2e6;
            page-break-inside: avoid; /* Prevent breaking card across pages */
        }
        
        .qr-wrapper {
            margin: 10px 0;
            background: #fff;
            padding: 10px;
            display: inline-block;
        }
        
        .qr-wrapper svg {
            display: block;
            width: 150px;
            height: 150px;
        }

        .title {
            font-size: 18px;
            font-weight: 700;
            color: #212529;
            margin: 0 0 5px 0;
        }

        .location {
            font-size: 14px;
            color: #6c757d;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .company-tag {
            font-size: 12px;
            font-weight: bold;
            color: #dc3545;
            margin-bottom: 5px;
        }

        @media print {
            body { 
                background-color: white; 
                padding: 0; 
                margin: 0;
            }
            .controls { display: none; }
            .qr-grid {
                /* For A4 Paper printing normally 3 columns fit well */
                grid-template-columns: repeat(3, 1fr);
                gap: 15px;
            }
            .qr-card {
                box-shadow: none;
                border: 1px dashed #ccc;
            }
        }
    </style>
</head>
<body>

    <div class="controls">
        <div>
            <h2 style="margin:0; font-size: 20px;">พิมพ์เครื่องหมาย QR Code ({{ count($extinguishers) }} รายการ)</h2>
        </div>
        <div>
            <button type="button" class="btn btn-outline" onclick="window.close()" style="margin-right: 10px;">ปิดหน้าต่าง</button>
            <button type="button" class="btn" onclick="window.print()">🖨️ พิมพ์เอกสาร</button>
        </div>
    </div>

    <div class="qr-grid">
        @foreach($extinguishers as $ext)
            <div class="qr-card">
                <div class="company-tag" style="display: flex; flex-direction: column; align-items: center; justify-content: center;">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" style="max-height: 40px; margin-bottom: 5px;" onerror="this.style.display='none'">
                </div>
                <h3 class="title">{{ $ext->serial_number }}</h3>
                <div class="qr-wrapper">
                    {!! $qrCodes[$ext->id] !!}
                </div>
                <p class="location">จุดติดตั้ง: {{ $ext->location->location_name ?? 'ไม่ระบุ' }}</p>
                @if($ext->house || $ext->zone)
                    <p class="location" style="font-size: 12px; margin-top:2px;">
                        {{ $ext->house ? '['.$ext->house.'] ' : '' }} {{ $ext->zone ? ' โซน: '.$ext->zone : '' }}
                    </p>
                @endif
            </div>
        @endforeach
    </div>

    <script>
        // Optional: Auto open print dialog when page loads
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
