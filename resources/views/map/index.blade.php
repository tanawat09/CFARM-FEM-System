@extends('layouts.app')

@section('page_title', 'แผนผังและจุดติดตั้ง (Floor Plan)')

@section('styles')
<style>
    .map-container {
        position: relative;
        width: 100%;
        max-width: 100%;
        border: 2px dashed #ccc;
        background-color: #f8f9fa;
        min-height: 400px;
        overflow: hidden;
        border-radius: 8px;
    }
    .map-image {
        display: block;
        width: 100%;
        height: auto;
        user-select: none;
        -webkit-user-drag: none;
    }
    .extinguisher-pin {
        position: absolute;
        transform: translate(-50%, -100%); /* Center bottom edge of pin to the coordinates */
        cursor: grab;
        z-index: 10;
        font-size: 24px;
        transition: transform 0.1s ease;
    }
    .extinguisher-pin:hover {
        transform: translate(-50%, -100%) scale(1.2);
        z-index: 100;
    }
    .extinguisher-pin.dragging {
        cursor: grabbing;
        opacity: 0.8;
    }
    
    .status-active { color: #198754; }
    .status-under_repair { color: #ffc107; text-shadow: 0 0 2px #333;}
    .status-damage { color: #dc3545; }
    .status-disposed { color: #6c757d; }
    
    .unpinned-list {
        max-height: 600px;
        overflow-y: auto;
    }
    
    .draggable-item {
        cursor: grab;
    }
    .draggable-item:active {
        cursor: grabbing;
    }
</style>
@endsection

@section('content')
<div class="row">
    <!-- Filters / Select Location -->
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-map text-primary me-2"></i> แผนผังจุดติดตั้งถังดับเพลิง</h5>
                    <p class="text-muted small mb-0 mt-1">ลากและวางหมุดถังดับเพลิงลงบนแผนผังเพื่อระบุตำแหน่งจริง</p>
                </div>
                
                <form action="{{ route('map.index') }}" method="GET" class="d-flex w-50">
                    <select name="location_id" class="form-select form-select-lg rounded-pill shadow-sm" onchange="this.form.submit()">
                        <option value="">-- เลือกพื้นที่ (Locations ที่มีรูปแผนผัง) --</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ ($selectedLocation && $selectedLocation->id == $loc->id) ? 'selected' : '' }}>
                                {{ $loc->location_name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>
    </div>

    @if(!$selectedLocation)
    <div class="col-12">
        <div class="card border-0 shadow-sm text-center py-5 rounded-4">
            <div class="card-body">
                <i class="bi bi-map text-muted opacity-25" style="font-size: 5rem;"></i>
                <h4 class="mt-3 text-secondary">กรุณาเลือกพื้นที่ติดตั้งด้านบน</h4>
                <p class="text-muted">หากไม่มีตัวเลือก กรุณาไปที่เมนู <b>จัดการพื้นที่ (Locations)</b> เพื่ออัปโหลดรูปภาพแผนผังอาคารก่อน</p>
            </div>
        </div>
    </div>
    @else
    
    <!-- Sidebar: Unpinned Items -->
    <div class="col-md-3 mb-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-bottom p-3">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-pin-angle text-danger"></i> ยังไม่ได้ปักหมุด</h6>
            </div>
            <div class="card-body p-0 unpinned-list" id="unpinned-container">
                @php
                    $unpinned = $extinguishers->whereNull('map_x');
                    $pinned = $extinguishers->whereNotNull('map_x');
                @endphp
                
                <ul class="list-group list-group-flush" id="unpinned-list">
                    @forelse($unpinned as $ext)
                    <li class="list-group-item p-3 draggable-item" draggable="true" data-id="{{ $ext->id }}" data-sn="{{ $ext->serial_number }}" data-status="{{ $ext->status }}">
                        <div class="d-flex w-100 justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 fw-bold">{{ $ext->serial_number }}</h6>
                                <small class="text-muted">{{ str_replace('_', ' ', $ext->type) }} {{ (float)$ext->size }} {{ $ext->size_unit }}</small>
                            </div>
                            <i class="bi bi-grip-vertical text-muted"></i>
                        </div>
                    </li>
                    @empty
                    <li class="list-group-item text-center py-4 text-muted" id="empty-unpinned-msg">
                        ปักหมุดครบทุกถังในพื้นที่นี้แล้ว
                    </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <!-- Main Map Area -->
    <div class="col-md-9 mb-4">
        <div class="card border-0 shadow-sm rounded-4 position-relative">
            <div class="card-body p-3">
                <div class="map-container" id="map-container">
                    <img src="{{ url('storage/' . $selectedLocation->floor_plan_image) }}" alt="Floor Plan for {{ $selectedLocation->location_name }}" class="map-image" id="map-image">
                    @foreach($pinned as $ext)
                        @php
                            $statusClass = 'status-' . $ext->status;
                            $iconColor = $ext->status == 'active' ? 'text-success' : ($ext->status == 'damage' ? 'text-danger' : ($ext->status == 'under_repair' ? 'text-warning' : 'text-secondary'));
                        @endphp
                        <div class="extinguisher-pin {{ $statusClass }}" 
                             id="pin-{{ $ext->id }}"
                             data-id="{{ $ext->id }}" 
                             style="left: {{ $ext->map_x }}%; top: {{ $ext->map_y }}%;"
                             title="S/N: {{ $ext->serial_number }}&#10;สถานะ: {{ $ext->status }}">
                            <img src="{{ asset('images/extinguisher-pin.png') }}" alt="Extinguisher" style="width: 24px; height: 24px;">
                        </div>
                    @endforeach
                </div>
                <div class="mt-3 d-flex justify-content-between align-items-center">
                    <p class="text-muted small mb-0"><i class="bi bi-info-circle"></i> ลากหมุดบนแผนที่เพื่อย้ายตำแหน่ง หรือดับเบิ้ลคลิกที่หมุดเพื่อยกเลิกการปัก</p>
                    <div class="small">
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 me-1"><i class="bi bi-circle-fill small"></i> ปกติ</span>
                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1 me-1"><i class="bi bi-circle-fill small"></i> ส่งซ่อม</span>
                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1"><i class="bi bi-circle-fill small"></i> ชำรุด</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @endif
</div>

<!-- Modal logic via JS alerts or Toast for simplicity right now -->

@endsection

@section('scripts')
@if($selectedLocation)
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const mapContainer = document.getElementById('map-container');
        const unpinnedItems = document.querySelectorAll('.draggable-item');
        
        let draggedElement = null;

        // --- Dragging from sidebar to map ---
        unpinnedItems.forEach(item => {
            item.addEventListener('dragstart', function(e) {
                draggedElement = this;
                e.dataTransfer.effectAllowed = 'move';
                // e.dataTransfer.setData('text/html', this.innerHTML); // Not really needed if we use variables
            });
            item.addEventListener('dragend', function(e) {
                draggedElement = null;
            });
        });

        mapContainer.addEventListener('dragover', function(e) {
            e.preventDefault(); // allow drop
            e.dataTransfer.dropEffect = 'move';
        });

        mapContainer.addEventListener('drop', function(e) {
            e.preventDefault();
            if (draggedElement) {
                const rect = mapContainer.getBoundingClientRect();
                const xPixel = e.clientX - rect.left;
                const yPixel = e.clientY - rect.top;
                
                // Calculate percentages
                const xPercent = (xPixel / rect.width) * 100;
                const yPercent = (yPixel / rect.height) * 100;
                
                const id = draggedElement.getAttribute('data-id');
                const sn = draggedElement.getAttribute('data-sn');
                const status = draggedElement.getAttribute('data-status');
                
                // Prevent duplicate pins
                if (document.getElementById('pin-' + id)) {
                    return;
                }
                
                // Disable drag to prevent multiple drops while saving
                draggedElement.style.opacity = '0.5';
                draggedElement.setAttribute('draggable', 'false');
                
                savePinAjax(id, xPercent, yPercent, function() {
                    createPinElement(id, sn, status, xPercent, yPercent);
                    draggedElement.remove(); // Remove from sidebar list
                    draggedElement = null;
                    checkEmptySidebar();
                });
            }
        });

        // --- Moving existing pins ---
        let isDraggingPin = false;
        let currentPin = null;

        mapContainer.addEventListener('mousedown', function(e) {
            if (e.target.closest('.extinguisher-pin')) {
                currentPin = e.target.closest('.extinguisher-pin');
                isDraggingPin = true;
                currentPin.classList.add('dragging');
            }
        });

        document.addEventListener('mousemove', function(e) {
            if (isDraggingPin && currentPin) {
                const rect = mapContainer.getBoundingClientRect();
                let xPixel = e.clientX - rect.left;
                let yPixel = e.clientY - rect.top;
                
                // Boundaries
                xPixel = Math.max(0, Math.min(xPixel, rect.width));
                yPixel = Math.max(0, Math.min(yPixel, rect.height));

                const xPercent = (xPixel / rect.width) * 100;
                const yPercent = (yPixel / rect.height) * 100;
                
                currentPin.style.left = xPercent + '%';
                currentPin.style.top = yPercent + '%';
            }
        });

        document.addEventListener('mouseup', function(e) {
            if (isDraggingPin && currentPin) {
                isDraggingPin = false;
                currentPin.classList.remove('dragging');
                
                // Save new position
                const xPercent = parseFloat(currentPin.style.left);
                const yPercent = parseFloat(currentPin.style.top);
                const id = currentPin.getAttribute('data-id');
                
                savePinAjax(id, xPercent, yPercent, null);
                currentPin = null;
            }
        });

        // --- Unpinning (Double click to remove pin) ---
        mapContainer.addEventListener('dblclick', function(e) {
            if (e.target.closest('.extinguisher-pin')) {
                const pin = e.target.closest('.extinguisher-pin');
                const id = pin.getAttribute('data-id');
                
                if(confirm('ต้องการยกเลิกการปักหมุดข้อนี้ใช่หรือไม่?')) {
                    removePinAjax(id, function() {
                        location.reload(); // Quickest way to refresh sidebar for now
                    });
                }
            }
        });

        // AJAX Helper to save
        function savePinAjax(extinguisher_id, map_x, map_y, onSuccess) {
            fetch("{{ route('map.save-pin') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    extinguisher_id: extinguisher_id,
                    map_x: map_x.toFixed(4),
                    map_y: map_y.toFixed(4)
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && onSuccess) {
                    onSuccess();
                }
            })
            .catch(error => console.error("Error saving pin:", error));
        }

        // AJAX Helper to remove
        function removePinAjax(extinguisher_id, onSuccess) {
            fetch("{{ route('map.remove-pin') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ extinguisher_id: extinguisher_id })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && onSuccess) {
                    onSuccess();
                }
            });
        }

        // DOM Helper Create Pin
        function createPinElement(id, sn, status, xPercent, yPercent) {
            // Remove any existing pin for this id just in case
            const existingPin = document.getElementById('pin-' + id);
            if (existingPin) {
                existingPin.remove();
            }

            const pin = document.createElement('div');
            pin.className = 'extinguisher-pin status-' + status;
            pin.id = 'pin-' + id;
            pin.setAttribute('data-id', id);
            pin.style.left = xPercent + '%';
            pin.style.top = yPercent + '%';
            pin.title = 'S/N: ' + sn + '\nสถานะ: ' + status;
            pin.innerHTML = '<img src="' + '{{ asset("images/extinguisher-pin.png") }}' + '" alt="Extinguisher" style="width: 24px; height: 24px;">';
            mapContainer.appendChild(pin);
        }

        function checkEmptySidebar() {
            const list = document.getElementById('unpinned-list');
            if (list.querySelectorAll('.draggable-item').length === 0) {
                if(!document.getElementById('empty-unpinned-msg')) {
                    const li = document.createElement('li');
                    li.className = 'list-group-item text-center py-4 text-muted';
                    li.id = 'empty-unpinned-msg';
                    li.innerText = 'ปักหมุดครบทุกถังในพื้นที่นี้แล้ว';
                    list.appendChild(li);
                }
            }
        }
    });
</script>
@endif
@endsection
