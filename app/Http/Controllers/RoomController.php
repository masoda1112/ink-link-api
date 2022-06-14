<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class RoomController extends Controller
{
    // controller
    // join→add or create and add
    // itempull
    // leave
    // routeここまで今日終わらせたい
    // responce設計（陸の欲しがってるjson,ステータスコード500の処理）
    // 15：FB認証、16：websocket
    // 17：修正
    // 18：デプロイ
    // statusコードは500と201か202
    // Q：userはuseridで取得して、userモデルから取得する感じか？->yes
    // itemも同様
    // Q:useridはauthenticateで取得する方法
    public function create(Request $request){
        try{
            $created_room = createHelper($request);
            return response()->json([
                "message" => "room created"
            ], 201);
        }catch(Exeption $e){
            return response()->json([
                "message" => "Internal Server Error"
            ], 500);
        }
    }

    // 処理の使い分けをする場合以下を使用
    // roomの人数による場合わけどうする？joinからの設計

    public function join(Request $request){
        // status_codeが2の部屋がある場合、status_code=2の部屋の
        // レスポンスはals_keyを返す
        try{
            $room = Room::where('status_id', 0)->oldest('updated_at')->get();
            if($room == null){
                createHelper($request);
            }else{
                addUser($request, $room);
            }
        } catch(Exeption $e){
            return response()->json([
                "message" => "Internal Server Error"
            ], 500);
        }
    }
    public function addUser(Request $request, Room $room){
        // authenticateからid取得する方法が分かり次第書き換える
        // als_keyの発効の仕方調べ次第response書き換える
        $user_id = 0;
        $user = User::find($user_id);

        $room->users()->attach($user_id);
        $room->user_count += 1;

        if($room->user_count >= 4){
            $room->status_id = 1;
        }

        return response()->json([
            "room_uniq_id" => $room->id,
            "als_key" => $room->als_key
        ], 200);
    }

    public function leaveUser(Request $request, Room $room){
        try{
            $user_id = 0;
            $user = User::find($user_id);
            $room = Room::find($request->room_id);
            $room->users()->detach($user);
            $room->user_count -= 1;
    
            if($room->user_count <= 0){
                $room->status_id = 2;
            }else{
                $room->status_id = 0;
            }
            $room->save();
    
            return response()->json([
                "message" => "room updated"
            ], 200);
        }catch(Exeption $e){
            return response()->json([
                "message" => "Internal Server Error"
            ], 500);
        }
    }

    public function selectItem(Request $request){

        try{
            $item_id = mt_rand(0, 20);
            $item = Item::find($item_id);
            $room = Room::find($request->room_id);
            $room->items()->attach($item);
            $room->save();
    
            return response()->json([
                "message" => "room updated"
            ], 200);

        }catch(Exeption $e){
            return response()->json([
                "message" => "Internal Server Error"
            ], 500);
        }
    }


    private function createHelper(Request $request){
        $room = new Room();
        $room->usercount = 1;
        // 0:waiting, 1:full, 2:close
        $room->status_id = 0;
        // ランダムな文字列にする
        $room->als_key = "test";
        $room->save();
        $lastroom = Room::latest()->first();
        addUser($request, $lastroom);
        $lastroom->save();

        return $lastroom;
    }
}
