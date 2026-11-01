<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasUniqueSlug
{
    /**
     * Boot the trait to generate unique slugs on create and update.
     */
    protected static function bootHasUniqueSlug(): void
    {
        static::creating(function (Model $model) {
            $model->slug = static::generateUniqueSlug($model);
        });

        static::updating(function (Model $model) {
            $sourceField = $model->slugSourceField();
            if ($model->isDirty($sourceField) && empty($model->slug)) {
                $model->slug = static::generateUniqueSlug($model);
            }
        });
    }

    /**
     * Source field to generate the slug from (default: 'name').
     */
    public function slugSourceField(): string
    {
        return property_exists($this, 'slugSource') ? $this->slugSource : 'name';
    }

    /**
     * Generate a unique slug checking for collisions.
     */
    public static function generateUniqueSlug(Model $model): string
    {
        $sourceField = $model->slugSourceField();
        $baseString = $model->{$sourceField} ?? 'item';
        $slug = Str::slug($baseString, '-', 'tr');

        if (empty($slug)) {
            $slug = 'item-' . Str::lower(Str::random(6));
        }

        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)
            ->when($model->exists, fn ($query) => $query->where($model->getKeyName(), '!=', $model->getKey()))
            ->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        return $slug;
    }
}

