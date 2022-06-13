<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class RoomController extends Controller
{
    //create, update→（changeStatus、 addUser, changeItem、 addItem）
    public function create(Request $request){
        $room = new Room();
        createHelper($request, $room);
    }

    public function update(Request $request, Room $room){
        // updateのadduser、changeItem,addItemの振り分け
        // requestの中身にupdate_typeを入れる
        createHelper($request, $room);
        // update_typeによる処理の使い分けをする場合以下のコードを使用
        // if($request->update_type == "add_user"){
        //     addUser($request, $room);
        // }else if($request->update_type == "change_item"){
        //     changeItem($request, $room);
        // }else if($request->update_type == "add_item"){
        //     addItem($request, $room);
        // }
    }

    // 処理の使い分けをする場合以下を使用
    // private function addUser(Request $request, Room $room){

    // }

    // private function changeItem(Request $request, Room $room){

    // }

    // private function addItem(Request $request, Room $room){

    // }

    private function createHelper(Request $request, Room $room){
        $room->usercount = 1;
        // 0:waiting, 1:full, 2:close
        $room->status_id = 0;
        $room->description = $request->room_description;
        $room->save();
        $lastroom = Room::latest()->first();
        $lastroom->users()->attach($request->user);
        $lastroom->save();
    }
}
