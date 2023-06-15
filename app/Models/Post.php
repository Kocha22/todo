<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $table = 'posts';
    protected $guarded=[];

    public function tags() {
        return $this->belongsToMany(Tag::class);
    }
    
    public function users() {
        return $this->belongsToMany(User::class);
    }
}
