@extends('layouts.app')

@section('page_title', 'พิมพ์ QR Code (ชุด)')

@section('content')
<div class="d-print-none mb-4">
    <button onclick="window.print()" class="btn btn-primary btn-lg rounded-pill px-4">
        <i class="bi bi-printer me-2"></i> พิมพ์ทั้งหมด ({{ count($equipments) }} รายการ)
    </button>
    <a href="{{ url()->previous() }}" class="btn btn-light btn-lg rounded-pill px-4 ms-2">กลับ</a>
</div>

<div class="row g-3">
    @foreach($equipments as $eq)
    <div class="col-md-4 col-lg-3">
        <div class="card border shadow-sm text-center p-3 rounded-4 qr-card">
            <i class="bi bi-{{ $eq->type == 'emergency_light' ? 'lightbulb text-warning' : 'droplet text-info' }} fs-3 mb-2"></i>
            <h6 class="fw-bold mb-1">{{ $eq->asset_code }}</h6>
            <small class="text-muted d-block mb-2">{{ $eq->location->location_name ?? '-' }}</small>
            <div class="mx-auto mb-2">
                {!! $qrCodes[$eq->id] !!}
            </div>
            <small class="text-muted">{{ $eq->type_name }}</small>
        </div>
    </div>
    @endforeach
</div>

<style>
    @media print {
        body { background: white !important; }
        .sidebar, .topbar, .d-print-none { display: none !important; }
        .main-content { padding: 0 !important; width: 100% !important; margin: 0 !important; }
        .qr-card { break-inside: avoid; border: 1px solid #000 !important; box-shadow: none !important; }
    }
</style>
@endsection
