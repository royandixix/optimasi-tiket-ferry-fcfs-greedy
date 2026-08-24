@php
    use App\Support\FerryTariff;

    $currentBooking = $pemesanan ?? null;
    $selectedTarif = old(
        'jenis_tarif',
        $currentBooking?->jenis_tarif ?? FerryTariff::DEFAULT_CODE
    );
    $selectedQuantity = max(
        (int) old('jumlah_tiket', $currentBooking?->jumlah_tiket ?? 1),
        1
    );
    $tarifItems = FerryTariff::all();
    $initialPricing = FerryTariff::calculate($selectedTarif, $selectedQuantity);
@endphp

<div class="row g-3 align-items-stretch" data-tariff-checkout>
    <div class="col-12 col-lg-6">
        <label for="jenis_tarif" class="form-label fw-semibold">
            Pilihan Harga / Jenis Muatan
        </label>

        <select
            name="jenis_tarif"
            id="jenis_tarif"
            class="form-select form-select-lg @error('jenis_tarif') is-invalid @enderror"
            required
        >
            @foreach ($tarifItems as $value => $item)
                @php
                    $price = max((int) ($item['price'] ?? 0), 0);
                    $group = (string) ($item['group'] ?? 'Tarif');
                    $label = (string) ($item['label'] ?? $value);
                    $description = (string) ($item['description'] ?? $label);
                    $unit = (string) ($item['unit'] ?? 'unit');
                @endphp

                <option
                    value="{{ $value }}"
                    data-price="{{ $price }}"
                    data-description="{{ $description }}"
                    data-unit="{{ $unit }}"
                    @selected($selectedTarif === $value)
                >
                    {{ $group }} — {{ $label }} — {{ FerryTariff::rupiah($price) }}
                </option>
            @endforeach
        </select>

        @error('jenis_tarif')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <small class="text-muted">
            Tarif rute {{ FerryTariff::routeName() }}. Harga ditentukan saat checkout.
        </small>

        <div class="alert alert-primary py-2 px-3 mt-2 mb-0" data-tariff-description>
            {{ $initialPricing['tarif_label'] }}
        </div>
    </div>

    <div class="col-12 col-lg-3">
        <label for="jumlah_tiket" class="form-label fw-semibold">
            Jumlah Tiket / Unit
        </label>

        <div class="input-group input-group-lg">
            <button
                type="button"
                class="btn btn-outline-secondary"
                data-quantity-minus
                aria-label="Kurangi jumlah"
            >−</button>

            <input
                type="number"
                name="jumlah_tiket"
                id="jumlah_tiket"
                value="{{ $selectedQuantity }}"
                class="form-control text-center @error('jumlah_tiket') is-invalid @enderror"
                min="1"
                step="1"
                inputmode="numeric"
                required
            >

            <button
                type="button"
                class="btn btn-outline-secondary"
                data-quantity-plus
                aria-label="Tambah jumlah"
            >+</button>

            <span class="input-group-text" data-tariff-unit>
                {{ $initialPricing['satuan'] ?? 'unit' }}
            </span>
        </div>

        <small id="capacity-helper" class="text-muted">
            Total otomatis bertambah sesuai jumlah.
        </small>

        @error('jumlah_tiket')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-lg-3">
        <label class="form-label fw-semibold">Ringkasan Checkout</label>

        <div class="border border-success bg-success-subtle rounded-4 p-3 h-100">
            <small class="text-muted d-block" data-tariff-selected-label>
                {{ $initialPricing['tarif_label'] }}
            </small>

            <div class="small mt-2">
                <span data-price-unit>
                    {{ FerryTariff::rupiah($initialPricing['harga_satuan']) }}
                </span>
                ×
                <span data-price-quantity>
                    {{ $initialPricing['jumlah_tiket'] }}
                </span>
            </div>

            <strong class="text-success fs-5 d-block mt-1" data-price-total>
                {{ FerryTariff::rupiah($initialPricing['total_harga']) }}
            </strong>
        </div>
    </div>
</div>

@once
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-tariff-checkout]').forEach(function (root) {
                const select = root.querySelector('#jenis_tarif');
                const quantityInput = root.querySelector('#jumlah_tiket');
                const description = root.querySelector('[data-tariff-description]');
                const selectedLabel = root.querySelector('[data-tariff-selected-label]');
                const unitLabel = root.querySelector('[data-tariff-unit]');
                const priceUnit = root.querySelector('[data-price-unit]');
                const priceQuantity = root.querySelector('[data-price-quantity]');
                const priceTotal = root.querySelector('[data-price-total]');
                const minusButton = root.querySelector('[data-quantity-minus]');
                const plusButton = root.querySelector('[data-quantity-plus]');

                if (! select || ! quantityInput) {
                    return;
                }

                const rupiah = new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    maximumFractionDigits: 0,
                });

                const normalizeQuantity = function () {
                    const max = Number.parseInt(quantityInput.max || '0', 10);
                    let quantity = Math.max(
                        Number.parseInt(quantityInput.value || '1', 10) || 1,
                        1
                    );

                    if (max > 0) {
                        quantity = Math.min(quantity, max);
                    }

                    quantityInput.value = quantity;
                    return quantity;
                };

                const updatePrice = function () {
                    const option = select.options[select.selectedIndex];

                    if (! option) {
                        return;
                    }

                    const price = Math.max(Number(option.dataset.price || 0), 0);
                    const quantity = normalizeQuantity();
                    const label = option.dataset.description || option.textContent.trim();
                    const unit = option.dataset.unit || 'unit';

                    description.textContent = label;
                    selectedLabel.textContent = label;
                    unitLabel.textContent = unit;
                    priceUnit.textContent = rupiah.format(price);
                    priceQuantity.textContent = quantity;
                    priceTotal.textContent = rupiah.format(price * quantity);
                };

                minusButton?.addEventListener('click', function () {
                    quantityInput.value = Math.max(normalizeQuantity() - 1, 1);
                    quantityInput.dispatchEvent(new Event('input', { bubbles: true }));
                });

                plusButton?.addEventListener('click', function () {
                    quantityInput.value = normalizeQuantity() + 1;
                    quantityInput.dispatchEvent(new Event('input', { bubbles: true }));
                });

                select.addEventListener('change', updatePrice);
                quantityInput.addEventListener('input', updatePrice);
                quantityInput.addEventListener('change', updatePrice);
                updatePrice();
            });
        });
    </script>
@endonce
