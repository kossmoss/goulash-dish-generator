<?php

namespace App\Console\Commands;

use App\Services\MenuService;
use App\Services\Recipe\RecipeCodeHelper;
use Illuminate\Console\Command;

class MenuCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:menu {dish=pizza} {recipe=dcciiii}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates menu with the given recipe template
                            Available options:
                              - pizza: Generate pizza menu';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dishCode = $this->argument('dish');
        $recipe = $this->argument('recipe');

        $ingredientTypeCodes = RecipeCodeHelper::normalizeRecipeTemplate($recipe);

        try {
            $menuService = new MenuService();
            $menu = $menuService->buildMenu($dishCode, $ingredientTypeCodes);

            echo json_encode($menu, JSON_UNESCAPED_UNICODE + JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            echo "\033[1;31mError generating menu:\033[0m\n";
            echo $e->getMessage() . "\n";
        }
    }
}
