<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class IngredientType
 * @package App\Models
 *
 * @property string $title
 * @property string $code
 *
 * @property Ingredient[] $ingredients
 */
class IngredientType extends Model
{
    public const CODE_DOUGH = 'd';
    public const CODE_CHEESE = 'c';
    public const CODE_INTERNALS = 'i';

    protected $table = 'ingredient_type';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'code',
    ];

    public function ingredients(): HasMany
    {
        return $this->hasMany(Ingredient::class, 'type_id');
    }
}
