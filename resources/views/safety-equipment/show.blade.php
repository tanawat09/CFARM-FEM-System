@extends('layouts.app')

@section('page_title', $safetyEquipment->type_name . ' : ' . $safetyEquipment->asset_code)

@section('content')
<div class="row">
    <div class="col-lg-5 mb-4">
        <!-- Equipment Details Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-{{ $safetyEquipment->type == 'emergency_light' ? 'warning' : 'info' }} text-{{ $safetyEquipment->type == 'emergency_light' ? 'dark' : 'white' }}">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-{{ $safetyEquipment->type == 'emergency_light' ? 'lightbulb' : 'droplet' }} me-2"></i>
                    {{ $safetyEquipment->type_name }}
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="text-muted" width="40%">รหัสทรัพย์สิน</td>
                        <td class="fw-bold">{{ $safetyEquipment->asset_code }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">S/N</td>
                        <td>{{ $safetyEquipment->serial_number ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">ยี่ห้อ / รุ่น</td>
                        <td>{{ $safetyEquipment->brand ?? '-' }} / {{ $safetyEquipment->model ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">พื้นที่</td>
                        <td><i class="bi bi-pin-map text-danger"></i> {{ $safetyEquipment->location->location_name ?? '-' }}</td>
                    </tr>
                    @if($safetyEquipment->house || $safetyEquipment->zone)
                    <tr>
                        <td class="text-muted">อาคาร / โซน</td>
                        <td>{{ $safetyEquipment->house ?? '-' }} / {{ $safetyEquipment->zone ?? '-' }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="text-muted">วันที่ติดตั้ง</td>
                        <td>{{ $safetyEquipment->install_date ? $safetyEquipment->install_date->format('d/m/Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">สถานะ</td>
                        <td>
                            @if($safetyEquipment->status == 'active')
                                <span class="badge bg-success rounded-pill px-3">{{ $safetyEquipment->status_name }}</span>
                            @elseif($safetyEquipment->status == 'under_repair')
                                <span class="badge bg-warning text-dark rounded-pill px-3">{{ $safetyEquipment->status_name }}</span>
                            @else
                                <span class="badge bg-secondary rounded-pill px-3">{{ $safetyEquipment->status_name }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">ตรวจครั้งถัดไป</td>
                        <td>
                            @if($safetyEquipment->next_inspection_date)
                                {{ $safetyEquipment->next_inspection_date->format('d/m/Y') }}
                            @else
                                <span class="text-muted">ยังไม่เคยตรวจ</span>
                            @endif
                        </td>
                    </tr>
                    @if($safetyEquipment->note)
                    <tr>
                        <td class="text-muted">หมายเหตุ</td>
                        <td>{{ $safetyEquipment->note }}</td>
                    </tr>
                    @endif
                </table>
            </div>
            <div class="card-footer bg-white d-flex gap-2">
                <a href="{{ route('equipment-inspections.create', ['equipment_id' => $safetyEquipment->id]) }}" class="btn btn-success flex-fill">
                    <i class="bi bi-clipboard-check me-1"></i> ตรวจเช็ค
                </a>
                @if(auth()->check() && auth()->user()->role == 'admin')
                <a href="{{ route('safety-equipment.edit', $safetyEquipment) }}" class="btn btn-outline-primary">
                    <i class="bi bi-pencil"></i>
                </a>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <!-- Inspection History -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i> ประวัติการตรวจเช็ค</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0 ps-3">เลขที่</th>
                                <th class="border-0">วันที่ตรวจ</th>
                                <th class="border-0">ผู้ตรวจ</th>
                                <th class="border-0">ผลตรวจ</th>
                                <th class="border-0 text-end pe-3">ดู</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($safetyEquipment->inspections->sortByDesc('inspected_at') as $insp)
                            <tr>
                                <td class="ps-3"><span class="fw-bold small">{{ $insp->inspection_no }}</span></td>
                                <td>{{ $insp->inspected_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $insp->inspectedBy->name ?? '-' }}</td>
                                <td>
                                    @if($insp->overall_result == 'pass')
                                        <span class="badge bg-success rounded-pill"><i class="bi bi-check-circle"></i> ผ่าน</span>
                                    @else
                                        <span class="badge bg-danger rounded-pill"><i class="bi bi-x-circle"></i> ไม่ผ่าน</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('equipment-inspections.show', $insp->id) }}" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="bi bi-clipboard display-6 text-secondary opacity-25 d-block mb-2"></i>
                                    ยังไม่มีประวัติการตรวจเช็ค
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
