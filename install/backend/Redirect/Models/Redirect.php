<?php

namespace Backend\Root\Redirect\Models;

use Illuminate\Database\Eloquent\Model;

class Redirect extends Model
{
	public $timestamps = false;
	protected $casts = [
    	'array_data' => 'array',
	];
}
