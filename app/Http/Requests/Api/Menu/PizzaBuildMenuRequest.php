<?php

namespace App\Http\Requests\Api\Menu;

use Illuminate\Foundation\Http\FormRequest;

class PizzaBuildMenuRequest extends FormRequest
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
            'recipe' => [
                'required',
                'regex:#^[dci]+$#i',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'recipe.regex' => "Pizza recipe code may contain only 'd', 'c' and 'i' characters",
        ];
    }
}
