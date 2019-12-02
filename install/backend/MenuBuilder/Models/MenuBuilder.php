<?php

namespace Backend\MenuBuilder\Models;

use Illuminate\Database\Eloquent\Model;

class MenuBuilder extends Model
{
	use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $dates = ['deleted_at'];
  	protected $table = 'menu_builder';
	protected $casts = [
    	'menu' => 'array',
	];
}
