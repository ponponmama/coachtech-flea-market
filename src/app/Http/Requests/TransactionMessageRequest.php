<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class TransactionMessageRequest extends FormRequest
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
            'message' => 'nullable|string|max:400',
            'image' => 'nullable|image|mimes:jpeg,png|max:2048',
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
            // まず画像のバリデーションエラーがあるかチェック
            $hasImageError = $validator->errors()->has('image');

            // 画像ファイルが存在するかチェック
            $hasImageFile = $this->hasFile('image') && $this->file('image');

            // 画像のバリデーションエラーがない場合のみ、有効な画像かどうかを判定
            $hasValidImage = false;
            if ($hasImageFile && !$hasImageError) {
                try {
                    $imageFile = $this->file('image');
                    if ($imageFile && $imageFile->isValid()) {
                        $mimeType = $imageFile->getMimeType();
                        $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg'];
                        if (in_array($mimeType, $allowedMimes)) {
                            $hasValidImage = true;
                        }
                    }
                } catch (\Exception $e) {
                    $hasValidImage = false;
                }
            }

            $message = $this->input('message');
            // メッセージがnull、空文字列、または空白のみの場合をチェック
            $messageValue = $message ?? '';
            $messageIsEmpty = trim($messageValue) === '';

            // デバッグ用（本番環境では削除）
            Log::info('TransactionMessageRequest validation', [
                'message' => $message,
                'messageValue' => $messageValue,
                'messageIsEmpty' => $messageIsEmpty,
                'hasImageError' => $hasImageError,
                'hasValidImage' => $hasValidImage,
                'hasImageFile' => $hasImageFile,
                'all_input' => $this->all(),
            ]);

            // 画像のバリデーションエラーがある場合は、「本文を入力してください」を追加しない
            // 画像のバリデーションエラーがない && 有効な画像もない && メッセージもない場合のみ、「本文を入力してください」を追加
            if (!$hasImageError && !$hasValidImage && $messageIsEmpty) {
                $validator->errors()->add('message', '本文を入力してください');
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'message.required' => '本文を入力してください',
            'message.max' => '本文は400文字以内で入力してください',
            'image.image' => '「.png」または「.jpeg」形式でアップロードしてください',
            'image.mimes' => '「.png」または「.jpeg」形式でアップロードしてください',
        ];
    }
}