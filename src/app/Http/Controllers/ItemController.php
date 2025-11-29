<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Item;
use App\Http\Requests\ExhibitionRequest; 

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

    public function store(ExhibitionRequest $request)
    {
        $validated = $request->validated();

        $path = null;
        if ($request->hasFile('image_path')) {
            $path = $request->file('image_path')->store('images', 'public');
        }
        $categoryString = implode(',', $validated['category_id']); 

        $item = Item::create([
            'user_id' => auth()->id(),
            'category_id' => $categoryString,
            'condition' => $validated['condition_id'],
            'name' => $validated['name'],
            'brand' => $validated['brand_name'] ?? null,
            'description' => $validated['description'],
            'price' => $validated['price'],
            'image_path' => $path,
            'is_recommended' => true,
        ]);

        return redirect()->route('items.show', $item)->with('success', '商品を出品しました。');
    }
    // test用
    public function recommended()
    {
        $items = Item::with('purchase')->where('status', 'selling')->when(auth()->check(), fn($q) => $q->where('user_id', '<>', auth()->id()))->get();

        // return view('items.recommended', compact('items'));
        return view('index', ['items' => $items, 'activeTab' => 'recommended']);
    }
    public function recommendedSearch(Request $request)
    {
        $keyword = $request->input('keyword');

        $items = Item::with('purchase')
        ->where('status', 'selling')
        ->when(auth()->check(), fn($q) => $q->where('user_id', '<>', auth()->id()))
        ->when($keyword, fn($q) => $q->where('name', 'LIKE', "%{$keyword}%"))
        ->get();

        // return view('items.recommended', compact('items'));
        return view('index', ['items' => $items, 'activeTab' => 'recommended']);
    }
}