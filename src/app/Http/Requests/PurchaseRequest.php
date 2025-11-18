<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
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
            'payment_method_type' => 'required|string|in:credit,convenience',
            'payment_method_id' => 'required_if:payment_method_type,credit|nullable|string',
        ];
    }
    public function messages()
    {
        return [
            'payment_method_type.required' => '支払い方法を選択してください。',
            'payment_method_type.in' => '無効な支払い方法が選択されました。',
            'payment_method_id.required_if' => 'クレジットカード決済には、カード情報の入力が必要です。',
        ];
    }
}
