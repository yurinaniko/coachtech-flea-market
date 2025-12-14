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

        $query = Item::with('condition');

        // ★ 自分が出品した商品を除外
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
        $conditions = \App\Models\Condition::all();
        $categories = \App\Models\Category::all();

        return view('items.item-sell', compact('conditions', 'categories'));
    }

    public function store(ExhibitionRequest $request)
    {
        $validated = $request->validated();

        // 画像保存
        if ($request->hasFile('img_url')) {
        $path = $request->file('img_url')->store('images', 'public');
        $validated['img_url'] = $path;
        }

       // 商品作成
        $item = Item::create([
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'brand' => $validated['brand'] ?? null,
            'condition_id' => $validated['condition_id'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'img_url' => $validated['img_url'],
        ]);

        // 中間テーブル（カテゴリ）
        $item->categories()->sync($validated['categories']);

        return redirect()->route('mypage.index')
            ->with('success', '商品を出品しました！');
    }

    public function show($id)
    {
        $item = Item::with([
            'categories',
            'comments.user',
            'users',
            'condition',
            'purchase'
        ])->findOrFail($id);

        $comments = $item->comments;

        return view('items.item-detail', compact('item', 'comments'));
    }

    public function update(Request $request, Item $item)
    {
        $item->update($request->all());

        $item->categories()->sync($request->categories);

        return redirect()->route('items.index');
    }

}
