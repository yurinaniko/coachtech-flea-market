<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{

    public function index()
    {
        $items = Item::all();
        return view('items.index', compact('items'));
    }

    public function favorite(Item $item)
    {
        Auth::user()->favorites()->attach($item->id);
        return back();
    }

    public function unfavorite(Item $item)
    {
        Auth::user()->favorites()->detach($item->id);
        return back();
    }

    public function sell() {
        return view('items.sell');
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $item = Item::create($request->all());

        // categories[] に選択されたIDが入っている
        $item->categories()->sync($request->categories);

        return redirect()->route('items.index');
    }

    public function show($id)
    {
        $item = Item::findOrFail($id);
        $comments = $item->comments()->orderBy('created_at', 'desc')->get();
        return view('items.item-detail', compact('item', 'comments'));
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, Item $item)
    {
        $item->update($request->all());

        $item->categories()->sync($request->categories);

        return redirect()->route('items.index');
    }

    public function destroy($id)
    {
        //
    }
}
