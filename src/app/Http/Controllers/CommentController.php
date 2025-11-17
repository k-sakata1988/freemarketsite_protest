<?php

namespace App\Http\Controllers;

use App\Http\Requests\Request;
use App\Models\Item;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, Item $item)
    {

        $comment = new Comment();
        $comment->comment = $request->comment;
        $comment->user_id = Auth::id();
        $comment->item_id = $item->id;
        $comment->save();
        $comment->load('user');

        return response()->json([
            'user' => $comment->user->name,
            'body' => $comment->comment,
        ]);
    }
}
