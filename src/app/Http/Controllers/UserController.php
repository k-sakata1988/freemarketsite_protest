<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Storage;
use App\Models\Address;

class UserController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $soldItems = $user->items()->latest()->get();
        $boughtItems = $user->purchases()
                        ->with('item')
                        ->latest()
                        ->get()
                        ->map(function ($purchase) {
                            return $purchase->item;
                        })
                        ->filter();

        return view('mypage.index', compact('user', 'soldItems', 'boughtItems'));
    }
    
    public function getTabItems(Request $request, $tabType)
    {
        $user = Auth::user();
        $items = collect();

        if ($tabType === 'sold') {
            $items = $user->items()->latest()->get();
        } elseif ($tabType === 'bought') {
            $items = $user->purchases()->with('item')->latest()->get()->map(function ($purchase){
                return $purchase->item;
            })->filter();
        }

        return view('mypage._item_list', ['items' => $items, 'tabType' => $tabType])->render();
    }

    public function edit(){
        $user = Auth::user();
        $user->load('address');
        return view('mypage.profile_edit', compact('user'));
    }

    public function setupProfile()
    {
        $user = Auth::user();
        $user->load('address');
        return view('mypage.profile_edit', compact('user'));
    }
    
    public function update(ProfileRequest $request)
    {
        $user = Auth::user();

        $userData = [
            'name' => $request->input('user_name'),
        ];

        if ($request->hasFile('profile_image')) {
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }

            $path = $request->file('profile_image')->store('profile_images', 'public');

            $userData['profile_image'] = $path;
        }

        $user->update($userData);

        $addressData = [
            'user_id' => $user->id,
            'postal_code' => $request->input('postal_code'),
            'address' => $request->input('address'),
            'building' => $request->input('building_name'),
        ];

        Address::updateOrCreate(
            ['user_id' => $user->id],
            $addressData
        );

        return redirect()->route('mypage.index')->with('success', 'プロフィールを更新しました。');
    }
}