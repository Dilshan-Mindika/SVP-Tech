<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display the cart contents.
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        
        return view('cart.index', compact('cart', 'subtotal'));
    }

    /**
     * Add a product to the cart.
     */
    public function add(Request $request, Product $product)
    {
        if ($product->stock <= 0) {
            return redirect()->back()->with('error', 'This product is currently out of stock.');
        }

        $cart = session()->get('cart', []);

        // If cart is empty, this is the first product
        if (empty($cart)) {
            $cart[$product->id] = [
                "id" => $product->id,
                "name" => $product->name,
                "quantity" => 1,
                "price" => $product->price,
                "image_path" => $product->image_path,
                "brand" => $product->brand,
                "slug" => $product->slug,
                "max_stock" => $product->stock
            ];
            session()->put('cart', $cart);
            return redirect()->back()->with('success', 'Product added to cart successfully!');
        }

        // If product already exists in cart, increment quantity
        if (isset($cart[$product->id])) {
            if ($cart[$product->id]['quantity'] >= $product->stock) {
                return redirect()->back()->with('error', 'Cannot add more. Not enough stock available.');
            }
            $cart[$product->id]['quantity']++;
            session()->put('cart', $cart);
            return redirect()->back()->with('success', 'Cart updated successfully!');
        }

        // If item doesn't exist in cart, add it
        $cart[$product->id] = [
            "id" => $product->id,
            "name" => $product->name,
            "quantity" => 1,
            "price" => $product->price,
            "image_path" => $product->image_path,
            "brand" => $product->brand,
            "slug" => $product->slug,
            "max_stock" => $product->stock
        ];
        session()->put('cart', $cart);
        
        return redirect()->back()->with('success', 'Product added to cart successfully!');
    }

    /**
     * Update the quantity of a product in the cart.
     */
    public function update(Request $request, $id)
    {
        $quantity = intval($request->quantity);
        
        if ($quantity <= 0) {
            return $this->remove($id);
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $product = Product::find($id);
            if (!$product) {
                return redirect()->back()->with('error', 'Product not found.');
            }

            if ($quantity > $product->stock) {
                return redirect()->back()->with('error', 'Cannot set quantity. Only ' . $product->stock . ' units are in stock.');
            }

            $cart[$id]['quantity'] = $quantity;
            session()->put('cart', $cart);
            return redirect()->back()->with('success', 'Cart updated successfully!');
        }

        return redirect()->back()->with('error', 'Item not found in cart.');
    }

    /**
     * Remove a product from the cart.
     */
    public function remove($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
            return redirect()->back()->with('success', 'Item removed from cart.');
        }

        return redirect()->back()->with('error', 'Item not found in cart.');
    }
}
