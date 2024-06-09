<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ApiController extends Controller
{
    /**
     * Sends normal JSON response
     *
     * @param mixed $result response data
     * @param string $message response contents description
     * @return Response|JsonResponse
     */
    final public function jsonResponse($result = null, $message = null): Response|JsonResponse
    {
        $response = [
            'success' => true,
            'data' => $result,
            'message' => $message,
        ];

        if (defined('LARAVEL_START') && config('app.debug')) {
            $response['profiling']['response_time'] = (float)sprintf('%01.4f', microtime(true) - LARAVEL_START);
        }

        return response()->json($response, 200, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Sends extended error response
     *
     * @param array|string $error error message
     * @param integer $code error code
     * @param array $errorMessages auxiliary error messages
     * @return Response|JsonResponse
     */
    final public function jsonError(array|string $error, $code = 400, $errorMessages = []): Response|JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
    }
}
