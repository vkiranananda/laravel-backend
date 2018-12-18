<?php

namespace Backend\Root\Page\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
	use \Illuminate\Database\Eloquent\SoftDeletes;

  	protected $dates = ['deleted_at'];

	protected $casts = [
    	'array_data' => 'array',
	];
}
