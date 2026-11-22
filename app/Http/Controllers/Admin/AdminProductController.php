<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminProductController extends Controller
{
    public function index(Request $request)
    {
        $editProduct = null;
        if ($request->filled('edit')) {
            $editProduct = Product::find($request->query('edit'));
        }

        $products = Product::orderByDesc('product_id')->get();
        return view('admin.products', compact('products', 'editProduct'));
    }

    public function store(Request $request)
    {
        $name = $request->input('product_name') ?? $request->input('name');
        $request->merge(['product_name' => $name]);

        $validated = $request->validate([
            'product_name' => ['required', 'string', 'max:150'],
            'category' => ['required', 'string', 'max:50'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'string', 'max:255'],
            'image_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'description' => ['nullable', 'string'],
        ]);

        $imagePath = ($validated['image'] ?? null) ?: 'assets/img/shop/whiskas.png';
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $dir = public_path('assets/img/uploads/products');
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            $filename = 'prod-' . time() . '-' . Str::random(5) . '.' . $file->getClientOriginalExtension();
            $file->move($dir, $filename);
            $imagePath = 'assets/img/uploads/products/' . $filename;
        }

        $slugBase = Str::slug($validated['product_name']) ?: 'produk';
        $slug = $slugBase;
        $counter = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = $slugBase . '-' . $counter++;
        }

        Product::create([
            'slug' => $slug,
            'product_name' => $validated['product_name'],
            'category' => $validated['category'],
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'image' => $imagePath,
            'description' => $validated['description'] ?? '',
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function update(Request $request, Product $product)
    {
        $name = $request->input('product_name') ?? $request->input('name');
        $request->merge(['product_name' => $name]);

        $validated = $request->validate([
            'product_name' => ['required', 'string', 'max:150'],
            'category' => ['required', 'string', 'max:50'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'string', 'max:255'],
            'image_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'description' => ['nullable', 'string'],
        ]);

        $imagePath = ($validated['image'] ?? null) ?: $product->image;
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $dir = public_path('assets/img/uploads/products');
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            $filename = 'prod-' . time() . '-' . Str::random(5) . '.' . $file->getClientOriginalExtension();
            $file->move($dir, $filename);
            $imagePath = 'assets/img/uploads/products/' . $filename;
        }

        $product->update([
            'product_name' => $validated['product_name'],
            'category' => $validated['category'],
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'image' => $imagePath,
            'description' => $validated['description'] ?? $product->description,
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        try {
            if ($product->orderDetails()->exists()) {
                return redirect()->route('admin.products.index')->with(
                    'error',
                    'Produk tidak dapat dihapus karena sudah tercatat dalam riwayat pesanan pelanggan.'
                );
            }
            $product->delete();
            return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus.');
        } catch (QueryException $e) {
            return redirect()->route('admin.products.index')->with(
                'error',
                'Produk tidak dapat dihapus karena masih terhubung dengan data transaksi.'
            );
        }
    }
}
