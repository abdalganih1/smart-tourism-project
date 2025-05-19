<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    // Primary key is 'id' by default

    protected $fillable = [
        'user_id',
        'target_type',
        'target_id',
        'parent_comment_id',
        'content',
    ];

    public function user()
    {
        // Comment belongs to a User. Comments table has user_id FK.
        return $this->belongsTo(User::class);
    }

    // Polymorphic relationship to the commented item
    public function target()
    {
        return $this->morphTo();
    }

    // Relationship for nested comments (replies) - Self-referencing
    public function parent()
    {
        // Comment belongs to a parent Comment. Comments table has parent_comment_id FK.
        return $this->belongsTo(Comment::class, 'parent_comment_id');
    }

    public function replies()
    {
        // Comment has many child Comments (replies). Children's parent_comment_id points back here.
        return $this->hasMany(Comment::class, 'parent_comment_id');
    }
}