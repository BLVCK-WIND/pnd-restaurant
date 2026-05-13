@extends('layouts.manage')

@section('title', 'Chi tiết Order')

@section('content')
<div>

    {{-- ── Header ── --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('manage.orders.index') }}"
           class="text-gray-400 hover:text-gray-600 transition text-sm">← Quay lại</a>
        <h1 class="text-2xl font-bold text-gray-800">
            Order — {{ $order->table->name }}
            <span class="text-sm font-normal text-gray-400 ml-2">{{ $order->table->area->name }}</span>
        </h1>

        {{-- Status badge --}}
        @php
            $statusCfg = [
                'open'      => ['label' => 'Đang mở',       'class' => 'bg-green-100 text-green-700'],
                'paid'      => ['label' => 'Đã thanh toán',  'class' => 'bg-blue-100 text-blue-700'],
                'cancelled' => ['label' => 'Đã huỷ',         'class' => 'bg-gray-100 text-gray-500'],
            ];
            $sc = $statusCfg[$order->status] ?? $statusCfg['open'];
        @endphp
        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $sc['class'] }}">
            {{ $sc['label'] }}
        </span>

        {{-- Nút huỷ order — chỉ hiện khi open, nằm góc phải --}}
        @if($order->status === 'open')
            <div class="ml-auto">
                <form action="{{ route('manage.orders.cancel', $order) }}" method="POST"
                      onsubmit="return confirm('Huỷ order này? Toàn bộ món đã gọi sẽ bị xoá và bàn sẽ được trả về.')">
                    @csrf
                    <button type="submit"
                            class="px-4 py-2 rounded-xl text-sm font-semibold
                                   bg-red-50 text-red-600 hover:bg-red-100 border border-red-200 transition">
                        ✕ Huỷ order
                    </button>
                </form>
            </div>
        @endif
    </div>

    {{-- Flash (nếu redirect về đây) --}}
    @if(session('error'))
        <div class="mb-4 px-4 py-3 rounded-xl text-sm text-red-700 bg-red-50 border border-red-200">
            ❌ {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- ══ Cột trái — Món đã gọi ══ --}}
        <div class="bg-white rounded-2xl shadow p-6 flex flex-col">
            <h2 class="font-semibold text-gray-700 text-lg border-b pb-2 mb-4">
                🧾 Món đã gọi
            </h2>

            <div id="order-items" class="flex-1 space-y-3 mb-4">
                @forelse($order->orderItems as $item)
                    <div id="item-{{ $item->id }}"
                         class="flex items-center justify-between bg-gray-50 rounded-xl px-4 py-3">
                        <div>
                            <p class="font-medium text-gray-800">{{ $item->menuItem->name }}</p>
                            <p class="text-xs text-gray-400">{{ number_format($item->unit_price) }}đ / món</p>
                        </div>
                        <div class="flex items-center gap-2">
                            @if($order->status === 'open')
                                <button onclick="updateItem({{ $order->id }}, {{ $item->id }}, 'decrement')"
                                        class="w-7 h-7 rounded-full bg-gray-200 text-gray-600
                                               hover:bg-red-100 hover:text-red-600 transition font-bold">−</button>
                            @endif

                            <span id="qty-{{ $item->id }}"
                                  class="w-8 text-center font-semibold text-gray-800">
                                {{ $item->quantity }}
                            </span>

                            @if($order->status === 'open')
                                <button onclick="updateItem({{ $order->id }}, {{ $item->id }}, 'increment')"
                                        class="w-7 h-7 rounded-full bg-gray-200 text-gray-600
                                               hover:bg-green-100 hover:text-green-600 transition font-bold">+</button>
                                <button onclick="removeItem({{ $order->id }}, {{ $item->id }})"
                                        class="w-7 h-7 rounded-full bg-red-50 text-red-500
                                               hover:bg-red-100 transition text-xs">✕</button>
                            @endif

                            <span id="subtotal-{{ $item->id }}"
                                  class="w-24 text-right font-medium text-gray-700">
                                {{ number_format($item->quantity * $item->unit_price) }}đ
                            </span>
                        </div>
                    </div>
                @empty
                    <div id="empty-message" class="text-center text-gray-400 py-8">
                        Chưa có món nào — thêm món từ menu bên phải
                    </div>
                @endforelse
            </div>

            {{-- Tổng + Thanh toán / Thông tin --}}
            <div class="border-t pt-4">
                <div class="flex items-center justify-between mb-4">
                    <span class="font-semibold text-gray-700">Tổng cộng:</span>
                    <span id="total-price" class="text-xl font-bold" style="color: #c8622a;">
                        {{ number_format($order->orderItems->sum(fn($i) => $i->quantity * $i->unit_price)) }}đ
                    </span>
                </div>

                @if($order->status === 'open')
                    {{-- Thanh toán --}}
                    <form action="{{ route('manage.orders.pay', $order) }}" method="POST" class="space-y-3">
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

                @elseif($order->status === 'paid')
                    <div class="px-4 py-3 rounded-xl bg-blue-50 border border-blue-200">
                        <p class="text-blue-700 font-semibold text-center mb-2">✅ Đã thanh toán</p>
                        @if($order->payment)
                            @php
                                $methodLabel = ['cash' => '💵 Tiền mặt', 'card' => '💳 Thẻ', 'transfer' => '📱 Chuyển khoản'];
                            @endphp
                            <div class="space-y-1 text-sm text-blue-600">
                                <div class="flex justify-between">
                                    <span>Phương thức:</span>
                                    <span class="font-medium">{{ $methodLabel[$order->payment->method] ?? '—' }}</span>
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
                        @endif
                    </div>

                @else
                    {{-- Cancelled --}}
                    <div class="px-4 py-3 rounded-xl text-center bg-red-50 border border-red-200">
                        <p class="text-red-600 font-semibold">❌ Order đã bị huỷ</p>
                        <p class="text-xs text-red-400 mt-1">Toàn bộ món đã được xoá</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- ══ Cột phải — Thực đơn ══ --}}
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

                <div class="space-y-4 max-h-[480px] overflow-y-auto pr-1">
                    @foreach($categories as $category)
                        <div class="category-group">
                            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">
                                {{ $category->name }}
                            </h3>
                            <div class="space-y-2">
                                @foreach($category->menuItems as $menuItem)
                                    <div class="menu-item flex items-center justify-between
                                                bg-gray-50 rounded-xl px-3 py-2"
                                         data-name="{{ strtolower($menuItem->name) }}">
                                        <div>
                                            <p class="text-sm font-medium text-gray-800">{{ $menuItem->name }}</p>
                                            <p class="text-xs text-orange-500 font-medium">{{ number_format($menuItem->price) }}đ</p>
                                        </div>
                                        <button onclick="addItem({{ $order->id }}, {{ $menuItem->id }})"
                                                class="w-8 h-8 rounded-full text-white font-bold transition hover:scale-110"
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
@endsection

