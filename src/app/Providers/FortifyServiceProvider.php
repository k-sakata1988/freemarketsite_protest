<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Redirect;
use Laravel\Fortify\Fortify;
use App\Http\Responses\RegisterResponse;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot()
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        $this->app->singleton(
            RegisterResponseContract::class,
            function () {
                return new class implements RegisterResponseContract {
                    public function toResponse($request){
                        return Redirect::route('mypage.profile.edit');
                    }
                };
            }
        );

        Fortify::registerView(function () {
            return view('auth.register');
        });

        Fortify::loginView(function () {
            return view('auth.login');
        });

        Fortify::authenticateUsing(function (Request $request) {
            $request->validate([
                'email' => ['required', 'string', 'email'],
                'password' => ['required', 'string'],
            ], [
                'email.required' => 'メールアドレスを入力してください。',
                'email.email' => '有効なメールアドレス形式で入力してください。',
                'password.required' => 'パスワードを入力してください。',
            ]);
            $user = \App\Models\User::where('email', $request->email)->first();
            if (! $user || ! \Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'email' => 'ログイン情報が登録されていません。',
                ]);
            }

            return $user;
        });


        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;
            return Limit::perMinute(10)->by($email . $request->ip());
        });
    }
}
