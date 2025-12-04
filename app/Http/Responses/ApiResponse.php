<?php

namespace App\Http\Responses;

class ApiResponse
{
    public static function success($data = null, $message = null, $code = 200, $meta = [])
    {
        $payload = ['success' => true, 'data' => $data];
        if ($message) $payload['message'] = $message;
        if (!empty($meta)) $payload['meta'] = $meta;
        return response()->json($payload, $code);
    }

    public static function error($message = null, $code = 422, $errors = [])
    {
        $payload = ['success' => false, 'message' => $message];
        if (!empty($errors)) $payload['errors'] = $errors;
        return response()->json($payload, $code);
    }
}
