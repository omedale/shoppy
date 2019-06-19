<?php
namespace App\Helpers;

class ErrorHelper {
    public static function  USR_04() {
        return response()->json([
            'error' => [
                'status' => 400,
                'code' => "USR_04",
                'message' => "The email already exists.",
                'field'=> "email"
            ]
        ])
        ->setStatusCode(400);
    }

    public static function USR_02($errors) {
        return response()->json([
            'error' => [
                'status' => 500,
                'code' => "USR_02",
                'message' => "The field(s) are/is required.",
                'field_errors'=> $errors
            ]
        ])
        ->setStatusCode(500);
    }

    public static function AUT_02() {
        return response()->json([
            'error' => [
                'status' => 401,
                'code' => "AUT_02",
                'message' => "Access Unauthorized",
            ]
        ])
        ->setStatusCode(401);
    }
}
