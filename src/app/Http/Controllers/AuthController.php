<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthLoginRequest;
use App\Http\Requests\AuthRegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(AuthRegisterRequest $req)
    {
        $data = $req-> validated();
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        return ApiResponse::success(new UserResource($user), 'Kayıt Başarılı', 201);
    }

    public function login(AuthLoginRequest $req)
    {
        if(!$token = auth('api')->attempt($req->validated())){
            return ApiResponse::error('Geçersiz Kimlik Bilgileri', [], 401);
        }
        return ApiResponse::success(['token' => $token], 'Giriş Başarılı');
    }

    public function profile(Request $r)
    {
        return ApiResponse::success(new UserResource($r->user()));
    }

    public function updateProfile(Request $r)
    {
        $r->validate([
            'name'      =>  ['sometimes','string','min:2'],
            'password'  =>  ['sometimes','string','min:8'],
        ]);
        $user = $r->user();
        if($r->filled('name'))          $user->name  = $r->input('name');
        if($r->filled('password'))      $user->password = Hash::make($r->input('password'));
        $user->save();

        return ApiResponse::success(new UserResource($user), 'Profil güncellendi.');
    }
}
