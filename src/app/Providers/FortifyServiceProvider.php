<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use App\Actions\Fortify\CreateNewUser;

class FortifyServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        Fortify::registerView(function () {
        return view('auth.register');
    });

    Fortify::loginView(function () {
        return view('auth.login');
    });

        Fortify::createUsersUsing(CreateNewUser::class);
    }
}
