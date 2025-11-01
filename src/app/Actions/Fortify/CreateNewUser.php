<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input)
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => $this->passwordRules(),
        ],
        [
            'name.required'=>'ユーザー名は必須です。',
            'email.required'=>'メールアドレスは必須です。',
            'email.email'=>'正しいメールアドレスを入力してください。',
            'email.unique'=>'このメールアドレスは既に登録されています。',
            'password.required'=>'パスワードは必須です。',
            'password.min'=>'パスワードは８文字以上で入力してください。',
            'password.confirmed'=>'確認用パスワードが一致していません。',
        ])->validate();

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);
    }
}
