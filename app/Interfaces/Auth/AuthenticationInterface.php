<?php

namespace App\Interfaces\Auth;

interface AuthenticationInterface
{

    public function register($request);


    public function login($request);

    public function logout($request);

    public function deleteUser($id);
}
