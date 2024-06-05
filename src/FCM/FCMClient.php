<?php

namespace HasibKamal\LaravelFCM\FCM;

use GuzzleHttp\Client;

class FCMClient
{
    protected $client;
    protected $projectId;
    protected $clientEmail;
    protected $privateKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->projectId = config('fcm.project_id');
        $this->clientEmail = config('fcm.client_email');
        $this->privateKey = config('fcm.private_key');
    }

    public function sendNotification(array $tokens, array $notification)
    {
        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";
        $headers = [
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
            'Content-Type' => 'application/json',
        ];

        $body = [
            'message' => [
                'token' => $tokens,
                'notification' => $notification,
            ]
        ];

        $response = $this->client->post($url, [
            'headers' => $headers,
            'json' => $body,
        ]);

        return json_decode($response->getBody(), true);
    }

    protected function getAccessToken()
    {
        $jwt = $this->createJWT();
        $client = new Client();

        $response = $client->post('https://oauth2.googleapis.com/token', [
            'form_params' => [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ],
        ]);

        $token = json_decode($response->getBody(), true);

        return $token['access_token'];
    }

    protected function createJWT()
    {
        $now = time();
        $expires = $now + 3600; // 1 hour expiration

        $payload = [
            'iss' => $this->clientEmail,
            'sub' => $this->clientEmail,
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $expires,
        ];

        $header = [
            'alg' => 'RS256',
            'typ' => 'JWT',
        ];

        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($header)));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($payload)));

        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, openssl_get_privatekey($this->privateKey), true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }
}
