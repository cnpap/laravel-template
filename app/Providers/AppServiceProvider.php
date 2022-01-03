<?php

namespace App\Providers;

use App\Cache\PermissionCache;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->isLocal()) {
            $this->app->register(IdeHelperServiceProvider::class);
        }
        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

        Validator::extend('phone', function ($attribute, $value) {
            return preg_match('@^1[3-9]\d{9}$@', $value);
        });
        Validator::extend('id', function ($attribute, $value) {
            return preg_match('@^[\da-z_]{6,16}$@', $value);
        });
        Validator::extend('redis_key', function ($attribute, $value) {
            return PermissionCache::safeKeys($value);
        });
        Validator::extend('range_datetime', function ($attribute, $value) {
            if (!is_array($value)) {
                return false;
            }
            if (count($value) !== 2) {
                return false;
            }
            $patten = '@^((([0-9]{3}[1-9]|[0-9]{2}[1-9][0-9]|[0-9][1-9][0-9]{2}|[1-9][0-9]{3})-(((0[13578]|1[02])-(0[1-9]|[12][0-9]|3[01]))|((0[469]|11)-(0[1-9]|[12][0-9]|30))|(02-(0[1-9]|[1][0-9]|2[0-8]))))|((([0-9]{2})(0[48]|[2468][048]|[13579][26])|((0[48]|[2468][048]|[3579][26])00))-02-29))\\s+([0-1]?[0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$@';
            foreach ($value as $datetime) {
                if (!preg_match($patten, $datetime)) {
                    return false;
                }
            }
            return true;
        });
    }
}
