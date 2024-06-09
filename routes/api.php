<?php

use Illuminate\Support\Facades\Route;

Route::get('menu/pizza', [\App\Http\Controllers\Api\Menu\PizzaController::class, 'index']);
