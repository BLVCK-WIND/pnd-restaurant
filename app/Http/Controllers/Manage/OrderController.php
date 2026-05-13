<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Table;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $today       = Carbon::today();
        $minDate     = $today->copy()->subWeek()->startOfWeek(); // Tuần trước
        $maxDate     = $today->copy();                            // Tối đa hôm nay (order chỉ có quá khứ/hiện tại)

        // Ngày đang xem — mặc định hôm nay
        $selectedDate = $request->filled('date')
            ? Carbon::parse($request->date)->startOfDay()
            : $today->copy();

        if ($selectedDate->lt($minDate)) $selectedDate = $minDate->copy();
        if ($selectedDate->gt($maxDate)) $selectedDate = $maxDate->copy();

        $prevDate  = $selectedDate->copy()->subDay();
        $nextDate  = $selectedDate->copy()->addDay();
        $canGoPrev = $prevDate->gte($minDate);
        $canGoNext = $nextDate->lte($maxDate);

        // Mặc định tab = open
        $activeStatus = $request->input('status', 'open');

        // Đếm số lượng theo status trong ngày
        $statusCounts = Order::whereDate('created_at', $selectedDate)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // Danh sách bàn để filter (chỉ bàn có order trong ngày)
        $tables = Table::whereHas('orders', function ($q) use ($selectedDate) {
            $q->whereDate('created_at', $selectedDate);
        })->with('area')->orderBy('name')->get();

        $orders = Order::with(['booking', 'table.area', 'staff', 'orderItems'])
            ->whereDate('created_at', $selectedDate)
            ->where('status', $activeStatus)
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->whereHas('table', fn($t) =>
                    $t->where('name', 'like', '%' . $request->search . '%')
                )->orWhereHas('booking', fn($b) =>
                    $b->where('guest_name', 'like', '%' . $request->search . '%')
                );
            })
            ->when($request->filled('table_id'), fn($q) =>
                $q->where('table_id', $request->table_id)
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('manage.orders.index', compact(
            'orders',
            'activeStatus',
            'statusCounts',
            'selectedDate',
            'prevDate',
            'nextDate',
            'canGoPrev',
            'canGoNext',
            'today',
            'minDate',
            'maxDate',
            'tables',
        ));
    }

    public function show(Order $order)
    {
        $order->load([
            'table.area',
            'booking',
            'staff',
            'orderItems.menuItem.category',
            'payment.staff',
        ]);

        $categories = Category::with(['menuItems' => function ($query) {
            $query->where('status', 'active');
        }])
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->get();

        return view('manage.orders.show', compact('order', 'categories'));
    }

    // ── Huỷ order ──────────────────────────────────────────────
    public function cancel(Order $order)
    {
        if ($order->status !== 'open') {
            return back()->with('error', 'Chỉ có thể huỷ order đang mở');
        }

        // Xoá toàn bộ order items
        $order->orderItems()->delete();

        // Cập nhật order → cancelled
        $order->update(['status' => 'cancelled']);

        // Cập nhật booking liên quan → completed (bàn trống trở lại)
        if ($order->booking) {
            $order->booking->update(['status' => 'completed']);
            $order->booking->addLog('completed', Auth::user()->id, 'Huỷ order — bàn trả về');
        }

        return redirect()
            ->route('manage.orders.index')
            ->with('success', 'Đã huỷ order và trả bàn thành công');
    }

    // ── AJAX: Thêm món ─────────────────────────────────────────
    public function addItem(Request $request, Order $order)
    {
        if ($order->status !== 'open') {
            return response()->json(['error' => 'Order này không thể thêm món'], 400);
        }

        $data     = $request->validate(['menu_item_id' => 'required|exists:menu_items,id']);
        $menuItem = MenuItem::findOrFail($data['menu_item_id']);

        $existingItem = $order->orderItems()
            ->where('menu_item_id', $menuItem->id)
            ->first();

        if ($existingItem) {
            $existingItem->increment('quantity');
            $item = $existingItem->fresh();
        } else {
            $item = $order->orderItems()->create([
                'menu_item_id' => $menuItem->id,
                'quantity'     => 1,
                'unit_price'   => $menuItem->price,
            ]);
        }

        return response()->json([
            'message' => 'Thêm món thành công',
            'item'    => [
                'id'         => $item->id,
                'name'       => $menuItem->name,
                'quantity'   => $item->quantity,
                'unit_price' => $item->unit_price,
                'subtotal'   => $item->quantity * $item->unit_price,
            ],
            'total' => $order->orderItems()->sum(\DB::raw('quantity * unit_price')),
        ]);
    }

    // ── AJAX: Tăng/giảm số lượng ───────────────────────────────
    public function updateItem(Request $request, Order $order, OrderItem $item)
    {
        if ($order->status !== 'open') {
            return response()->json(['error' => 'Order này không thể chỉnh sửa'], 400);
        }

        $data = $request->validate(['action' => 'required|in:increment,decrement']);

        if ($data['action'] === 'increment') {
            $item->increment('quantity');
        } else {
            if ($item->quantity <= 1) {
                $item->delete();
                return response()->json([
                    'message' => 'Đã xoá món',
                    'deleted' => true,
                    'total'   => $order->orderItems()->sum(\DB::raw('quantity * unit_price')),
                ]);
            }
            $item->decrement('quantity');
        }

        $item->refresh();

        return response()->json([
            'message'  => 'Cập nhật thành công',
            'deleted'  => false,
            'quantity' => $item->quantity,
            'subtotal' => $item->quantity * $item->unit_price,
            'total'    => $order->orderItems()->sum(\DB::raw('quantity * unit_price')),
        ]);
    }

    // ── AJAX: Xoá món ──────────────────────────────────────────
    public function removeItem(Order $order, OrderItem $item)
    {
        if ($order->status !== 'open') {
            return response()->json(['error' => 'Order này không thể chỉnh sửa'], 400);
        }

        $item->delete();

        return response()->json([
            'message' => 'Đã xoá món',
            'total'   => $order->orderItems()->sum(\DB::raw('quantity * unit_price')),
        ]);
    }

    // ── Thanh toán ─────────────────────────────────────────────
    public function pay(Request $request, Order $order)
    {
        if ($order->status !== 'open') {
            return back()->with('error', 'Order này không thể thanh toán');
        }

        if ($order->orderItems()->count() === 0) {
            return back()->with('error', 'Order chưa có món nào');
        }

        $data   = $request->validate(['method' => 'required|in:cash,card,transfer']);
        $amount = $order->orderItems()->sum(\DB::raw('quantity * unit_price'));

        $order->update(['status' => 'paid']);

        Payment::create([
            'order_id' => $order->id,
            'staff_id' => Auth::user()->id,
            'amount'   => $amount,
            'method'   => $data['method'],
            'paid_at'  => now(),
        ]);

        if ($order->booking) {
            $order->booking->update(['status' => 'completed']);
            $order->booking->addLog('completed', Auth::user()->id, 'Thanh toán hoàn tất');
        }

        return redirect()
            ->route('manage.orders.index')
            ->with('success', 'Thanh toán thành công!');
    }
}