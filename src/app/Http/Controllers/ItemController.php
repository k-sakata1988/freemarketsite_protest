<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;

class ItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['create', 'store']);
    }

    public function index(Request $request)
    {
        $keyword = $request->input('keyword');
        $activeTab = $request->input('tab') ?? (auth()->check() ? 'mylist' : 'recommended');

        if ($activeTab === 'mylist' && auth()->check()) {
            $items = Auth::user()->favorites()
                ->with('purchase')
                ->when($keyword, fn($q) => $q->where('name', 'LIKE', "%{$keyword}%"))
                ->get();
        } else {
            $items = Item::recommended()
                ->with('purchase')
                ->when(auth()->check(), fn($q) => $q->where('user_id', '<>', auth()->id()))
                ->when($keyword, fn($q) => $q->where('name', 'LIKE', "%{$keyword}%"))
                ->get();
        }

        return view('index', compact('items', 'activeTab', 'keyword'));
    }

    public function fetchTabItems($type, Request $request)
    {
        $keyword = $request->input('keyword');

        if ($type === 'recommended' || !auth()->check()) {
            $items = Item::recommended()
                ->with('purchase')
                ->when(auth()->check(), fn($q) => $q->where('user_id', '<>', auth()->id()))
                ->when($keyword, fn($q) => $q->where('name', 'LIKE', "%{$keyword}%"))
                ->get();
        } elseif ($type === 'mylist' && auth()->check()) {
            $items = Auth::user()->favorites()
                ->with('purchase')  
                ->when($keyword, fn($q) => $q->where('name', 'LIKE', "%{$keyword}%"))
                ->get();
        } else {
            $items = collect();
        }

        return view('items._items', compact('items'));
    }

    public function show(Item $item)
    {
        $item->load(['comments.user']);

        return view('items.show', compact('item'));
    }

    public function create()
    {
        return view('items.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|integer|min:0',
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $item = new Item();
        $item->fill($validated);
        $item->user_id = auth()->id();

        if ($request->hasFile('image_path')) {
            $path = $request->file('image_path')->store('images', 'public');
            $item->image_path = $path;
        }

        $item->save();

        return redirect()->route('items.index')->with('success', '商品を出品しました。');
    }
}
