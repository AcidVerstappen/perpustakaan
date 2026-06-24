<?php

namespace App\Providers;

use App\Repositories\BookRepository;
use App\Repositories\Interfaces\BookRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(\App\Repositories\Interfaces\BookRepositoryInterface::class, \App\Repositories\BookRepository::class);
        $this->app->bind(\App\Repositories\Interfaces\MemberRepositoryInterface::class, \App\Repositories\MemberRepository::class);
        $this->app->bind(\App\Repositories\Interfaces\BorrowingRepositoryInterface::class, \App\Repositories\BorrowingRepository::class);
        $this->app->bind(\App\Repositories\Interfaces\ReturnRepositoryInterface::class, \App\Repositories\ReturnRepository::class);
        $this->app->bind(\App\Repositories\Interfaces\FineRepositoryInterface::class, \App\Repositories\FineRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
