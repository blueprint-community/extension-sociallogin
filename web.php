<?php
// client.php
use Illuminate\Support\Facades\Route;
use Pterodactyl\BlueprintFramework\Extensions\sociallogin;

Route::get('/redirect/{provider}', [sociallogin\SocialAuthController::class, 'redirect'])->name('sociallogin.redirect');
Route::get('/callback', [sociallogin\SocialAuthController::class, 'callback'])->name('sociallogin.callback');
Route::get('/providers', [sociallogin\SocialAuthController::class, 'providers'])->name('sociallogin.providers');
Route::get('/connections', [sociallogin\SocialAuthController::class, 'connections'])->name('sociallogin.connections');

Route::get('/connect/{provider}', [sociallogin\SocialConnectController::class, 'connect'])->name('sociallogin.connect');
Route::get('/disconnect/{provider}', [sociallogin\SocialConnectController::class, 'disconnect'])->name('sociallogin.disconnect');
