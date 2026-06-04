@props([
    'id',
    'name',
    'label' => 'Foto',
    'required' => false,
    'autoStart' => false,
])

<div
    class="attendance-camera-wrap mb-3"
    data-attendance-camera
    data-required="{{ $required ? '1' : '0' }}"
    data-auto-start="{{ $autoStart ? '1' : '0' }}"
>
    <label class="form-label" for="{{ $id }}">{{ $label }} @if($required)<span class="text-danger">*</span>@endif</label>
    <div class="attendance-camera border rounded p-3 bg-light text-center">
        <video class="w-100 rounded d-none attendance-camera-video" data-camera-video playsinline autoplay muted></video>
        <img class="w-100 rounded d-none attendance-camera-preview" data-camera-preview alt="Preview foto absen" />
        <canvas class="d-none" data-camera-canvas></canvas>
        <input
            type="file"
            name="{{ $name }}"
            id="{{ $id }}"
            class="d-none"
            data-camera-input
            accept="image/jpeg,image/png,image/webp"
            tabindex="-1"
        />
        <div class="d-flex flex-wrap gap-2 justify-content-center">
            <button type="button" class="btn btn-outline-primary" data-camera-start>
                <i class="bx bx-camera me-1"></i> Buka Kamera
            </button>
            <button type="button" class="btn btn-primary d-none" data-camera-capture>
                <i class="bx bx-aperture me-1"></i> Ambil Foto
            </button>
            <button type="button" class="btn btn-outline-secondary d-none" data-camera-retake>
                <i class="bx bx-revision me-1"></i> Ulangi
            </button>
        </div>
        <p class="small text-muted mb-0 mt-2" data-camera-status>
            @if($autoStart)Menyiapkan kamera...@else Foto diambil langsung dari kamera perangkat.@endif
        </p>
    </div>
    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

@once
    @push('scripts')
        @vite('resources/js/attendance-camera.js')
    @endpush
@endonce
