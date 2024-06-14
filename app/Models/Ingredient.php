<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Ingredient
 * @package App\Models
 *
 * @property string $title
 * @property int $type_id
 * @property float $price
 *
 * @property IngredientType $type
 */
class Ingredient extends Model
{
    protected $table = 'ingredient';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type_id',
        'title',
        'price',
    ];

    public function type(): BelongsTo
    {
        return $this->belongsTo(IngredientType::class, 'type_id');
    }
}
