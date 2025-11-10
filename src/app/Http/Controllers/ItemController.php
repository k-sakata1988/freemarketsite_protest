<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\Condition;
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
                ->where('status', 'selling')
                ->when($keyword, fn($q) => $q->where('name', 'LIKE', "%{$keyword}%"))
                ->get();
        } else {
            $items = Item::recommended()
                ->with('purchase')
                ->where('status', 'selling')
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
                ->where('status', 'selling')
                ->when(auth()->check(), fn($q) => $q->where('user_id', '<>', auth()->id()))
                ->when($keyword, fn($q) => $q->where('name', 'LIKE', "%{$keyword}%"))
                ->get();
        } elseif ($type === 'mylist' && auth()->check()) {
            $items = Auth::user()->favorites()
                ->with('purchase')
                ->where('status', 'selling')
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
        $categories = Category::all();
        $conditions = Condition::all();

        return view('items.create', compact('categories', 'conditions'));
    }

    public function store(Request $request){
        $validated = $request->validate([
            'image_path' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_id' => 'required|array',
            'category_id.*' => 'exists:categories,id',
            'condition_id' => 'required|exists:conditions,id',
            'name' => 'required|string|max:255',
            'brand_name' => 'nullable|string|max:255',
            'description' => 'required|string',
            'price' => 'required|integer|min:100',
        ]);

        $path = null;
        if ($request->hasFile('image_path')) {
            $path = $request->file('image_path')->store('images', 'public');
        }

        $categoryString = implode(',', $request->category_id); 

        $item = Item::create([
            'user_id' => auth()->id(),
            'category_id' => $categoryString,
            'condition' => $request->condition_id,
            'name' => $request->name,
            'brand' => $request->brand_name,
            'description' => $request->description,
            'price' => $request->price,
            'image_path' => $path,
            'is_recommended' => true,
        ]);

        return redirect()->route('items.show', $item)->with('success', '商品を出品しました。');
    }
}