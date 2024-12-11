<?php

namespace Decodeblock\ApiUtility;

use Decodeblock\ApiUtility\Exceptions\IsNullException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class ApiUtility
{
    protected $secretKey;

    protected $client;

    protected $baseUrl;

    protected $response;

    public function __construct()
    {
        $this->setSecretKey();
        $this->setBaseUrl();
        $this->setClient();
    }

    private function setSecretKey()
    {
        $this->secretKey = Config::get('ercaspay.secretKey');
    }

    private function setBaseUrl()
    {
        $this->baseUrl = Config::get('ercaspay.baseUrl');
    }

    private function setClient($verifySsl = true)
    {
        // initiate Guzzle HTTP client or any preferred client.
        $authBearer = 'Bearer ' . $this->secretKey;

        $this->client = new Client(
            [
                'base_uri' => $this->baseUrl,
                'verify' => $verifySsl,
                'headers' => [
                    'Authorization' => $authBearer,
                    'Content-Type'  => 'application/json',
                    'Accept'        => 'application/json'
                ]
            ]
        );
    }

    private function setResponse($relativeUrl, $method, $body = [])
    {
        if (empty($method)) {
            throw new IsNullException("Empty method not allowed");
        }

        try {

            $this->response = $this->client->{strtolower($method)}(
                $relativeUrl,
                ["body" => json_encode($body)]
            );
        } catch (ClientException $e) {
            // Handle 4xx errors
            $errorResponse = json_decode($e->getResponse()->getBody(), true);
            throw new \Exception('Client error: ' . $errorResponse['errorMessage'] ?? $e->getMessage());
        } catch (ServerException $e) {
            // Handle 5xx errors
            throw new \Exception('Server error: ' . $e->getMessage());
        } catch (RequestException $e) {
            // Handle other types of exceptions (e.g., network errors)
            throw new \Exception('Request failed: ' . $e->getMessage());
        }

        return $this;
    }

    private function getResponse()
    {
        if (empty($this->response)) {
            throw new IsNullException("No response found");
        }
        return json_decode($this->response->getBody(), true);
    }

    public function initiateTransaction($data)
    {
        // Validate and set defaults for required fields
        $requiredFields = [
            'amount',
            'paymentReference',
            'paymentMethods',
            'customerName',
            'customerEmail',
            'currency'
        ];

        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new \InvalidArgumentException("The field '{$field}' is required.");
            }
        }

        $this->setResponse('/api/v1/payment/initiate', 'POST', $data);

        $response = $this->getResponse();

        return $response;
    }

    public function verifyTransaction($transactionRef)
    {
        if (empty($transactionRef)) {
            throw new IsNullException("Transaction reference is required.");
        }

        $this->setResponse('/api/v1/payment/transaction/verify/' . $transactionRef, 'GET');

        return $this->getResponse();
    }

    public function initiateBankTransfer($transactionRef)
    {
        if (empty($transactionRef)) {
            throw new IsNullException("Transaction reference is required.");
        }

        $this->setResponse('/api/v1/payment/bank-transfer/request-bank-account/' . $transactionRef, 'GET');

        return $this->getResponse();
    }

    public function initiateUssdTransaction($transactionRef, $bankName)
    {
        if (empty($transactionRef)) {
            throw new IsNullException("Transaction reference is required.");
        }
        if (empty($bankName)) {
            throw new IsNullException("Bank name is required.");
        }

        $this->setResponse('/api/v1/payment/ussd/request-ussd-code/' . $transactionRef, 'POST', ['bank_name' => $bankName]);

        return $this->getResponse();
    }

    public function getBankListForUssd()
    {
        $this->setResponse('/api/v1/payment/ussd/supported-banks', 'GET');

        return $this->getResponse();
    }

    public function generatePaymentReferenceUuid(): string
    {
        return Str::uuid()->toString();
    }

    public function fetchTransactionDetails($transactionRef)
    {
        if (empty($transactionRef)) {
            throw new IsNullException("Transaction reference is required.");
        }

        $this->setResponse('/api/v1/payment/details/' . $transactionRef, 'GET');

        return $this->getResponse();
    }

    public function fetchTransactionStatus($transactionRef, $paymentReference, $paymentMethod)
    {
        if (empty($transactionRef)) {
            throw new IsNullException("Transaction reference is required.");
        }
        if (empty($paymentMethod)) {
            throw new IsNullException("Payment method is required.");
        }
        if (empty($paymentReference)) {
            throw new IsNullException("Payment reference is required.");
        }

        $this->setResponse('/api/v1/payment/status/' . $transactionRef, 'POST', ['payment_method' => $paymentMethod, 'reference' => $paymentReference]);

        return $this->getResponse();
    }

    public function cancelTransaction($transactionRef)
    {
        if (empty($transactionRef)) {
            throw new IsNullException("Transaction reference is required.");
        }

        $this->setResponse('/api/v1/payment/cancel/' . $transactionRef, 'GET');

        return $this->getResponse();
    }

    public function initiateCardTransaction($transactionRef, $cardNumber, $cardCvv, $cardExpiryMonth, $cardExpiryYear, $pin)
    {
        if (empty($transactionRef)) {
            throw new IsNullException("Transaction reference is required.");
        }

        $cardDetails = [
            'cvv' => $cardCvv,
            'pin' => $pin,
            'expiryDate' => $cardExpiryMonth . '' . $cardExpiryYear,
            'pan' => $cardNumber
        ];

        $publicKeyPath = "C:\Users\gabri\.ssh\public_key.pem";

        $encryptor = new CardEncryptor($publicKeyPath);
        $encryptedCard = $encryptor->encrypt($cardDetails);

        // Function not working yet
        //BUG: Client error: The device details field is required.
        //TODO: Add devicedetail to this guy
        $data = [
            'transactionReference' => $transactionRef,
            'payload' => $encryptedCard
        ];

        $this->setResponse('/api/v1/payment/cards/initialize', 'POST', $data);

        return $this->getResponse();
    }
}
