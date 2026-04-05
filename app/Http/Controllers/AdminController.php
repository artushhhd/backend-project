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
        $user->update(['role' => 1]);

        return response()->json([
            'message' => "Пользователь {$user->name} теперь Админ (1)."
        ]);
    }

    public function makeSuperAdmin($id)
    {
        if ((int)auth()->user()->role !== 2) {
            return response()->json(['message' => 'У вас нет прав для назначения Super Admin'], 403);
        }

        $user = User::findOrFail($id);
        $user->update(['role' => 2]);

        return response()->json([
            'message' => "Пользователь {$user->name} теперь Super Admin (2)!"
        ]);
    }
    public function makeModerator($id)
    {
        $authRole=(int)auth()->user()->role;
        if ($authRole !== 2 && $authRole !== 1) {
            return response()->json(['message' => 'У вас нет прав для назначения Moderator'],403);
        }
        $user = User::findOrFail($id);
        if ($user->role === 2 && $authRole !== 2) {
            return response()->json(['message' => 'Невозможно понизить Super Admin до Moderator'],403);
        }
        $user->update(['role' => 3]);
        return response()->json([
            'message' => "Пользователь {$user->name} теперь Moderator (3)!"
        ]);
    }

    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();
        $role=(int)auth()->user()->role;

        if($role===3){
            $ower=User::find($product->user_id);
            return response()->json(['message' => 'Товар удален. У вас нет прав для удаления других товаров.']);

        }


        return response()->json(['message' => 'Товар удален.']);
    }

    public function destroyUser($id)
    {
        $user = User::findOrFail($id);

        if (auth()->id() == $id) {
            return response()->json(['message' => 'Вы не можете удалить свой собственный аккаунт'], 403);
        }

        DB::transaction(function () use ($user) {
            $this->recursiveDelete($user);
        });

        return response()->json(['message' => 'Пользователь и вся его структура удалены.']);
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
