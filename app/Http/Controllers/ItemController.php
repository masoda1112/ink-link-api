<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class ItemController extends Controller
{
    // create, 
    public function create(Request $request){
        $item = new Item();
        $item->name = $request->item_name;
        $item->type = $request->item_type;
        $item->description = $request->item_description;
        $item->save();
    }
}
