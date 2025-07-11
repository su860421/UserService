<?php

namespace App\Providers;

use App\Contracts\OrganizationsServiceInterface;
use App\Services\OrganizationsService;
use App\Contracts\OrganizationsRepositoryInterface;
use App\Repositories\OrganizationsRepository;
use App\Models\Organizations;
use App\Observers\OrganizationsObserver;

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
        $this->app->bind(OrganizationsServiceInterface::class, OrganizationsService::class);
        $this->app->bind(OrganizationsRepositoryInterface::class, OrganizationsRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 註冊 Organizations Observer
        \App\Models\Organizations::observe(\App\Observers\OrganizationsObserver::class);
    }
}
