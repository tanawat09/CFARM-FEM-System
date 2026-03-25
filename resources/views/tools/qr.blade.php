<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>QR Code - {{ $tool->tool_code }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; }
        @media print {
            .no-print { display:none; }
            body { margin:0; padding:0; }
        }
        .qr-card {
            border: 2px solid #333;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            max-width: 300px;
            margin: 30px auto;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="text-center no-print mb-3">
            <button class="btn btn-primary" onclick="window.print()"><i class="bi bi-printer"></i> พิมพ์</button>
            <button class="btn btn-secondary" onclick="window.close()">ปิด</button>
        </div>
        <div class="qr-card">
            <div class="mb-2 fw-bold" style="font-size:1.1rem;">{{ $tool->tool_name }}</div>
            <div>{!! $qrCode !!}</div>
            <div class="mt-2 fw-bold" style="font-size:1.2rem;">{{ $tool->tool_code }}</div>
            <div class="text-muted small">{{ $tool->type_name }}</div>
            <div class="text-muted small">{{ $tool->location->location_name ?? '' }}</div>
        </div>
    </div>
</body>
</html>
