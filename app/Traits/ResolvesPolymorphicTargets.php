<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait ResolvesPolymorphicTargets
{
    /**
     * Maps a target_type string to its corresponding Model class name.
     * This is useful for polymorphic relationships.
     *
     * @param string $targetType
     * @return string|null
     */
    protected function mapTargetTypeToModel(string $targetType): ?string
    {
        // Define your mapping here. Keys should be the exact strings used in target_type column (e.g., 'TouristSite', 'Product')
        // Values should be the full class names of the models.
        $mapping = [
            'TouristSite' => \App\Models\TouristSite::class,
            'Product' => \App\Models\Product::class,
            'Article' => \App\Models\Article::class,
            'Hotel' => \App\Models\Hotel::class,
            'SiteExperience' => \App\Models\SiteExperience::class,
            // Add other polymorphic target types here
        ];

        // Convert the incoming targetType (e.g., 'tourist-sites') to the expected model key (e.g., 'TouristSite')
        $studlyTargetType = Str::studly(Str::singular($targetType));
        return $mapping[$studlyTargetType] ?? null;
    }

    /**
     * Maps a target_type string to its corresponding database table name.
     *
     * @param string $targetType
     * @return string|null
     */
    protected function mapTargetTypeToTable(string $targetType): ?string
    {
        $modelClass = $this->mapTargetTypeToModel($targetType);

        if ($modelClass && class_exists($modelClass)) {
            // Get table name from the model instance
            return (new $modelClass())->getTable();
        }

        return null;
    }
}