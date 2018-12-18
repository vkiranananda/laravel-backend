<?php

namespace Backend\Root\Blog\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
	use \Illuminate\Database\Eloquent\SoftDeletes;
	// use Laravel\Scout\Searchable;

  	protected $dates = ['deleted_at'];
  	protected $table = 'blog';
	protected $casts = [
    	'array_data' => 'array',
	];
}
