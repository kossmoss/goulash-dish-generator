<?php

namespace App\Http\Requests\Api\Menu;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class PizzaBuildMenuRequest extends FormRequest
{
    /**
     * @var string[]
     */
    protected array $ingredientTypeCodes;

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

    /**
     * Perform additional check:
     *
     * Pizza must contain exactly one dough
     * and at least one cheese
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function getValidatorInstance(): \Illuminate\Contracts\Validation\Validator
    {
        $validator = parent::getValidatorInstance();

        $validator->after(function (Validator $validator) {
            $ingredientTypeCodes = $this->ingredientTypeCodes();

            if ($ingredientTypeCodes['d'] <> 1) {
                $validator->errors()->add(
                    'recipe', "Can't add more than one dough to the recipe"
                );
            }

            if ($ingredientTypeCodes['c'] < 1) {
                $validator->errors()->add(
                    'recipe', "Must add at least one cheese to the recipe"
                );
            }
        });

        return $validator;
    }

    /**
     * Get array of ingredient types and their amounts
     */
    public function ingredientTypeCodes(): array
    {
        $this->normalizeIngredientTypeCodes();
        return $this->ingredientTypeCodes;
    }

    /**
     * Converts given ingredient codes into normalized state
     */
    private function normalizeIngredientTypeCodes()
    {
        if (empty($this->ingredientTypeCodes)) {
            // instantiate codes by default if they aren't present
            $this->ingredientTypeCodes = [
                'd' => 0,
                'c' => 0,
                'i' => 0,
            ];

            $recipe = $this->get('recipe');

            for ($c = 0; $c < strlen($recipe); $c++) {
                $code = strtolower($recipe[$c]);
                $this->ingredientTypeCodes[$code] = isset($this->ingredientTypeCodes[$code]) ? ++$this->ingredientTypeCodes[$code] : 1;
            }
        }
    }
}
