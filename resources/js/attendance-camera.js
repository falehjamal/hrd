/**
 * Absensi: ambil foto langsung dari kamera (getUserMedia).
 */
function initAttendanceCamera(root) {
    const video = root.querySelector('[data-camera-video]');
    const canvas = root.querySelector('[data-camera-canvas]');
    const preview = root.querySelector('[data-camera-preview]');
    const fileInput = root.querySelector('[data-camera-input]');
    const btnStart = root.querySelector('[data-camera-start]');
    const btnCapture = root.querySelector('[data-camera-capture]');
    const btnRetake = root.querySelector('[data-camera-retake]');
    const statusEl = root.querySelector('[data-camera-status]');
    const required = root.dataset.required === '1';
    const autoStart = root.dataset.autoStart === '1';

    let stream = null;

    const setStatus = (text) => {
        if (statusEl) {
            statusEl.textContent = text;
        }
    };

    const stopStream = () => {
        if (stream) {
            stream.getTracks().forEach((track) => track.stop());
            stream = null;
        }
    };

    const notifyCaptured = () => {
        root.dispatchEvent(new CustomEvent('attendance-camera:captured', { bubbles: true }));
    };

    const setFileFromBlob = (blob) => {
        const file = new File([blob], 'absen-' + Date.now() + '.jpg', { type: 'image/jpeg' });
        const dt = new DataTransfer();
        dt.items.add(file);
        fileInput.files = dt.files;
        notifyCaptured();
    };

    const startCamera = async () => {
        if (!navigator.mediaDevices?.getUserMedia) {
            setStatus('Browser tidak mendukung kamera.');
            alert('Browser tidak mendukung kamera. Gunakan HTTPS atau browser terbaru.');

            return false;
        }

        try {
            setStatus('Meminta izin kamera...');
            stopStream();
            stream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: { ideal: 'environment' },
                    width: { ideal: 1280 },
                    height: { ideal: 720 },
                },
                audio: false,
            });
            video.srcObject = stream;
            await video.play();
            video.classList.remove('d-none');
            preview.classList.add('d-none');
            btnStart?.classList.add('d-none');
            btnCapture?.classList.remove('d-none');
            btnRetake?.classList.add('d-none');
            setStatus('Kamera siap — tekan Ambil Foto');
            root.dispatchEvent(new CustomEvent('attendance-camera:ready', { bubbles: true }));

            return true;
        } catch (err) {
            setStatus('Kamera belum diizinkan.');
            if (!autoStart) {
                alert('Tidak dapat mengakses kamera: ' + (err.message || 'izinkan akses kamera'));
            }
            root.dispatchEvent(
                new CustomEvent('attendance-camera:error', { bubbles: true, detail: { message: err.message } })
            );

            return false;
        }
    };

    btnStart?.addEventListener('click', () => startCamera());

    btnCapture?.addEventListener('click', () => {
        if (!video.videoWidth) {
            return;
        }

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);
        stopStream();
        video.classList.add('d-none');

        canvas.toBlob(
            (blob) => {
                if (!blob) {
                    alert('Gagal mengambil foto.');

                    return;
                }
                setFileFromBlob(blob);
                preview.src = URL.createObjectURL(blob);
                preview.classList.remove('d-none');
                btnCapture.classList.add('d-none');
                btnRetake?.classList.remove('d-none');
                setStatus('Foto sudah diambil.');
            },
            'image/jpeg',
            0.88
        );
    });

    btnRetake?.addEventListener('click', async () => {
        fileInput.value = '';
        const dt = new DataTransfer();
        fileInput.files = dt.files;
        preview.classList.add('d-none');
        btnRetake?.classList.add('d-none');
        root.dispatchEvent(new CustomEvent('attendance-camera:cleared', { bubbles: true }));

        if (autoStart) {
            await startCamera();
        } else {
            stopStream();
            video.classList.add('d-none');
            btnStart?.classList.remove('d-none');
            setStatus('');
        }
    });

    const form = root.closest('form');
    form?.addEventListener('submit', (e) => {
        if (required && !fileInput.files?.length) {
            e.preventDefault();
            alert('Ambil foto dari kamera terlebih dahulu.');
        }
    });

    window.addEventListener('beforeunload', stopStream);

    if (autoStart) {
        btnStart?.classList.add('d-none');
        setStatus('Menyiapkan kamera...');
        startCamera();
    }
}

document.querySelectorAll('[data-attendance-camera]').forEach(initAttendanceCamera);
