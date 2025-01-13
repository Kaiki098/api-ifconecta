<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Services\UserService;


class UserController
{
    public function login(Request $request, Response $response)
    {
        $body = $request::body();

        $userService = UserService::auth($body);

        if (isset($userService['error'])) {
            return $response::json([
                'error' => true,
                'success' => false,
                'message' => $userService['error']
            ], 401);
        }

        $response::json([
            'error' => false,
            'success' => true,
            'jwt' => $userService
        ], 200);
    }

}