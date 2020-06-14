<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth')->except('login', 'register');
    }

    protected function create(array $data)
    {
        return User::forceCreate([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'api_token' => hash('sha256', $data['api_token']),
        ]);
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255', 'unique:users',],
            'email' => ['required', 'string', 'email', 'max:255',],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    protected function username()
    {
        return 'name';
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();
        ///
        //        if (!request('password')){
        //            return 123;
        //        }

        $api_token = Str::random(50);
        $data = array_merge($request->all(), compact('api_token'));
        $this->create($data);

        return compact('api_token');
    }
    public function logout()
    {
        auth()->user()->update(['api_token' => null]);

        return ['message' => '退出登录成功'];
    }

    public function login()
    {
        $user = User::where($this->username(), request($this->username()))
            ->firstOrFail();

        if (!password_verify(request('password'), $user->password)) {
            return response()->json(
                ['error' => '抱歉，账号名或者密码错误！'],
                403
            );
        }

        $api_token = Str::random(80);
        $user->update(['api_token' => hash('sha256', $api_token)]);

        return compact('api_token');
    }

    public function refresh()
    {
        $api_token = Str::random(80);
        auth()->user()->update(['api_token' => hash('sha256', $api_token)]);

        return compact('api_token');
    }
}
