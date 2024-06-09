<?php

namespace App\Http\Controllers\Api\Menu;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\Menu\PizzaBuildMenuRequest;

class PizzaController extends ApiController
{
    public function index(PizzaBuildMenuRequest $request): \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
    {
        return $this->jsonResponse([
            'recipe' => $request->input('recipe'),
        ]);
    }
}
