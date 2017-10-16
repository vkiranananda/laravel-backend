<?php

namespace Backend\Upload\Models;

use Illuminate\Database\Eloquent\Model;

class MediaFile extends Model
{
	protected $casts = [
    	'sizes' => 'array',
	];
}
