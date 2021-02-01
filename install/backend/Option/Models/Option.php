<?php

namespace Backend\Option\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
	protected $dates = ['deleted_at'];
	protected $casts = [
    	'array_data' => 'array',
	];
}
