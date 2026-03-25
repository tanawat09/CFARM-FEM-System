<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>พิมพ์ QR Code เครื่องมือช่าง</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; }
        @media print {
            .no-print { display:none; }
            body { margin:0; padding:0; }
        }
        .qr-item {
            border: 2px solid #333;
            border-radius: 10px;
            padding: 12px;
            text-align: center;
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="text-center no-print mb-4">
            <button class="btn btn-primary" onclick="window.print()">พิมพ์ทั้งหมด ({{ $tools->count() }})</button>
            <button class="btn btn-secondary" onclick="window.close()">ปิด</button>
        </div>
        <div class="row g-3">
            @foreach($tools as $t)
            <div class="col-6 col-md-4 col-lg-3">
                <div class="qr-item">
                    <div class="fw-bold small mb-1">{{ $t->tool_name }}</div>
                    {!! $qrCodes[$t->id] !!}
                    <div class="fw-bold mt-1">{{ $t->tool_code }}</div>
                    <small class="text-muted">{{ $t->location->location_name ?? '' }}</small>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</body>
</html>
