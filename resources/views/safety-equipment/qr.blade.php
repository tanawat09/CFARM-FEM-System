@extends('layouts.app')

@section('page_title', 'พิมพ์ QR Code')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card border-0 shadow text-center py-5 rounded-4">
            <div class="card-body">
                <i class="bi bi-{{ $safetyEquipment->type == 'emergency_light' ? 'lightbulb text-warning' : 'droplet text-info' }} display-4 mb-3 d-block"></i>
                <h4 class="fw-bold text-dark mb-1">{{ $safetyEquipment->asset_code }}</h4>
                <p class="text-muted mb-1">{{ $safetyEquipment->type_name }}</p>
                <p class="text-muted mb-4">{{ $safetyEquipment->location->location_name ?? 'ไม่มีพื้นที่ติดตั้ง' }}</p>

                <div class="qr-wrapper bg-white p-4 border rounded-4 d-inline-block shadow-sm mx-auto mb-4" style="width: fit-content;">
                    {!! $qrCode !!}
                </div>
                
                <h6 class="text-muted mt-2">สแกนเพื่อเข้าถึงการตรวจเช็ค</h6>
                <div class="mt-5 d-print-none">
                    <button onclick="window.print()" class="btn btn-primary btn-lg px-4 rounded-pill">
                        <i class="bi bi-printer me-2"></i> พิมพ์ QR Code นี้
                    </button>
                    <a href="{{ route('safety-equipment.index', ['type' => $safetyEquipment->type]) }}" class="btn btn-light btn-lg px-4 ms-2 rounded-pill">
                        กลับรายการ
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        body { background: white !important; }
        .sidebar, .topbar { display: none !important; }
        .main-content { padding: 0 !important; width: 100% !important; margin: 0 !important; }
        .card { box-shadow: none !important; border: 1px solid #000 !important; max-width: 400px; margin: 0 auto; }
    }
</style>
@endsection
