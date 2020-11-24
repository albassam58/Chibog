<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;

class Order extends BaseModel
{
    use HasFactory;

    /*
		Status:
			1. Cart
			2. Pending
			3. Processing
			4. For Delivery
			5. Delivered
			6. Reject
    */

    protected $appends = ['name', 'total'];

    protected $guarded = [];

    public function getNameAttribute() {
        return $this->item ? $this->item->name : "";
    }

    public function getTotalAttribute() {
        return $this->amount * $this->quantity;
    }

    public function getStatusAttribute($value) {
    	switch ($value) {
    		case 1:
    			return [
                    "id" => 1,
    				"name" => "Cart",
    				"color" => "grey"
    			];
    			break;
    		case 2:
    			return [
                    "id" => 2,
    				"name" => "Pending",
    				"color" => "grey"
    			];
    			break;
    		case 3:
    			return [
                    "id" => 3,
    				"name" => "Processing",
    				"color" => "orange"
    			];
    			break;
    		case 4:
    			return [
                    "id" => 4,
    				"name" => "For Delivery",
    				"color" => "blue"
    			];
    			break;
    		case 5:
    			return [
                    "id" => 5,
    				"name" => "Delivered",
    				"color" => "green"
    			];
    			break;
    		case 6:
    			return [
                    "id" => 6,
    				"name" => "Cancelled",
    				"color" => "red"
    			];
    			break;
    		
    		default:
    			return [
                    "id" => 0,
    				"name" => "",
    				"color" => "grey"
    			];
    			break;
    	}
    }

    public function store() {
        return $this->belongsTo('App\Models\Store', 'store_id');
    }

    public function item() {
    	return $this->belongsTo('App\Models\Item', 'item_id');
    }
}
