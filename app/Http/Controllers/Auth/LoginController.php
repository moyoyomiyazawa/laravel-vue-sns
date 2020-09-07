<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

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

    /**
     * 各サービスの認証画面へリダイレクトする
     *
     * @param string $provider
     * @return void
     */
    public function redirectToProvider(string $provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback(Request $request, string $provider)
    {
        // サービスからユーザー情報を取得
        $providerUser = Socialite::driver($provider)->stateless()->user();

        // usersテーブルにサービスのメールアドレスが存在するか調べる
        // コレクションのメソッドfirst()は存在しない場合nullを返す
        $user = User::where('email', $providerUser->getEmail())->first();

        // 既に登録済のユーザの場合
        if ($user) {
            // ログイン状態にする（第2引数をtrueにすることでログイン状態を維持できる）
            $this->guard()->login($user, true);
            // ログイン後の画面（記事一覧）へ遷移させる
            return $this->sendLoginResponse($request);
        }

        // 未登録ユーザの場合（usersテーブルにサービスのメールアドレスが存在しない場合）
        return redirect()->route('register.{provider}', [
            'provider' => $provider,
            'email' => $providerUser->getEmail(),
            'token' => $providerUser->token,
        ]);
    }
}
