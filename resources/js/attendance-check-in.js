/**
 * Halaman Absen Saya: GPS + kamera otomatis aktif saat halaman dibuka.
 */
(function () {
    const form = document.getElementById('check-in-form');
    if (!form) {
        return;
    }

    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    const gpsStatus = document.getElementById('gps-status');
    const submitBtn = document.getElementById('submit-btn');
    const readyBanner = document.getElementById('absen-ready-banner');

    let gpsWatchId = null;
    let gpsReady = false;
    let cameraReady = false;

    const updateSubmitState = () => {
        const hasPhoto = form.querySelector('[data-camera-input]')?.files?.length > 0;
        const hasGps = latInput?.value !== '' && lngInput?.value !== '';
        const canSubmit = hasPhoto && hasGps && gpsReady;

        if (submitBtn) {
            submitBtn.disabled = !canSubmit;
        }

        if (readyBanner) {
            if (cameraReady && gpsReady && !hasPhoto) {
                readyBanner.classList.remove('d-none');
                readyBanner.classList.remove('alert-warning');
                readyBanner.classList.add('alert-success');
                readyBanner.innerHTML =
                    '<i class="bx bx-check-circle me-1"></i> Kamera & lokasi siap. Ambil foto lalu tekan tombol absen.';
            } else if (!gpsReady || !cameraReady) {
                readyBanner.classList.remove('d-none');
                readyBanner.classList.remove('alert-success');
                readyBanner.classList.add('alert-warning');
            } else if (hasPhoto && hasGps) {
                readyBanner.classList.remove('d-none');
                readyBanner.classList.remove('alert-warning');
                readyBanner.classList.add('alert-success');
                readyBanner.innerHTML =
                    '<i class="bx bx-check-circle me-1"></i> Siap dikirim. Tekan tombol absen di bawah.';
            }
        }
    };

    form.addEventListener('attendance-camera:ready', () => {
        cameraReady = true;
        updateSubmitState();
    });

    form.addEventListener('attendance-camera:error', () => {
        cameraReady = false;
        updateSubmitState();
    });

    form.addEventListener('attendance-camera:captured', updateSubmitState);
    form.addEventListener('attendance-camera:cleared', updateSubmitState);

    const applyPosition = (pos) => {
        latInput.value = pos.coords.latitude;
        lngInput.value = pos.coords.longitude;
        gpsReady = true;
        if (gpsStatus) {
            gpsStatus.textContent =
                'Lokasi GPS siap (' +
                pos.coords.latitude.toFixed(5) +
                ', ' +
                pos.coords.longitude.toFixed(5) +
                ')';
        }
        updateSubmitState();
    };

    const onGpsError = (err) => {
        gpsReady = false;
        if (gpsStatus) {
            gpsStatus.textContent =
                'Gagal mengambil GPS: ' + (err.message || 'izinkan akses lokasi (pilih Selalu izinkan jika ada)');
        }
        updateSubmitState();
    };

    if (!navigator.geolocation) {
        if (gpsStatus) {
            gpsStatus.textContent = 'Browser tidak mendukung GPS.';
        }

        return;
    }

    if (gpsStatus) {
        gpsStatus.textContent = 'Meminta izin lokasi...';
    }

    const geoOptions = {
        enableHighAccuracy: true,
        timeout: 20000,
        maximumAge: 60000,
    };

    navigator.geolocation.getCurrentPosition(applyPosition, onGpsError, geoOptions);

    gpsWatchId = navigator.geolocation.watchPosition(applyPosition, () => {}, geoOptions);

    window.addEventListener('beforeunload', () => {
        if (gpsWatchId !== null) {
            navigator.geolocation.clearWatch(gpsWatchId);
        }
    });
})();
