<?php

namespace App\Services\Socialite;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\User;

abstract class HemisAbstractProvider extends AbstractProvider
{
    protected $scopes = [];

    abstract protected function getBaseUrl(): string;

    protected function getAuthUrl($state): string
    {
        return $this->buildAuthUrlFromBase($this->getBaseUrl() . '/oauth/authorize', $state);
    }

    protected function getTokenUrl(): string
    {
        return $this->getBaseUrl() . '/oauth/access-token';
    }

    protected function getUserByToken($token): array
    {
        $response = $this->getHttpClient()->get(
            $this->getBaseUrl() . '/oauth/api/user',
            [
                'query'   => ['fields' => 'id,uuid,type,name,login,picture,email,university_id,phone'],
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept'        => 'application/json',
                ],
            ]
        );

        return json_decode($response->getBody(), true) ?? [];
    }

    protected function mapUserToObject(array $user): User
    {
        return (new User())->setRaw($user)->map([
            'id'     => $user['id']      ?? null,
            'uuid'   => $user['uuid']    ?? null,
            'name'   => $user['name']    ?? null,
            'email'  => $user['email']   ?? null,
            'avatar' => $user['picture'] ?? null,
            'login'  => $user['login']   ?? null,
            'phone'  => $user['phone']   ?? null,
            'type'   => $user['type']    ?? null,
        ]);
    }
}
