<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\API\v1\BaseController;
use App\Models\Order;
use App\Models\Store;
use App\Models\StoreReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StoreReviewController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $params = $request->all();
            $searchColumns = ['first_name', 'last_name'];

            $query = new StoreReview();
            $query = $query->whereHas('store', function($q) {
                $q->where('vendor_id', auth('sanctum')->user()->id);
            });

            $store = $this->applySearch($query, $params, $searchColumns);

            return $this->sendResponse($store);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            if (Auth::user()) {
                $name = [
                    'first_name'    => Auth::user()->first_name,
                    'last_name'     => Auth::user()->last_name,
                ];
            } else {
                $name = [
                    'first_name'    => "Anonymous",
                    'last_name'     => "",
                ];
            }

            $request->merge($name);

            DB::beginTransaction();
            
            // get the store reviews count and ratings before saving the new one
            $store = Store::where('id', $request->store_id)->withCount('reviews')->first();

            $review = StoreReview::create($request->except('orders'));

            // compute avg ratings to update the store ratings
            if ($store) {
                // ((baseAverage * count) + newNumber) / (count + 1))
                $movingAverage = (($store->rate * $store->reviews_count) + $request->rate) / ($store->reviews_count + 1);
                $store->rate = $movingAverage;
                $store->save();
            }

            // update orders rated to prevent double rating on order
            Order::whereIn('id', $request->orders)->update([
                'rated' => 1
            ]);

            DB::commit();

            return $this->sendResponse($review);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\StoreReview  $storeReview
     * @return \Illuminate\Http\Response
     */
    public function show(StoreReview $storeReview)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\StoreReview  $storeReview
     * @return \Illuminate\Http\Response
     */
    public function edit(StoreReview $storeReview)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\StoreReview  $storeReview
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, StoreReview $storeReview)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\StoreReview  $storeReview
     * @return \Illuminate\Http\Response
     */
    public function destroy(StoreReview $storeReview)
    {
        //
    }
}
