<?php

namespace Backend\Category\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
	use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $casts = [
    	'array_data' => 'array',
	];

}
