<?php

namespace Vanguard\Models;

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class ColorClass extends Model
{
    protected $table = 'colors_class';

    protected $fillable = [
        'class_id', 'sub_class', 'description', 'color'
    ];
}
