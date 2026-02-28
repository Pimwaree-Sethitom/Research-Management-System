<?php

namespace RMS\Backend\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtService
{
    private string $secret = "super_secret_key_change_this";
    private string $algo = "HS256";

    public function generate(array $payload): string
    {
        $payload['iat'] = time();
        $payload['exp'] = time() + (60 * 60); // 1 ชั่วโมง

        return JWT::encode($payload, $this->secret, $this->algo);
    }

    public function validate(string $token): object
    {
        return JWT::decode($token, new Key($this->secret, $this->algo));
    }
}