@php
    use function Filament\Support\prepare_inherited_attributes;

    $fieldWrapperView = $getFieldWrapperView();
    $datalistOptions = $getDatalistOptions();
    $extraAlpineAttributes = $getExtraAlpineAttributes();
    $extraAttributeBag = $getExtraAttributeBag();
    $hasInlineLabel = $hasInlineLabel();
    $id = $getId();
    $isConcealed = $isConcealed();
    $isDisabled = $isDisabled();
    $statePath = $getStatePath();
    $placeholder = $getPlaceholder();
    $readerId = 'qr-reader-' . md5($statePath);

    $inputAttributes = $getExtraInputAttributeBag()
        ->merge($extraAlpineAttributes, escape: false)
        ->merge([
            'disabled' => $isDisabled,
            'id' => $id,
            'inputmode' => $getInputMode(),
            'list' => $datalistOptions ? $id . '-list' : null,
            'max' => (! $isConcealed) ? $getMaxValue() : null,
            'maxlength' => (! $isConcealed) ? $getMaxLength() : null,
            'min' => (! $isConcealed) ? $getMinValue() : null,
            'minlength' => (! $isConcealed) ? $getMinLength() : null,
            'placeholder' => filled($placeholder)
                ? e($placeholder)
                : null,
            'readonly' => $isReadOnly(),
            'required' => $isRequired() && (! $isConcealed),
            'type' => 'text',
            $applyStateBindingModifiers('wire:model') => $statePath,
        ], escape: false)
        ->class(['w-full']);
@endphp

<x-dynamic-component
    :component="$fieldWrapperView"
    :field="$field"
    :has-inline-label="$hasInlineLabel"
    class="fi-fo-text-input-wrp"
