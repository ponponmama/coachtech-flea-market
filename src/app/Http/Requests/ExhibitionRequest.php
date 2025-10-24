<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class ExhibitionRequest extends FormRequest
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
            'name' => 'required|string',
            'description' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png|max:2048',
            'category' => 'required|array',
            'category.*' => 'required|integer|exists:categories,id',
            'condition' => 'required|string',
            'price' => 'required|integer|min:0',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // デバッグ用ログ
            Log::info('ExhibitionRequest バリデーション:', [
                'all_data' => $this->all(),
                'has_image' => $this->hasFile('image'),
                'image_name' => $this->file('image') ? $this->file('image')->getClientOriginalName() : 'なし',
                'price' => $this->price,
                'name' => $this->name,
                'description' => $this->description,
                'condition' => $this->condition,
                'category' => $this->category,
            ]);
        });
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => '商品名を入力してください',
            'description.required' => '商品説明を入力してください',
            'description.max' => '商品説明は255文字以内で入力してください',
            'image.required' => '商品画像を選択してください',
            'image.image' => '画像ファイルを選択してください',
            'image.mimes' => '画像はjpegまたはpng形式で選択してください',
            'image.max' => '画像サイズは2MB以下で選択してください',
            'category.required' => '商品のカテゴリーを選択してください',
            'category.array' => '商品のカテゴリーを選択してください',
            'category.*.required' => '商品のカテゴリーを選択してください',
            'category.*.exists' => '有効なカテゴリーを選択してください',
            'condition.required' => '商品の状態を選択してください',
            'price.required' => '商品価格を入力してください',
            'price.integer' => '商品価格は数値で入力してください',
            'price.min' => '商品価格は0円以上で入力してください',
        ];
    }
}
