<?php

namespace Backend\User\Models;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    public $timestamps = false;

	protected $casts = [
    	'array_data' => 'array',
	];
}
