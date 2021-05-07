<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin Builder
 */
class Category extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'parent_id'];

    public function childs(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id', 'id');
    }
}
