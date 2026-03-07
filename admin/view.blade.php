<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Social Settings</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="control-label">Registrations</label>
                        <div>
                            <select id="allowRegister" class="form-control">
                                <option value="1" @if($allowRegister) selected @endif>Allowed</option>
                                <option value="0" @if(!$allowRegister) selected @endif>Disallowed</option>
                            </select>
                            <p class="text-muted"><small>If enabled, unknown users can register and create an
                                    account on this panel with their OAuth provider.</small></p>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label">Connect</label>
                        <div>
                            <select id="allowConnecting" class="form-control">
                                <option value="1" @if($allowConnecting) selected @endif>Allowed</option>
                                <option value="0" @if(!$allowConnecting) selected @endif>Disallowed</option>
                            </select>
                            <p class="text-muted"><small>If enabled, users can directly connect their account from
                                    the login screen. This will match the email address to an existing
                                    account.</small></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <button class="btn btn-sm btn-primary pull-right" id="submit-settings">Save</button>
            </div>
        </div>
    </div>
</div>

<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">Social Providers</h3>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="form-group col-md-12">
                <table class="table">
                    <tr>
                        <th>Provider</th>
                        <th>Name</th>
                        <th class="text-center">Enabled</th>
                        <th>Client ID</th>
                        <th>
                            <div>
                                Client Secret
                                <p class="text-muted"><small>Leave blank to continue using the existing client
                                        secret.</small></p>
                            </div>
                        </th>
                    </tr>
                    @foreach($providers as $provider)
                        <tr provider="{{ $provider->short_name }}">
                            <td>{{ $provider->short_name }}</td>
                            <td><input type="text" class="form-control" name="{{ $provider->short_name }}-name"
                                    value="{{ old($provider->short_name . '-name', $provider->name) }}"></td>
                            <td class="text-center"><input type="checkbox" class="inline-block"
                                    name="{{ $provider->short_name }}-toggle" @if ($provider->enabled) checked @endif></td>
                            <td><input type="text" class="form-control" name="{{ $provider->short_name }}-client_id"
                                    value="{{ old($provider->short_name . '-client_id', $provider->client_id) }}"></td>
                            <td><input type="password" class="form-control" name="{{ $provider->short_name }}-client_secret"
                                    value="{{ old($provider->short_name . '-client_secret') }}"></td>
                            <td><button class="btn btn-danger btn-tiny btn-pill delete-button"
                                    provider="{{ $provider->short_name }}"><i class="fa fa-trash"></i></button></td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
    <div class="box-footer">
        <div class="pull-right">
            <button class="btn btn-sm btn-success" id="add-provider">Add New</button>
            <button class="btn btn-sm btn-primary" id="submit-providers">Save</button>
            {!! csrf_field() !!}
        </div>
    </div>
</div>

<div class="modal" tabindex="-1" role="dialog" id="add-modal">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Provider</h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-info" role="alert" id="add-new:alert">
                            Please enter a valid provider short name.
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="control-label">Provider Short Name</label>
                        <div>
                            <select class="form-control" name="add-newprovider-short_name"
                                id="add-newprovider-short_name">
                                @foreach(json_decode($allProviders) as $provider)
                                    <option value="{{ $provider }}">{{ $provider }}</option>
                                @endforeach
                            </select>
                            <p class="text-muted small">Please enter a provider short name from <a
                                    href="https://socialiteproviders.com/" target="_blank">Socialite</a>. Some providers
                                might not work as expected, or require special access.</p>
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="control-label">Provider Name</label>
                        <div>
                            <input type="text" class="form-control" name="add-new:provider-name"
                                id="add-new:provider-name" />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="control-label">Client ID</label>
                        <div>
                            <input type="text" class="form-control" name="add-new:provider-client_id" />
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="control-label">Client Secret</label>
                        <div>
                            <input type="password" class="form-control" name="add-new:provider-client_secret" />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        Please set the callback/redirect URL to <code>{{ route('sociallogin.callback') }}</code>.
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="add-modal-close">Close</button>
                <button type="button" class="btn btn-primary" id="add-modal-save">Save</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer-scripts')
