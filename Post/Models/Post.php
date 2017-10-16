<?php

namespace Backend\Post\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
	use \Illuminate\Database\Eloquent\SoftDeletes;
	// use Laravel\Scout\Searchable;

  	protected $dates = ['deleted_at'];

	protected $casts = [
    	'array_data' => 'array',
	];
	public function relationFields()
	{
		return $this->hasMany('Backend\Post\Models\PostField');
	}
}
