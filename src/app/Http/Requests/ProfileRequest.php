<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_name' => 'required|string|max:20',
            'postal_code' => 'required|regex:/^\d{3}-\d{4}$/',
            'address' => 'required|string|max:255',
            'building_name' => 'nullable|string|max:255',
            'profile_image' => 'nullable|image|mimes:jpeg,png|max:2048',
        ];
    }
    public function messages(){
        return [
            'profile_image.image' => 'プロフィール画像は画像ファイル形式でアップロードしてください。',
            'profile_image.mimes' => 'プロフィール画像の拡張子はJPEGまたはPNGのみが許可されています。',
            'profile_image.max' => 'プロフィール画像のファイルサイズは2MB以内にしてください。',
            'user_name.required' => 'ユーザー名は必ず入力してください。',
            'user_name.max'=>'ユーザー名は20文字以内で入力してください',
            'postal_code.required'=>'郵便番号は必須です。',
            'postal_code.regex' => '郵便番号は「XXX-YYYY」の形式（ハイフン含む8文字）で入力してください。',
            'address.required'=>'住所は入力必須です。都道府県、市区町村、番地までお願いします。'
        ];
    }
}
