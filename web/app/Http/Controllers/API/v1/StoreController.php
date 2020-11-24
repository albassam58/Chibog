<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\API\v1\BaseController;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoreController extends BaseController
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
            $query = new Store();

            if ($request->exclude) {
                $query = $query->where('city', '<>', $request->exclude);
            }
            $query = $query->withCount('reviews')->with('vendor')->where('status', 2);
            $stores = $this->applySearch($query);

            return $this->sendResponse($stores);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param  string  $query
     * @return \Illuminate\Http\Response
     */
    public function search($query)
    {
        try {
            $stores = new Store();

            if ($query) {
                $stores = $stores->where(function($q) use($query) {
                    $q->where('name', 'LIKE', "%$query%")
                    ->orWhere('region', 'LIKE', "%$query%")
                    ->orWhere('province', 'LIKE', "%$query%")
                    ->orWhere('city', 'LIKE', "%$query%")
                    ->orWhere('barangay', 'LIKE', "%$query%")
                    ->orWhere('street', 'LIKE', "%$query%");
                })->where('status', 2);
            }

            $stores = $stores->with('vendor');
            $stores = $this->applySearch($stores);
            return $this->sendResponse($stores);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param  string  $city
     * @return \Illuminate\Http\Response
     */
    public function city($city)
    {
        try {
            $query = new Store();
            $query = $query->where('city', $city)->where('status', 2)->withCount('reviews')->with('vendor');
            $stores = $this->applySearch($query);
            return $this->sendResponse($stores);
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
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $store = Store::withCount('reviews')->with('vendor')->find($id);//->->first();
            return $this->sendResponse($store);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function edit(Store $store)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Store $store)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function destroy(Store $store)
    {
        //
    }
}
