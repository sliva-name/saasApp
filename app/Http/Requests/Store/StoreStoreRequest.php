<?php

namespace App\Http\Requests\Store;

use Illuminate\Foundation\Http\FormRequest;

class StoreStoreRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'plan' => 'required|in:Free,Basic,Pro',
            'custom_domain' => 'nullable|string|regex:/^[a-z0-9]+([\-]?[a-z0-9]+)*(\.[a-z]{2,})+$/i|unique:domains,domain|max:50',
            'theme_id' => ['nullable', 'integer', 'exists:themes,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'plan.required' => 'План обязателен для выбора.',
            'plan.in' => 'Выбран недопустимый план.',
            'custom_domain.regex' => 'Домен должен быть в правильном формате.',
            'custom_domain.unique' => 'Этот домен уже используется.',
            'custom_domain.max' => 'Домен не может быть длиннее 50 символов.',
            'theme_id.exists' => 'Выбранная тема не существует.',
        ];
    }
}
