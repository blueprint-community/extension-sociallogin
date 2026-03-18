<?php

namespace Pterodactyl\BlueprintFramework\Extensions\sociallogin\Drivers;

use GuzzleHttp\RequestOptions;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;

class OsuSocialiteProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * {@inheritdoc}
     */
    protected $scopes = [
        'identify'
    ];

    /**
     * {@inheritdoc}
     */
    protected $consent = false;

    /**
     * {@inheritdoc}
     */
    protected $scopeSeperator = '+';

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase(
            'https://osu.ppy.sh/oauth/authorize',
            $state
        );
    }

    /**
     * Whether to prompt the user for consent every time or not.
     *
     * @return $this
     */
    public function withConsent()
    {
        $this->consent = true;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return 'https://osu.ppy.sh/oauth/token';
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessTokenResponse($code)
    {
        $url = $this->getTokenUrl();
        $postFields = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->redirectUrl
        ];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Bearer '.$this->clientSecret
        ]);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new \Exception('cURL error: ' . curl_error($ch));
        }
        curl_close($ch);

        return json_decode($response, true);
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get(
            'https://osu.ppy.sh/api/v2/me',
            [
                RequestOptions::HEADERS => [
                    'Authorization' => 'Bearer '.$token,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
            ]
        );

        return json_decode((string) $response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'avatar' => $user['avatar_url'],
            'id' => $user['id'],
            'is_bot' => $user['is_bot'],
            'is_restricted' => $user['is_restricted'],
            'is_supporter' => $user['is_supporter'],
            'username' => $user['username'],
            'nickname' => $user['username'],
            'name' => $user['username'] ?? null,
        ]);
    }
}
