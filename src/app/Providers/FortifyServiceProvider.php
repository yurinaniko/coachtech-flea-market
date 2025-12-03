<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use App\Actions\Fortify\CreateNewUser;
use Laravel\Fortify\Contracts\RegisterResponse;

class FortifyServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        app()->singleton(RegisterResponse::class, function () {
            return new class implements RegisterResponse {
                public function toResponse($request)
                {
                    return redirect()->route('mypage.index');
                }
            };
        });

        Fortify::registerView(function () {
        return view('auth.register');
        });

        Fortify::loginView(function () {
        return view('auth.login');
        });

        Fortify::createUsersUsing(CreateNewUser::class);
    }
}
