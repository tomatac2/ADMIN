<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class City extends Model
{
    use HasFactory;

    /**
     * The States that are mass assignable.
     *
     * @var array<int, string>
     */

    public $fillable = [
        'name',
        'state_id',
    ];

    protected $visible = [
        'id',
        'name',
    ];

    /**
     * @return BelongsTo
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state_id');
    }


    public function getNameAttribute($value): string
    {
        $translated = __('cities.' . $value);
        return $translated === 'cities.' . $value ? $value : $translated;
    }

    public function getCreatedAtAttribute($value)
    {
        return date("d-m-Y", strtotime($value));
    }

    public function getUpdatedAtAttribute($value)
    {
        return date("d-m-Y", strtotime($value));
    }
}
