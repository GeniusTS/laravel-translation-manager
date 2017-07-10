<?php

namespace GeniusTS\TranslationManager;


use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

/**
 * Class TranslationManagerServiceProvider
 *
 * @package GeniusTS\TranslationManager
 */
class TranslationManagerServiceProvider extends ServiceProvider
{

    /**
     * @var string
     */
    protected $path = __DIR__ . "/../resources/";

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishResources()
            ->registerRoutes();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->loadViewsFrom("{$this->path}views", 'translation_manager');
        $this->mergeConfigFrom("{$this->path}config/translation_manager.php", 'translation_manager');
    }

    /**
     * publish resources files
     *
     * @return $this
     */
    protected function publishResources()
    {
        $this->publishes([
            "{$this->path}views" => resource_path('views/vendor/translation_manager'),
        ], 'views');

        $this->publishes([
            "{$this->path}config/translation_manager.php" => config_path('translation_manager.php'),
        ], 'config');

        return $this;
    }

    /**
     * Register package routes
     *
     * @return $this
     */
    protected function registerRoutes()
    {
        $prefix = config('translation_manager.prefix');

        Route::group([
            'prefix'     => $prefix,
            'middleware' => config('translation_manager.middleware', []),
            'namespace'  => '\GeniusTS\TranslationManager\Controllers',
        ], function ($router)
        {
            $router->get("/", "Controller@index")->name('translation_manager.index');
            $router->get("/edit/{language}/{file}/{namespace?}", "Controller@edit")->name('translation_manager.edit');
            $router->put("/edit/{language}/{file}/{namespace?}", "Controller@update")->name('translation_manager.update');
            $router->get("/ajax/files", "Controller@files")->name('translation_manager.files');
        });

        return $this;
    }
}