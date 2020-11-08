<?php

namespace Backend\Root\User\Models;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
	protected $connection = 'frontend';
    public $timestamps = false;

	protected $casts = [
    	'array_data' => 'array',
	];
}