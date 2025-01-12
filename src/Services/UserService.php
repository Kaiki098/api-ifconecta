<?php

namespace App\Services;

use App\Utils\Validator;
use Exception;
use App\Http\JWT;

class UserService
{
    public static function auth(array $data) 
    {
        try {
            // Verifica se todos os campos estão preenchidos
            $fields = Validator::validate([
                'username' => $data['username'],
                'password' => $data['password']
            ]);

            if ($fields['username'] == 'admin' && $fields['password'] == '#ifconecta2025') {
                return JWT::generate(['username' => $fields['username']]);
            }

            return ['error' => 'Invalid username or password. Please try again.'];

        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
     }
}