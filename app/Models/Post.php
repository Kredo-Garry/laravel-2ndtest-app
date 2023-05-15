<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\Like;

class Post extends Model
{
    use HasFactory;

    # A post belongs to a user
    # Use this method to get the owner of the post
    public function user(){
        return $this->belongsTo(User::class);
    }

    # Use this to get the categories under this post
    # 1 to many relationship
    public function categoryPost(){
        return $this->hasMany(CategoryPost::class);
    }

    # Use this to get all the comments, and what post has been commented
    public function comments(){
        return $this->hasMany(Comment::class);
    }

    # Use this  method to get the likes of the post
    public function likes(){
        return $this->hasMany(Like::class);
    }

    public function isLiked(){ //false
        return $this->likes()->where('user_id', Auth::user()->id)->exists();
    }

}
