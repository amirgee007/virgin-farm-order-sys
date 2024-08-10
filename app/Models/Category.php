<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $table = 'categories';
    protected $guarded = [];

    static function dutchCategories()
    {
        return  Category::where('product_type' , 'dutch')->pluck('category_id')->toArray();
//        return [
//            81, 57, 61, 82, 35
//        ];
    }
}
