<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Interfaces\Auth\AuthenticationInterface;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $auth;

    /**
     * Summary of __construct
     * @param \App\Interfaces\Auth\AuthenticationInterface $auth
     */
    public function __construct(AuthenticationInterface $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Summary of registerUser
     * @param \App\Http\Requests\Auth\RegisterRequest $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function registerUser(RegisterRequest $request)
    {
        $data = $this->auth->register($request);
        return $this->getMessage($data['message'], $data['data'], $data['code']);
    }

    /**
     * Summary of login
     * @param \App\Http\Requests\Auth\LoginRequest $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $data = $this->auth->login($request);
        return $this->getMessage($data['message'], $data['data'], $data['code']);
    }

    /**
     * Summary of logout
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $data = $this->auth->logout($request);
        return $this->getMessage($data['message'], $data['data'], $data['code']);
    }

    /**
     * Summary of destroy
     * @param mixed $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $data = $this->auth->deleteUser($id);
        return $this->getMessage($data['message'], $data['data'], $data['code']);
    }
}
