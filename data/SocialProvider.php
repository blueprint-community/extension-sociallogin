<?php

namespace Pterodactyl\Models;

/**
 * @property int $id
 * @property bool $enabled
 * @property string $name
 * @property string $short_name
 * @property string $client_id
 * @property string $client_secret
 */
class SocialProvider extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'social_providers';

    /**
     * Disable timestamps on this model.
     */
    public $timestamps = false;

    /**
     * Fields that are mass assignable.
     */
    protected $fillable = [
        'enabled',
        'name',
        'short_name',
        'client_id',
        'client_secret',
    ];

    public static array $validationRules = [
        'enabled' => 'boolean',
        'name' => 'required|string',
        'short_name' => 'required|string',
        'client_id' => 'required_if:enabled,true|string|nullable',
        'client_secret' => 'required_if:enabled,true|string|nullable',
    ];

    public const SUPPORTED_PROVIDERS = [
        'amazon' => [
            'name' => 'Amazon',
            'short_name' => 'amazon',
            'message' => [
                'type' => 'warning',
                'message' => 'This provider requires a custom module which you have to install with the following command:<br><code>composer require socialiteproviders/amazon</code>'
            ]
        ],
        'apple' => [
            'name' => 'Apple',
            'short_name' => 'apple',
            'message' => [
                'type' => 'warning',
                'message' => 'This provider requires a custom module which you have to install with the following command:<br><code>composer require socialiteproviders/apple</code>'
            ]
        ],
        'authentik' => [
            'name' => 'Authentik',
            'short_name' => 'authentik',
            'message' => [
                'type' => 'warning',
                'message' => 'This provider requires a custom module which you have to install with the following command:<br><code>composer require socialiteproviders/authentik</code>'
            ]
        ],
        'discord' => [
            'name' => 'Discord',
            'short_name' => 'discord',
            'message' => [
                'type' => 'warning',
                'message' => 'This provider requires a custom module which you have to install with the following command:<br><code>composer require socialiteproviders/discord</code>'
            ]
        ],
        'dribbble' => [
            'name' => 'Dribbble',
            'short_name' => 'dribbble',
            'message' => [
                'type' => 'warning',
                'message' => 'This provider requires a custom module which you have to install with the following command:<br><code>composer require socialiteproviders/dribbble</code>'
            ]
        ],
        'dropbox' => [
            'name' => 'Dropbox',
            'short_name' => 'dropbox',
            'message' => [
                'type' => 'warning',
                'message' => 'This provider requires a custom module which you have to install with the following command:<br><code>composer require socialiteproviders/dropbox</code>'
            ]
        ],
        'facebook' => [
            'name' => 'Facebook',
            'short_name' => 'facebook',
            'message' => [
                'type' => 'success',
                'message' => 'This provider works out of the box.'
            ]
        ],
        'figma' => [
            'name' => 'Figma',
            'short_name' => 'figma',
            'message' => [
                'type' => 'warning',
                'message' => 'This provider requires a custom module which you have to install with the following command:<br><code>composer require socialiteproviders/figma</code>'
            ]
        ],
        'github' => [
            'name' => 'GitHub',
            'short_name' => 'github',
            'message' => [
                'type' => 'success',
                'message' => 'This provider works out of the box.'
            ]
        ],
        'gitlab' => [
            'name' => 'GitLab',
            'short_name' => 'gitlab',
            'message' => [
                'type' => 'success',
                'message' => 'This provider works out of the box.'
            ]
        ],
        'google' => [
            'name' => 'Google',
            'short_name' => 'google',
            'message' => [
                'type' => 'success',
                'message' => 'This provider works out of the box.'
            ]
        ],
        'instagram' => [
            'name' => 'Instagram',
            'short_name' => 'instagram',
            'message' => [
                'type' => 'warning',
                'message' => 'This provider requires a custom module which you have to install with the following command:<br><code>composer require socialiteproviders/instagram</code>'
            ]
        ],
        'linkedin' => [
            'name' => 'LinkedIn',
            'short_name' => 'linkedin',
            'message' => [
                'type' => 'success',
                'message' => 'This provider works out of the box.'
            ]
        ],
        'medium' => [
            'name' => 'Medium',
            'short_name' => 'medium',
            'message' => [
                'type' => 'warning',
                'message' => 'This provider requires a custom module which you have to install with the following command:<br><code>composer require socialiteproviders/medium</code>'
            ]
        ],
        'microsoft' => [
            'name' => 'Microsoft',
            'short_name' => 'microsoft',
            'message' => [
                'type' => 'success',
                'message' => 'This provider works out of the box.'
            ]
        ],
        'modrinth' => [
            'name' => 'Modrinth',
            'short_name' => 'modrinth',
            'message' => [
                'type' => 'success',
                'message' => 'This provider works out of the box.'
            ]
        ],
        'osu' => [
            'name' => 'osu!',
            'short_name' => 'osu',
            'message' => [
                'type' => 'success',
                'message' => 'This provider works out of the box.'
            ]
        ],
        'paypal' => [
            'name' => 'PayPal',
            'short_name' => 'paypal',
            'message' => [
                'type' => 'warning',
                'message' => 'This provider requires a custom module which you have to install with the following command:<br><code>composer require socialiteproviders/paypal</code>'
            ]
        ],
        'paymenter' => [
            'name' => 'Paymenter',
            'short_name' => 'paymenter',
            'message' => [
                'type' => 'warning',
                'message' => 'This provider works out of the box. You will have to set <code>paymenter.url</code> in your <code>config/services.php</code> file to the URL of your Paymenter instance.'
            ]
        ],
        'pinterest' => [
            'name' => 'Pinterest',
            'short_name' => 'pinterest',
            'message' => [
                'type' => 'warning',
                'message' => 'This provider requires a custom module which you have to install with the following command:<br><code>composer require socialiteproviders/pinterest</code>'
            ]
        ],
        'reddit' => [
            'name' => 'Reddit',
            'short_name' => 'reddit',
            'message' => [
                'type' => 'warning',
                'message' => 'This provider requires a custom module which you have to install with the following command:<br><code>composer require socialiteproviders/reddit</code>'
            ]
        ],
        'slack' => [
            'name' => 'Slack',
            'short_name' => 'slack',
            'message' => [
                'type' => 'success',
                'message' => 'This provider works out of the box.'
            ]
        ],
        'snapchat' => [
            'name' => 'Snapchat',
            'short_name' => 'snapchat',
            'message' => [
                'type' => 'warning',
                'message' => 'This provider requires a custom module which you have to install with the following command:<br><code>composer require socialiteproviders/snapchat</code>'
            ]
        ],
        'spotify' => [
            'name' => 'Spotify',
            'short_name' => 'spotify',
            'message' => [
                'type' => 'success',
                'message' => 'This provider works out of the box.'
            ]
        ],
        'steam' => [
            'name' => 'Steam',
            'short_name' => 'steam',
            'message' => [
                'type' => 'warning',
                'message' => 'This provider requires a custom module which you have to install with the following command:<br><code>composer require socialiteproviders/steam</code>'
            ]
        ],
        'telegram' => [
            'name' => 'Telegram',
            'short_name' => 'telegram',
            'message' => [
                'type' => 'warning',
                'message' => 'This provider requires a custom module which you have to install with the following command:<br><code>composer require socialiteproviders/telegram</code>'
            ]
        ],
        'tiktok' => [
            'name' => 'TikTok',
            'short_name' => 'tiktok',
            'message' => [
                'type' => 'warning',
                'message' => 'This provider requires a custom module which you have to install with the following command:<br><code>composer require socialiteproviders/tiktok</code>'
            ]
        ],
        'trello' => [
            'name' => 'Trello',
            'short_name' => 'trello',
            'message' => [
                'type' => 'warning',
                'message' => 'This provider requires a custom module which you have to install with the following command:<br><code>composer require socialiteproviders/trello</code>'
            ]
        ],
        'tumblr' => [
            'name' => 'Tumblr',
            'short_name' => 'tumblr',
            'message' => [
                'type' => 'warning',
                'message' => 'This provider requires a custom module which you have to install with the following command:<br><code>composer require socialiteproviders/tumblr</code>'
            ]
        ],
        'twitch' => [
            'name' => 'Twitch',
            'short_name' => 'twitch',
            'message' => [
                'type' => 'warning',
                'message' => 'This provider requires a custom module which you have to install with the following command:<br><code>composer require socialiteproviders/twitch</code>'
            ]
        ],
        'twitter' => [
            'name' => 'Twitter',
            'short_name' => 'twitter',
            'message' => [
                'type' => 'success',
                'message' => 'This provider works out of the box.'
            ]
        ],
        'uber' => [
            'name' => 'Uber',
            'short_name' => 'uber',
            'message' => [
                'type' => 'warning',
                'message' => 'This provider requires a custom module which you have to install with the following command:<br><code>composer require socialiteproviders/uber</code>'
            ]
        ],
        'unsplash' => [
            'name' => 'Unsplash',
            'short_name' => 'unsplash',
            'message' => [
                'type' => 'warning',
                'message' => 'This provider requires a custom module which you have to install with the following command:<br><code>composer require socialiteproviders/unsplash</code>'
            ]
        ],
        'vercel' => [
            'name' => 'Vercel',
            'short_name' => 'vercel',
            'message' => [
                'type' => 'warning',
                'message' => 'This provider requires a custom module which you have to install with the following command:<br><code>composer require socialiteproviders/vercel</code>'
            ]
        ],
        'whmcs' => [
            'name' => 'WHMCS',
            'short_name' => 'whmcs',
            'message' => [
                'type' => 'warning',
                'message' => 'This provider requires a custom module which you have to install with the following command:<br><code>composer require socialiteproviders/whmcs</code><br>Please check the CONFIGURATION.md file included with Social Login for further instructions.'
            ]
        ],
        'wordpress' => [
            'name' => 'WordPress',
            'short_name' => 'wordpress',
            'message' => [
                'type' => 'warning',
                'message' => 'This provider requires a custom module which you have to install with the following command:<br><code>composer require socialiteproviders/wordpress</code>'
            ]
        ],
    ];

    public const ALLPROVIDERS = [
        'acclaim',
        'admitad',
        'adobe',
        'aikido',
        'amazon',
        'amocrm',
        'angellist',
        'apple',
        'appnet',
        'arcgis',
        'asana',
        'atlassian',
        'auth0',
        'authentik',
        'autodeskaps',
        'aweber',
        'azure',
        'azureadb2c',
        'battlenet',
        'bexio',
        'binance',
        'bitbucket',
        'bitly',
        'bitrix24',
        'blackboard',
        'box',
        'buffer',
        'campaignmonitor',
        'cheddar',
        'claveunica',
        'clerk',
        'clover',
        'cognito',
        'coinbase',
        'constantcontact',
        'coursera',
        'dailymotion',
        'dataporten',
        'deezer',
        'deviantart',
        'digitalocean',
        'discogs',
        'discord',
        'disqus',
        'docusign',
        'douban',
        'dribbble',
        'dropbox',
        'envato',
        'etsy',
        'eventbrite',
        'eveonline',
        'exment',
        'eyeem',
        'fablabs',
        'facebook',
        'faceit',
        'figma',
        'fitbit',
        'fivehundredpixel',
        'flattr',
        'flexkids',
        'flexmls',
        'flickr',
        'foursquare',
        'franceconnect',
        'fusionauth',
        'garmin_connect',
        'gettyimages',
        'gitea',
        'gitee',
        'github',
        'gitlab',
        'goodreads',
        'google',
        'govbr',
        'graph',
        'gumroad',
        'habrcareer',
        'harid',
        'harvest',
        'headhunter',
        'heroku',
        'hubspot',
        'humanapi',
        'ifsp',
        'imgur',
        'imis',
        'indeed',
        'instagram',
        'instagrambasic',
        'instructure',
        'intercom',
        'ivao',
        'jira',
        'jumpcloud',
        'kanidm',
        'keycloak',
        'laravelpassport',
        'lichess',
        'lifesciencelogin',
        'line',
        'linkedin',
        'linode',
        'live',
        'mailchimp',
        'mailru',
        'makerlog',
        'mattermost',
        'mediacube',
        'mediawiki',
        'medium',
        'meetup',
        'mercadolibre',
        'microsoft',
        'minecraft',
        'mixcloud',
        'modrinth',
        'mollie',
        'monday',
        'monzo',
        'musicbrainz',
        'naver',
        'netlify',
        'neto',
        'nextcloud',
        'nocks',
        'notion',
        'oauthgen',
        'odnoklassniki',
        'okta',
        'onelogin',
        'openstreetmap',
        'orcid',
        'oschina',
        'osu',
        'ovh',
        'patreon',
        'paymill',
        'paymenter',
        'paypal',
        'paypalsandbox',
        'peeringdb',
        'pinterest',
        'pipedrive',
        'pixnet',
        'planningcenter',
        'podio',
        'pr0gramm',
        'procore',
        'producthunt',
        'projectv',
        'pushbullet',
        'qq',
        'quickbooks',
        'readability',
        'redbooth',
        'reddit',
        'rekono',
        'roblox',
        'runkeeper',
        'sage',
        'salesforce',
        'salesloft',
        'scistarter',
        'sharepoint',
        'shopify',
        'slack',
        'smashcast',
        'snapchat',
        'soundcloud',
        'spotify',
        'stackexchange',
        'starling',
        'startgg',
        'steam',
        'steem',
        'stocktwits',
        'strava',
        'streamelements',
        'streamlabs',
        'stripe',
        'subscribestar',
        'superoffice',
        'surfconext',
        'teamleader',
        'teamservice',
        'teamweek',
        'telegram',
        'thirtysevensignals',
        'threads',
        'tiktok',
        'todoist',
        'toyhouse',
        'trakt',
        'trello',
        'tumblr',
        'tvshowtime',
        'twentythreeandme',
        'twitcasting',
        'twitch',
        'twitter',
        'uaepass',
        'uber',
        'ucl',
        'ufs',
        'ufutx',
        'unsplash',
        'untappd',
        'usos',
        'vatsim',
        'venmo',
        'vercel',
        'versionone',
        'vimeo',
        'vkontakte',
        'wave',
        'webex',
        'webflow',
        'wechat_service_account',
        'wechat_web',
        'weibo',
        'weixin',
        'weixinweb',
        'whmcs',
        'withings',
        'wordpress',
        'worldcoin',
        'xero',
        'xing',
        'xrel',
        'yahoo',
        'yammer',
        'yandex',
        'yiban',
        'youtube',
        'zalo',
        'zendesk',
        'zoho',
        'zoom',
    ];
}
