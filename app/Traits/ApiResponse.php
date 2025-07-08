<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
   /**
     * Returns a standardized success response with the provided data, message, and HTTP status code.
     *
     * This method formats the response to indicate a successful operation. It includes a success
     * flag, an optional message, the data to be returned, and an HTTP status code. The response
     * is structured as a JSON object and the status code defaults to 200 (OK), but can be customized.
     *
     * @param mixed $data The data to be included in the response, typically the result of the operation.
     * @param string|null $message An optional message providing additional context about the success.
     * @param int $code The HTTP status code for the response (default is 200).
     *
     * @return \Illuminate\Http\JsonResponse A JSON response containing the success status, message, data, and code.
     */
    public function success($code = 200, $message = null, $data = []): JsonResponse
    {
        return response()->json([
            'success' => (bool) true,
            'code' => (int) $code,
            'message' => $message,
            'data' => $data,
            'timestamp' => now()->toIso8601String() . ' GMT' . now()->format('P'),
        ], $code);
    }




    /**
     * Returns a standardized error response with the provided data, message, and HTTP status code.
     *
     * This method formats the response to indicate an error or failure in the operation. It includes
     * an error flag, an optional message, the error details or data, and an HTTP status code. The
     * response is structured as a JSON object, and the status code defaults to 500 (Internal Server Error),
     * but can be customized to reflect different types of errors.
     *
     * @param mixed $data The data to be included in the response, typically containing error details.
     * @param string|null $message An optional message providing additional context about the error.
     * @param int $code The HTTP status code for the response (default is 500).
     *
     * @return \Illuminate\Http\JsonResponse A JSON response containing the error status, message, data, and code.
     */
    public function error($code = 500, $message = null, $error = []): JsonResponse
    {
        return response()->json([
            'status' => (bool) false,
            'code' => (int) $code,
            'message' => $message,
            'error' => $error,
            'timestamp' => now()->toIso8601String() . ' GMT' . now()->format('P'),
        ], $code);
    }
}