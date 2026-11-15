<?php

namespace App\Auth;

use Google\Client;
use Exception;

class GoogleAuth
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'client_id' => GOOGLE_CLIENT_ID
        ]);
    }

    public function verifyToken(string $idToken): array
    {
        $payload = $this->client->verifyIdToken($idToken);

        if (!$payload) {
            throw new Exception('Invalid Google ID Token');
        }

        if (
            ($payload['aud'] ?? '') !== GOOGLE_CLIENT_ID ||
            !in_array(
                $payload['iss'] ?? '',
                ['accounts.google.com', 'https://accounts.google.com'],
                true
            )
        ) {
            throw new Exception('Invalid token issuer or audience');
        }

        if (($payload['email_verified'] ?? false) !== true) {
            throw new Exception('Google email is not verified');
        }

        if (empty($payload['sub']) || empty($payload['email'])) {
            throw new Exception('Missing Google account information');
        }

        return [
            'google_sub' => $payload['sub'],
            'name' => $payload['name'] ?? 'Google User',
            'email' => $payload['email'],
            'avatar' => $payload['picture'] ?? null
        ];
    }
}