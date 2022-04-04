<?php

namespace PhilWilliammee\SamlServiceProvider\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SamlLogin extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $casts = [
        'not_on_or_after' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        if (!$this->user_id || !config('saml-service-provider.user_model')) {
            return null;
        }
        return $this->belongsTo(config('saml-service-provider.user_model'));
    }
}
