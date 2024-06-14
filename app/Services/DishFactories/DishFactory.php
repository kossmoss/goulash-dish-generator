<?php

namespace App\Services\DishFactories;

use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class DishFactory
{
    /**
     * Returns minimum amount of specified ingredient type
     *
     * @param $ingredientTypeCode
     * @return int
     */
    public function ingredientTypeMinAmount($ingredientTypeCode): int
    {
        return 0;
    }

    /**
     * Returns maximum amount of specified ingredient type
     * or null, if there's no maximum amount
     *
     * @param $ingredientTypeCode
     * @return int|null
     */
    public function ingredientTypeMaxAmount($ingredientTypeCode): ?int
    {
        return null;
    }

    /**
     * Performs correctness of the specified amount of ingredient type in the recipe
     *
     * @param string $ingredientTypeCode
     * @param int $availableAmount
     * @param int $recipeAmount
     * @throws \Exception
     */
    public function checkAmount(string $ingredientTypeCode, int $recipeAmount, int $availableAmount)
    {
        # Minimum amount check

        $minAmount = static::ingredientTypeMinAmount($ingredientTypeCode);

        if ($recipeAmount < $minAmount) {
            throw new HttpException(400, "Minimum amount of ingredients of type '$ingredientTypeCode' is $minAmount");
        }

        if ($recipeAmount === 0) {
            // no need to perform any other calculations
            return;
        }

        # Maximum amount checks

        $maxAmount = static::ingredientTypeMaxAmount($ingredientTypeCode);

        if ($maxAmount && $recipeAmount > $maxAmount) {
            throw new HttpException(400, "Maximum amount of ingredient type '$ingredientTypeCode' exceeded: more than $maxAmount ingredients");
        }

        // Additional check if database has enough ingredients
        if ($recipeAmount > $availableAmount) {
            throw new HttpException(422, "Not enough ingredients found for the type '$ingredientTypeCode': $availableAmount ingredients is available");
        }

        // Additional check if factory has wrong settings
        if ($minAmount < 0 || ($maxAmount && $minAmount > $maxAmount)) {
            Log::critical("Wrong set of minimum and maximum amounts for ingredient type code '$ingredientTypeCode' at " . static::class);
            throw new \Exception("Internal error: wrong minimum and maximum amount settings");
        }
    }
}
