<?php

namespace App\Services\Recipe;

class RecipeCodeHelper
{
    /**
     * Converts given ingredient codes string into normalized state
     */
    public static function normalizeRecipeTemplate(string $recipeTemplate): array
    {
        // instantiate codes by default if they aren't present in the template
        $normalizedRecipe = [
            'd' => 0,
            'c' => 0,
            'i' => 0,
        ];

        for ($c = 0; $c < strlen($recipeTemplate); $c++) {
            $code = strtolower($recipeTemplate[$c]);
            $normalizedRecipe[$code] = isset($normalizedRecipe[$code]) ? ++$normalizedRecipe[$code] : 1;
        }

        return $normalizedRecipe;
    }
}
