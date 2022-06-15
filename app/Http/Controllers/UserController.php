<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    //create
    public function create(Request $request){
        // リクエストボディはrequest->input()で書く必要があるかも？
        Log::debug($request->name);
        try{
            $user = new User();
            $user->name = $request->name;
            $user->sex = (bool)$request->sex;
            $user->age = intval($request->age);
            $user->facebook_id = $request->facebook_id;
            $user->password = $request->password;
            $user->status = false;
            $user->save();
            return response()->json([
                "name" => $user->name,
                "age" => $user->age,
                "sex" => $user->sex,
            ], 200);
        }catch(Exeption $e){
            return response()->json([
                "message" => "Internal Server Error"
            ], 500);
        }
    }

    public function show(Request $request){
        // パスパラメータはrequest->で取得可能
        try{
            $user = User::find($request->user_id);
            return response()->json([
                "name" => $user->name,
                "age" => $user->age,
                "sex" => $user->sex,
            ], 200);
        }catch(Exeption $e){
            return response()->json([
                "message" => "Internal Server Error"
            ], 500);
        } 
    }

    public function update(Request $request){
        // パスパラメータはrequest->で取得可能
        try{
            $user = User::find($request->id);
            if($request->name){
                $user->name = $request->name;
            }
            if($request->age){
                $user->age = intval($request->age);
            }
            if($request->sex){
                $user->sex = (bool)$request->sex;
            }
            $user->save();
            return response()->json([
                "name" => $user->name,
                "age" => $user->age,
                "sex" => $user->sex,
            ], 200);
        }catch(Exeption $e){
            return response()->json([
                "message" => "Internal Server Error"
            ], 500); 
        }

    }
}
