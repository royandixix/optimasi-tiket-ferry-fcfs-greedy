<div class="col-12">
    <div class="border rounded-4 p-4">
        <h5 class="mb-1">Metode Pembayaran</h5>
        <p class="text-muted mb-4">Pilih cash di tempat atau transfer bank.</p>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="border rounded-4 p-3 w-100 h-100">
                    <div class="form-check m-0">
                        <input
                            class="form-check-input"
                            type="radio"
                            name="metode_pembayaran"
                            id="payment-cash"
                            value="cash"
                            {{ old('metode_pembayaran', 'cash') === 'cash' ? 'checked' : '' }}
                        >
                        <span class="form-check-label fw-semibold">Cash di Tempat</span>
                    </div>
                    <div class="small text-muted mt-2">
                        Bayar di tempat menggunakan kode pembayaran yang dibuat setelah pemesanan berhasil.
                    </div>
                </label>
            </div>

            <div class="col-md-6">
                <label class="border rounded-4 p-3 w-100 h-100">
                    <div class="form-check m-0">
                        <input
                            class="form-check-input"
                            type="radio"
                            name="metode_pembayaran"
                            id="payment-transfer"
                            value="transfer_bank"
                            {{ old('metode_pembayaran') === 'transfer_bank' ? 'checked' : '' }}
                        >
                        <span class="form-check-label fw-semibold">Transfer Bank</span>
                    </div>
                    <div class="small text-muted mt-2">
                        Transfer ke rekening tujuan lalu upload gambar bukti transaksi.
                    </div>
                </label>
            </div>
        </div>

        <div id="transfer-payment-fields" class="mt-4 d-none">
            <div class="alert alert-info rounded-4">
                <div class="fw-semibold mb-2">Rekening Tujuan</div>
                <div>Bank: {{ config('ferry_payment.bank_name') }}</div>
                <div>No. Rekening: {{ config('ferry_payment.account_number') }}</div>
                <div>Atas Nama: {{ config('ferry_payment.account_name') }}</div>
            </div>

            <label for="bukti_transfer" class="form-label fw-semibold">Bukti Transfer</label>
            <input
                type="file"
                name="bukti_transfer"
                id="bukti_transfer"
                class="form-control"
                accept="image/jpeg,image/png,image/webp"
            >
            <div class="form-text">Format JPG, JPEG, PNG, atau WEBP. Maksimal 5 MB.</div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const cash = document.getElementById('payment-cash');
    const transfer = document.getElementById('payment-transfer');
    const transferFields = document.getElementById('transfer-payment-fields');
    const proof = document.getElementById('bukti_transfer');

    function updatePaymentFields() {
        const isTransfer = transfer && transfer.checked;

        if (transferFields) {
            transferFields.classList.toggle('d-none', !isTransfer);
        }

        if (proof) {
            proof.required = Boolean(isTransfer);
        }
    }

    cash?.addEventListener('change', updatePaymentFields);
    transfer?.addEventListener('change', updatePaymentFields);
    updatePaymentFields();
});
</script>
