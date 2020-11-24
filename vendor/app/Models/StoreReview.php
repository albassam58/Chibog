<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;

class StoreReview extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['full_name', 'reversed_full_name'];

    public function getFullNameAttribute() {
        return $this->first_name . " " . $this->last_name;
    }

    public function getReversedFullNameAttribute() {
        return $this->last_name . ", " . $this->first_name;
    }

    public function user() {
    	return $this->belongsTo('App\Models\User', 'created_by')->select(['first_name', 'last_name']);
    }
}
