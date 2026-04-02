# Authentik Setup

This guide explains how to enable **Authentik OAuth login** for the SocialLogin Blueprint extension.

## 1. Install the Authentik provider

Run the following command in your Pterodactyl panel root:

    composer require socialiteproviders/authentik

---

## 2. Configure `config/services.php`

Add the following entry:

    'authentik' => [
        'base_url' => env('AUTHENTIK_BASE_URL'),
        'client_id' => env('AUTHENTIK_CLIENT_ID'),
        'client_secret' => env('AUTHENTIK_CLIENT_SECRET'),
        'redirect' => env('AUTHENTIK_REDIRECT_URI'),
    ],

---

## 3. Add environment variables

Add these to your `.env` file:

    AUTHENTIK_BASE_URL=https://auth.example.com/
    AUTHENTIK_CLIENT_ID=your-client-id
    AUTHENTIK_CLIENT_SECRET=your-client-secret
    AUTHENTIK_REDIRECT_URI=https://panel.example.com/extensions/sociallogin/callback

---

## 4. Enable the provider in Pterodactyl Panel

- Navigate to `Admin / Extensions / SocialLogin`
- Add a new provider
- Set the provider short name to: `authentik`
- Enter your client ID and secret in the admin panel
- Make sure the provider is enabled

---

## Notes

- Ensure your Authentik application uses **OAuth2 / OpenID Connect**
- The redirect URL must match exactly in Authentik