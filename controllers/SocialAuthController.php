<?php

namespace Pterodactyl\BlueprintFramework\Extensions\sociallogin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;
use Pterodactyl\Http\Controllers\Controller;
use Pterodactyl\Models\SocialProvider;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Event;
use Pterodactyl\Models\SocialConnection;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Pterodactyl\BlueprintFramework\Libraries\ExtensionLibrary\Admin\BlueprintAdminLibrary as BlueprintExtensionLibrary;
use Pterodactyl\Facades\Activity;
use Pterodactyl\Models\User;
use Pterodactyl\Services\Users\UserCreationService;

class SocialAuthController extends Controller
{
    public function __construct(
        private BlueprintExtensionLibrary $blueprint,
        private UserCreationService $creationService
    ) {
    }

    /**
     * Get available Social providers
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function providers(Request $request): JsonResponse
    {
        $availableProviders = SocialProvider::where('enabled', true)->whereNotNull('client_id')->whereNotNull('client_secret')->get();
        $availableProviders = $availableProviders->map(function ($provider) {
            return $this->formatProvider($provider);
        });
        return response()->json($availableProviders);
    }

    /**
     * Get available Social connections
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function connections(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            abort(404);
        }

        $availableProviders = SocialProvider::where('enabled', true)->whereNotNull('client_id')->whereNotNull('client_secret')->get();
        $connections = $availableProviders->map(function ($provider) use ($user) {
            $formattedProvider = $this->formatProvider($provider);

            $connection = SocialConnection::where('user_id', $user->id)->where('provider_id', $provider->id)->first();
            if ($connection) {
                $formattedProvider['connection'] = [
                    'id' => $connection->auth_id,
                    'name' => $connection->auth_name,
                ];
            }
            return $formattedProvider;
        });
        return response()->json($connections);
    }

    /**
     * Redirect to the provider's website
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirect(Request $request, string $provider): RedirectResponse
    {
        $provider = SocialProvider::where('short_name', $provider)->first();
        if (!$provider) {
            abort(404);
        }
        if (!$provider->enabled) {
            abort(404);
        }
        if (!$provider->client_id || !$provider->client_secret) {
            abort(404);
        }

        $this->setProviderConfig($provider);

        $request->session()->put('social_provider', $provider->short_name);

        return Socialite::driver($provider->short_name)->redirect();
    }

    /**
     * Validate and login Social user.
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function callback(Request $request): RedirectResponse
    {
        $providerShortName = $request->session()->pull('social_provider');
        if (!$providerShortName) {
            return redirect()->route('index');
        }

        $provider = SocialProvider::where('short_name', $providerShortName)->first();
        if (!$provider) {
            abort(404, 'Social provider not found.');
        }

        $this->setProviderConfig($provider);
        $socialUser = Socialite::driver($provider->short_name)->user();

        $socialUserId = $socialUser->getId();
        $socialUserName = $socialUser->getName();
        $socialEmail = $socialUser->getEmail();

        // If user is already logged in, connect social account
        if ($request->user()) {
            return $this->connectSocialAccount($request->user(), $provider, $socialUserId, $socialUserName);
        }

        // If user is not logged in, attempt to find or create matching user
        $connection = SocialConnection::where('provider_id', $provider->id)->where('auth_id', $socialUserId)->first();
        if (!$connection) {
            $connection = $this->handleNewSocialUser($provider, $socialUserId, $socialUserName, $socialEmail);
            if (!$connection) {
                return redirect('/auth/login?message=notconnected&provider=' . $provider->short_name);
            }
        }

        $user = $connection->user;
        auth()->login($user, true);

        Activity::event('auth:social.login')
            ->property('provider', $provider->name)
            ->withRequestMetadata()
            ->subject($user)
            ->log();

        return redirect()->route('index');
    }

    /**
     * Link a social account to an already authenticated user.
     */
    protected function connectSocialAccount(User $user, SocialProvider $provider, string $socialUserId, string $socialUserName): RedirectResponse
    {
        $connection = SocialConnection::firstOrNew([
            'user_id' => $user->id,
            'provider_id' => $provider->id,
        ]);

        $connection->auth_id = $socialUserId;
        $connection->auth_name = $socialUserName;
        $connection->save();

        Activity::event('auth:social.connect')
            ->property('provider', $provider->name)
            ->property('name', $socialUserName)
            ->withRequestMetadata()
            ->subject($user)
            ->log();

        return redirect('/account/social');
    }

