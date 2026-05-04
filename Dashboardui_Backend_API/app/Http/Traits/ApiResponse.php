<?php

namespace App\Http\Traits;

trait ApiResponse
{
    /**
     * Return a success JSON response
     *
     * @param mixed $data
     * @param string|null $message
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function success($data = null, $message = null, $code = 200)
    {
        $response = [
            'success' => true,
            'timestamp' => now()->toIso8601String(),
            'version' => 'v1',
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        if ($message !== null) {
            $response['message'] = $message;
        }

        return response()->json($response, $code);
    }

    /**
     * Return an error JSON response
     *
     * @param string $message
     * @param int $code
     * @param mixed $errors
     * @return \Illuminate\Http\JsonResponse
     */
    protected function error($message, $code = 400, $errors = null)
    {
        $response = [
            'success' => false,
            'message' => $message,
            'timestamp' => now()->toIso8601String(),
            'version' => 'v1',
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Return a validation error JSON response
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @return \Illuminate\Http\JsonResponse
     */
    protected function validationError($validator)
    {
        return $this->error(
            'Validation failed',
            422,
            $validator->errors()
        );
    }

    /**
     * Return an unauthorized JSON response
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function unauthorized($message = 'Unauthorized')
    {
        return $this->error($message, 401);
    }

    /**
     * Return a forbidden JSON response
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function forbidden($message = 'Forbidden')
    {
        return $this->error($message, 403);
    }

    /**
     * Return a not found JSON response
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function notFound($message = 'Resource not found')
    {
        return $this->error($message, 404);
    }
}
