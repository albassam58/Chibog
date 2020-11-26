<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;

class OrderNotification extends BaseModel
{
    use HasFactory;

    /*
		Status:
			1. Not yet read
			2. Read
    */

	protected $guarded = [];
}
