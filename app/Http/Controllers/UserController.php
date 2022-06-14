<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    //create
    public function create(Request $request){
        // リクエストボディはrequest->input()で書く必要があるかも？
        try{
            $user = new User();
            $user->name = $request->user_name;
            $user->sex = $request->user_sex;
            $user->age = $request->user_age;
            $user->facebook_id = $request->user_facebook_id;
            $user->password = $request->user_password;
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
            $user = User::find($request->user_id);
            $user->name = $request->user_name;
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
