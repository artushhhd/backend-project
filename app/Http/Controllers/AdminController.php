<?php

namespace App\Http\Controllers;

use App\Models\{User, Product};
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        return response()->json([
            'users' => User::with('descendants')->get(),
            'products' => Product::all()
        ]);
    }

    public function getUserProducts($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user->products);
    }

    public function makeAdmin($id)
    {
        $user = User::findOrFail($id);
        $user->update(['role' => 'admin']);

        return response()->json([
            'message' => "Пользователь {$user->name} теперь Администратор."
        ]);
    }

    public function makeSuperAdmin($id)
    {
        if (auth()->user()->role !== 'superadmin') {
            return response()->json(['message' => 'У вас нет прав для назначения Super Admin'], 403);
        }

        $user = User::findOrFail($id);
        $user->update(['role' => 'superadmin']);

        return response()->json([
            'message' => "Пользователь {$user->name} теперь Super Admin!"
        ]);
    }

    public function makeModerator($id)
    {
        $authRole = auth()->user()->role;

        if ($authRole !== 'superadmin' && $authRole !== 'admin') {
            return response()->json(['message' => 'У вас нет прав для назначения Moderator'], 403);
        }

        $user = User::findOrFail($id);

        if ($user->role === 'superadmin' && $authRole !== 'superadmin') {
            return response()->json(['message' => 'Невозможно понизить Super Admin до Moderator'], 403);
        }

        $user->update(['role' => 'moderator']);
        return response()->json([
            'message' => "Пользователь {$user->name} теперь Moderator!"
        ]);
    }

    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return response()->json(['message' => 'Товар успешно удален.']);
    }

    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        $currentUser = auth()->user();

        if ($currentUser->id == $id) {
            return response()->json(['message' => 'Вы не можете удалить свой собственный аккаунт'], 403);
        }

        if ($user->role === 'superadmin' && $currentUser->role !== 'superadmin') {
            return response()->json(['message' => 'Недостаточно прав для удаления Super Admin'], 403);
        }

        DB::transaction(function () use ($user) {
            $this->recursiveDelete($user);
        });

        return response()->json(['message' => 'Пользователь и вся его структура успешно удалены.']);
    }

    private function recursiveDelete(User $user)
    {
        foreach ($user->children as $child) {
            $this->recursiveDelete($child);
        }

        foreach ($user->products as $product) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $product->delete();
        }

        $user->delete();
    }
}
