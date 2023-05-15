<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryPost extends Model
{
    use HasFactory;

    protected $table = 'category_post';
    protected $fillable = ['category_id', 'post_id']; // createMany([category[1,2,3]]);
    public $timestamps = false;

    # Use this method to get the category
    public function category(){
        return $this->belongsTo(Category::class);
    }

}
