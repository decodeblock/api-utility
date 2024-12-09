<?php

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Response as ResponseFacade;

use Decodeblock\ApiUtility\Traits\ApiResponder;

// Anonymous class to use the trait
beforeEach(function () {
    $this->testClass = new class {
        use ApiResponder;
    };
});

it('returns a success response with metadata', function () {
    $message = 'Operation successful';
    $data = ['key' => 'value'];
    $metadata = ['page' => 1, 'total' => 100];

    $response = $this->testClass->successResponse($message, Response::HTTP_OK, $data, $metadata);

    expect($response->getStatusCode())->toBe(Response::HTTP_OK)
        ->and($response->getData(true))->toMatchArray([
            'success' => true,
            'message' => $message,
            'code' => Response::HTTP_OK,
            'data' => $data,
            'metadata' => $metadata,
        ]);
});

it('returns a failure response with metadata', function () {
    $message = 'An error occurred';
    $code = Response::HTTP_BAD_REQUEST;
    $data = ['error_detail' => 'Invalid request'];
    $metadata = ['attempt' => 3];

    $response = $this->testClass->failureResponse($message, $code, $data, $metadata);

    expect($response->getStatusCode())->toBe($code)
        ->and($response->getData(true))->toMatchArray([
            'success' => false,
            'message' => $message,
            'code' => $code,
            'data' => $data,
            'metadata' => $metadata,
        ]);
});

it('returns a me endpoint response for logged-in users', function () {
    $user = ['id' => 1, 'name' => 'John Doe'];

    $response = $this->testClass->meEndpointResponse($user);

    expect($response->getStatusCode())->toBe(Response::HTTP_OK)
        ->and($response->getData(true))->toMatchArray([
            'logged_in' => true,
            'user' => $user,
        ]);
});

it('returns a me endpoint response for logged-out users', function () {
    $response = $this->testClass->meEndpointResponse(null);

    expect($response->getStatusCode())->toBe(Response::HTTP_OK)
        ->and($response->getData(true))->toMatchArray([
            'logged_in' => false,
            'user' => null,
        ]);
});
