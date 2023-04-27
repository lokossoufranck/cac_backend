<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\API\V1\Module\ModuleController;
use App\Models\Module\Module;
use Illuminate\Auth\Notifications\ResetPassword;

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

   
    ResetPassword::createUrlUsing(function ($user, string $token) {
        return env('FRONTEND_URL').'reset-password/email/'.$user->email.'/token/'.$token;
        //return 'https://example.com/reset-password?token='.$token;
    });
        
    /* $m= new Module();
    $user_modules= new ModuleController($m);
    $profile_of_user=$user_modules->list_by_user();
   
        View::share('profile_of_user',$profile_of_user);*/
    }
}
