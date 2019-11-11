<?php

namespace Backend\Blog\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
	use \Illuminate\Database\Eloquent\SoftDeletes;

  	protected $dates = ['deleted_at'];
  	protected $table = 'blog';
	protected $casts = [
    	'array_data' => 'array',
	];
}
