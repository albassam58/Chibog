<?php

namespace App\Models;

use App\FullTextSearch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory, FullTextSearch;

    /*
		Status:
			1. Pending
			2. Approved
			3. Rejected
    */

    protected $guarded = [];

    protected $searchable = [
        'name',
        'region',
        'province',
        'city',
        'barangay',
        'street'
    ];

    public function vendor() {
    	return $this->belongsTo('App\Models\Vendor', 'vendor_id')->select(['id', 'first_name', 'last_name', 'mobile_number', 'email', 'region', 'province', 'city', 'barangay', 'street']);
    }

    public function reviews() {
        return $this->hasMany('App\Models\StoreReview', 'store_id');
    }

    public function items() {
    	return $this->hasMany('App\Models\Item', 'store_id');
    }
}
