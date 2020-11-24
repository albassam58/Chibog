<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    /*
        Status:
            1. Pending
            2. Approved
            3. Rejected
    */

    protected $guarded = [];

    protected $appends = ['special_instruction'];

    public function getSpecialInstructionAttribute() {
        return null;
    }

    public function getStatusAttribute($value) {
    	switch ($value) {
    		case 1:
    			return "Pending";
    			break;
    		case 2:
    			return "Approved";
    			break;
    		case 3:
    			return "Rejected";
    			break;
            case 4:
                return "Available";
                break;
            case 5:
                return "Not Available";
                break;
    		
    		default:
    			return "";
    			break;
    	};
    }

    public function store() {
        return $this->belongsTo('App\Models\Store', 'store_id');
    }

    public function foodType() {
    	return $this->belongsTo('App\Models\FoodType', 'food_type_id');
    }

    public function cuisine() {
    	return $this->belongsTo('App\Models\Cuisine', 'cuisine_id');
    }
}
