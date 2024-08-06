<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CommentRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'post_slug' => 'required|exists:posts,slug',
            'content' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'post_slug.required' => 'Post slug gereklidir.',
            'post_slug.exists' => 'Bu post slug geçersiz.',
            'content.required' => 'Yorum içeriği boş olamaz.',
        ];
    }
}