@push('scripts')
<script>
    const csrfToken = '{{ csrf_token() }}';

    function formatMoney(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount) + 'đ';
    }

    function updateTotal(total) {
        document.getElementById('total-price').textContent = formatMoney(total);
    }

    async function addItem(orderId, menuItemId) {
        const res = await fetch(`/manage/orders/${orderId}/items`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ menu_item_id: menuItemId }),
        });
        const data = await res.json();
        if (!res.ok) { alert(data.error); return; }

        const existingItem = document.getElementById(`item-${data.item.id}`);
        if (existingItem) {
            document.getElementById(`qty-${data.item.id}`).textContent     = data.item.quantity;
            document.getElementById(`subtotal-${data.item.id}`).textContent = formatMoney(data.item.subtotal);
        } else {
            const emptyMsg = document.getElementById('empty-message');
            if (emptyMsg) emptyMsg.remove();
            document.getElementById('order-items').insertAdjacentHTML('beforeend', `
                <div id="item-${data.item.id}" class="flex items-center justify-between bg-gray-50 rounded-xl px-4 py-3">
                    <div>
                        <p class="font-medium text-gray-800">${data.item.name}</p>
                        <p class="text-xs text-gray-400">${formatMoney(data.item.unit_price)} / món</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="updateItem(${orderId}, ${data.item.id}, 'decrement')"
                                class="w-7 h-7 rounded-full bg-gray-200 text-gray-600 hover:bg-red-100 hover:text-red-600 transition font-bold">−</button>
                        <span id="qty-${data.item.id}" class="w-8 text-center font-semibold text-gray-800">${data.item.quantity}</span>
                        <button onclick="updateItem(${orderId}, ${data.item.id}, 'increment')"
                                class="w-7 h-7 rounded-full bg-gray-200 text-gray-600 hover:bg-green-100 hover:text-green-600 transition font-bold">+</button>
                        <button onclick="removeItem(${orderId}, ${data.item.id})"
                                class="w-7 h-7 rounded-full bg-red-50 text-red-500 hover:bg-red-100 transition text-xs">✕</button>
                        <span id="subtotal-${data.item.id}" class="w-24 text-right font-medium text-gray-700">${formatMoney(data.item.subtotal)}</span>
                    </div>
                </div>
            `);
        }
        updateTotal(data.total);
    }

    async function updateItem(orderId, itemId, action) {
        const res = await fetch(`/manage/orders/${orderId}/items/${itemId}`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ action }),
        });
        const data = await res.json();
        if (!res.ok) { alert(data.error); return; }

        if (data.deleted) {
            document.getElementById(`item-${itemId}`).remove();
            if (!document.getElementById('order-items').children.length) {
                document.getElementById('order-items').innerHTML =
                    `<div id="empty-message" class="text-center text-gray-400 py-8">Chưa có món nào — thêm món từ menu bên phải</div>`;
            }
        } else {
            document.getElementById(`qty-${itemId}`).textContent      = data.quantity;
            document.getElementById(`subtotal-${itemId}`).textContent = formatMoney(data.subtotal);
        }
        updateTotal(data.total);
    }

    async function removeItem(orderId, itemId) {
        if (!confirm('Xoá món này?')) return;
        const res = await fetch(`/manage/orders/${orderId}/items/${itemId}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken },
        });
        const data = await res.json();
        if (!res.ok) { alert(data.error); return; }

        document.getElementById(`item-${itemId}`).remove();
        if (!document.getElementById('order-items').children.length) {
            document.getElementById('order-items').innerHTML =
                `<div id="empty-message" class="text-center text-gray-400 py-8">Chưa có món nào — thêm món từ menu bên phải</div>`;
        }
        updateTotal(data.total);
    }

    function filterMenu() {
        const kw = document.getElementById('menu-search').value.toLowerCase();
        document.querySelectorAll('.menu-item').forEach(el => {
            el.style.display = el.dataset.name.includes(kw) ? 'flex' : 'none';
        });
    }
</script>
@endpush