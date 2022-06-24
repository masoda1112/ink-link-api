<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Auth;

class UserController extends Controller
{
    /**
     * @var Firebase
     */
    private $firebase;

    /**
     * コンストラクタインジェクションで $firebase を用意します
     * @param Firebase $firebase
    */
    
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }
    //create
    public function create(Request $request, $uid){
        // リクエストボディはrequest->input()で書く必要があるかも？
        // $userName = $this->auth->getUser($uid)->displayName;
        $userName = $request->name;
        if(!$userName){
            $userName = "nanashisan";
        }
        // $user = $this->auth.currentUser;
        $user = new User();
        $user->name = $userName;
        // $user->sex = (bool)$request->sex;
        // $user->age = intval($request->age);
        $user->firebase_uid = $uid;
        $user->status = false;
        $user->save();
        return $user;
    }

    public function show(Request $request){
        // パスパラメータはrequest->で取得可能
        try{
            $cullentUri = explode('/',$_SERVER['REQUEST_URI']);
            $id = end($cullentUri);
            $user = User::find($id);
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

    // 引数を$idToken→Requestに変更
    public function login(Request $request)
    {
        // headerからuid取得
        // $header = $request->headers;
        // $idToken = $header->get('token');

        $header = getallheaders();
        $authorization = $header['Authorization'];
        $idToken = ltrim(ltrim($authorization, 'Bearer'));
        $uid = $this->getUidByToken($idToken);
        dd($uid);
        //laravelでtokenを発行する為にlaravelの導入とmodelに追加が必要
        //uidを使ってトークンを取得
        $user = User::where('firebase_uid', $uid)->first();
        if(!$user){
            $user = $this->create($request, $uid);
        }

        $tokenResult = $user->createToken('Personal Access Token');
        // トークンの期限
        // $expires_at = Carbon::now()->addWeeks(1);

        $user->access_token = $tokenResult->accessToken;
        $user->save();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            // 'expires_at' => Carbon::parse(
            //     $expires_at
            // )->toDateTimeString()
        ]);
    }

    //トークンからUidを取得する。
    public function getUidByToken($idToken)
    {
        try {
            $verifiedIdToken = $this->auth->verifyIdToken($idToken);
        } catch (InvalidToken $e) {
            echo 'The token is invalid: ' . $e->getMessage();
        } catch (\InvalidArgumentException $e) {
            echo 'The token could not be parsed: ' . $e->getMessage();
        }

        $uid = $verifiedIdToken->claims()->get('sub');

        return $uid;
    }
}
