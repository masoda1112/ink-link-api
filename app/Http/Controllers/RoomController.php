<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Item;
use App\Models\Room;
use \GuzzleHttp\Client;
use Carbon\Carbon;
// use Illuminate\Support\Facades\Auth;
use Kreait\Firebase\Contract\Auth;

class RoomController extends Controller
{
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    public function create(Request $request){
        try{
            $this->createHelper($request);
            return response()->json([
                "message" => "room created"
            ], 200);
        }catch(Exeption $e){
            return response()->json([
                "message" => "Internal Server Error"
            ], 500);
        }
    }

    public function join(Request $request){
        try{
            $room = Room::where('status_id', 0)->oldest('updated_at')->get();
            if($room->isEmpty()){
                $room = $this->createHelper($request);
            }else{
                $this->addUser($request, $room);
            }
        } catch(Exeption $e){
            return response()->json([
                "message" => "Internal Server Error"
            ], 500);
        }
        return response()->json([
            "room_uniq_id" => $room->id,
            "als_key" => $room->als_key
        ], 200);
    }
    public function addUser(Request $request, Room $room){
        // authenticateからid取得する方法が分かり次第書き換える
        // als_keyの発効の仕方調べ次第response書き換える
        $user_id = auth()->id();
        $user = User::find($user_id);
        $room->users()->attach([$user_id => ['created_at' => Carbon::now(), 'updated_at' => Carbon::now(),'stay_time' => 0, 'status_id' => 0]]);
        $room->user_count += 1;
        if($room->user_count == 4){
            $room->status_id = 1;
        }

        $room->save();
        
        return response()->json([
            "room_uniq_id" => $room->id,
            "als_key" => $room->als_key
        ], 200);
    }

    public function leaveUser(Request $request){
        try{
            $user_id = auth()->id();
            // $user = User::find($user_id);
            $room = Room::find($request->room_id);
            $room->users()->detach($user_id);
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
            // テスト後に戻す
            // $item_id = mt_rand(0, 20);
            $item_id = 4;
            $item = Item::find($item_id);
            $room = Room::find($request->room_id);
            $room->items()->attach([$item_id => ['created_at' => Carbon::now(), 'updated_at' => Carbon::now(),'using_time' => 0,'status_id' => 0 ]]);
            $this->almosyncPost($room);
            $room->save();
            return response()->json([
                "message" => "room updated",
                "item_id" => $item->id,
                "item_name" => $item->name,
                "item_description" => $item->description
            ], 200);

        }catch(Exeption $e){
            return response()->json([
                "message" => "Internal Server Error"
            ], 500);
        }
    }

    private function createHelper(Request $request){
        $room = new Room();
        $room->user_count = 0;
        // 0:waiting, 1:full, 2:close
        $room->status_id = 0;
        // ランダムな文字列にする
        $room->als_key = md5(uniqid());
        $room->save();
        $lastroom = Room::latest()->first();
        $this->addUser($request, $lastroom);
        $lastroom->save();
        return $lastroom;
    }

    private function almosyncPost(Room $room){
        $ALMOSYNC_URL = 'https://almosync-test-api.herokuapp.com/';
        $client = new Client([
            'base_uri' => $ALMOSYNC_URL,
        ]);

        $options = [];
        $response = $client->post('/api/v1/messages/card',['form_params' => ['almosync_key' => $room->als_key]]);
    }
}