    /**
     * Handle registration or linking when a social user is new.
     */
    protected function handleNewSocialUser(SocialProvider $provider, string $socialUserId, string $socialUserName, ?string $socialEmail): ?SocialConnection
    {
        $allowRegister = $this->blueprint->dbGet('sociallogin', 'allow_register');
        $allowConnecting = $this->blueprint->dbGet('sociallogin', 'allow_connecting');

        if (!$allowRegister && !$allowConnecting) {
            return null;
        }

        // Try linking with an existing user by email
        if ($allowConnecting && $socialEmail) {
            if ($user = User::where('email', $socialEmail)->first()) {
                return SocialConnection::create([
                    'user_id' => $user->id,
                    'provider_id' => $provider->id,
                    'auth_id' => $socialUserId,
                    'auth_name' => $socialUserName,
                ]);
            }
        }

        // Create a new user if allowed
        if ($allowRegister) {
            $newUser = $this->creationService->handle([
                'username' => str_random(8),
                'email' => $socialEmail,
                'name_first' => str_random(8),
                'name_last' => str_random(8),
                'root_admin' => false,
            ]);

            return SocialConnection::create([
                'user_id' => $newUser->id,
                'provider_id' => $provider->id,
                'auth_id' => $socialUserId,
                'auth_name' => $socialUserName,
            ]);
        }

        return null;
    }

