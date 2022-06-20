<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Room;
use \GuzzleHttp\Client;
// use Illuminate\Support\Facades\Auth;
use Kreait\Firebase\Contract\Auth;

class RoomController extends Controller
{
    private const ALMOSYNC_URL = 'https://juuq-test-api.herokuapp.com';

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    public function create(Request $request){
        try{
            createHelper($request);
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
                $this->createHelper($request);
            }else{
                $this->addUser($request, $room);
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
        $user_id = auth()->id();
        $user = User::find($user_id);

        $room->users()->attach($user_id);
        $room->user_count += 1;

        if($room->user_count == 4){
            $room->status_id = 1;
        }else if($room->user_count == 2){
            selectItem();
        }

        $room->save();
        
        return response()->json([
            "room_uniq_id" => $room->id,
            "als_key" => $room->als_key
        ], 200);
    }

    public function leaveUser(Request $request, Room $room){
        try{
            $user_id = auth()->id();;
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
            almosyncPost($room);
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
        $room->als_key = md5(uniqid());
        $room->save();
        $lastroom = Room::latest()->first();
        dd($lastroom);
        addUser($request, $lastroom);
        $lastroom->save();
    }

    private function almosyncPost(Room $room){
        $client = new Client([
            'base_uri' => ALMOSYNC_URL,
        ]);

        $method = 'POST';
        $options = [];
        $response = $client->request('POST','/api/v1/messages/cards',['form_params' => ['almosync_key' => $room->als_key]]);
    }
}
