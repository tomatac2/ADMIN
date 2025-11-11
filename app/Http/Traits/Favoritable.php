<?php
namespace App\Http\Traits;

use App\Models\Favorite;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Favoritable
{
    public function favorites(): MorphMany
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }

    public function favorite(int $userId): void
    {
        $this->favorites()->firstOrCreate(['user_id' => $userId]);
    }

    public function unfavorite(int $userId): void
    {
        $this->favorites()->where('user_id', $userId)->delete();
    }

    public function toggleFavorite(int $userId): bool
    {
        $exists = $this->favorites()->where('user_id', $userId)->exists();
        if ($exists) { $this->unfavorite($userId); return false; }
        $this->favorite($userId); return true;
    }

    public function isFavoritedBy(int $userId): bool
    {
        return $this->favorites()->where('user_id', $userId)->exists();
    }
}