    private function setProviderConfig(SocialProvider $provider)
    {
        if (FacadesRequest::server('HTTP_X_FORWARDED_PROTO') == 'https') {
            URL::forceScheme('https');
        }

        config([
            "services.{$provider->short_name}.client_id" => $provider->client_id,
            "services.{$provider->short_name}.client_secret" => Crypt::decryptString($provider->client_secret),
            "services.{$provider->short_name}.redirect" => route('sociallogin.callback')
        ]);

        $providers = [
            'acclaim' => \SocialiteProviders\Acclaim\Provider::class,
            'admitad' => \SocialiteProviders\Admitad\Provider::class,
            'adobe' => \SocialiteProviders\Adobe\Provider::class,
            'aikido' => \SocialiteProviders\Aikido\Provider::class,
            'amazon' => \SocialiteProviders\Amazon\Provider::class,
            'amocrm' => \SocialiteProviders\AmoCRM\Provider::class,
            'angellist' => \SocialiteProviders\AngelList\Provider::class,
            'apple' => \SocialiteProviders\Apple\Provider::class,
            'appnet' => \SocialiteProviders\AppNet\Provider::class,
            'arcgis' => \SocialiteProviders\ArcGIS\Provider::class,
            'asana' => \SocialiteProviders\Asana\Provider::class,
            'atlassian' => \SocialiteProviders\Atlassian\Provider::class,
            'auth0' => \SocialiteProviders\Auth0\Provider::class,
            'authentik' => \SocialiteProviders\Authentik\Provider::class,
            'autodeskaps' => \SocialiteProviders\AutodeskAPS\Provider::class,
            'aweber' => \SocialiteProviders\Aweber\Provider::class,
            'azure' => \SocialiteProviders\Azure\Provider::class,
            'azureadb2c' => \SocialiteProviders\AzureADB2C\Provider::class,
            'battlenet' => \SocialiteProviders\Battlenet\Provider::class,
            'bexio' => \SocialiteProviders\Bexio\Provider::class,
            'binance' => \SocialiteProviders\Binance\Provider::class,
            'bitbucket' => \SocialiteProviders\Bitbucket\Provider::class,
            'bitly' => \SocialiteProviders\Bitly\Provider::class,
            'bitrix24' => \SocialiteProviders\Bitrix24\Provider::class,
            'blackboard' => \SocialiteProviders\Blackboard\Provider::class,
            'box' => \SocialiteProviders\Box\Provider::class,
            'buffer' => \SocialiteProviders\Buffer\Provider::class,
            'campaignmonitor' => \SocialiteProviders\CampaignMonitor\Provider::class,
            'cheddar' => \SocialiteProviders\Cheddar\Provider::class,
            'claveunica' => \SocialiteProviders\ClaveUnica\Provider::class,
            'clerk' => \SocialiteProviders\Clerk\Provider::class,
            'clover' => \SocialiteProviders\Clover\Provider::class,
            'cognito' => \SocialiteProviders\Cognito\Provider::class,
            'coinbase' => \SocialiteProviders\Coinbase\Provider::class,
            'constantcontact' => \SocialiteProviders\ConstantContact\Provider::class,
            'coursera' => \SocialiteProviders\Coursera\Provider::class,
            'dailymotion' => \SocialiteProviders\Dailymotion\Provider::class,
            'dataporten' => \SocialiteProviders\Dataporten\Provider::class,
            'deezer' => \SocialiteProviders\Deezer\Provider::class,
            'deviantart' => \SocialiteProviders\Devianart\Provider::class,
            'digitalocean' => \SocialiteProviders\DigitalOcean\Provider::class,
            'discogs' => \SocialiteProviders\Discogs\Provider::class,
            'discord' => \SocialiteProviders\Discord\Provider::class,
            'disqus' => \SocialiteProviders\Disqus\Provider::class,
            'docusign' => \SocialiteProviders\DocuSign\Provider::class,
            'douban' => \SocialiteProviders\Douban\Provider::class,
            'dribbble' => \SocialiteProviders\Dribbble\Provider::class,
            'dropbox' => \SocialiteProviders\Dropbox\Provider::class,
            'envato' => \SocialiteProviders\Envato\Provider::class,
            'etsy' => \SocialiteProviders\Etsy\Provider::class,
            'eventbrite' => \SocialiteProviders\Eventbrite\Provider::class,
            'eveonline' => \SocialiteProviders\Eveonline\Provider::class,
            'exment' => \SocialiteProviders\Exment\Provider::class,
            'eyeem' => \SocialiteProviders\EyeEm\Provider::class,
            'fablabs' => \SocialiteProviders\Fablabs\Provider::class,
            'facebook' => \SocialiteProviders\Facebook\Provider::class,
            'faceit' => \SocialiteProviders\Faceit\Provider::class,
            'figma' => \SocialiteProviders\Figma\Provider::class,
            'fitbit' => \SocialiteProviders\Fitbit\Provider::class,
            'fivehundredpixel' => \SocialiteProviders\FiveHundredPixel\Provider::class,
            'flattr' => \SocialiteProviders\Flattr\Provider::class,
            'flexkids' => \SocialiteProviders\Flexkids\Provider::class,
            'flexmls' => \SocialiteProviders\Flexmls\Provider::class,
            'flickr' => \SocialiteProviders\Flickr\Provider::class,
            'foursquare' => \SocialiteProviders\Foursquare\Provider::class,
            'franceconnect' => \SocialiteProviders\FranceConnect\Provider::class,
            'fusionauth' => \SocialiteProviders\FusionAuth\Provider::class,
            'garmin_connect' => \SocialiteProviders\GarminConnect\Provider::class,
            'gettyimages' => \SocialiteProviders\GettyImages\Provider::class,
            'gitea' => \SocialiteProviders\Gitea\Provider::class,
            'gitee' => \SocialiteProviders\Gitee\Provider::class,
            'github' => \SocialiteProviders\GitHub\Provider::class,
            'gitlab' => \SocialiteProviders\GitLab\Provider::class,
            'goodreads' => \SocialiteProviders\Goodreads\Provider::class,
            'google' => \SocialiteProviders\Google\Provider::class,
            'govbr' => \SocialiteProviders\GovBR\Provider::class,
            'graph' => \SocialiteProviders\Graph\Provider::class,
            'gumroad' => \SocialiteProviders\Gumroad\Provider::class,
            'habrcareer' => \SocialiteProviders\HabrCareer\Provider::class,
            'harid' => \SocialiteProviders\HarID\Provider::class,
            'harvest' => \SocialiteProviders\Harvest\Provider::class,
            'headhunter' => \SocialiteProviders\HeadHunter\Provider::class,
            'heroku' => \SocialiteProviders\Heroku\Provider::class,
            'hubspot' => \SocialiteProviders\HubSpot\Provider::class,
            'humanapi' => \SocialiteProviders\HumanApi\Provider::class,
            'ifsp' => \SocialiteProviders\IFSP\Provider::class,
            'imgur' => \SocialiteProviders\Imgur\Provider::class,
            'imis' => \SocialiteProviders\Imis\Provider::class,
            'indeed' => \SocialiteProviders\Indeed\Provider::class,
            'instagram' => \SocialiteProviders\Instagram\Provider::class,
            'instagrambasic' => \SocialiteProviders\InstagramBasic\Provider::class,
            'instructure' => \SocialiteProviders\Instructure\Provider::class,
            'intercom' => \SocialiteProviders\Intercom\Provider::class,
            'ivao' => \SocialiteProviders\Ivao\Provider::class,
            'jira' => \SocialiteProviders\Jira\Provider::class,
            'jumpcloud' => \SocialiteProviders\JumpCloud\Provider::class,
            'kanidm' => \SocialiteProviders\Kanidm\Provider::class,
            'keycloak' => \SocialiteProviders\Keycloak\Provider::class,
            'laravelpassport' => \SocialiteProviders\LaravelPassport\Provider::class,
            'lichess' => \SocialiteProviders\Lichess\Provider::class,
            'lifesciencelogin' => \SocialiteProviders\LifeScienceLogin\Provider::class,
            'line' => \SocialiteProviders\Line\Provider::class,
            'linkedin' => \SocialiteProviders\LinkedIn\Provider::class,
            'linode' => \SocialiteProviders\Linode\Provider::class,
            'live' => \SocialiteProviders\Live\Provider::class,
            'mailchimp' => \SocialiteProviders\MailChimp\Provider::class,
            'mailru' => \SocialiteProviders\Mailru\Provider::class,
            'makerlog' => \SocialiteProviders\MakerLog\Provider::class,
            'mattermost' => \SocialiteProviders\MatterMost\Provider::class,
            'mediacube' => \SocialiteProviders\MediaCube\Provider::class,
            'mediawiki' => \SocialiteProviders\Mediawiki\Provider::class,
            'medium' => \SocialiteProviders\Medium\Provider::class,
            'meetup' => \SocialiteProviders\Meetup\Provider::class,
            'mercadolibre' => \SocialiteProviders\MercadoLibre\Provider::class,
            'microsoft' => \SocialiteProviders\Microsoft\Provider::class,
            'minecraft' => \SocialiteProviders\Minecraft\Provider::class,
            'mixcloud' => \SocialiteProviders\Mixcloud\Provider::class,
            'modrinth' => \Pterodactyl\BlueprintFramework\Extensions\sociallogin\Drivers\ModrinthSocialiteProvider::class,
            'mollie' => \SocialiteProviders\Mollie\Provider::class,
            'monday' => \SocialiteProviders\Monday\Provider::class,
            'monzo' => \SocialiteProviders\Monzo\Provider::class,
            'musicbrainz' => \SocialiteProviders\MusicBrainz\Provider::class,
            'naver' => \SocialiteProviders\Naver\Provider::class,
            'netlify' => \SocialiteProviders\Netlify\Provider::class,
            'neto' => \SocialiteProviders\Neto\Provider::class,
            'nextcloud' => \SocialiteProviders\Nextcloud\Provider::class,
            'nocks' => \SocialiteProviders\Nocks\Provider::class,
            'notion' => \SocialiteProviders\Notion\Provider::class,
            'oauthgen' => \SocialiteProviders\OAuthgen\Provider::class,
            'odnoklassniki' => \SocialiteProviders\Odnoklassniki\Provider::class,
            'okta' => \SocialiteProviders\Okta\Provider::class,
            'onelogin' => \SocialiteProviders\Onelogin\Provider::class,
            'openstreetmap' => \SocialiteProviders\OpenStreetMap\Provider::class,
            'orcid' => \SocialiteProviders\Orcid\Provider::class,
            'oschina' => \SocialiteProviders\OSChina\Provider::class,
            'osu' => \Pterodactyl\BlueprintFramework\Extensions\sociallogin\Drivers\OsuSocialiteProvider::class,
            'ovh' => \SocialiteProviders\Ovh\Provider::class,
            'patreon' => \SocialiteProviders\Patreon\Provider::class,
            'paymill' => \SocialiteProviders\Paymill\Provider::class,
            'paymenter' => \Pterodactyl\BlueprintFramework\Extensions\sociallogin\Drivers\PaymenterSocialiteProvider::class,
            'paypal' => \SocialiteProviders\PayPal\Provider::class,
            'paypalsandbox' => \SocialiteProviders\PayPalSandbox\Provider::class,
            'peeringdb' => \SocialiteProviders\PeeringDB\Provider::class,
            'pinterest' => \SocialiteProviders\Pinterest\Provider::class,
            'pipedrive' => \SocialiteProviders\Pipedrive\Provider::class,
            'pixnet' => \SocialiteProviders\Pixnet\Provider::class,
            'planningcenter' => \SocialiteProviders\PlanningCenter\Provider::class,
            'podio' => \SocialiteProviders\Podio\Provider::class,
            'pr0gramm' => \SocialiteProviders\Pr0gramm\Provider::class,
            'procore' => \SocialiteProviders\Procore\Provider::class,
            'producthunt' => \SocialiteProviders\ProductHunt\Provider::class,
            'projectv' => \SocialiteProviders\ProjectV\Provider::class,
            'pushbullet' => \SocialiteProviders\Pushbullet\Provider::class,
            'qq' => \SocialiteProviders\QQ\Provider::class,
            'quickbooks' => \SocialiteProviders\QuickBooks\Provider::class,
            'readability' => \SocialiteProviders\Readability\Provider::class,
            'redbooth' => \SocialiteProviders\Redbooth\Provider::class,
            'reddit' => \SocialiteProviders\Reddit\Provider::class,
            'rekono' => \SocialiteProviders\Rekono\Provider::class,
            'roblox' => \SocialiteProviders\Roblox\Provider::class,
            'runkeeper' => \SocialiteProviders\RunKeeper\Provider::class,
            'sage' => \SocialiteProviders\Sage\Provider::class,
            'salesforce' => \SocialiteProviders\SalesForce\Provider::class,
            'salesloft' => \SocialiteProviders\Salesloft\Provider::class,
            'scistarter' => \SocialiteProviders\SciStarter\Provider::class,
            'sharepoint' => \SocialiteProviders\SharePoint\Provider::class,
            'shopify' => \SocialiteProviders\Shopify\Provider::class,
            'slack' => \SocialiteProviders\Slack\Provider::class,
            'smashcast' => \SocialiteProviders\Smashcast\Provider::class,
            'snapchat' => \SocialiteProviders\Snapchat\Provider::class,
            'soundcloud' => \SocialiteProviders\SoundCloud\Provider::class,
            'spotify' => \SocialiteProviders\Spotify\Provider::class,
            'stackexchange' => \SocialiteProviders\StackExchange\Provider::class,
            'starling' => \SocialiteProviders\Starling\Provider::class,
            'startgg' => \SocialiteProviders\StartGg\Provider::class,
            'steam' => \SocialiteProviders\Steam\Provider::class,
            'steem' => \SocialiteProviders\Steem\Provider::class,
            'stocktwits' => \SocialiteProviders\StockTwits\Provider::class,
            'strava' => \SocialiteProviders\Strava\Provider::class,
            'streamelements' => \SocialiteProviders\StreamElements\Provider::class,
            'streamlabs' => \SocialiteProviders\Streamlabs\Provider::class,
            'stripe' => \SocialiteProviders\Stripe\Provider::class,
            'subscribestar' => \SocialiteProviders\Subscribestar\Provider::class,
            'superoffice' => \SocialiteProviders\SuperOffice\Provider::class,
            'surfconext' => \SocialiteProviders\SURFcontext\Provider::class,
            'teamleader' => \SocialiteProviders\Teamleader\Provider::class,
            'teamservice' => \SocialiteProviders\TeamService\Provider::class,
            'teamweek' => \SocialiteProviders\Teamweek\Provider::class,
            'telegram' => \SocialiteProviders\Telegram\Provider::class,
            'thirtysevensignals' => \SocialiteProviders\ThirtySevenSignals\Provider::class,
            'threads' => \SocialiteProviders\Threads\Provider::class,
            'tiktok' => \SocialiteProviders\TikTok\Provider::class,
            'todoist' => \SocialiteProviders\Todoist\Provider::class,
            'toyhouse' => \SocialiteProviders\Toyhouse\Provider::class,
            'trakt' => \SocialiteProviders\Trakt\Provider::class,
            'trello' => \SocialiteProviders\Trello\Provider::class,
            'tumblr' => \SocialiteProviders\Tumblr\Provider::class,
            'tvshowtime' => \SocialiteProviders\TVShowTime\Provider::class,
            'twentythreeandme' => \SocialiteProviders\TwentyThreeAndMe\Provider::class,
            'twitcasting' => \SocialiteProviders\TwitCasting\Provider::class,
            'twitch' => \SocialiteProviders\Twitch\Provider::class,
            'twitter' => \SocialiteProviders\Twitter\Provider::class,
            'uaepass' => \SocialiteProviders\UAEPass\Provider::class,
            'uber' => \SocialiteProviders\Uber\Provider::class,
            'ucl' => \SocialiteProviders\UCL\Provider::class,
            'ufs' => \SocialiteProviders\UFS\Provider::class,
            'ufutx' => \SocialiteProviders\Ufutx\Provider::class,
            'unsplash' => \SocialiteProviders\Unsplash\Provider::class,
            'untappd' => \SocialiteProviders\Untappd\Provider::class,
            'usos' => \SocialiteProviders\Usos\Provider::class,
            'vatsim' => \SocialiteProviders\Vatsim\Provider::class,
            'venmo' => \SocialiteProviders\Venmo\Provider::class,
            'vercel' => \SocialiteProviders\Vercel\Provider::class,
            'versionone' => \SocialiteProviders\VersionOne\Provider::class,
            'vimeo' => \SocialiteProviders\Vimeo\Provider::class,
            'vkontakte' => \SocialiteProviders\VKontakte\Provider::class,
            'wave' => \SocialiteProviders\Wave\Provider::class,
            'webex' => \SocialiteProviders\Webex\Provider::class,
            'webflow' => \SocialiteProviders\Webflow\Provider::class,
            'wechat_service_account' => \SocialiteProviders\WeChatServiceAccount\Provider::class,
            'wechat_web' => \SocialiteProviders\WeChatWeb\Provider::class,
            'weibo' => \SocialiteProviders\Weibo\Provider::class,
            'weixin' => \SocialiteProviders\Weixin\Provider::class,
            'weixinweb' => \SocialiteProviders\WeixinWeb\Provider::class,
            'whmcs' => \SocialiteProviders\Whmcs\Provider::class,
            'withings' => \SocialiteProviders\Withings\Provider::class,
            'wordpress' => \SocialiteProviders\WordPress\Provider::class,
            'worldcoin' => \SocialiteProviders\Worldcoin\Provider::class,
            'xero' => \SocialiteProviders\Xero\Provider::class,
            'xing' => \SocialiteProviders\Xing\Provider::class,
            'xrel' => \SocialiteProviders\xREL\Provider::class,
            'yahoo' => \SocialiteProviders\Yahoo\Provider::class,
            'yammer' => \SocialiteProviders\Yammer\Provider::class,
            'yandex' => \SocialiteProviders\Yandex\Provider::class,
            'yiban' => \SocialiteProviders\Yiban\Provider::class,
            'youtube' => \SocialiteProviders\YouTube\Provider::class,
            'zalo' => \SocialiteProviders\Zalo\Provider::class,
            'zendesk' => \SocialiteProviders\Zendesk\Provider::class,
            'zoho' => \SocialiteProviders\Zoho\Provider::class,
            'zoom' => \SocialiteProviders\Zoom\Provider::class,
        ];

        $providerClass = array_get($providers, $provider->short_name);

        if (class_exists($providerClass)) {
            if (str_starts_with($providerClass, 'SocialiteProviders\\')) {
                Event::listen(function (\SocialiteProviders\Manager\SocialiteWasCalled $event) use ($provider, $providerClass) {
                    $event->extendSocialite($provider->short_name, $providerClass);
                });
            } else {
                Socialite::extend($provider->short_name, function ($app) use ($provider, $providerClass) {
                    $config = config('services.' . $provider->short_name);
                    return Socialite::buildProvider($providerClass, $config);
                });
            }
        }
    }

    private function formatProvider(SocialProvider $provider)
    {
        return [
            'id' => $provider->id,
            'name' => $provider->name,
            'short_name' => $provider->short_name,
        ];
    }
}
