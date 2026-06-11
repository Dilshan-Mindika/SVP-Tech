<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    /**
     * Display the checkout page.
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty. Add products to proceed.');
        }

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        return view('checkout.index', compact('cart', 'subtotal'));
    }

    /**
     * Place a new order.
     */
    public function store(Request $request)
    {
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // Validate the checkout details
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string',
            'payment_method' => 'required|string|in:cod,bank_transfer',
        ]);

        // Calculate total and verify stock
        $total = 0;
        foreach ($cart as $id => $item) {
            $product = Product::find($id);
            if (!$product) {
                return redirect()->route('cart.index')->with('error', "Product {$item['name']} no longer exists.");
            }
            if ($product->stock < $item['quantity']) {
                return redirect()->route('cart.index')->with('error', "Sorry, only {$product->stock} units of {$product->name} are available.");
            }
            $total += $item['price'] * $item['quantity'];
        }

        try {
            DB::beginTransaction();

            // Create Order
            $order = Order::create([
                'user_id' => auth()->id(),
                'order_number' => 'CT-' . strtoupper(Str::random(10)),
                'total' => $total,
                'status' => 'pending',
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'shipping_address' => $request->shipping_address,
                'payment_method' => $request->payment_method,
            ]);

            // Save items & decrement stock
            foreach ($cart as $id => $item) {
                $product = Product::find($id);
                
                // Decrement stock
                $product->decrement('stock', $item['quantity']);

                // Create item
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }

            DB::commit();

            // Clear session cart
            session()->forget('cart');

            return redirect()->route('checkout.success', $order->id)->with('success', 'Order placed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display order success page.
     */
    public function success($orderId)
    {
        $order = Order::with('items.product')->findOrFail($orderId);
        
        // Ensure this user can view the order
        if (auth()->check() && $order->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        return view('checkout.success', compact('order'));
    }
}
