<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreChatMessageRequest;
use App\Models\Purchase;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function show(Purchase $purchase)
    {
        $purchase->refresh();
        $user = Auth::user();

        if ($purchase->user_id !== $user->id && $purchase->item->user_id !== $user->id) {
            abort(403);
        }

        $messages = $purchase->messages()->with('sender')->orderBy('created_at')->get();

        $purchase->messages() ->where('receiver_id', $user->id) ->whereNull('read_at') ->update(['read_at' => now()]);

        $partner = $purchase->user_id === $user->id ? $purchase->item->user : $purchase->user;

        $tradingPurchases = Purchase::where(function($query) use ($user) {
            $query->where('user_id', $user->id)->orWhereHas('item', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        })->where('id', '!=', $purchase->id)->with('item')->get();

        $userId = $user->id;

        $showRatingModal = false;
        $ratingTarget = null;

        if ($purchase->buyer_completed_at) {
            if ($purchase->user_id === $userId && is_null($purchase->buyer_rating)) {
                $showRatingModal = true;
                $ratingTarget = 'seller';
            } elseif ($purchase->item->user_id === $userId && is_null($purchase->seller_rating)) {
                $showRatingModal = true;
                $ratingTarget = 'buyer';
            }
        }

        $showCompleteButton = ($purchase->user_id === $userId) && ($purchase->status === 'purchased') && (is_null($purchase->buyer_rating));

        return view('chat.show', compact(
            'purchase',
            'messages',
            'partner',
            'tradingPurchases',
            'showRatingModal',
            'ratingTarget',
            'showCompleteButton'
        ));
    }

    public function store(StoreChatMessageRequest $request, Purchase $purchase)
    {
        $user = Auth::user();

        if (
            $purchase->user_id !== $user->id &&
            $purchase->item->user_id !== $user->id
        ) {
            abort(403);
        }

        $receiverId = $purchase->user_id === $user->id ? $purchase->item->user_id : $purchase->user_id;

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('chat_images', 'public');
        }

        Message::create([
            'purchase_id' => $purchase->id,
            'sender_id' => $user->id,
            'receiver_id' => $receiverId,
            'message' => $request->message,
            'image_path' => $imagePath,
        ]);

        return redirect()->route('chat.show', $purchase);
    }


    public function update(Request $request, Message $message)
    {

        if ($message->sender_id !== Auth::id()) {
            abort(403);
        }
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $message->update([
            'message' => $request->message,
        ]);

        return back()->with('success', 'メッセージを更新しました');
    }

    public function destroy(Message $message)
    {

        if ($message->sender_id !== Auth::id()) {
            abort(403);
        }

        $message->delete();

        return back()->with('success', 'メッセージを削除しました');
    }
}