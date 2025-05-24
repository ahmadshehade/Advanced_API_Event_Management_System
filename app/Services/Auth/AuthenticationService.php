<?php

namespace App\Services\Auth;

use App\Traits\ManagerFile;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use App\Interfaces\Auth\AuthenticationInterface;
use App\Models\User;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthenticationService implements AuthenticationInterface
{

    use ManagerFile;
    /**
     * Summary of register
     * @param mixed $request
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return array{code: int, data: array{token: string, user: User, message: string}}
     */
    public function register($request)
    {
        try {
            DB::beginTransaction();
            $validatedData = $request->validated();
            $user = new User();
            $user->name = $validatedData['name'];
            $user->email = $validatedData['email'];
            $user->password = Hash::make($validatedData['password']);
            $user->save();

            $token = $user->createToken('auth_user')->plainTextToken;
            $user->assignRole('userRole');
            $data = [
                'message' => 'Successfully Register new User',
                'data' => [
                    'token' => $token,
                    'user' => $user,
                ],
                'code' => 201
            ];
            $this->uploadImages(
                $request,
                'image',
                User::class,
                $user->id,
                'Users/' . $user->name . '-' . $user->id
            );

            DB::commit();




            return $data;
        } catch (Exception $e) {
            DB::rollBack();
            throw new HttpResponseException(
                response()->json([
                    'message' => $e->getMessage()
                ], 500)
            );
        }
    }


    /**
     * Summary of login
     * @param mixed $request
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @throws \Illuminate\Http\Exceptions\ThrottleRequestsException
     * @return array{code: int, data: array{token: string, user: User, message: string}}
     */
    public function login($request)
    {
        $email = $request->input('email');
        $key = Str::lower($email) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw new HttpResponseException(response()->json([
                'message' => "Too many login attempts. Please try again in $seconds seconds."
            ], 429));
        }

        try {
            $validateData = $request->validated();

            $user = User::where('email', $validateData['email'])->first();

            if (!$user || !Hash::check($validateData['password'], $user->password)) {
                RateLimiter::hit($key, 60);
                throw new ThrottleRequestsException(response()->json([
                    'message' => 'User not found or invalid password'
                ], 404));
            }

            RateLimiter::clear($key);

            $token = $user->createToken('auth_user')->plainTextToken;

            $data = [
                'message' => 'Successfully logged in user',
                'data' => [
                    'token' => $token,
                    'user' => $user,
                ],
                'code' => 200
            ];
            return $data;
        } catch (Exception $e) {
            throw new HttpResponseException(response()->json([
                'message' => $e->getMessage()
            ], 500));
        }
    }




    /**
     * Summary of logout
     * @param mixed $request
     * @return array{code: int, data: bool, message: string}
     */
    public function logout($request)
    {
        $user = $request->user();
        $user->tokens()->delete();
        $data = [
            'message' => 'Successfully Logout User',
            'data' => true,
            'code' => 200
        ];
        return $data;
    }

    /**
     * Summary of deleteUser
     * @param mixed $id
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return array{code: int, data: bool, message: string}
     */
    public function deleteUser($id)
    {
        $user = User::find($id);
        if (!$user) {
            throw new HttpResponseException(
                response()->json([
                    'message' => 'User Not Found'
                ], 404)
            );
        }
        $imageIds = $user->image;
        if ($imageIds) {
            $this->deleteImages(User::class, $user->id, $imageIds);
        }
        $user->delete();
        return [
            'message' => 'Successfully Deleted User',
            'data' => true,
            'code' => 200
        ];
    }
}
