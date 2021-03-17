<?php


namespace Pudongping\Weather;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;


class ServiceProvider extends LaravelServiceProvider implements DeferrableProvider
{

    public function register()
    {
        $this->app->singleton(Weather::class, function () {
            return new Weather(config('weather.key'));
        });

        $this->app->alias(Weather::class, 'weather');
    }

    public function provides()
    {
        return [Weather::class, 'weather'];
    }

    public function boot()
    {
        // 发布 laravel 的配置文件
        $this->publishes([
            __DIR__ . '/config/weather.php' => config_path('weather.php'),
        ]);
    }

}