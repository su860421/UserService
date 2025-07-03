<?php

namespace App\Providers;

use App\Contracts\OrganiztionsServiceInterface;
use App\Services\OrganiztionsService;
use App\Contracts\OrganiztionsRepositoryInterface;
use App\Repositories\OrganiztionsRepository;

use Illuminate\Support\ServiceProvider;
use App\Contracts\UserRepositoryInterface;
use App\Contracts\UserServiceInterface;
use App\Contracts\AuthServiceInterface;
use App\Repositories\UserRepository;
use App\Services\UserService;
use App\Services\AuthService;
use App\Contracts\AuthorizationServiceInterface;
use App\Services\AuthorizationService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(UserServiceInterface::class, UserService::class);
        $this->app->bind(AuthServiceInterface::class, AuthService::class);
        $this->app->bind(AuthorizationServiceInterface::class, AuthorizationService::class);
        $this->app->bind(OrganiztionsServiceInterface::class, OrganiztionsService::class);
        $this->app->bind(OrganiztionsRepositoryInterface::class, OrganiztionsRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
