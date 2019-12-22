<?php

namespace Backend\BaseMod\Models;

use Illuminate\Database\Eloquent\Model;

class BaseMod extends Model
{
	use \Illuminate\Database\Eloquent\SoftDeletes;

  	protected $dates = ['deleted_at'];

	protected $casts = [
    	'array_data' => 'array',
	];
}
