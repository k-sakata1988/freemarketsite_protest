<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function toggle(Item $item)
    {
        $user = Auth::user();

        if ($user->favorites()->where('item_id', $item->id)->exists()) {
            $user->favorites()->detach($item->id);
            $liked = false;
        } else {
            $user->favorites()->attach($item->id);
            $liked = true;
        }

        $count = $item->favorites()->count();

        return response()->json([
            'liked' => $liked,
            'count' => $count,
            'item_id' => $item->id
        ]);
    }
}
