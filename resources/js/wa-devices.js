const initWaDevices = () => {
    const devicesGrid = document.getElementById('devices-grid');
    if (!devicesGrid) return;

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const openModal = (id) => {
        const modal = document.getElementById(id);
        if (!modal) return;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    };

    const closeModal = (id) => {
        const modal = document.getElementById(id);
        if (!modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    };

    document.getElementById('open-add-device')?.addEventListener('click', () => openModal('add-device-modal'));
    document.getElementById('open-add-device-card')?.addEventListener('click', () => openModal('add-device-modal'));

    document.querySelectorAll('[data-close-modal]').forEach((button) => {
        button.addEventListener('click', () => closeModal(button.dataset.closeModal));
    });

    const qrModal = document.getElementById('qr-modal');
    const qrImage = document.getElementById('qr-image');
    const qrLoading = document.getElementById('qr-loading');
    const qrStatus = document.getElementById('qr-status');
    const qrError = document.getElementById('qr-error');
    const qrTitle = document.getElementById('qr-modal-title');

    let pollInterval = null;

    const stopPolling = () => {
        if (pollInterval) {
            clearInterval(pollInterval);
            pollInterval = null;
        }
    };

    const updateQrUi = (device) => {
        if (!device) return;

        if (device.status === 'connected') {
            qrLoading?.classList.add('hidden');
            qrImage?.classList.add('hidden');
            qrStatus.textContent = 'Berhasil terhubung!';
            qrStatus.className = 'mt-4 text-sm font-medium text-emerald-600';
            stopPolling();
            setTimeout(() => {
                closeModal('qr-modal');
                window.location.reload();
            }, 1500);
            return;
        }

        if (device.qrcode) {
            qrLoading?.classList.add('hidden');
            qrImage?.classList.remove('hidden');
            qrImage.src = device.qrcode;
            qrStatus.textContent = 'Scan QR code dengan WhatsApp di ponsel Anda';
            qrStatus.className = 'mt-4 text-sm font-medium text-slate-600';
        } else {
            qrLoading?.classList.remove('hidden');
            qrImage?.classList.add('hidden');
            qrStatus.textContent = device.status === 'connecting' ? 'Menunggu QR code...' : 'Memulai session...';
        }
    };

    const pollStatus = async (deviceId) => {
        try {
            const response = await fetch(`/dashboard/devices/${deviceId}/status`, {
                headers: { Accept: 'application/json' },
            });

            if (!response.ok) return;

            const data = await response.json();
            updateQrUi(data.device);
        } catch {
            // Abaikan error polling sementara.
        }
    };

    const connectDevice = async (deviceId, deviceName) => {
        stopPolling();

        if (qrTitle) qrTitle.textContent = `Hubungkan ${deviceName}`;
        qrError?.classList.add('hidden');
        if (qrError) qrError.textContent = '';
        qrLoading?.classList.remove('hidden');
        qrImage?.classList.add('hidden');
        if (qrStatus) {
            qrStatus.textContent = 'Memulai session...';
            qrStatus.className = 'mt-4 text-sm font-medium text-slate-600';
        }

        openModal('qr-modal');

        try {
            const response = await fetch(`/dashboard/devices/${deviceId}/connect`, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Gagal menghubungkan perangkat.');
            }

            updateQrUi(data.device);
            pollInterval = setInterval(() => pollStatus(deviceId), 2500);
        } catch (error) {
            qrLoading?.classList.add('hidden');
            qrError?.classList.remove('hidden');
            if (qrError) qrError.textContent = error.message || 'Terjadi kesalahan.';
            if (qrStatus) {
                qrStatus.textContent = 'Gagal memulai koneksi';
                qrStatus.className = 'mt-4 text-sm font-medium text-red-600';
            }
        }
    };

    const disconnectDevice = async (deviceId) => {
        if (!confirm('Putuskan koneksi perangkat ini?')) return;

        try {
            const response = await fetch(`/dashboard/devices/${deviceId}/disconnect`, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
            });

            if (!response.ok) {
                const data = await response.json();
                throw new Error(data.message || 'Gagal memutuskan perangkat.');
            }

            window.location.reload();
        } catch (error) {
            alert(error.message || 'Terjadi kesalahan.');
        }
    };

    document.querySelectorAll('[data-action="connect"]').forEach((button) => {
        button.addEventListener('click', () => {
            connectDevice(button.dataset.deviceId, button.dataset.deviceName);
        });
    });

    document.querySelectorAll('[data-action="disconnect"]').forEach((button) => {
        button.addEventListener('click', () => {
            disconnectDevice(button.dataset.deviceId);
        });
    });

    qrModal?.addEventListener('click', (event) => {
        if (event.target === qrModal) {
            stopPolling();
            closeModal('qr-modal');
        }
    });
};

document.addEventListener('DOMContentLoaded', initWaDevices);
