<?php

namespace Pterodactyl\BlueprintFramework\Extensions\sociallogin\Drivers;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;
use Illuminate\Support\Facades\Http;

class PaymenterSocialiteProvider extends AbstractProvider implements ProviderInterface
{
    /**
    * {@inheritdoc}
    */
    protected $scopes = [
        'profile'
    ];

    /**
     * The separating character for the requested scopes.
     *
     * @var string
     */
    protected $scopeSeparator = ' ';

    private function getPaymenterUrl()
    {
        $baseUrl = config('services.paymenter.url');

        return rtrim($baseUrl, '/');
    }

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase($this->getPaymenterUrl() . '/oauth/authorize', $state);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return $this->getPaymenterUrl() . '/api/oauth/token';
    }

    public function getAccessTokenResponse($code)
    {
        $response = Http::asForm()->post($this->getTokenUrl(), [
            'grant_type'    => $this->getTokenFields($code)['grant_type'],
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri'  => $this->redirectUrl,
            'code'          => $code,
            'scope'         => $this->formatScopes($this->getScopes(), $this->scopeSeparator),
        ]);

        return $response->json();
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get($this->getPaymenterUrl() . '/api/me');

        return $response->json();
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw($user)->map([
            'id' => $user['id'],
            'email' => $user['email'],
            'name' => $user['first_name'] . ' ' . $user['last_name'],
            'email_verified' => $user['email_verified_at'] !== null,
        ]);
    }
}
