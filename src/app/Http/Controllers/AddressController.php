<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Address;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    /**
     * 住所変更画面を表示する (update.blade.php)
     *
     * @param Item $item 購入対象の商品
     * @return \Illuminate\View\View
     */
    public function update(Item $item)
    {
        $user = Auth::user();
        $address = Address::where('user_id', $user->id)->first(); 

        return view('address.update', [
            'user' => $user,
            'item' => $item,
            'address' => $address,
        ]);
    }


    public function saveAddress(Request $request)
    {
        $request->validate([
            'postal_code' => ['required', 'string', 'max:10'],
            'address' => ['required', 'string', 'max:255'],
            'building_name' => ['nullable', 'string', 'max:255'],
            'item_id' => ['required', 'exists:items,id'],
        ]); 

        $user = Auth::user();
        $itemId = $request->input('item_id');

        $data = [
            'user_id' => $user->id,
            'postal_code' => $request->postal_code,
            'address' => $request->address,
            'building' => $request->building_name,
        ];

        Address::updateOrCreate(
            ['user_id' => $user->id],
            $data 
        );

        return redirect()->route('purchase.create', ['item' => $itemId])->with('success', '配送先住所が更新されました。');
    }
}