@parent
<script>
    $('#add-newprovider-short_name').select2({
        placeholder: 'Select a provider',
        dropdownParent: $('#add-modal')
    }).change();

    function saveProviders() {
        let providers = [];
        $('table tr').map(function () {
            providers.push($(this).attr('provider') ?? null);
        });
        let newProviders = [];
        providers.filter(function (provider) { return provider !== null; }).forEach(function (provider) {
            let formProvider = $('tr[provider="' + provider + '"]');
            let data = {
                'short_name': provider,
                'enabled': formProvider.find('input[name="' + provider + '-toggle"]').is(':checked') ? 1 : 0
            };
            if (formProvider.find('input[name="' + provider + '-name"]').val()) {
                data['name'] = formProvider.find('input[name="' + provider + '-name"]').val();
            }
            if (formProvider.find('input[name="' + provider + '-client_id"]').val()) {
                data['client_id'] = formProvider.find('input[name="' + provider + '-client_id"]').val();
            }
            if (formProvider.find('input[name="' + provider + '-client_secret"]').val()) {
                data['client_secret'] = formProvider.find('input[name="' + provider + '-client_secret"]').val();
            }
            newProviders.push(data);
        });
        return $.ajax({
            method: 'POST',
            url: '/admin/extensions/sociallogin',
            contentType: 'application/json',
            data: JSON.stringify({
                'do': 'save',
                'providers': newProviders
            }),
            headers: { 'X-CSRF-Token': $('input[name="_token"]').val() }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            swal({
                title: 'Error',
                text: 'An error occurred while attempting to save the Social settings. ' + errorThrown,
                icon: 'error'
            });
        });
    }
    const submitProvidersButton = document.getElementById('submit-providers');
    submitProvidersButton.addEventListener('click', function () {
        saveProviders().done(function () {
            swal({
                title: 'Success',
                text: 'Social settings have been updated successfully.',
                icon: 'success'
            }, function () {
                location.reload();
            });
        });
    });

    function saveSettings() {
        const allowRegister = document.getElementById('allowRegister').value;
        const allowConnecting = document.getElementById('allowConnecting').value;
        return $.ajax({
            method: 'PATCH',
            url: '/admin/extensions/sociallogin',
            contentType: 'application/json',
            data: JSON.stringify({
                allow_register: allowRegister == '1',
                allow_connecting: allowConnecting == '1'
            }),
            headers: { 'X-CSRF-Token': $('input[name="_token"]').val() }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            swal({
                title: 'Error',
                text: 'An error occurred while attempting to save the Social settings. ' + errorThrown,
                icon: 'error'
            });
        });
    }
    const submitSettingsButton = document.getElementById('submit-settings');
    submitSettingsButton.addEventListener('click', function () {
        saveSettings().done(function () {
            swal({
                title: 'Success',
                text: 'Social settings have been updated successfully.',
                icon: 'success'
            }, function () {
                location.reload();
            });
        });
    });

    const deleteButtons = document.querySelectorAll('.delete-button');
    deleteButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            swal({
                title: 'Are you sure?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!'
            }, function () {
                $.ajax({
                    method: 'POST',
                    url: '/admin/extensions/sociallogin',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        'do': 'delete',
                        'provider': button.getAttribute('provider')
                    }),
                    headers: { 'X-CSRF-Token': $('input[name="_token"]').val() }
                }).fail(function (jqXHR, textStatus, errorThrown) {
                    swal({
                        title: 'Error',
                        text: 'An error occurred while attempting to delete the Social settings. ' + errorThrown,
                        icon: 'error'
                    });
                }).done(function () {
                    location.reload();
                });
            });
        });
    });

    const addProviderButton = document.getElementById('add-provider');
    addProviderButton.addEventListener('click', function () {
        $('#add-modal').modal('show');
    });

    const addSubmitButton = document.getElementById('add-modal-save');
    addSubmitButton.addEventListener('click', function () {
        const shortName = $('select[name="add-newprovider-short_name"]').val();
        const fullName = $('input[name="add-new:provider-name"]').val();
        const clientId = $('input[name="add-new:provider-client_id"]').val();
        const clientSecret = $('input[name="add-new:provider-client_secret"]').val();
        if (!shortName || !fullName) {
            swal({
                title: 'Error',
                text: 'The short name and name fields are required.',
                icon: 'error'
            });
            return;
        }
        const form = $('tr[provider="' + shortName + '"]');
        if (form.length) {
            swal({
                title: 'Error',
                text: 'A Social provider with this short name already exists.',
                icon: 'error'
            });
            return;
        }
        $('#add-modal').modal('hide');
        $('table tbody').append(`
            <tr provider="${shortName}">
                <td>${shortName}</td>
                <td><input type="text" class="form-control" name="${shortName}-name" value="${fullName}"></td>
                <td class="text-center"><input type="checkbox" class="inline-block" name="${shortName}-toggle" checked></td>
                <td><input type="text" class="form-control" name="${shortName}-client_id" value="${clientId}"></td>
                <td><input type="password" class="form-control" name="${shortName}-client_secret" value="${clientSecret}"></td>
            </tr>
        `);
    });
    const addCloseButton = document.getElementById('add-modal-close');
    addCloseButton.addEventListener('click', function () {
        $('#add-modal').modal('hide');
    });

    $('#add-newprovider-short_name').on('select2:select', function (e) {
        const supportedProviders = {!! $supportedProviders !!};
        const allProviders = {!! $allProviders !!};
        const alertBanner = document.getElementById('add-new:alert');

        const shortName = e.params.data.text;
        const providerInfo = supportedProviders[shortName];
        const nameField = document.getElementById('add-new:provider-name');

        if (!providerInfo) {
            if (allProviders.includes(shortName)) {
                alertBanner.className = 'alert alert-warning';
                alertBanner.innerHTML = 'This provider might not be working out of the box. Please check the <a href="https://socialiteproviders.com/about/" target="_blank">documentation</a> for more information.';
            } else {
                alertBanner.className = 'alert alert-danger';
                alertBanner.innerHTML = 'This provider does not exist in the documentation. Please check the <a href="https://socialiteproviders.com/about/" target="_blank">documentation</a> for valid providers.';
            }
            nameField.value = shortName;
        }

        nameField.value = providerInfo.name;
        if (providerInfo.message) {
            alertBanner.className = 'alert alert-' + providerInfo.message.type;
            alertBanner.innerHTML = providerInfo.message.message;
        }
    });
</script>