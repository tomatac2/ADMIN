<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class State extends Model
{
    use HasFactory;

    /**
     * The States that are mass assignable.
     *
     * @var array<int, string>
     */
    
    public $fillable = [
        'name',
        'country_id',
    ];

    protected $visible = [
        'id',
        'name',
    ];

    /**
     * @return BelongsTo
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function cities(){
        return $this->hasMany(City::class);
    }

    public function getNameAttribute($value): string
    {
        $translated = __('states.' . $value);
        return $translated === 'states.' . $value ? $value : $translated;
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
