<?php

namespace App\Services\DishFactories;

use App\Models\IngredientType;

class PizzaFactory extends DishFactory
{
    public function ingredientTypeMinAmount($ingredientTypeCode): int
    {
        return match($ingredientTypeCode) {
            IngredientType::CODE_DOUGH, IngredientType::CODE_CHEESE => 1,
            default => 0,
        };
    }

    public function ingredientTypeMaxAmount($ingredientTypeCode): ?int
    {
        return match($ingredientTypeCode) {
            IngredientType::CODE_DOUGH => 1,
            default => null,
        };
    }
}
