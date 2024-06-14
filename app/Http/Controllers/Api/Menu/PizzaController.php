<?php

namespace App\Http\Controllers\Api\Menu;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\Menu\PizzaBuildMenuRequest;
use App\Services\MenuService;
use App\Services\Recipe\RecipeCodeHelper;
use Exception;

class PizzaController extends ApiController
{
    /**
     * @param PizzaBuildMenuRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    public function index(PizzaBuildMenuRequest $request): \Illuminate\Http\JsonResponse
    {
        $ingredientTypeCodes = RecipeCodeHelper::normalizeRecipeTemplate(
            $request->input('recipe')
        );

        $menuService = new MenuService();
        $menu = $menuService->buildMenu(MenuService::PIZZA, $ingredientTypeCodes);

        return $this->jsonResponse($menu);
    }
}
