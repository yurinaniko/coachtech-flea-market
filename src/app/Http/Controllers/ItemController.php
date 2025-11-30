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
        //
    }

    public function show($id)
    {
        $item = Item::findOrFail($id);
        return view('items.show', compact('item'));
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
