<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;

    // Primary key is 'id' by default
    public $timestamps = false; // No timestamps in schema

    protected $fillable = [
        'name',
        'description',
        'parent_category_id',
    ];

    // Hierarchical relationship (Self-referencing)
    public function parent()
    {
        // ProductCategory belongs to a parent ProductCategory. ProductsCategory table has parent_category_id FK.
        // Specify FK and Local Key if they don't match conventions ('parent_category_id' vs 'id')
        return $this->belongsTo(ProductCategory::class, 'parent_category_id', 'id');
    }

    public function children()
    {
        // ProductCategory has many children ProductCategory. Children's parent_category_id points back here.
        return $this->hasMany(ProductCategory::class, 'parent_category_id', 'id');
    }

    // Relationship to products in this category
    public function products()
    {
        // ProductCategory has many Products. Products table has category_id FK.
        return $this->hasMany(Product::class, 'category_id');
    }
}