@extends('layouts.app')

@section('page_title', 'สแกน QR Code')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-qr-code-scan text-primary me-2"></i> สแกน QR Code ถังดับเพลิง</h5>
                <a href="{{ route('dashboard') }}" class="btn btn-light btn-sm rounded-pill px-3"><i class="bi bi-x-lg"></i> ปิด</a>
            </div>
            
            <div class="card-body p-4 text-center">
                <p class="text-muted mb-4 pb-2">หันกล้องมือถือของท่านไปยัง QR Code ที่ติดอยู่บนถังดับเพลิง เพื่อดูข้อมูลหรือทำการตรวจเช็ค</p>
                
                <div id="reader" class="mx-auto rounded-4 overflow-hidden shadow-sm border" style="width: 100%; max-width: 400px; min-height: 300px;"></div>
                
                <div class="mt-4 pt-2">
                    <button id="restart-btn" class="btn btn-outline-primary rounded-pill px-4 d-none">
                        <i class="bi bi-arrow-clockwise me-2"></i>เริ่มสแกนใหม่
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Load html5-qrcode -->
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const html5QrCode = new Html5Qrcode("reader");
        const restartBtn = document.getElementById('restart-btn');
        
        const config = { fps: 10, qrbox: { width: 250, height: 250 } };

        const onScanSuccess = (decodedText, decodedResult) => {
            // Stop scanning
            html5QrCode.stop().then((ignore) => {
                // Determine if it's a URL or an ID string
                if (decodedText.startsWith('http://') || decodedText.startsWith('https://')) {
                    // It's a full URL, redirect directly
                    window.location.href = decodedText;
                } else {
                    // Try to guess if it's just an ID and redirect manually
                    // For the scope of this project, we assume QR points to the show page URL
                    console.log('Scanned text is not a URL:', decodedText);
                    alert("สแกนพบข้อมูล: " + decodedText + "\n(กรุณาตั้งค่า QR ให้เป็น URL ของระบบ)");
                    restartBtn.classList.remove('d-none');
                }
            }).catch((err) => {
                console.error("Failed to stop scanning.", err);
            });
        };

        const onScanFailure = (error) => {
            // handle scan failure, usually better to ignore and keep scanning
            // console.warn(`Code scan error = ${error}`);
        };

        // Start scanning with front/back camera (prefer back)
        html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess, onScanFailure)
        .catch(err => {
            console.error("Error starting scanner", err);
            alert("ไม่สามารถเปิดกล้องได้ กรุณาตรวจสอบสิทธิ์การเข้าถึงกล้องบนเบราว์เซอร์ของท่าน");
        });

        restartBtn.addEventListener('click', function() {
            restartBtn.classList.add('d-none');
            html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess, onScanFailure);
        });
    });
</script>
@endsection
