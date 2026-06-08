document.addEventListener('DOMContentLoaded', () => {
    const panel = document.getElementById('wa-scan-panel');

    if (!panel) {
        return;
    }

    const connectUrl = panel.dataset.connectUrl;
    const statusUrl = panel.dataset.statusUrl;
    const disconnectUrl = panel.dataset.disconnectUrl;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

    const statusBadge = document.getElementById('wa-status-badge');
    const statusLabel = document.getElementById('wa-status-label');
    const phoneDisplay = document.getElementById('wa-phone-display');
    const phoneEmpty = document.getElementById('wa-phone-empty');
    const qrContainer = document.getElementById('wa-qr-container');
    const qrImage = document.getElementById('wa-qr-image');
    const qrHint = document.getElementById('wa-qr-hint');
    const alertBox = document.getElementById('wa-alert');
    const btnConnect = document.getElementById('wa-btn-connect');
    const btnDisconnect = document.getElementById('wa-btn-disconnect');

    let pollTimer = null;
    let pollAttempts = 0;
    const maxPollAttempts = 120;

    const stateStyles = {
        connected: 'bg-success',
        waiting_scan: 'bg-warning text-dark',
        connecting: 'bg-info text-dark',
        disconnected: 'bg-secondary',
    };

    function showAlert(message, type = 'danger') {
        if (!alertBox) {
            return;
        }

        alertBox.className = `alert alert-${type} mb-3`;
        alertBox.textContent = message;
        alertBox.classList.remove('d-none');
    }

    function hideAlert() {
        alertBox?.classList.add('d-none');
    }

    function setLoading(isLoading) {
        btnConnect.disabled = isLoading;
        btnDisconnect.disabled = isLoading;
    }

    function updateUi(data) {
        const state = data?.state ?? 'disconnected';
        const label = data?.state_label ?? 'Tidak Terhubung';
        const phone = data?.phone ?? null;
        const qrCode = data?.qr_code ?? null;

        statusBadge.className = `badge ${stateStyles[state] ?? 'bg-secondary'}`;
        statusBadge.textContent = label;
        statusLabel.textContent = label;

        if (phone) {
            phoneDisplay.textContent = phone;
            phoneDisplay.classList.remove('d-none');
            phoneEmpty?.classList.add('d-none');
        } else {
            phoneDisplay.textContent = '';
            phoneDisplay.classList.add('d-none');
            phoneEmpty?.classList.remove('d-none');
        }

        if (state === 'waiting_scan' && qrCode) {
            qrContainer.classList.remove('d-none');
            qrImage.src = qrCode;
            qrHint.textContent = 'Buka WhatsApp di ponsel → Perangkat Tertaut → Tautkan Perangkat, lalu scan QR di bawah.';
        } else if (state === 'connecting') {
            qrContainer.classList.remove('d-none');
            qrImage.removeAttribute('src');
            qrHint.textContent = 'Menghubungkan ke WhatsApp...';
        } else {
            qrContainer.classList.add('d-none');
            qrImage.removeAttribute('src');
        }

        btnConnect.classList.toggle('d-none', state === 'connected');
        btnDisconnect.classList.toggle('d-none', state !== 'connected');
    }

    function stopPolling() {
        if (pollTimer) {
            clearInterval(pollTimer);
            pollTimer = null;
        }

        pollAttempts = 0;
    }

    function startPolling() {
        stopPolling();

        pollTimer = setInterval(async () => {
            pollAttempts += 1;

            if (pollAttempts > maxPollAttempts) {
                stopPolling();
                showAlert('Waktu scan habis. Silakan klik Hubungkan untuk mencoba lagi.');

                return;
            }

            try {
                const response = await fetch(statusUrl, {
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                const result = await response.json();

                if (!result.success) {
                    showAlert(result.message || 'Gagal memeriksa status koneksi.');
                    stopPolling();

                    return;
                }

                updateUi(result.data);

                if (result.data?.state === 'connected') {
                    stopPolling();
                    hideAlert();
                }
            } catch {
                showAlert('Gagal memeriksa status koneksi.');
                stopPolling();
            }
        }, 3000);
    }

    async function requestJson(url, method = 'GET') {
        const response = await fetch(url, {
            method,
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        const result = await response.json();

        if (!response.ok || !result.success) {
            throw new Error(result.message || 'Permintaan gagal.');
        }

        return result;
    }

    btnConnect?.addEventListener('click', async () => {
        hideAlert();
        setLoading(true);

        try {
            const result = await requestJson(connectUrl, 'POST');
            updateUi(result.data);

            if (result.data?.state === 'connected') {
                return;
            }

            if (result.data?.state === 'waiting_scan' || result.data?.state === 'connecting') {
                startPolling();
            }
        } catch (error) {
            showAlert(error.message || 'Gagal memulai sesi WhatsApp.');
        } finally {
            setLoading(false);
        }
    });

    btnDisconnect?.addEventListener('click', async () => {
        hideAlert();
        setLoading(true);
        stopPolling();

        try {
            const result = await requestJson(disconnectUrl, 'DELETE');
            updateUi(result.data);
        } catch (error) {
            showAlert(error.message || 'Gagal memutuskan sesi WhatsApp.');
        } finally {
            setLoading(false);
        }
    });

    fetch(statusUrl, {
        headers: {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
    })
        .then((response) => response.json())
        .then((result) => {
            if (result.success) {
                updateUi(result.data);
            }
        })
        .catch(() => {
            // Abaikan error saat load awal.
        });
});
