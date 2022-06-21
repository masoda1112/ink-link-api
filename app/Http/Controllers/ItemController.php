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
            $item->name = $request->name;
            $item->type = $request->type;
            $item->description = $request->description;
            $item->save();
            return response()->json([
                "id" => $item->id,
                "name" => $item->name,
                "type" => $item->type,
                "description" => $item->description,
                "message" => "item created"
            ], 200);
        }catch(Exeption $e){
            return response()->json([
                "message" => "Internal Server Error"
            ], 500);
        }
    }
}
