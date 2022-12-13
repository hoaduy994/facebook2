<?php

namespace App\Providers;

use App\Http\Resources\CommentResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\PostResourceCollection;
use App\Http\Resources\UserResource;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // PostResource::withoutWrapping();
        // UserResource::withoutWrapping();
        // CommentResource::withoutWrapping();
        // PostResourceCollection::withoutWrapping();
    }
}
