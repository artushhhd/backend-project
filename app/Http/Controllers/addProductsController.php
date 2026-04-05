<?php
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ShopRequest;

class AddProductsController extends Controller
{
    public function store(ShopRequest $request)
    {
        $imgPath = $request->file('image')->store('products', 'public');

        $product = Product::create([
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
            'stock'       => $request->stock,
            'image'       => $imgPath,
            'is_active'   => true,
            'user_id'     => Auth::id(),
        ]);

        return response()->json(['message' => 'Product added', 'product' => $product], 201);
    }

    public function index()
    {
        $products = Product::with('user')->where('is_active', true)->get();
        return response()->json($products);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if ((int)$product->user_id !== (int)Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
