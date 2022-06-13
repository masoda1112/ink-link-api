<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    //create
    public function create(Request $request){
        $user = new User();
        $user->name = $request->user_name;
        $user->sex = $request->user_sex;
        $user->age = $request->user_age;
        $user->facebook_id = $request->user_facebook_id;
        $user->password = $request->user_password;
        $user->status = false;
        $user->save();
    }
}
