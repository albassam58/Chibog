<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Scopes\OrderByScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderNotification extends BaseModel
{
    use HasFactory;

    /*
		Status:
			1. Not yet read
			2. Read
    */

	public static function boot() {
        parent::boot();
        static::addGlobalScope(new OrderByScope('id', 'DESC'));
    }

	protected $guarded = [];
}
