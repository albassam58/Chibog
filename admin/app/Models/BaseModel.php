<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class BaseModel extends Model
{
	use SoftDeletes;

	public static function boot()
    {
        parent::boot();

        static::creating(function($model)
        {
            $user = Auth::user();

            if ($user) {
	            $model->created_by = $user->id;
	            $model->updated_by = $user->id;
	        }

            $user = auth('sanctum')->user();

            if ($user) {
                $model->created_by = $user->id;
                $model->updated_by = $user->id;
            }
        });

        static::updating(function($model)
        {
            $user = Auth::user();

            if ($user) {
            	$model->updated_by = $user->id;
            }

            $user = auth('sanctum')->user();

            if ($user) {
                $model->updated_by = $user->id;
            }
        });
    }
}