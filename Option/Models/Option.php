<?php

namespace Backend\Option\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
	public $timestamps = false;
	protected $casts = [
    	'array_data' => 'array',
	];
}
