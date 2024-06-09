<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ingredient extends Model
{
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
