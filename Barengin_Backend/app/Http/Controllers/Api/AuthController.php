<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponse;

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $user = DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => trim($data['name']),
                'username' => $data['username'] ?? null,
                'email' => strtolower(trim($data['email'])),
                'password' => $data['password'],
                'adult_confirmed_at' => now(),
                'terms_accepted_at' => now(),
                'privacy_accepted_at' => now(),
                'is_active' => true,
            ]);

            $user->profile()->create([
                'display_name' => trim($data['name']),
                'country_code' => 'ID',
            ]);

            return $user;
        });

        $token = $user->createToken($data['device_name'] ?? 'barengin-mobile')->plainTextToken;
        $user->load(['profile', 'interests']);

        return $this->success([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => UserResource::make($user)->resolve($request),
        ], 'Registrasi berhasil.', 201);
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();
        $user = User::query()->where('email', strtolower(trim($data['email'])))->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return $this->failure('Email atau password tidak sesuai.', 401);
        }

        if (! $user->is_active) {
            return $this->failure('Akun sedang dinonaktifkan.', 403);
        }

        $user->forceFill(['last_login_at' => now()])->save();

        $token = $user->createToken($data['device_name'] ?? 'barengin-mobile')->plainTextToken;
        $user->load(['profile', 'interests']);

        return $this->success([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => UserResource::make($user)->resolve($request),
        ], 'Login berhasil.');
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return $this->success(null, 'Logout berhasil.');
    }

    public function me(Request $request)
    {
        $user = $request->user()->load(['profile', 'interests']);

        return $this->success(
            UserResource::make($user)->resolve($request),
            'Data pengguna berhasil dimuat.'
        );
    }
}
