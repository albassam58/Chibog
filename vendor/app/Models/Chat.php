<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;

class Chat extends BaseModel
{
    use HasFactory;

    /*
		Status:
			1. Not yet read
			2. Read
    */

	protected $guarded = [];

	public function vendor() {
		return $this->belongsTo('App\Models\Vendor', 'vendor_id');
	}

	public function user() {
		return $this->belongsTo('App\Models\User', 'user_id');
	}

}
