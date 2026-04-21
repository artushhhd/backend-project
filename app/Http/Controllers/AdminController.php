<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Получение всех данных (users + products)
     * Соответствует Route::get('/data')
     */
    public function index()
    {
        return response()->json([
            'users' => User::all(),
            'products' => Product::all()
        ]);
    }

    /**
     * Удаление пользователя
     * Соответствует Route::delete('/users/{id}')
     */
    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        $currentUser = auth()->user();

        // Защита: нельзя удалить самого себя
        if ($currentUser->id == $user->id) {
            return response()->json(['message' => 'Cannot delete your own account'], 400);
        }

        // Защита: админ не может удалить супер-админа
        if ($currentUser->role !== 'superadmin' && $user->role === 'superadmin') {
            return response()->json(['message' => 'Insufficient permissions'], 403);
        }

        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }

    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }

    public function makeAdmin($id)
    {
        $user = User::findOrFail($id);
        $user->update(['role' => 'admin']);
        return response()->json(['message' => 'Role updated to Admin']);
    }

    public function makeModerator($id)
    {
        $user = User::findOrFail($id);
        $user->update(['role' => 'moderator']);
        return response()->json(['message' => 'Role updated to Moderator']);
    }

    public function makeSuperAdmin($id)
    {
        if (auth()->user()->role !== 'superadmin') {
            return response()->json(['message' => 'Only SuperAdmin can perform this action'], 403);
        }

        $user = User::findOrFail($id);
        $user->update(['role' => 'superadmin']);
        return response()->json(['message' => 'Role updated to SuperAdmin']);
    }

    public function getUserProducts($id)
    {
        $products = Product::where('user_id', $id)->get();
        return response()->json($products);
    }
}
