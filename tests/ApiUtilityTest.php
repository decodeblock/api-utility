<?php

use Decodeblock\ApiUtility\ApiUtility;
use Decodeblock\ApiUtility\Exceptions\IsNullException;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Config;
use Mockery;
use Mockery\MockInterface;

function accessPrivateProperty($object, $propertyName)
{
    $reflection = new ReflectionClass($object);
    $property = $reflection->getProperty($propertyName);
    $property->setAccessible(true);
    return $property;
}

function callPrivateMethod($object, $methodName, ...$args)
{
    $reflection = new ReflectionClass($object);
    $method = $reflection->getMethod($methodName);
    $method->setAccessible(true);

    // Invoking the method and passing arguments
    return $method->invoke($object, ...$args);
}

beforeEach(function () {
    // Mock only the necessary config values for ApiUtility
    $this->mockConfig = Mockery::mock('alias:Illuminate\Support\Facades\Config');
    $this->mockConfig->shouldReceive('get')
        ->with('ercaspay.secretKey')->andReturn('ECRS-TEST-SK9suJneFkk1o8gBaUmOHCBIt9jRWN88QbaKAvoBRu');
    $this->mockConfig->shouldReceive('get')
        ->with('ercaspay.baseUrl')->andReturn('https://gw.ercaspay.com/api/v1');

    // // Mock database config to avoid triggering database-related code
    // $this->mockConfig->shouldReceive('get')
    //     ->with('database.default')->andReturn(null);
    // $this->mockConfig->shouldReceive('get')
    //     ->with('database.connections.mysql')->andReturn(null);
});

// Ensure Mockery is closed after each test
afterEach(fn() => Mockery::close());

it('initializes with correct configuration', function () {
    $apiUtility = new ApiUtility();

    $secretKey = accessPrivateProperty($apiUtility, 'secretKey')->getValue($apiUtility);
    $baseUrl = accessPrivateProperty($apiUtility, 'baseUrl')->getValue($apiUtility);
    $client = accessPrivateProperty($apiUtility, 'client')->getValue($apiUtility);
    // Your assertions
    expect($secretKey)->toBe('ECRS-TEST-SK9suJneFkk1o8gBaUmOHCBIt9jRWN88QbaKAvoBRu');
    expect($baseUrl)->toBe('https://gw.ercaspay.com/api/v1');
});

it('throws exception when required fields are not provided', function () {
    $apiUtility = new ApiUtility();

    expect(fn() => $apiUtility->initiateTransaction([]))
        ->toThrow(InvalidArgumentException::class);
});

it('generates a payment reference in UUID format', function () {
    $apiUtility = new ApiUtility();
    $paymentRef = $apiUtility->generatePaymentReferenceUuid();
    expect($paymentRef)
        ->toBeString()
        ->toMatch('/[0-9a-fA-F\-]{36}/'); // Matches UUID format
});

// it('does my thirdparty api call testing', function () {
//     // Arrange: Prepare the data to be sent
//     $data = [
//         "amount" => 100,
//         "paymentReference" => "R5md7gd9b4s3h2j5d67g",
//         "paymentMethods" => "card,bank-transfer,ussd,qrcode",
//         "customerName" => "John Doe",
//         "customerEmail" => "johndoe@gmail.com",
//         "customerPhoneNumber" => "09061626364",
//         "redirectUrl" => "https://omolabakeventures.com",
//         "description" => "The description for this payment goes here",
//         "currency" => "NGN",
//         "feeBearer" => "customer",
//         "metadata" => [
//             "firstname" => "Ola",
//             "lastname" => "Benson",
//             "email" => "iie@mail.com"
//         ]
//     ];

//     // Act: Instantiate ApiUtility and call initiateTransaction
//     $apiUtility = new ApiUtility();
//     callPrivateMethod($apiUtility, 'setClient', false);  // This will call the private setClient method



//     // Ensure setResponse was called correctly and set the response
//     $response = $apiUtility->initiateCardTransaction('ERCS|20241211043045|1733887845644','4000000000002503',111,03,50,1111);

//     dd($response);
// });

