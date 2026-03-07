<?php

namespace Pterodactyl\BlueprintFramework\Extensions\sociallogin;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Pterodactyl\Facades\Activity;
use Pterodactyl\Http\Controllers\Controller;
use Pterodactyl\Models\SocialConnection;
use Pterodactyl\Models\SocialProvider;

class SocialConnectController extends Controller
{
    protected function connect(Request $request, string $provider): RedirectResponse
    {
        $user = $request->user();
        if (!$user) {
            abort(404);
        }

        return redirect()->route('sociallogin.redirect', ['provider' => $provider]);
    }

    /**
     * Remove an OAuth connection.
     *
     * @param \Illuminate\Http\Request
     * @param string
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function disconnect(Request $request, string $provider): RedirectResponse
    {
        $user = $request->user();
        if (!$user) {
            abort(404);
        }

        $SocialProvider = SocialProvider::where('short_name', $provider)->first();
        if (!$SocialProvider) {
            abort(404);
        }

        $connection = SocialConnection::where('user_id', $user->id)->where('provider_id', $SocialProvider->id)->first();
        if (!$connection) {
            abort(404);
        }

        Activity::event('auth:social.disconnect')
            ->property('provider', $provider)
            ->withRequestMetadata()
            ->subject($user)
            ->log();

        $connection->delete();
        return redirect('/account/social');
    }
}
