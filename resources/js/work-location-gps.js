/**
 * Form Lokasi Kerja: isi latitude/longitude dari GPS perangkat saat ini.
 */
(function () {
    const btn = document.getElementById('btn-use-current-gps');
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    const statusEl = document.getElementById('gps-pick-status');

    if (!btn || !latInput || !lngInput) {
        return;
    }

    const setStatus = (message, type = 'muted') => {
        if (!statusEl) {
            return;
        }

        statusEl.textContent = message;
        statusEl.className = 'small text-' + type;
    };

    const geoOptions = {
        enableHighAccuracy: true,
        timeout: 20000,
        maximumAge: 0,
    };

    btn.addEventListener('click', () => {
        if (!navigator.geolocation) {
            setStatus('Browser tidak mendukung GPS.', 'danger');

            return;
        }

        btn.disabled = true;
        setStatus('Mengambil lokasi GPS...', 'muted');

        navigator.geolocation.getCurrentPosition(
            (pos) => {
                latInput.value = pos.coords.latitude.toFixed(7);
                lngInput.value = pos.coords.longitude.toFixed(7);
                setStatus(
                    'Lokasi berhasil diisi (' +
                        pos.coords.latitude.toFixed(5) +
                        ', ' +
                        pos.coords.longitude.toFixed(5) +
                        ')',
                    'success'
                );
                btn.disabled = false;
            },
            (err) => {
                setStatus(
                    'Gagal mengambil GPS: ' + (err.message || 'izinkan akses lokasi di browser'),
                    'danger'
                );
                btn.disabled = false;
            },
            geoOptions
        );
    });
})();
