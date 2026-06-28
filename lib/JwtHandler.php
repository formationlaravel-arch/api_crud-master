<?php

class JwtHandler
{
    private string $secret;
    private string $issuer;
    private string $audience;
    private int $expire;

    public function __construct()
    {
        $config = require __DIR__ . '/../config/config.php';
        $jwtConfig = $config['jwt'];
        $this->secret = $jwtConfig['secret'];
        $this->issuer = $jwtConfig['issuer'];
        $this->audience = $jwtConfig['audience'];
        $this->expire = $jwtConfig['expire'];
    }

    public function generateToken(array $payload): string
    {
        $header = $this->base64UrlEncode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
        $issuedAt = time();
        $data = array_merge($payload, [
            'iat' => $issuedAt,
            'exp' => $issuedAt + $this->expire,
            'iss' => $this->issuer,
            'aud' => $this->audience,
        ]);
        $payloadEncoded = $this->base64UrlEncode(json_encode($data));
        $signature = $this->sign($header . '.' . $payloadEncoded);
        return $header . '.' . $payloadEncoded . '.' . $signature;
    }

    public function validateToken(string $token): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }
        [$header, $payload, $signature] = $parts;
        $expectedSignature = $this->sign($header . '.' . $payload);
        if (!hash_equals($expectedSignature, $signature)) {
            return null;
        }
        $decoded = json_decode($this->base64UrlDecode($payload), true);
        if (!$decoded || !isset($decoded['exp']) || time() > $decoded['exp']) {
            return null;
        }
        return $decoded;
    }

    private function sign(string $data): string
    {
        return $this->base64UrlEncode(hash_hmac('sha256', $data, $this->secret, true));
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $data): string
    {
        $padding = 4 - (strlen($data) % 4);
        if ($padding < 4) {
            $data .= str_repeat('=', $padding);
        }
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
