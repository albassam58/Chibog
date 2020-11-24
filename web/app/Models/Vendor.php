<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;

class Vendor extends BaseModel
{
    use HasFactory;

    protected $hidden = ['password', 'api_token', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at'];
}
