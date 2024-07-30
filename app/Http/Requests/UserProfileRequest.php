<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'surname' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $this->user()->id,
            'current_password' => 'sometimes|required|string|min:4',
            'new_password' => 'sometimes|required|string|min:4|confirmed|different:current_password',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Ad alanı boş bırakılmamalıdır.',
            'surname.required' => 'Soyad alanı boş bırakılmamalıdır.',
            'email.required' => 'E-posta alanı boş bırakılmamalıdır.',
            'email.email' => 'Geçersiz e-posta adresi.',
            'email.unique' => 'Bu E-posta adresi zaten kullanılmaktadır.',
            'current_password.min' => 'Mevcut şifre en az 4 karakter olmalıdır.',
            'current_password.required' => 'Mevcut şifre alanı boş bırakılmamalıdır.',
            'new_password.required' => 'Yeni şifre alanı boş bırakılmamalıdır.',
            'new_password.min' => 'Yeni şifre en az 4 karakter olmalıdır.',
            'new_password.confirmed' => 'Yeni şifreler eşleşmiyor.',
            'new_password.different' => 'Yeni şifre mevcut şifre ile aynı olamaz.',
        ];
    }
}
