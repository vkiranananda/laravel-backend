<?php

namespace Backend\Root\Form\Models;

use Illuminate\Database\Eloquent\Model;

class MediaFile extends Model
{
	protected $casts = [
    	'sizes' => 'array',
    	'array_data' => 'array',
	];
}
