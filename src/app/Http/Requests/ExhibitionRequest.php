<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'image_path' => ['required', 'image', 'mimes:jpeg,png', 'max:2048'],
            'category_id' => ['required', 'array'],
            'category_id.*' => ['exists:categories,id'],
            'condition_id' => ['required', 'exists:conditions,id'],
            'name' => ['required', 'string', 'max:255'],
            'brand_name' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'price' => ['required', 'integer', 'min:0'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'image_path.required' => '商品画像をアップロードしてください。',
            'image_path.image' => '商品画像は画像ファイル形式でアップロードしてください。',
            'image_path.mimes' => '商品画像の拡張子はJPEGまたはPNGのみが許可されています。',
            'image_path.max' => '商品画像のファイルサイズは2MB以内にしてください。',
            'category_id.required' => '商品のカテゴリーを1つ以上選択してください。',
            'category_id.array' => 'カテゴリーの選択形式が不正です。',
            'condition_id.required' => '商品の状態を選択してください。',
            'name.required' => '商品名は必ず入力してください。',
            'name.max' => '商品名は255文字以内で入力してください。',
            'description.required' => '商品の説明は必ず入力してください。',
            'description.max' => '商品の説明は255文字以内で入力してください。',
            'price.required' => '販売価格は必ず入力してください。',
            'price.integer' => '販売価格は整数で入力してください。',
            'price.min' => '販売価格は0円以上で設定してください。',
        ];
    }
}