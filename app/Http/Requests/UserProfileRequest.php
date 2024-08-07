<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserProfileRequest extends BaseFormRequest
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
        $rules = [
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $this->user()->id,
        ];

        if ($this->isMethod('post')) {
            $rules['current_password'] = 'required|string|min:4';
            $rules['new_password'] = 'sometimes|nullable|string|min:4|confirmed|different:current_password';
        }

        if ($this->isMethod('delete')) {
            $rules['delete_password'] = 'required|string|min:4';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => 'Ad alanı boş bırakılmamalıdır.',
            'surname.required' => 'Soyad alanı boş bırakılmamalıdır.',
            'email.required' => 'E-posta alanı boş bırakılmamalıdır.',
            'email.email' => 'Geçersiz e-posta adresi.',
            'email.unique' => 'Bu E-posta adresi zaten kullanılmaktadır.',
            'current_password.required' => 'Mevcut şifre alanı boş bırakılmamalıdır.',
            'current_password.min' => 'Mevcut şifre en az 4 karakter olmalıdır.',
            'new_password.min' => 'Yeni şifre en az 4 karakter olmalıdır.',
            'new_password.confirmed' => 'Yeni şifreler eşleşmiyor.',
            'new_password.different' => 'Yeni şifre mevcut şifre ile aynı olamaz.',
            'delete_password.required' => 'Mevcut şifre alanı boş bırakılmamalıdır.',
            'delete_password.min' => 'Mevcut şifre en az 4 karakter olmalıdır.',
        ];
    }
}
