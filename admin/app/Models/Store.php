<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    /*
		Status:
			1. Pending
			2. Approved
			3. Rejected
    */

    protected $appends = ['picture'];

    protected $guarded = [];

    public function getPictureAttribute() {
    	try {
	    	return [
	    		"name" => basename($this->logo),
	    		"type" => mime_content_type($this->logo),
	    		"size" => filesize($this->logo)
	    	];
	    } catch (\Exception $e) {
	    	return [];
	    }
    }

    public function vendor() {
    	return $this->belongsTo('App\Models\Vendor', 'vendor_id');
    }

    public function reviews() {
        return $this->hasMany('App\Models\StoreReview', 'store_id');
    }

    public function items() {
    	return $this->hasMany('App\Models\Item', 'store_id');
    }
}
