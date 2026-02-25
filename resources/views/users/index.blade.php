@extends('layouts.app')

@section('page_title', 'ข้อมูลผู้ใช้งาน (Users)')

@section('content')
<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
    <div class="card-header bg-white border-bottom border-light p-4 pt-3 pb-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 text-dark">ผู้ดูแลและเจ้าหน้าที่ในระบบ</h5>
        <div>
            <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm px-4 rounded-pill shadow-sm fw-medium"><i class="bi bi-person-plus-fill me-1"></i> เพิ่มผู้ใช้ใหม่</a>
        </div>
    </div>
    
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th class="ps-4 py-3 fw-medium small text-uppercase" width="25%">ชื่อ - นามสกุล</th>
                        <th class="py-3 fw-medium small text-uppercase" width="20%">อีเมล / เบอร์ติดต่อ</th>
                        <th class="py-3 fw-medium small text-uppercase" width="20%">แผนก / รหัสพนักงาน</th>
                        <th class="py-3 fw-medium small text-uppercase" width="15%">สิทธิ์การใช้งาน (Role)</th>
                        <th class="py-3 fw-medium small text-uppercase text-center" width="10%">สถานะ</th>
                        <th class="pe-4 py-3 fw-medium small text-uppercase text-end" width="10%">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr class="border-bottom border-light">
                        <td class="ps-4 py-3">
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=EBF4FA&color=1e3a5f&bold=true" class="rounded-circle me-3" width="40" height="40" alt="Avatar">
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark">{{ $user->name }}</h6>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="text-dark fw-medium small"><i class="bi bi-envelope text-muted"></i> {{ $user->email }}</div>
                            <div class="text-muted small"><i class="bi bi-telephone"></i> {{ $user->phone ?? '-' }}</div>
                        </td>
                        <td>
                            <span class="d-block small text-dark fw-medium">{{ $user->department ?? '-' }}</span>
                            <span class="text-muted small">ID: {{ $user->employee_id ?? '-' }}</span>
                        </td>
                        <td>
                            @if($user->role == 'admin')
                                <span class="badge bg-primary text-white border border-primary px-3 py-1 rounded-pill bg-opacity-75"><i class="bi bi-shield-lock-fill me-1"></i> ผู้ดูแลระบบ</span>
                            @else
                                <span class="badge bg-info text-dark border border-info px-3 py-1 rounded-pill bg-opacity-25"><i class="bi bi-person-workspace me-1"></i> จป. ตรวจสอบ</span>
                            @endif
                        </td>
                        <td class="text-center">
                             @if($user->status == 'active')
                                 <span class="text-success"><i class="bi bi-circle-fill small me-1"></i> ปกติ</span>
                             @else
                                 <span class="text-danger"><i class="bi bi-circle-fill small me-1"></i> ระงับ</span>
                             @endif
                        </td>
                        <td class="pe-4 text-end">
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm rounded-circle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width: 32px; height: 32px; line-height: 1;">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                                    <li><a class="dropdown-item py-2" href="{{ route('users.edit', $user->id) }}"><i class="bi bi-pencil me-2 text-primary"></i> แก้ไขข้อมูล / รหัสผ่าน</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบผู้ใช้งานท่านนี้อย่างถาวร?');" class="m-0 p-0">
                                            @csrf
                                            @method('DELETE')
                                            <button class="dropdown-item py-2 text-danger" type="submit"><i class="bi bi-trash me-2"></i> ลบผู้ใช้งาน</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-people display-4 d-block mb-3 opacity-25"></i>
                            <h5 class="fw-normal">ไม่พบข้อมูลผู้ใช้งาน</h5>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if(isset($users) && $users->hasPages())
    <div class="card-footer bg-white border-top border-light p-3 d-flex justify-content-center">
        {{ $users->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
