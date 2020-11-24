<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getStatusAttribute($value) {
    	switch ($value) {
    		case 1:
    			return [
                    "id" => 1,
                    "name" => "Pending"
                ];
    			break;
    		case 2:
                return [
                    "id" => 2,
                    "name" => "Approved"
                ];
    			break;
    		case 3:
                return [
                    "id" => 3,
                    "name" => "Rejected"
                ];
    			break;
            case 4:
                return [
                    "id" => 4,
                    "name" => "Available"
                ];
                break;
            case 5:
                return [
                    "id" => 5,
                    "name" => "Not Available"
                ];
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
