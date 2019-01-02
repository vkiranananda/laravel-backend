<?php

namespace Backend\Root\MediaFile\Models;

use Illuminate\Database\Eloquent\Model;

class MediaFile extends Model
{
	protected $casts = [
    	'sizes' => 'array',
    	'array_data' => 'array',
	];
}
