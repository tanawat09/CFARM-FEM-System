@extends('layouts.app')

@section('page_title', 'Audit Logs')

@section('content')
<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
    <div class="card-header bg-white border-bottom border-light p-4 pt-3 pb-3">
        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-journal-text me-2"></i> Audit Logs</h5>
    </div>

    {{-- Filters --}}
    <div class="card-body border-bottom bg-light p-3">
        <form method="GET" action="{{ route('audit-logs.index') }}" class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label small fw-medium mb-1">User</label>
                <select name="user_id" class="form-select form-select-sm">
                    <option value="">-- ทั้งหมด --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-medium mb-1">Action</label>
                <select name="action" class="form-select form-select-sm">
                    <option value="">-- ทั้งหมด --</option>
                    <option value="created" {{ request('action') == 'created' ? 'selected' : '' }}>Created</option>
                    <option value="updated" {{ request('action') == 'updated' ? 'selected' : '' }}>Updated</option>
                    <option value="deleted" {{ request('action') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-medium mb-1">Model</label>
                <select name="model" class="form-select form-select-sm">
                    <option value="">-- ทั้งหมด --</option>
                    @foreach($modelTypes as $model)
                        <option value="{{ $model }}" {{ request('model') == $model ? 'selected' : '' }}>{{ $model }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-medium mb-1">From</label>
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-medium mb-1">To</label>
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-search me-1"></i> Filter</button>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th class="ps-4 py-3 fw-medium small text-uppercase" width="15%">Date/Time</th>
                        <th class="py-3 fw-medium small text-uppercase" width="12%">User</th>
                        <th class="py-3 fw-medium small text-uppercase" width="8%">Action</th>
                        <th class="py-3 fw-medium small text-uppercase" width="12%">Model</th>
                        <th class="py-3 fw-medium small text-uppercase" width="43%">Changes</th>
                        <th class="pe-4 py-3 fw-medium small text-uppercase" width="10%">IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr class="border-bottom border-light">
                        <td class="ps-4 py-2">
                            <span class="small text-dark">{{ $log->created_at->format('d/m/Y') }}</span><br>
                            <span class="small text-muted">{{ $log->created_at->format('H:i:s') }}</span>
                        </td>
                        <td>
                            <span class="small fw-medium text-dark">{{ $log->user->name ?? 'System' }}</span>
                        </td>
                        <td>
                            @if($log->action === 'created')
                                <span class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1 rounded-pill"><i class="bi bi-plus-circle me-1"></i>Created</span>
                            @elseif($log->action === 'updated')
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning px-2 py-1 rounded-pill"><i class="bi bi-pencil me-1"></i>Updated</span>
                            @elseif($log->action === 'deleted')
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-2 py-1 rounded-pill"><i class="bi bi-trash me-1"></i>Deleted</span>
                            @endif
                        </td>
                        <td>
                            <span class="small fw-medium text-dark">{{ class_basename($log->auditable_type) }}</span><br>
                            <span class="small text-muted">ID: {{ $log->auditable_id }}</span>
                        </td>
                        <td>
                            @if($log->action === 'created' && $log->new_values)
                                <div class="small text-muted" style="max-height: 80px; overflow-y: auto;">
                                    @foreach(array_slice($log->new_values, 0, 5) as $key => $val)
                                        <span class="text-success">{{ $key }}</span>: {{ Str::limit(is_array($val) ? json_encode($val) : $val, 50) }}<br>
                                    @endforeach
                                    @if(count($log->new_values) > 5)
                                        <span class="text-muted">... +{{ count($log->new_values) - 5 }} fields</span>
                                    @endif
                                </div>
                            @elseif($log->action === 'updated' && $log->old_values && $log->new_values)
                                <div class="small" style="max-height: 80px; overflow-y: auto;">
                                    @foreach($log->new_values as $key => $newVal)
                                        <span class="fw-medium">{{ $key }}</span>:
                                        <span class="text-danger">{{ Str::limit(is_array($log->old_values[$key] ?? '') ? json_encode($log->old_values[$key] ?? '') : ($log->old_values[$key] ?? ''), 30) }}</span>
                                        <i class="bi bi-arrow-right text-muted small"></i>
                                        <span class="text-success">{{ Str::limit(is_array($newVal) ? json_encode($newVal) : $newVal, 30) }}</span><br>
                                    @endforeach
                                </div>
                            @elseif($log->action === 'deleted' && $log->old_values)
                                <div class="small text-muted" style="max-height: 80px; overflow-y: auto;">
                                    @foreach(array_slice($log->old_values, 0, 3) as $key => $val)
                                        <span class="text-danger">{{ $key }}</span>: {{ Str::limit(is_array($val) ? json_encode($val) : $val, 50) }}<br>
                                    @endforeach
                                    @if(count($log->old_values) > 3)
                                        <span class="text-muted">... +{{ count($log->old_values) - 3 }} fields</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td class="pe-4">
                            <span class="small text-muted">{{ $log->ip_address ?? '-' }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-journal-text display-4 d-block mb-3 opacity-25"></i>
                            <h5 class="fw-normal">ยังไม่มี Audit Log</h5>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(isset($logs) && $logs->hasPages())
    <div class="card-footer bg-white border-top border-light p-3 d-flex justify-content-center">
        {{ $logs->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
