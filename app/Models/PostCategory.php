<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // Relasi dengan Post
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
