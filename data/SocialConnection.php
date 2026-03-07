<?php

namespace Pterodactyl\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $provider_id
 * @property string $auth_id
 * @property string $auth_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Pterodactyl\Models\User $user
 * @property \Pterodactyl\Models\SocialProvider $provider
 */
class SocialConnection extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'social_connections';

    /**
     * Fields that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'provider_id',
        'auth_id',
        'auth_name',
    ];

    public static array $validationRules = [
        'user_id' => 'required|integer',
        'provider_id' => 'required|integer',
        'auth_id' => 'required|string',
        'auth_name' => 'required|string',
    ];

    /**
     * Gets the user associated with this connection.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Gets the provider associated with this connection.
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(SocialProvider::class, 'provider_id');
    }
}
