<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ShopController extends Controller
{
    private function getCartItems(Request $request): array
    {
        $rawCart = $request->session()->get('cart', []);
        if (! is_array($rawCart) || empty($rawCart)) {
            return [[], 0, 0];
        }

        $products = Product::whereIn('product_id', array_keys($rawCart))->get()->keyBy('product_id');
        $items = [];
        $subtotal = 0;
        $totalCount = 0;

        foreach ($rawCart as $productId => $qty) {
            $product = $products->get((int) $productId);
            $qty = max(1, (int) $qty);
            if (! $product) {
                continue;
            }
            $lineSubtotal = (float) $product->price * $qty;
            $items[] = [
                'product' => $product,
                'quantity' => $qty,
                'subtotal' => $lineSubtotal,
            ];
            $subtotal += $lineSubtotal;
            $totalCount += $qty;
        }

        return [$items, $subtotal, $totalCount];
    }

    public function index(Request $request)
    {
        $products = Product::orderBy('product_id')->get();
        [, , $cartCount] = $this->getCartItems($request);

        return view('shop.index', compact('products', 'cartCount'));
    }

    public function cart(Request $request)
    {
        [$items, $subtotal, $cartCount] = $this->getCartItems($request);

        return view('shop.cart', compact('items', 'subtotal', 'cartCount'));
    }

    public function addToCart(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:product,product_id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:99'],
        ]);

        $product = Product::findOrFail($data['product_id']);
        $addQty = (int) ($data['quantity'] ?? 1);

        $cart = $request->session()->get('cart', []);
        $currentQty = (int) ($cart[$product->product_id] ?? 0);
        $newQty = $currentQty + $addQty;

        if ($product->stock < $newQty) {
            return back()->with('error', "Stok {$product->product_name} hanya tersisa {$product->stock}.");
        }

        $cart[$product->product_id] = $newQty;
        $request->session()->put('cart', $cart);

        return back()->with('success', "{$product->product_name} berhasil dimasukkan ke keranjang 🛒");
    }

    public function updateCart(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:product,product_id'],
            'action' => ['required', 'in:increase,decrease,remove'],
        ]);

        $cart = $request->session()->get('cart', []);
        $id = (int) $data['product_id'];

        if (isset($cart[$id])) {
            if ($data['action'] === 'increase') {
                $product = Product::find($id);
                if ($product && $cart[$id] + 1 <= $product->stock) {
                    $cart[$id]++;
                } else {
                    return back()->with('error', 'Jumlah mencapai batas stok tersedia.');
                }
            } elseif ($data['action'] === 'decrease') {
                $cart[$id]--;
                if ($cart[$id] < 1) {
                    unset($cart[$id]);
                }
            } else {
                unset($cart[$id]);
            }
        }

        $request->session()->put('cart', $cart);

        return redirect()->route('cart.index');
    }

    public function checkoutForm(Request $request)
    {
        [$items, $subtotal, $cartCount] = $this->getCartItems($request);

        if (empty($items)) {
            return redirect()->route('shop.index')->with('error', 'Keranjang belanja masih kosong.');
        }

        return view('shop.checkout', compact('items', 'subtotal', 'cartCount'));
    }

    public function processCheckout(Request $request)
    {
        $data = $request->validate([
            'recipient_name' => ['required', 'string', 'max:100'],
            'recipient_phone' => ['required', 'string', 'max:30'],
            'delivery_method' => ['required', 'in:pickup,delivery'],
            'shipping_address' => ['nullable', 'required_if:delivery_method,delivery', 'string', 'max:300'],
        ], [
            'shipping_address.required_if' => 'Alamat pengantaran wajib diisi jika memilih metode pengantaran.',
        ]);

        $rawCart = $request->session()->get('cart', []);
        if (empty($rawCart)) {
            throw ValidationException::withMessages([
                'cart' => 'Keranjang belanja masih kosong.',
            ]);
        }

        $user = Auth::user();

        DB::transaction(function () use ($rawCart, $data, $user) {
            $subtotal = 0;
            $lockedItems = [];

            foreach ($rawCart as $productId => $qty) {
                $qty = max(1, (int) $qty);
                $product = Product::lockForUpdate()->find((int) $productId);

                if (! $product || $product->stock < $qty) {
                    throw ValidationException::withMessages([
                        'stock' => "Stok produk " . ($product->product_name ?? "#{$productId}") . " tidak mencukupi (tersisa " . ($product->stock ?? 0) . ").",
                    ]);
                }

                $lineSubtotal = (float) $product->price * $qty;
                $subtotal += $lineSubtotal;
                $lockedItems[] = [
                    'product' => $product,
                    'quantity' => $qty,
                    'unit_price' => (float) $product->price,
                    'subtotal' => $lineSubtotal,
                ];
            }

            $shippingFee = $data['delivery_method'] === 'delivery' ? 15000 : 0;
            $totalPrice = $subtotal + $shippingFee;
            $orderCode = 'ORD-' . now()->format('ymd') . '-' . strtoupper(substr(uniqid(), -6));

            $order = Order::create([
                'order_code' => $orderCode,
                'user_id' => $user->user_id,
                'recipient_name' => trim($data['recipient_name']),
                'recipient_phone' => trim($data['recipient_phone']),
                'delivery_method' => $data['delivery_method'],
                'shipping_address' => $data['delivery_method'] === 'delivery' ? trim((string) $data['shipping_address']) : '-',
                'shipping_fee' => $shippingFee,
                'total_price' => $totalPrice,
                'order_date' => now(),
                'status' => 'pending',
            ]);

            foreach ($lockedItems as $item) {
                OrderDetail::create([
                    'order_id' => $order->order_id,
                    'product_id' => $item['product']->product_id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['subtotal'],
                ]);

                $item['product']->decrement('stock', $item['quantity']);
            }

            Payment::create([
                'order_id' => $order->order_id,
                'payment_date' => now(),
                'amount' => $totalPrice,
                'payment_method' => 'Simulasi Checkout',
                'payment_status' => 'paid',
            ]);
        });

        $request->session()->forget('cart');

        return redirect()->route('orders.history')->with('success', 'Pesanan berhasil dibuat dan tercatat di database!');
    }

    public function orderHistory()
    {
        $orders = Order::with(['details.product', 'payment'])
            ->where('user_id', Auth::id())
            ->orderByDesc('order_id')
            ->get();

        return view('shop.history', compact('orders'));
    }

    public function invoice(Order $order)
    {
        if ($order->user_id !== Auth::id() && ! Auth::user()->isAdmin()) {
            abort(403);
        }

        $order->load(['details.product', 'payment', 'user']);
        return view('shop.invoice', compact('order'));
    }

    public function cancelOrder(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->status !== 'pending') {
            return back()->with('error', 'Hanya pesanan yang masih berstatus Menunggu Konfirmasi (Pending) yang dapat dibatalkan.');
        }

        DB::transaction(function () use ($order) {
            $order->load('details.product');
            foreach ($order->details as $detail) {
                if ($detail->product) {
                    $detail->product->increment('stock', $detail->quantity);
                }
            }

            $order->update(['status' => 'cancelled']);
            if ($order->payment) {
                $order->payment->update(['payment_status' => 'failed']);
            }
        });

        return redirect()->route('orders.history')->with('success', "Pesanan {$order->order_code} berhasil dibatalkan dan stok produk telah dikembalikan.");
    }
}
