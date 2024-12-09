<?php

namespace Decodeblock\ApiUtility\Traits;

use Illuminate\Http\Response;

trait ApiResponder
{
    public function successResponse($message, $code = Response::HTTP_OK, $data = null, $metadata = null)
    {

        return response()->json([
            'success' => true,
            'message' => $message,
            'code' => $code,
            'data' => $data,
            'metadata' => $metadata,
        ], $code);
    }

    public function failureResponse($message, $code = Response::HTTP_INTERNAL_SERVER_ERROR, $data = null, $metadata = null)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'code' => $code,
            'data' => $data,
            'metadata' => $metadata,
        ], $code);
    }

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
