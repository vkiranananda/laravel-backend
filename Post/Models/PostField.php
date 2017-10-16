<?php

namespace Backend\Post\Models;

use Illuminate\Database\Eloquent\Model;

class PostField extends Model
{
	public $timestamps = false;
	protected $fillable = ['field_name', 'value'];

    public function post()
    {
        return $this->belongsTo('Backend\Post\Models\Post');
    }
}
