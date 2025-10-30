<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'recipe_id',
        'parent_id',
        'content',
        'upvotes',
        'downvotes',
    ];

    protected $casts = [
        'upvotes' => 'integer',
        'downvotes' => 'integer',
    ];

    protected $appends = ['score'];

    public function getScoreAttribute()
    {
        return $this->upvotes - $this->downvotes;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function votes()
    {
        return $this->hasMany(CommentVote::class);
    }

    public function userVote($userId)
    {
        return $this->votes()->where('user_id', $userId)->first();
    }
}
