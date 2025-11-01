<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('mypage.index', compact('user'));
    }
    public function getTabItems(Request $request, $tabType)
    {
        $user = Auth::user();
        $items = collect(); 

        if ($tabType === 'sold') {
            $items = $user->items()->latest()->get(); 
        } elseif ($tabType === 'bought') {
            $items = $user->purchases()
                          ->with('item')
                          ->latest()
                          ->get()
                          ->map(function ($purchase) {
                              return $purchase->item;
                          })
                          ->filter();
        }

        return view('mypage._item_list', ['items' => $items, 'tabType' => $tabType])->render();
    }

    public function edit()
    {
        $user = Auth::user();
        return view('mypage.profile_edit', compact('user'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'user_name' => 'required|string|max:20',
            'postal_code' => 'required|regex:/^\d{3}-\d{4}$/',
            'address' => 'required|string|max:255',
            'building_name' => 'nullable|string|max:255',
            'profile_image' => 'nullable|image|max:2048',
        ]);

        $user = Auth::user();
        $data = $request->only(['user_name', 'postal_code', 'address', 'building_name']);

        if ($request->hasFile('profile_image')) {
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }

            $path = $request->file('profile_image')->store('profile_images', 'public');
            $data['profile_image'] = $path;
        }

        $user->update($data);

        return redirect()->route('mypage.profile.edit')->with('success', 'プロフィールを更新しました。');
    }
}
