<?php

namespace Pterodactyl\Http\Controllers\Admin\Extensions\sociallogin;

use Illuminate\View\View;
use Illuminate\Http\Response;
use Illuminate\View\Factory as ViewFactory;
use Pterodactyl\Http\Controllers\Controller;
use Pterodactyl\Http\Requests\Admin\AdminFormRequest;
use Pterodactyl\Models\SocialProvider;
use Illuminate\Support\Facades\Crypt;

// https://blueprint.zip/docs/?page=documentation/$blueprint
use Pterodactyl\BlueprintFramework\Libraries\ExtensionLibrary\Admin\BlueprintAdminLibrary as BlueprintExtensionLibrary;

class socialloginExtensionController extends Controller
{
    public function __construct(
        private ViewFactory $view,
        private BlueprintExtensionLibrary $blueprint,
    ) {
    }

    public function index(): View
    {
        $providers = SocialProvider::all();
        $supportedProviders = SocialProvider::SUPPORTED_PROVIDERS;
        $allProviders = SocialProvider::ALLPROVIDERS;
        $allowRegister = $this->blueprint->dbGet('sociallogin', 'allow_register');
        $allowConnecting = $this->blueprint->dbGet('sociallogin', 'allow_connecting');

        return $this->view->make(
            'admin.extensions.sociallogin.index',
            [
                'providers' => $providers,
                'supportedProviders' => json_encode($supportedProviders),
                'allProviders' => json_encode($allProviders),
                'allowRegister' => $allowRegister,
                'allowConnecting' => $allowConnecting,

                'root' => "/admin/extensions/sociallogin",
                'blueprint' => $this->blueprint,
            ]
        );
    }

    public function update(socialloginSettingsFormRequest $request): Response
    {
        $this->blueprint->dbSet('sociallogin', 'allow_register', $request->normalize()['allow_register']);
        $this->blueprint->dbSet('sociallogin', 'allow_connecting', $request->normalize()['allow_connecting']);

        return response('', 204);
    }

    public function post(socialloginConnectionsFormRequest $request): Response
    {
        $do = $request->normalize()['do'];
        if ($do === 'save') {
            $providers = $request->normalize()['providers'];
            foreach ($providers as $provider) {
                $model = SocialProvider::firstOrNew([
                    'short_name' => $provider['short_name'],
                ]);
                $model->enabled = $provider['enabled'];
                $model->name = $provider['name'];
                $model->client_id = $provider['client_id'] ?? null;
                if (isset($provider['client_secret'])) {
                    $model->client_secret = Crypt::encryptString($provider['client_secret']);
                }
                if (!$model->client_id || !$model->client_secret) {
                    $model->enabled = false;
                }
                $model->save();
            }
        } else if ($do == 'delete') {
            $provider = $request->normalize()['provider'];
            SocialProvider::where('short_name', $provider)->delete();
        }

        return response('', 204);
    }
}

class socialloginSettingsFormRequest extends AdminFormRequest
{
    public function rules(): array
    {
        return [
            'allow_register' => 'required|boolean',
            'allow_connecting' => 'required|boolean',
        ];
    }
}

class socialloginConnectionsFormRequest extends AdminFormRequest
{
  public function rules(): array
  {
    return [
        'do' => 'required|string|in:save,delete',
        'providers' => 'required_if:do,save|array',
        'provider' => 'required_if:do,delete|string',
    ];
  }

  public function attributes(): array
  {
    return [
        'providers' => 'Providers',
        'provider' => 'Provider',
    ];
  }
}
