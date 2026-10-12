<?php

namespace Vibefilter\Filament\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * A statement the filter has been asked about.
 *
 * @property int $id
 * @property string $text
 * @property string $hash
 * @property Carbon|null $last_used_at
 */
class Question extends Model
{
    protected $table = 'vibefilter_questions';

    protected $fillable = ['text', 'hash', 'last_used_at'];

    protected $casts = [
        'last_used_at' => 'datetime',
    ];

    /**
     * @return HasMany<Decision, $this>
     */
    public function decisions(): HasMany
    {
        return $this->hasMany(Decision::class);
    }
}
