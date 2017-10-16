<?php

namespace Backend\News\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
	use \Illuminate\Database\Eloquent\SoftDeletes;
	// use Laravel\Scout\Searchable;

  	protected $dates = ['deleted_at'];

	protected $casts = [
    	'array_data' => 'array',
	];
}
