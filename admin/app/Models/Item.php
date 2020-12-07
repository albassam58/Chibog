<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;

class Item extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['dishValue'];

    public function getDishValueAttribute() {
        switch ($this->dish) {
            case 1:
                return [
                    "id" => 1,
                    "name" => "Main Dish"
                ];
                break;
            case 2:
                return [
                    "id" => 2,
                    "name" => "Appetizer"
                ];
                break;
            case 3:
                return [
                    "id" => 3,
                    "name" => "Dessert"
                ];
                break;
            case 4:
                return [
                    "id" => 4,
                    "name" => "Drinks"
                ];
                break;
            
            default:
                return [
                    "id" => null,
                    "name" => ""
                ];
                break;
        };
    }

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
