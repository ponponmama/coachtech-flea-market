<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
            'name' => 'required|string|max:20',
            'postal_code' => 'required|string|regex:/^\d{3}-\d{4}$/',
            'address' => 'required|string',
            'building_name' => 'nullable|string',
            'profile-image' => 'nullable|image|mimes:jpeg,png|max:2048',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'ユーザー名を入力してください',
            'name.max' => 'ユーザー名は20文字以内で入力してください',
            'postal_code.required' => '郵便番号を入力してください',
            'postal_code.regex' => '郵便番号はハイフンありの8文字で入力してください（例：123-4567）',
            'address.required' => '住所を入力してください',
            'profile-image.image' => '画像ファイルを選択してください',
            'profile-image.mimes' => '画像はjpegまたはpng形式で選択してください',
            'profile-image.max' => '画像サイズは2MB以下で選択してください',
        ];
    }
}