>
    <div
        x-load-js="[
            'https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js',
            'https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js'
        ]"
        x-data="{
            scanner: null,
            isStarting: false,
            isScanning: false,
            scanComplete: false,
            isProcessingImage: false,
            torchAvailable: false,
            torchEnabled: false,
            zoomAvailable: false,
            zoom: 1,
            minZoom: 1,
            maxZoom: 1,
            status: 'loading',
            message: 'Menyiapkan pemindai QR...',

            get realtimeAvailable() {
                return Boolean(
                    window.isSecureContext
                    && navigator.mediaDevices
                    && navigator.mediaDevices.getUserMedia
                );
            },

            async initScanner() {
                let attempts = 0;

                while (
                    typeof window.Html5Qrcode === 'undefined'
                    && attempts < 80
                ) {
                    await new Promise(
                        (resolve) => setTimeout(resolve, 100)
                    );

                    attempts++;
                }

                if (typeof window.Html5Qrcode === 'undefined') {
                    this.status = 'error';

                    this.message =
                        'Library pemindai QR gagal dimuat. ' +
                        'Periksa koneksi internet lalu muat ulang halaman.';

                    return;
                }

                if (this.realtimeAvailable) {
                    await this.startCamera();
                    return;
                }

                this.status = 'fallback';

                this.message =
                    'Mode realtime tidak tersedia pada HTTP. ' +
                    'Tekan Scan QR dengan Kamera HP untuk membuka kamera.';
            },

            async startCamera() {
                if (this.isStarting || this.isScanning) {
                    return;
                }

                this.scanComplete = false;

                if (! this.realtimeAvailable) {
                    this.status = 'fallback';

                    this.message =
                        'Mode realtime membutuhkan secure context. ' +
                        'Gunakan tombol Scan QR dengan Kamera HP.';

                    return;
                }

                this.isStarting = true;
                this.status = 'loading';
                this.message = 'Meminta izin kamera...';

                try {
                    await this.stopCamera();

                    const reader = document.getElementById(
                        @js($readerId)
                    );

                    if (reader) {
                        reader.innerHTML = '';
                    }

                    this.scanner = new window.Html5Qrcode(
                        @js($readerId),
                        {
                            formatsToSupport: [
                                window.Html5QrcodeSupportedFormats.QR_CODE,
                            ],
                            useBarCodeDetectorIfSupported: true,
                            verbose: false,
                        }
                    );

                    await this.scanner.start(
                        {
                            facingMode: {
                                ideal: 'environment',
                            },
                            width: {
                                ideal: 1920,
                            },
                            height: {
                                ideal: 1080,
                            },
                        },
                        {
                            fps: 20,
                            disableFlip: false,
                            qrbox: (viewfinderWidth, viewfinderHeight) => {
                                const size = Math.floor(
                                    Math.min(viewfinderWidth, viewfinderHeight) * 0.78
                                );

                                return {
                                    width: Math.max(size, 180),
                                    height: Math.max(size, 180),
                                };
                            },
                        },
                        async (decodedText) => {
                            await this.handleScan(decodedText);
                        },
                        () => {
                            // Kesalahan membaca setiap frame diabaikan.
                        },
                    );

                    this.isScanning = true;
                    await this.configureCameraEnhancements();
                    this.status = 'scanning';

                    this.message =
                        'Kamera aktif tanpa batas waktu. Hindari pantulan langsung dan arahkan QR Code ke kotak pemindaian.';
                } catch (error) {
                    console.error('QR scanner error:', error);

                    this.isScanning = false;
                    this.status = 'error';

                    const errorText = String(error ?? '');

                    if (
                        error?.name === 'NotAllowedError'
                        || errorText.includes('NotAllowed')
                        || errorText.includes('Permission')
                    ) {
                        this.message =
                            'Izin kamera ditolak. Izinkan kamera pada ' +
                            'pengaturan browser atau gunakan Scan QR ' +
                            'dengan Kamera HP.';
                    } else if (
                        error?.name === 'NotFoundError'
                        || errorText.includes('NotFound')
                    ) {
                        this.message =
                            'Kamera tidak ditemukan pada perangkat ini.';
                    } else if (
                        error?.name === 'NotReadableError'
                        || errorText.includes('NotReadable')
                    ) {
                        this.message =
                            'Kamera sedang digunakan aplikasi lain. ' +
                            'Tutup aplikasi kamera lalu coba kembali.';
                    } else {
                        this.message =
                            'Kamera realtime gagal dibuka. Gunakan tombol ' +
                            'Scan QR dengan Kamera HP.';
                    }
                } finally {
                    this.isStarting = false;
                }
            },


            async configureCameraEnhancements() {
                if (! this.scanner || ! this.isScanning) {
                    return;
                }

                try {
                    const capabilities =
                        this.scanner.getRunningTrackCapabilities?.() || {};

                    this.torchAvailable = Boolean(capabilities.torch);

                    const zoomCapability = capabilities.zoom;

                    if (zoomCapability) {
                        this.minZoom = Number(zoomCapability.min ?? 1);
                        this.maxZoom = Number(zoomCapability.max ?? this.minZoom);
                        this.zoom = Math.min(
                            Math.max(1, this.minZoom),
                            this.maxZoom
                        );
                        this.zoomAvailable = this.maxZoom > this.minZoom;
                    } else {
                        this.zoomAvailable = false;
                    }

                    const advanced = {};

                    if (
                        Array.isArray(capabilities.focusMode)
                        && capabilities.focusMode.includes('continuous')
                    ) {
                        advanced.focusMode = 'continuous';
                    }

                    if (
                        Array.isArray(capabilities.exposureMode)
                        && capabilities.exposureMode.includes('continuous')
                    ) {
                        advanced.exposureMode = 'continuous';
                    }

                    if (
                        Array.isArray(capabilities.whiteBalanceMode)
                        && capabilities.whiteBalanceMode.includes('continuous')
                    ) {
                        advanced.whiteBalanceMode = 'continuous';
                    }

                    if (this.zoomAvailable) {
                        advanced.zoom = this.zoom;
                    }

                    if (Object.keys(advanced).length > 0) {
                        await this.scanner.applyVideoConstraints({
                            advanced: [advanced],
                        });
                    }
                } catch (error) {
                    console.debug(
                        'Optimasi fokus/exposure kamera tidak didukung perangkat.',
                        error
                    );
                }
            },

            async toggleTorch() {
                if (! this.scanner || ! this.isScanning || ! this.torchAvailable) {
                    return;
                }

                const nextState = ! this.torchEnabled;

                try {
                    await this.scanner.applyVideoConstraints({
                        advanced: [{ torch: nextState }],
                    });

                    this.torchEnabled = nextState;
                    this.message = nextState
                        ? 'Lampu kamera aktif. Matikan jika pantulan pada QR terlalu kuat.'
                        : 'Lampu kamera dimatikan untuk mengurangi pantulan.';
                } catch (error) {
                    console.debug('Torch kamera tidak dapat diubah.', error);
                }
            },

            async applyZoom(value) {
                if (! this.scanner || ! this.isScanning || ! this.zoomAvailable) {
                    return;
                }

                const requested = Number(value || 1);
                const normalized = Math.min(
                    Math.max(requested, this.minZoom),
                    this.maxZoom
                );

                try {
                    await this.scanner.applyVideoConstraints({
                        advanced: [{ zoom: normalized }],
                    });

                    this.zoom = normalized;
                } catch (error) {
                    console.debug('Zoom kamera tidak dapat diubah.', error);
                }
            },

            async refocusCamera() {
                if (! this.scanner || ! this.isScanning) {
                    return;
                }

                await this.configureCameraEnhancements();
                this.message =
                    'Fokus dan exposure dioptimalkan ulang. Tahan HP stabil dan miringkan sedikit bila ada pantulan lampu.';
            },

            async waitForJsQr() {
                let attempts = 0;

                while (
                    typeof window.jsQR === 'undefined'
                    && attempts < 100
                ) {
                    await new Promise(
                        (resolve) => setTimeout(resolve, 100)
                    );

                    attempts++;
                }

                return typeof window.jsQR !== 'undefined';
            },

            async decodeWithBarcodeDetector(file) {
                if (
                    ! ('BarcodeDetector' in window)
                    || typeof window.createImageBitmap !== 'function'
                ) {
                    return null;
                }

                let bitmap = null;

                try {
                    const supportedFormats =
                        typeof window.BarcodeDetector.getSupportedFormats === 'function'
                            ? await window.BarcodeDetector.getSupportedFormats()
                            : ['qr_code'];

                    if (! supportedFormats.includes('qr_code')) {
                        return null;
                    }

                    bitmap = await window.createImageBitmap(file);

                    const detector = new window.BarcodeDetector({
                        formats: ['qr_code'],
                    });

                    const results = await detector.detect(bitmap);
                    const value = results?.[0]?.rawValue;

                    return value
                        ? String(value).trim()
                        : null;
                } catch (error) {
                    console.debug(
                        'BarcodeDetector belum berhasil membaca QR.',
                        error
                    );

                    return null;
                } finally {
                    bitmap?.close?.();
                }
            },

            async loadImageFromFile(file) {
                const objectUrl = URL.createObjectURL(file);

                try {
                    return await new Promise((resolve, reject) => {
                        const image = new Image();

                        image.onload = () => resolve(image);
                        image.onerror = () => reject(
                            new Error('Gambar tidak dapat dibuka.')
                        );

                        image.src = objectUrl;
                    });
                } finally {
                    URL.revokeObjectURL(objectUrl);
                }
            },

            calculateOtsuThreshold(values) {
                const histogram = new Uint32Array(256);

                for (let index = 0; index < values.length; index++) {
                    histogram[values[index]]++;
                }

                const total = values.length;
                let totalSum = 0;

                for (let value = 0; value < 256; value++) {
                    totalSum += value * histogram[value];
                }

                let backgroundWeight = 0;
                let backgroundSum = 0;
                let bestVariance = -1;
                let bestThreshold = 127;

                for (let threshold = 0; threshold < 256; threshold++) {
                    backgroundWeight += histogram[threshold];

                    if (backgroundWeight === 0) {
                        continue;
                    }

                    const foregroundWeight = total - backgroundWeight;

                    if (foregroundWeight === 0) {
                        break;
                    }

                    backgroundSum += threshold * histogram[threshold];

                    const backgroundMean =
                        backgroundSum / backgroundWeight;

                    const foregroundMean =
                        (totalSum - backgroundSum) / foregroundWeight;

                    const difference =
                        backgroundMean - foregroundMean;

                    const variance =
                        backgroundWeight
                        * foregroundWeight
                        * difference
                        * difference;

                    if (variance > bestVariance) {
                        bestVariance = variance;
                        bestThreshold = threshold;
                    }
                }

                return bestThreshold;
            },

            createProcessedCanvas(
                image,
                cropRatio = 1,
                maxDimension = 2000,
                mode = 'normal'
            ) {
                const naturalWidth =
                    image.naturalWidth || image.width;

                const naturalHeight =
                    image.naturalHeight || image.height;

                const sourceWidth = Math.max(
                    1,
                    Math.round(naturalWidth * cropRatio)
                );

                const sourceHeight = Math.max(
                    1,
                    Math.round(naturalHeight * cropRatio)
                );

                const sourceX = Math.round(
                    (naturalWidth - sourceWidth) / 2
                );

                const sourceY = Math.round(
                    (naturalHeight - sourceHeight) / 2
                );

                const scale = Math.min(
                    1,
                    maxDimension / Math.max(sourceWidth, sourceHeight)
                );

                const width = Math.max(
                    1,
                    Math.round(sourceWidth * scale)
                );

                const height = Math.max(
                    1,
                    Math.round(sourceHeight * scale)
                );

                const canvas = document.createElement('canvas');
                canvas.width = width;
                canvas.height = height;

                const context = canvas.getContext('2d', {
                    willReadFrequently: true,
                });

                context.imageSmoothingEnabled = true;
                context.imageSmoothingQuality = 'high';

                context.drawImage(
                    image,
                    sourceX,
                    sourceY,
                    sourceWidth,
                    sourceHeight,
                    0,
                    0,
                    width,
                    height
                );

                if (mode === 'normal') {
                    return canvas;
                }

                const imageData = context.getImageData(
                    0,
                    0,
                    width,
                    height
                );

                const pixels = imageData.data;
                const grayscale = new Uint8ClampedArray(
                    width * height
                );

                for (
                    let pixelIndex = 0, valueIndex = 0;
                    pixelIndex < pixels.length;
                    pixelIndex += 4, valueIndex++
                ) {
                    const red = pixels[pixelIndex];
                    const green = pixels[pixelIndex + 1];
                    const blue = pixels[pixelIndex + 2];

                    if (mode === 'red-channel') {
                        grayscale[valueIndex] = red;
                    } else if (mode === 'green-channel') {
                        grayscale[valueIndex] = green;
                    } else if (mode === 'blue-channel') {
                        grayscale[valueIndex] = blue;
                    } else {
                        grayscale[valueIndex] = Math.round(
                            (red * 0.299)
                            + (green * 0.587)
                            + (blue * 0.114)
                        );
                    }
                }

                let threshold = 127;

                if (mode === 'otsu') {
                    threshold = this.calculateOtsuThreshold(grayscale);
                }

                let integral = null;
                let integralWidth = 0;

                if (mode === 'adaptive') {
                    integralWidth = width + 1;
                    integral = new Float64Array(
                        integralWidth * (height + 1)
                    );

                    for (let y = 1; y <= height; y++) {
                        let rowSum = 0;

                        for (let x = 1; x <= width; x++) {
                            rowSum += grayscale[
                                ((y - 1) * width) + (x - 1)
                            ];

                            integral[(y * integralWidth) + x] =
                                integral[((y - 1) * integralWidth) + x]
                                + rowSum;
                        }
                    }
                }

                const radius = Math.max(
                    8,
                    Math.floor(Math.min(width, height) / 70)
                );

                for (
                    let pixelIndex = 0, valueIndex = 0;
                    pixelIndex < pixels.length;
                    pixelIndex += 4, valueIndex++
                ) {
                    let value = grayscale[valueIndex];

                    if (mode === 'contrast') {
                        value = ((value - 128) * 1.65) + 128;
                    } else if (mode === 'dark') {
                        value = 255 * Math.pow(value / 255, 1.45);
                        value = ((value - 128) * 1.35) + 128;
                    } else if (mode === 'light') {
                        value = 255 * Math.pow(value / 255, 0.68);
                        value = ((value - 128) * 1.25) + 128;
                    } else if (mode === 'highlight') {
                        value = 255 * Math.pow(value / 255, 1.25);

                        if (value > 205) {
                            value = 205 + ((value - 205) * 0.18);
                        }

                        value = ((value - 128) * 1.55) + 128;
                    } else if (mode === 'glare') {
                        value = Math.min(value, 212);
                        value = ((value - 118) * 1.85) + 118;
                    } else if (
                        mode === 'red-channel'
                        || mode === 'green-channel'
                        || mode === 'blue-channel'
                    ) {
                        value = ((value - 128) * 1.75) + 128;
                    } else if (mode === 'otsu') {
                        value = value < threshold ? 0 : 255;
                    } else if (mode === 'adaptive') {
                        const x = valueIndex % width;
                        const y = Math.floor(valueIndex / width);

                        const x1 = Math.max(0, x - radius);
                        const y1 = Math.max(0, y - radius);
                        const x2 = Math.min(width - 1, x + radius);
                        const y2 = Math.min(height - 1, y + radius);

                        const left = x1;
                        const top = y1;
                        const right = x2 + 1;
                        const bottom = y2 + 1;

                        const sum =
                            integral[(bottom * integralWidth) + right]
                            - integral[(top * integralWidth) + right]
                            - integral[(bottom * integralWidth) + left]
                            + integral[(top * integralWidth) + left];

                        const area =
                            (right - left) * (bottom - top);

                        const localMean = sum / area;

                        value = value < (localMean - 7)
                            ? 0
                            : 255;
                    }

                    value = Math.max(
                        0,
                        Math.min(255, Math.round(value))
                    );

                    pixels[pixelIndex] = value;
                    pixels[pixelIndex + 1] = value;
                    pixels[pixelIndex + 2] = value;
                }

                context.putImageData(imageData, 0, 0);

                return canvas;
            },

            decodeCanvasWithJsQr(canvas) {
                if (typeof window.jsQR === 'undefined') {
                    return null;
                }

                const context = canvas.getContext('2d', {
                    willReadFrequently: true,
                });

                const imageData = context.getImageData(
                    0,
                    0,
                    canvas.width,
                    canvas.height
                );

                const result = window.jsQR(
                    imageData.data,
                    imageData.width,
                    imageData.height,
                    {
                        inversionAttempts: 'attemptBoth',
                    }
                );

                return result?.data
                    ? String(result.data).trim()
                    : null;
            },

            async decodeImageRobustly(file) {
                let value = await this.decodeWithBarcodeDetector(file);

                if (value) {
                    return value;
                }

                const reader = document.getElementById(@js($readerId));

                if (reader) {
                    reader.innerHTML = '';
                }

                try {
                    this.scanner = new window.Html5Qrcode(
                        @js($readerId),
                        {
                            formatsToSupport: [
                                window.Html5QrcodeSupportedFormats.QR_CODE,
                            ],
                            useBarCodeDetectorIfSupported: true,
                            verbose: false,
                        }
                    );

                    value = await this.scanner.scanFile(file, true);

                    if (value) {
                        return String(value).trim();
                    }
                } catch (error) {
                    console.debug(
                        'Pemindaian gambar standar belum berhasil.',
                        error
                    );
                } finally {
                    await this.stopCamera();
                }

                const jsQrReady = await this.waitForJsQr();

                if (! jsQrReady) {
                    return null;
                }

                const image = await this.loadImageFromFile(file);

                const passes = [
                    { crop: 1, max: 2400, mode: 'normal' },
                    { crop: 0.94, max: 2200, mode: 'normal' },
                    { crop: 1, max: 2000, mode: 'contrast' },
                    { crop: 0.92, max: 1900, mode: 'highlight' },
                    { crop: 1, max: 2000, mode: 'glare' },
                    { crop: 0.96, max: 1900, mode: 'green-channel' },
                    { crop: 0.90, max: 1800, mode: 'red-channel' },
                    { crop: 0.90, max: 1800, mode: 'blue-channel' },
                    { crop: 1, max: 1800, mode: 'dark' },
                    { crop: 1, max: 1800, mode: 'light' },
                    { crop: 0.88, max: 1600, mode: 'otsu' },
                    { crop: 0.82, max: 1400, mode: 'adaptive' },
                    { crop: 0.70, max: 1400, mode: 'contrast' },
                ];

                for (let index = 0; index < passes.length; index++) {
                    const pass = passes[index];

                    this.message =
                        'Menganalisis QR Code otomatis ('
                        + (index + 1)
                        + '/'
                        + passes.length
                        + ')...';

                    await new Promise(
                        (resolve) => requestAnimationFrame(resolve)
                    );

                    const canvas = this.createProcessedCanvas(
                        image,
                        pass.crop,
                        pass.max,
                        pass.mode
                    );

                    value = this.decodeCanvasWithJsQr(canvas);

                    canvas.width = 1;
                    canvas.height = 1;

                    if (value) {
                        return value;
                    }
                }

                return null;
            },

            openPhotoScanner() {
                this.$refs.qrFile.value = '';
                this.$refs.qrFile.click();
            },

            async scanImage(event) {
                const file = event.target.files?.[0];

                if (! file || this.isProcessingImage) {
                    return;
                }

                this.scanComplete = false;
                this.isProcessingImage = true;
                this.status = 'loading';
                this.message = 'Membaca QR Code secara otomatis...';

                try {
                    await this.stopCamera();

                    const decodedText =
                        await this.decodeImageRobustly(file);

                    if (! decodedText) {
                        throw new Error('QR_NOT_DETECTED');
                    }

                    await this.handleScan(decodedText);
                } catch (error) {
                    console.error('QR image scan error:', error);

                    this.status = 'error';
                    this.message =
                        'QR Code belum berhasil dibaca. Sistem sudah ' +
                        'mencoba beberapa mode pemindaian otomatis. ' +
                        'Silakan ambil ulang gambar QR.';

                    await this.stopCamera();
                } finally {
                    this.isProcessingImage = false;
                    event.target.value = '';
                }
            },

            async handleScan(decodedText) {
                if (this.scanComplete) {
                    return;
                }

                const value = String(decodedText ?? '').trim();

                if (value === '') {
                    return;
                }

                this.scanComplete = true;
                this.status = 'success';

                this.message =
                    'QR Code berhasil dibaca. Tekan Validasi Sekarang.';

                $wire.$set(@js($statePath), value, true);

                const input = document.getElementById(@js($id));

                if (input) {
                    input.value = value;

                    input.dispatchEvent(
                        new InputEvent('input', {
                            bubbles: true,
                            inputType: 'insertText',
                            data: value,
                        })
                    );

                    input.dispatchEvent(
                        new Event('change', {
                            bubbles: true,
                        })
                    );
                }

                console.log('QR berhasil dibaca:', value);

                await this.stopCamera(false);
            },

            async stopCamera(changeStatus = false) {
                if (! this.scanner) {
                    this.isScanning = false;
                    return;
                }

                try {
                    if (this.isScanning) {
                        await this.scanner.stop();
                    }
                } catch (error) {
                    console.debug(
                        'Scanner sudah berhenti.',
                        error
                    );
                }

                try {
                    await this.scanner.clear();
                } catch (error) {
                    console.debug(
                        'Area scanner sudah bersih.',
                        error
                    );
                }

                this.scanner = null;
                this.isScanning = false;
                this.torchAvailable = false;
                this.torchEnabled = false;
                this.zoomAvailable = false;

                if (changeStatus) {
                    this.status = this.realtimeAvailable
                        ? 'idle'
                        : 'fallback';

                    this.message = this.realtimeAvailable
                        ? 'Kamera dihentikan.'
                        : 'Tekan Scan QR dengan Kamera HP.';
                }
            },

            async restartCamera() {
                await this.stopCamera();
                await this.startCamera();
            },
        }"
        x-init="setTimeout(() => initScanner(), 300)"
        x-on:close-modal.window="stopCamera()"
        style="display: grid; gap: 1rem;"
    >
        <div
            x-show="
                status === 'loading'
                || status === 'scanning'
                || status === 'idle'
                || status === 'fallback'
            "
            style="
                padding: 0.75rem 1rem;
                border: 1px solid rgba(59, 130, 246, 0.35);
                border-radius: 0.75rem;
                background: rgba(59, 130, 246, 0.10);
            "
        >
            <strong x-text="message"></strong>
        </div>

        <div
            x-show="status === 'success'"
            style="
                padding: 0.75rem 1rem;
                border: 1px solid rgba(34, 197, 94, 0.35);
                border-radius: 0.75rem;
                background: rgba(34, 197, 94, 0.10);
            "
        >
            <strong x-text="message"></strong>
        </div>

        <div
            x-show="status === 'error'"
            style="
                padding: 0.75rem 1rem;
                border: 1px solid rgba(239, 68, 68, 0.35);
                border-radius: 0.75rem;
                background: rgba(239, 68, 68, 0.10);
            "
        >
            <strong x-text="message"></strong>
        </div>

        <div
            wire:ignore
            style="
                overflow: hidden;
                min-height: 310px;
                border-radius: 0.85rem;
                background: #000;
            "
        >
            <div
                id="{{ $readerId }}"
                style="width: 100%; min-height: 310px;"
            ></div>
        </div>

        <input
            x-ref="qrFile"
            type="file"
            accept="image/*"
            capture="environment"
            x-on:change="scanImage($event)"
            style="display: none;"
        />

        <div
            style="
                display: flex;
                flex-wrap: wrap;
                gap: 0.5rem;
            "
        >
            <x-filament::button
                type="button"
                color="primary"
                x-show="
                    realtimeAvailable
                    && (
                        status === 'error'
                        || status === 'idle'
                        || status === 'success'
                        || status === 'fallback'
                    )
                "
                x-on:click="restartCamera()"
            >
                Buka Kamera Realtime
            </x-filament::button>

            <x-filament::button
                type="button"
                color="warning"
                x-show="status !== 'scanning'"
                x-bind:disabled="isProcessingImage"
                x-on:click="openPhotoScanner()"
            >
                <span
                    x-text="isProcessingImage
                        ? 'Sedang Membaca QR...'
                        : 'Scan QR dengan Kamera HP'"
                ></span>
            </x-filament::button>

            <x-filament::button
                type="button"
                color="warning"
                x-show="status === 'scanning' && torchAvailable"
                x-on:click="toggleTorch()"
            >
                <span x-text="torchEnabled ? 'Matikan Lampu' : 'Nyalakan Lampu'"></span>
            </x-filament::button>

            <x-filament::button
                type="button"
                color="info"
                x-show="status === 'scanning'"
                x-on:click="refocusCamera()"
            >
                Fokus Ulang
            </x-filament::button>

            <x-filament::button
                type="button"
                color="gray"
                x-show="status === 'scanning'"
                x-on:click="stopCamera(true)"
            >
                Hentikan Kamera
            </x-filament::button>
        </div>

        <div
            x-show="status === 'scanning' && zoomAvailable"
            style="display: grid; gap: 0.35rem;"
        >
            <label style="font-size: 0.85rem; font-weight: 600;">
                Zoom kamera: <span x-text="Number(zoom).toFixed(1) + '×'"></span>
            </label>
            <input
                type="range"
                x-bind:min="minZoom"
                x-bind:max="maxZoom"
                step="0.1"
                x-model.number="zoom"
                x-on:input.debounce.150ms="applyZoom($event.target.value)"
            />
        </div>

        <div>
            <x-filament::input.wrapper
                :disabled="$isDisabled"
                :valid="! $errors->has($statePath)"
                :attributes="
                    prepare_inherited_attributes(
                        $extraAttributeBag
                    )->class(['fi-fo-text-input'])
                "
            >
                <input
                    {{ $inputAttributes->class(['fi-input']) }}
                />
            </x-filament::input.wrapper>

            @if ($datalistOptions)
                <datalist id="{{ $id }}-list">
                    @foreach ($datalistOptions as $value => $label)
                        <option value="{{ $value }}">
                            {{ $label }}
                        </option>
                    @endforeach
                </datalist>
            @endif
        </div>

        <p
            style="
                margin: 0;
                font-size: 0.8rem;
                opacity: 0.75;
            "
        >
            Pemindaian tidak memakai batas waktu. Pada HTTPS kamera realtime akan terus membaca sampai QR ditemukan. Bila ada pantulan lampu, miringkan HP sedikit, gunakan Fokus Ulang, atau matikan lampu kamera.
        </p>
    </div>
</x-dynamic-component>
