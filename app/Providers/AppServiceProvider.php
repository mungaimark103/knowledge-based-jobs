<?php

namespace App\Providers;

use App\Services\AuditLogger;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production') || !empty($_SERVER['HTTP_X_FORWARDED_PROTO']) || !empty($_ENV['VERCEL'])) {
            URL::forceScheme('https');
        }

        $this->configureDefaults();
        $this->registerAuditListeners();
    }

    protected function registerAuditListeners(): void
    {
        Event::listen(Login::class, function ($event) {
            if ($event->user) {
                AuditLogger::log('LOGIN', "User '{$event->user->name}' ({$event->user->role}) logged in.", null, $event->user);
            }
        });

        Event::listen(Logout::class, function ($event) {
            if ($event->user) {
                AuditLogger::log('LOGOUT', "User '{$event->user->name}' ({$event->user->role}) logged out.", null, $event->user);
            }
        });
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
