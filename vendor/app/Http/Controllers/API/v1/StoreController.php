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
        $stores = new Store();

        if ($request->exclude) {
            $stores = $stores->withCount('reviews')->where('city', '<>', $request->exclude);
        }

        return $stores->paginate(50);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  string  $query
     * @return \Illuminate\Http\Response
     */
    public function search($query)
    {
        $stores = new Store();

        if ($query) {
            $stores = $stores->where(function($q) use($query) {
                $q->where('name', 'LIKE', "%$query%")
                ->orWhere('region', 'LIKE', "%$query%")
                ->orWhere('province', 'LIKE', "%$query%")
                ->orWhere('city', 'LIKE', "%$query%")
                ->orWhere('barangay', 'LIKE', "%$query%")
                ->orWhere('street', 'LIKE', "%$query%");
            });
        }

        return $stores->paginate(50);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function vendor()
    {
        return Store::where('vendor_id', Auth::user()->id)->withCount('reviews')->with('vendor')->paginate(50);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  string  $city
     * @return \Illuminate\Http\Response
     */
    public function city($city)
    {
        return Store::where('city', $city)->paginate(50);
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
        $request->merge([
            'vendor_id'       => Auth::user()->id,
            'schedule_day'  => implode(',', $request->schedule_day),
            'status'        => 1
        ]);

        $store = Store::create($request->all());

        return $this->sendResponse($store);
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
        try {
            $request->merge([
                'schedule_day'  => implode(',', $request->schedule_day)
            ]);
            $store->update($request->except(['id', 'picture', 'vendor', 'reviews_count', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at']));
            return $this->sendResponse($store);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
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

    /**
     * Upload a file in the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request)
    {
        try {
            $storeId = $request->store_id;
            $directory = public_path('stores/' . $storeId);

            // delete all files
            $this->recursiveDelete($directory);

            // initialize directory again to create folders
            $directory = public_path('stores/' . $storeId);
            $fileName = time() . '.' . $request->file->getClientOriginalExtension();
            $filePath = "stores/" . $storeId . "/" . $fileName;
            $request->file->move($directory, $fileName);

            $store = Store::find($storeId);
            $store->update([
                'logo' => $filePath
            ]);

            return $this->sendResponse($store);
        } catch (\Error $e) {
            return $this->sendError($e->getMessage());
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Delete a file or recursively delete a directory
     *
     * @param string $str Path to file or directory
     */
    public function recursiveDelete($str)
    {
        if (is_file($str)) {
            return @unlink($str);
        } else if (is_dir($str)) {
            $scan = glob(rtrim($str,'/') . '/*');
            foreach($scan as $index=>$path) {
                $this->recursiveDelete($path);
            }
            return @rmdir($str);
        }
    }
}
