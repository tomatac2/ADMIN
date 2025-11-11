<?php

namespace App\Providers;

use App\Facades\WMenu;
use App\Interfaces\FireStoreInterface;
use App\Models\Plugin;
use App\Services\FaceRecognition\AwsRekognitionService;
use App\Services\FaceRecognition\Contracts\FaceRecognitionService;
use App\Services\WidgetManager;
use App\Observers\PluginObserver;
use App\Services\FireStoreService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\Facades\Translatable;
use Illuminate\Http\Resources\Json\JsonResource;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->bind('Menu', fn () => new WMenu());

        $this->app->singleton(FaceRecognitionService::class, AwsRekognitionService::class);

        $this->app->singleton(WidgetManager::class, fn () => new WidgetManager());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();
        Plugin::observe(PluginObserver::class);
        Translatable::fallback(fallbackAny: true);
        JsonResource::withoutWrapping();
        Model::automaticallyEagerLoadRelationships();
    }
}
