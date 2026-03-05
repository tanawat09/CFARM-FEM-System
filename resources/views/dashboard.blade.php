@extends('layouts.app')

@section('page_title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4 text-center">
                <i class="bi bi-speedometer2 display-1 text-primary mb-3"></i>
                <h4 class="fw-bold">ยินดีต้อนรับเข้าสู่ระบบจัดการถังดับเพลิง</h4>
                <p class="text-muted mb-0">คุณได้เข้าสู่ระบบเรียบร้อยแล้ว: ยินดีต้อนรับกลับมา, <strong>{{ auth()->user()->name ?? 'ผู้ใช้งาน' }}</strong>!</p>
            </div>
        </div>
    </div>
</div>
@endsection
