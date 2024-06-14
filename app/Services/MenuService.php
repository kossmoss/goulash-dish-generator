<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Models\IngredientType;
use App\Services\DishFactories\DishFactory;
use App\Services\DishFactories\PizzaFactory;

/**
 * Class MenuService
 *
 * Generates menu for the specified dish
 *
 * @package App\Services
 */
class MenuService
{
    public const PIZZA = 'pizza';

    /**
     * @var array
     */
    private array $ingredients;

    /**
     * @var array
     */
    private array $variations;

    /**
     * Menu generator
     *
     * @param string $dishCode
     * @param array[] $ingredientTypeCodes
     * @return array
     * @throws \Exception
     */
    public function buildMenu(string $dishCode, array $ingredientTypeCodes): array
    {
        $dishFactory = match ($dishCode) {
            self::PIZZA => new PizzaFactory(),
            default => null,
        };

        if (!$dishFactory) {
            throw new \Exception("Invalid dish code: '$dishCode'");
        }

        # Preparing data for the menu generation

        $this->initIngredients($ingredientTypeCodes);

        $this->buildIngredientVariations($dishFactory, $ingredientTypeCodes);

        $ingredientSets = $this->buildDishIngredientSets();

        # Menu generation

        $menu = [];

        foreach ($ingredientSets as $ingredientSet) {
            $menuItem = [
                'price' => 0,
                'products' => [],
            ];

            foreach ($ingredientSet as $ingredientTypeCode => $ingredientsVariation) {
                foreach ($ingredientsVariation as $ingredientId) {
                    $menuItem = $this->menuItemAddIngredient($menuItem, $ingredientTypeCode, $ingredientId);
                }
            }

            $menu[] = $menuItem;
        }

        return $menu;
    }

    /**
     * Gets ingredients' information from DB
     *
     * @param array $ingredientTypeCodes
     */
    private function initIngredients(array $ingredientTypeCodes)
    {
        if (empty($this->ingredients)) {
            $this->ingredients = [];

            $ingredientTypes = IngredientType::query()
                ->with('ingredients')
                ->whereIn('code', array_keys($ingredientTypeCodes))
                ->get()->all();

            foreach ($ingredientTypes as $ingredientType) {
                /* @var IngredientType $ingredientType */
                $this->ingredients[$ingredientType->code] = [];
                foreach ($ingredientType->ingredients as $ingredient) {
                    $this->ingredients[$ingredientType->code][$ingredient->id] = $ingredient;
                }
            }
        }
    }

    /**
     * Build all the variations of ingredients for the specified ingredient type amount
     *
     * @throws \Exception
     */
    private function buildIngredientVariations(DishFactory $factory, array $ingredientTypeCodes)
    {
        $this->variations = [];

        foreach ($ingredientTypeCodes as $ingredientTypeCode => $recipeAmount) {
            $this->variations[$ingredientTypeCode] = [];

            if (!isset($this->ingredients[$ingredientTypeCode])) {
                throw new \Exception("Invalid ingredient type code: " . $ingredientTypeCode);
            }
            $availableAmount = count($this->ingredients[$ingredientTypeCode]);

            $factory->checkAmount($ingredientTypeCode, $recipeAmount, $availableAmount);

            $ingredientIds = array_keys($this->ingredients[$ingredientTypeCode]);
            $this->variations[$ingredientTypeCode] = $this->buildVariationsRecursive($ingredientIds, [], 0,
                $recipeAmount);
        }
    }

    private function buildVariationsRecursive(
        array $ingredientIds,
        array $added,
        int $minIndex,
        int $recipeAmount
    ): array {
        $maxIndex = count($ingredientIds) - $recipeAmount + count($added);

        $results = [];

        if (count($added) < $recipeAmount) {
            for ($idx = $minIndex; $idx <= $maxIndex; $idx++) {
                $variant = $added;
                $variant[] = $ingredientIds[$idx];
                if ($recipeAmount - count($variant) > 0) {
                    $results = array_merge($results, $this->buildVariationsRecursive(
                        $ingredientIds,
                        $variant,
                        $idx + 1,
                        $recipeAmount
                    ));
                } else {
                    $results[] = $variant;
                }
            }
        }

        return $results;
    }

    /**
     * Builds sets of ingredients ids for the final menu generation
     *
     * @return array
     */
    private function buildDishIngredientSets(): array
    {
        $recipeCodes = [];

        foreach ($this->variations as $ingredientTypeCode => $ingredientVariations) {
            if (!empty($ingredientVariations)) {
                $recipeCodes[] = $ingredientTypeCode;
            }
        }

        return $this->buildIngredientSetRecursive($recipeCodes, []);
    }

    private function buildIngredientSetRecursive(array $recipeCodes, array $currentSet): array
    {
        $results = [];

        $nextIngredientCode = $recipeCodes[count($currentSet)];

        foreach ($this->variations[$nextIngredientCode] as $variation) {
            $set = $currentSet;
            $set[$nextIngredientCode] = $variation;
            if (count($set) === count($recipeCodes)) {
                $results[] = $set;
            } else {
                $results = array_merge($results, $this->buildIngredientSetRecursive($recipeCodes, $set));
            }
        }

        return $results;
    }

    /**
     * Adds ingredient to  the final menu item, and calculates item price
     *
     * @param $menuItem
     * @param $ingredientTypeCode
     * @param $ingredientId
     * @return array
     */
    private function menuItemAddIngredient($menuItem, $ingredientTypeCode, $ingredientId): array
    {
        $ingredient = $this->ingredients[$ingredientTypeCode][$ingredientId];
        /* @var Ingredient $ingredient */

        $menuItem['price'] += $ingredient->price;
        $menuItem['products'][] = [
            'type' => $ingredient->type->title,
            'value' => $ingredient->title,
        ];

        return $menuItem;
    }
}
