<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class FaqCategory extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;

    /**
     * The Categories that are mass assignable.
     *
     * @var array
     */
    public $translatable = [
        'name',
    ];

    protected $fillable = [
        'name',
        'status',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    public function faqs(): HasMany
    {
        return $this->hasMany(Faq::class, 'faq_category_id');
    }
}
