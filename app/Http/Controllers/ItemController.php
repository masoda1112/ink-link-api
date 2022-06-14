<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class ItemController extends Controller
{
    // create, 
    public function create(Request $request){
        try{
            $item = new Item();
            $item->name = $request->item_name;
            $item->type = $request->item_type;
            $item->description = $request->item_description;
            $item->save();
            return response()->json([
                "message" => "room created"
            ], 200);
        }catch(Exeption $e){
            return response()->json([
                "message" => "Internal Server Error"
            ], 500);
        }
    }
}
