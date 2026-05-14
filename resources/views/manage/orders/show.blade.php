@extends('layouts.manage')

@section('title', 'Chi tiết Order')

@section('content')
<div>

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('manage.orders.index') }}"
           class="text-gray-400 hover:text-gray-600 transition">← Quay lại</a>
        <h1 class="text-2xl font-bold text-gray-800">
            Order — {{ $order->table->name }}
            <span class="text-sm font-normal text-gray-400 ml-2">
                {{ $order->table->area->name }}
            </span>
        </h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Cột trái — Món đã gọi --}}
        <div class="bg-white rounded-2xl shadow p-6 flex flex-col">
            <h2 class="font-semibold text-gray-700 text-lg border-b pb-2 mb-4">
                🧾 Món đã gọi
            </h2>

            <div id="order-items" class="flex-1 space-y-3 mb-4">
                @forelse($order->orderItems as $item)
                    <div id="item-{{ $item->id }}"
                         class="bg-gray-50 rounded-xl px-4 py-3">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex-1">
                                <p class="font-medium text-gray-800">
                                    {{ $item->menuItem->name }}
                                </p>
                                {{-- Options đã chọn --}}
                                @if($item->orderItemOptions->count() > 0)
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        {{ $item->orderItemOptions->map(fn($o) => $o->optionValue->name)->join(', ') }}
                                    </p>
                                @endif
                                <p class="text-xs text-gray-400 mt-0.5">
                                    {{ number_format($item->unit_price) }}đ
                                    @if($item->orderItemOptions->sum('extra_price') > 0)
                                        + {{ number_format($item->orderItemOptions->sum('extra_price')) }}đ options
                                    @endif
                                </p>
                            </div>

                            <div class="flex items-center gap-2">
                                @if($order->status === 'open')
                                    <button onclick="updateItem({{ $order->id }}, {{ $item->id }}, 'decrement')"
                                            class="w-7 h-7 rounded-full bg-gray-200 text-gray-600
                                                   hover:bg-red-100 hover:text-red-600 transition font-bold">
                                        −
                                    </button>
                                @endif

                                <span id="qty-{{ $item->id }}"
                                      class="w-8 text-center font-semibold text-gray-800">
                                    {{ $item->quantity }}
                                </span>

                                @if($order->status === 'open')
                                    <button onclick="updateItem({{ $order->id }}, {{ $item->id }}, 'increment')"
                                            class="w-7 h-7 rounded-full bg-gray-200 text-gray-600
                                                   hover:bg-green-100 hover:text-green-600 transition font-bold">
                                        +
                                    </button>
                                    <button onclick="removeItem({{ $order->id }}, {{ $item->id }})"
                                            class="w-7 h-7 rounded-full bg-red-50 text-red-500
                                                   hover:bg-red-100 transition text-xs">
                                        ✕
                                    </button>
                                @endif

                                <span id="subtotal-{{ $item->id }}"
                                      class="w-24 text-right font-medium text-gray-700">
                                    {{ number_format($item->subtotal) }}đ
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div id="empty-message" class="text-center text-gray-400 py-8">
                        Chưa có món nào — thêm món từ menu bên phải
                    </div>
                @endforelse
            </div>

            {{-- Tổng tiền --}}
            <div class="border-t pt-4">
                <div class="flex items-center justify-between">
                    <span class="font-semibold text-gray-700">Tổng cộng:</span>
                    <span id="total-price" class="text-xl font-bold" style="color: #c8622a;">
                        {{ number_format($order->total) }}đ
                    </span>
                </div>

                @if($order->status === 'open')
                    <div class="mt-4">
                        <form action="{{ route('manage.orders.pay', $order) }}"
                              method="POST" class="space-y-3">
                            @csrf
                            <select name="method"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200
                                           focus:outline-none focus:border-orange-400 bg-gray-50 text-sm">
                                <option value="cash">💵 Tiền mặt</option>
                                <option value="card">💳 Thẻ</option>
                                <option value="transfer">📱 Chuyển khoản</option>
                            </select>
                            <button type="submit"
                                    onclick="return confirm('Xác nhận thanh toán?')"
                                    class="w-full py-3 rounded-xl text-white font-semibold
                                           transition hover:-translate-y-0.5"
                                    style="background: linear-gradient(135deg, #c8622a, #f5a623);">
                                💰 Thanh toán
                            </button>
                        </form>
                    </div>
                @else
                    <div class="mt-4 px-4 py-3 rounded-xl text-center
                                {{ $order->status === 'paid' ? 'bg-blue-50' : 'bg-gray-50' }}">
                        @if($order->status === 'paid' && $order->payment)
                            <p class="text-blue-700 font-semibold mb-2">✅ Đã thanh toán</p>
                            <div class="space-y-1 text-sm text-blue-600">
                                <div class="flex justify-between">
                                    <span>Phương thức:</span>
                                    <span class="font-medium">
                                        @php
                                            $methods = ['cash' => '💵 Tiền mặt', 'card' => '💳 Thẻ', 'transfer' => '📱 Chuyển khoản'];
                                        @endphp
                                        {{ $methods[$order->payment->method] ?? '—' }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Số tiền:</span>
                                    <span class="font-medium">{{ number_format($order->payment->amount) }}đ</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Thời gian:</span>
                                    <span class="font-medium">{{ $order->payment->paid_at->format('H:i d/m/Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Thu ngân:</span>
                                    <span class="font-medium">{{ $order->payment->staff->name ?? '—' }}</span>
                                </div>
                            </div>
                        @else
                            <p class="text-gray-500">❌ Đã huỷ</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- Cột phải — Menu --}}
        <div class="bg-white rounded-2xl shadow p-6">
            <h2 class="font-semibold text-gray-700 text-lg border-b pb-2 mb-4">
                🍽️ Thực đơn
            </h2>

            @if($order->status === 'open')
                <input type="text" id="menu-search"
                       placeholder="🔍 Tìm món ăn..."
                       oninput="filterMenu()"
                       class="w-full px-4 py-2 rounded-xl border border-gray-200
                              focus:outline-none focus:border-orange-400 bg-gray-50 text-sm mb-4">

                <div class="space-y-4 max-h-[500px] overflow-y-auto pr-1">
                    @foreach($categories as $category)
                        {{-- Lấy tất cả option của category --}}
                        @php
                            $categoryOptions = $category->optionGroups;
                        @endphp
                        <div class="category-group">
                            <h3 class="text-xs font-semibold text-gray-400 uppercase
                                       tracking-wider mb-2">
                                {{ $category->name }}
                            </h3>
                            <div class="space-y-2">
                                @foreach($category->menuItems as $menuItem)
                                    @php
                                        // Merge option của category + option riêng của món
                                        $allOptions = $categoryOptions
                                            ->merge($menuItem->optionGroups)
                                            ->unique('id');
                                    @endphp
                                    <div class="menu-item flex items-center justify-between
                                                bg-gray-50 rounded-xl px-3 py-2"
                                         data-name="{{ strtolower($menuItem->name) }}">
                                        <div>
                                            <p class="text-sm font-medium text-gray-800">
                                                {{ $menuItem->name }}
                                            </p>
                                            <p class="text-xs text-orange-500 font-medium">
                                                {{ number_format($menuItem->price) }}đ
                                            </p>
                                        </div>
                                        <button onclick="openOptionPopup({{ $menuItem->id }}, '{{ addslashes($menuItem->name) }}', {{ $menuItem->price }}, {{ $allOptions->toJson() }})"
                                                class="w-8 h-8 rounded-full text-white font-bold
                                                       transition hover:scale-110"
                                                style="background: linear-gradient(135deg, #c8622a, #f5a623);">
                                            +
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center text-gray-400 py-8">
                    Order đã đóng — không thể thêm món
                </div>
            @endif
        </div>

    </div>
</div>

{{-- Popup chọn Options --}}
<div id="option-overlay"
     class="fixed inset-0 bg-black bg-opacity-40 z-50 hidden items-center justify-center"
     onclick="closeOptionPopup()">

    <div class="bg-white rounded-2xl shadow-xl p-6 w-96 max-h-[90vh] overflow-y-auto mx-4"
         onclick="event.stopPropagation()">

        <h3 class="font-semibold text-gray-800 text-lg mb-1" id="popup-item-name"></h3>
        <p class="text-orange-500 font-medium text-sm mb-4" id="popup-item-price"></p>

        <div id="popup-options" class="space-y-4 mb-5"></div>

        {{-- Ghi chú --}}
        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú</label>
            <input type="text" id="popup-note"
                   placeholder="VD: không hành, ít cay..."
                   class="w-full px-4 py-2 rounded-xl border border-gray-200
                          focus:outline-none focus:border-orange-400 bg-gray-50 text-sm">
        </div>

        {{-- Tổng phụ thu --}}
        <div class="flex justify-between text-sm font-medium text-gray-700 mb-4
                    border-t pt-3">
            <span>Tổng:</span>
            <span id="popup-total" class="text-orange-500"></span>
        </div>

        <div class="flex gap-3">
            <button onclick="confirmAddItem()"
                    class="flex-1 py-2.5 rounded-xl text-white text-sm font-semibold transition"
                    style="background: linear-gradient(135deg, #c8622a, #f5a623);">
                Thêm vào order
            </button>
            <button onclick="closeOptionPopup()"
                    class="flex-1 py-2.5 rounded-xl text-sm font-semibold
                           bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                Huỷ
            </button>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
    const csrfToken = '{{ csrf_token() }}';
    const orderId   = {{ $order->id }};

    let currentMenuItemId = null;
    let currentBasePrice  = 0;
    let currentOptions    = [];

    // Format tiền
    function formatMoney(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount) + 'đ';
    }

    // Cập nhật tổng tiền
    function updateTotal(total) {
        document.getElementById('total-price').textContent = formatMoney(total);
    }

    // Mở popup chọn options
    function openOptionPopup(menuItemId, name, price, options) {
        currentMenuItemId = menuItemId;
        currentBasePrice  = price;
        currentOptions    = options;

        document.getElementById('popup-item-name').textContent = name;
        document.getElementById('popup-item-price').textContent = formatMoney(price);
        document.getElementById('popup-note').value = '';

        const container = document.getElementById('popup-options');
        container.innerHTML = '';

        if (options.length === 0) {
            container.innerHTML = '<p class="text-sm text-gray-400 text-center">Không có tuỳ chọn</p>';
        } else {
            options.forEach(group => {
                const groupEl = document.createElement('div');
                groupEl.innerHTML = `
                    <p class="text-sm font-semibold text-gray-700 mb-2">
                        ${group.name}
                        ${group.is_required ? '<span class="text-red-500 text-xs ml-1">*bắt buộc</span>' : ''}
                    </p>
                    <div class="space-y-1" id="group-${group.id}"></div>
                `;
                container.appendChild(groupEl);

                const valuesContainer = groupEl.querySelector(`#group-${group.id}`);
                group.option_values.forEach(value => {
                    const inputType = group.is_multiple ? 'checkbox' : 'radio';
                    const inputName = `option_group_${group.id}`;

                    const label = document.createElement('label');
                    label.className = 'flex items-center gap-2 cursor-pointer px-3 py-2 rounded-xl hover:bg-gray-50 transition';
                    label.innerHTML = `
                        <input type="${inputType}"
                               name="${inputName}"
                               value="${value.id}"
                               data-price="${value.extra_price}"
                               onchange="recalcPopupTotal()"
                               class="w-4 h-4 accent-orange-500">
                        <span class="text-sm text-gray-700 flex-1">${value.name}</span>
                        ${value.extra_price > 0
                            ? `<span class="text-xs text-orange-500">+${formatMoney(value.extra_price)}</span>`
                            : '<span class="text-xs text-gray-400">Miễn phí</span>'}
                    `;
                    valuesContainer.appendChild(label);
                });
            });
        }

        recalcPopupTotal();

        document.getElementById('option-overlay').classList.remove('hidden');
        document.getElementById('option-overlay').classList.add('flex');
    }

    // Đóng popup
    function closeOptionPopup() {
        document.getElementById('option-overlay').classList.add('hidden');
        document.getElementById('option-overlay').classList.remove('flex');
    }

    // Tính lại tổng trong popup
    function recalcPopupTotal() {
        let extraTotal = 0;
        document.querySelectorAll('#popup-options input:checked').forEach(input => {
            extraTotal += parseInt(input.dataset.price || 0);
        });
        document.getElementById('popup-total').textContent =
            formatMoney(currentBasePrice + extraTotal);
    }

    // Xác nhận thêm món
    async function confirmAddItem() {
        // Kiểm tra các group bắt buộc đã chọn chưa
        let valid = true;
        currentOptions.forEach(group => {
            if (group.is_required) {
                const checked = document.querySelectorAll(
                    `input[name="option_group_${group.id}"]:checked`
                ).length;
                if (checked === 0) {
                    alert(`Vui lòng chọn "${group.name}"`);
                    valid = false;
                }
            }
        });
        if (!valid) return;

        // Lấy các option đã chọn
        const selectedOptions = [...document.querySelectorAll('#popup-options input:checked')]
            .map(input => parseInt(input.value));

        const note = document.getElementById('popup-note').value;

        try {
            const res = await fetch(`/manage/orders/${orderId}/items`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    menu_item_id: currentMenuItemId,
                    options: selectedOptions,
                    note,
                }),
            });

            const data = await res.json();
            if (!res.ok) { alert(data.error); return; }

            // Thêm row vào danh sách món
            const emptyMsg = document.getElementById('empty-message');
            if (emptyMsg) emptyMsg.remove();

            const container = document.getElementById('order-items');
            container.insertAdjacentHTML('beforeend', `
                <div id="item-${data.item.id}" class="bg-gray-50 rounded-xl px-4 py-3">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex-1">
                            <p class="font-medium text-gray-800">${data.item.name}</p>
                            ${data.item.options
                                ? `<p class="text-xs text-gray-400 mt-0.5">${data.item.options}</p>`
                                : ''}
                            <p class="text-xs text-gray-400 mt-0.5">
                                ${formatMoney(data.item.unit_price)}
                                ${data.item.extra_price > 0
                                    ? ` + ${formatMoney(data.item.extra_price)} options`
                                    : ''}
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button onclick="updateItem(${orderId}, ${data.item.id}, 'decrement')"
                                    class="w-7 h-7 rounded-full bg-gray-200 text-gray-600
                                           hover:bg-red-100 hover:text-red-600 transition font-bold">−</button>
                            <span id="qty-${data.item.id}"
                                  class="w-8 text-center font-semibold text-gray-800">
                                ${data.item.quantity}
                            </span>
                            <button onclick="updateItem(${orderId}, ${data.item.id}, 'increment')"
                                    class="w-7 h-7 rounded-full bg-gray-200 text-gray-600
                                           hover:bg-green-100 hover:text-green-600 transition font-bold">+</button>
                            <button onclick="removeItem(${orderId}, ${data.item.id})"
                                    class="w-7 h-7 rounded-full bg-red-50 text-red-500
                                           hover:bg-red-100 transition text-xs">✕</button>
                            <span id="subtotal-${data.item.id}"
                                  class="w-24 text-right font-medium text-gray-700">
                                ${formatMoney(data.item.subtotal)}
                            </span>
                        </div>
                    </div>
                </div>
            `);

            updateTotal(data.total);
            closeOptionPopup();

        } catch (err) {
            alert('Lỗi kết nối server');
            console.error(err);
        }
    }

    // Tăng/giảm số lượng
    async function updateItem(orderId, itemId, action) {
        const res = await fetch(`/manage/orders/${orderId}/items/${itemId}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({ action }),
        });

        const data = await res.json();
        if (!res.ok) { alert(data.error); return; }

        if (data.deleted) {
            document.getElementById(`item-${itemId}`).remove();
            if (document.getElementById('order-items').children.length === 0) {
                document.getElementById('order-items').innerHTML = `
                    <div id="empty-message" class="text-center text-gray-400 py-8">
                        Chưa có món nào — thêm món từ menu bên phải
                    </div>`;
            }
        } else {
            document.getElementById(`qty-${itemId}`).textContent      = data.quantity;
            document.getElementById(`subtotal-${itemId}`).textContent = formatMoney(data.subtotal);
        }

        updateTotal(data.total);
    }

    // Xoá món
    async function removeItem(orderId, itemId) {
        if (!confirm('Xoá món này?')) return;

        const res = await fetch(`/manage/orders/${orderId}/items/${itemId}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken },
        });

        const data = await res.json();
        if (!res.ok) { alert(data.error); return; }

        document.getElementById(`item-${itemId}`).remove();

        if (document.getElementById('order-items').children.length === 0) {
            document.getElementById('order-items').innerHTML = `
                <div id="empty-message" class="text-center text-gray-400 py-8">
                    Chưa có món nào — thêm món từ menu bên phải
                </div>`;
        }

        updateTotal(data.total);
    }

    // Search món
    function filterMenu() {
        const keyword = document.getElementById('menu-search').value.toLowerCase();
        document.querySelectorAll('.menu-item').forEach(item => {
            item.style.display = item.dataset.name.includes(keyword) ? 'flex' : 'none';
        });
    }
</script>
@endpush