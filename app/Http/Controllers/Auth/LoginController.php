<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Http\Controllers\UserController;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function redirectToProvider()
    {
        return Socialite::driver('facebook')->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback()
    {
        $user = Socialite::driver('facebook')->user();

        // すでにFacebook登録済みじゃなかったらユーザーを登録する
        $userModel = User::where('facebook_id', $user->id)->first();

        if (!$userModel) {
            $userModel = new User();
            $userModel->name = $user->name;
            $userModel->sex = (bool)$user->sex;
            $userModel->age = intval($user->age);
            $userModel->facebook_id = $user->facebook_id;
            $userModel->password = $user->password;
            $userModel->status = false;
            $userModel->save();
        }
        // ログインする
        Auth::login($userModel);
        // /homeにリダイレクト
        return Redirect::route('home');
    }
}
