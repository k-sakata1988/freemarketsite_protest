<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Address;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function create($item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = Auth::user();

        $address = Address::where('user_id', $user->id)->first();

        return view('purchase.create', compact('item', 'user','address'));
    }
    public function store(Request $request, Item $item)
    {
        $request->validate([
            'payment_method' => 'required|string|in:credit,convenience',
        ]);

        $user = Auth::user();
        $paymentMethod = $request->input('payment_method');

        if ($paymentMethod === 'convenience') {
            $paymentMethod = 'convenience_store';
        } elseif ($paymentMethod === 'credit') {
            $paymentMethod = 'credit_card';
        }

        $address = \App\Models\Address::where('user_id', $user->id)->first();

        if (!$address) {
            return back()->with('error', '購入処理を実行するには、まず配送先住所を登録してください。');
        }

        if ($item->isSoldOut()) {
            return back()->with('error', 'この商品は売り切れました。');
        }

        \App\Models\Purchase::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'address_id' => $address->id,
            'payment_method' => $paymentMethod,
            'status' => 'purchased',
        ]);

        return redirect()->route('items.index')->with('success', '商品を購入しました！');
    }

}
