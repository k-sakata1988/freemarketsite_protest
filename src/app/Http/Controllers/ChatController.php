<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function show(Purchase $purchase)
    {
        $user = Auth::user();
        if (
            $purchase->user_id !== $user->id &&
            $purchase->item->user_id !== $user->id
            ) {
                abort(403);
            }

        $messages = $purchase->messages()->with('sender')->orderBy('created_at')->get();

        $purchase->messages()->where('receiver_id', $user->id)->whereNull('read_at')->update(['read_at' => now()]);

        return view('chat.show', compact('purchase', 'messages'));
    }


    public function store(Request $request, Purchase $purchase)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $user = Auth::user();

        if (
            $purchase->user_id !== $user->id &&
            $purchase->item->user_id !== $user->id
        ) {
            abort(403);
        }

        $receiverId = $purchase->user_id === $user->id? $purchase->item->user_id: $purchase->user_id;

        Message::create([
            'purchase_id' => $purchase->id,
            'sender_id' => $user->id,
            'receiver_id' => $receiverId,
            'message' => $request->message,
        ]);

        return redirect()->route('chat.show', $purchase);
    }

}