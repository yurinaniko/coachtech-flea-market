<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ItemRequest;
use App\Http\Requests\ExhibitionRequest;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');
        $tab = $request->query('tab', 'recommend');
        if ($tab === 'mylist') {
            if (Auth::check()) {
                $items = Auth::user()
                ->favorites()
                ->with('condition', 'purchase')
                ->get();
            } else {
            $items = collect();
            }
        return view('items.index', compact('items', 'keyword'));
        }
        $query = Item::with('condition', 'purchase');
        if (Auth::check()) {
            $query->where('user_id', '!=', Auth::id());
        }
        if (!empty($keyword)) {
            $query->where('name', 'LIKE', '%' . $keyword . '%');
        }
        $items = $query->get();
        return view('items.index', compact('items', 'keyword'));
    }

    public function sell() {
        $categories = Category::all();
        $conditions = Condition::all();
        return view('items.item-sell', compact('categories', 'conditions'));
    }

    public function create()
    {
        $conditions = Condition::all();
        $categories = Category::all();
        return view('items.item-sell', compact('conditions', 'categories'));
    }

    public function store(ExhibitionRequest $request)
    {
        $validated = $request->validated();
        if ($request->hasFile('img_url')) {
        $path = $request->file('img_url')->store('images', 'public');
        $validated['img_url'] = $path;
        }
        $item = Item::create([
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'brand' => $validated['brand'] ?? null,
            'condition_id' => $validated['condition_id'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'img_url' => $validated['img_url'],
        ]);
        $item->categories()->sync($validated['categories']);
        return redirect()->route('mypage.index');
    }

    public function show($id)
    {
        $item = Item::with([
            'categories',
            'comments.user',
            'condition',
            'purchase',
        ])
            ->withCount(['favorites', 'comments'])
            ->findOrFail($id);

        return view('items.item-detail', [
            'item' => $item,
            'comments' => $item->comments,
        ]);
    }

    public function update(ItemRequest $request, Item $item)
    {
        $validated = $request->validated();
        $imagePath = $item->img_url;
        if ($request->hasFile('img_url')) {
            $imagePath = $request->file('img_url')->store('images', 'public');
        }
        $item->update([
            'name' => $validated['name'],
            'brand' => $validated['brand'] ?? null,
            'condition_id' => $validated['condition_id'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'img_url' => $imagePath,
        ]);
        $item->categories()->sync($validated['categories']);
        return redirect()->route('items.index');
    }
}
