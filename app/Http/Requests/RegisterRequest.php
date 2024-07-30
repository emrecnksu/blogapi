<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:4|confirmed',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Ad alanı boş bırakılmamalıdır',
            'surname.required' => 'Soyad alanı boş bırakılmamalıdır',
            'email.required' => 'E-posta alanı boş bırakılmamalıdır',
            'email.unique' => 'Bu E-posta adresi zaten kullanılmaktadır',
            'password.required' => 'Şifre alanı boş bırakılmamalıdır',
            'password.min' => 'Şifre en az 4 karakter olmalıdır',
            'password.confirmed' => 'Şifreler eşleşmiyor',
        ];
    }
}
