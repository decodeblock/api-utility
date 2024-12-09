<?php

namespace Decodeblock\ApiUtility\Traits;

use Illuminate\Http\Response;

trait ApiResponder
{
    /**
     * Returns a success response with the given message, code, data and metadata
     *
     * @param string $message Response message
     * @param int $code HTTP status code
     * @param mixed $data Response data
     * @param mixed|null $metadata Optional metadata
     * @return \Illuminate\Http\JsonResponse
     */
    public function successResponse($message, $code , $data, $metadata = null)
    {

        return response()->json([
            'success' => true,
            'message' => $message,
            'code' => $code,
            'data' => $data,
            'metadata' => $metadata,
        ], $code);
    }

    /**
     * Returns a failure response with the given message, code, data and metadata
     *
     * @param string $message Response message
     * @param int $code HTTP status code
     * @param mixed $data Response data
     * @param mixed|null $metadata Optional metadata
     * @return \Illuminate\Http\JsonResponse
     */
    public function failureResponse($message, $code, $data, $metadata = null)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'code' => $code,
            'data' => $data,
            'metadata' => $metadata,
        ], $code);
    }

    /**
     * Returns a response for the /me endpoint with user login status and details
     *
     * @param mixed|null $user User object or null
     * @return \Illuminate\Http\JsonResponse
     */
    public function meEndpointResponse($user)
    {
        $isLoggedIn = $user == null ? false : true;

        return response()->json(
            [
                'logged_in' => $isLoggedIn,
                'user' => $user,
            ],
        );
    }
}
