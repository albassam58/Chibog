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
            $params = $request->all();
            $searchColumns = ['name'];

            $query = new Store();
            $query = $query->where('vendor_id', auth('sanctum')->user()->id)->withCount('reviews')->with('vendor');

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
        $request->validate([
            'name'              => 'required|string',
            'description'       => 'required|string',
            'logo'              => 'required|file|mimetypes:image/jpeg,image/png,image/tiff,image/bmp,image/webp|max:2000',
            'social_media'      => 'required|url',
            'region'            => 'required|string',
            'province'          => 'required|string',
            'city'              => 'required|string',
            'barangay'          => 'required|string',
            'street'            => 'required|string',
            'type'              => 'required|integer',
            'schedule_day'      => 'required|string',
            'schedule_time_in'  => 'required|date_format:H:i',
            'schedule_time_out' => 'required|date_format:H:i'
        ]);

        try {
            $limit = 1;

            // rejected is not counted
            $countStores = Store::where('status', '<>', 3)->where('vendor_id', auth('sanctum')->user()->id)->count();

            if ($countStores >= $limit) throw new \Exception("Store limit exceeded. You are only allowed to make $limit store per vendor.");

            $request->merge([
                'vendor_id'       => Auth::user()->id,
                'status'        => 1
            ]);

            $store = Store::create($request->except('logo'));

            if ($request->hasFile('logo')) {
                $directory = public_path('stores/' . $store->id);

                // delete all files
                recursiveDelete($directory);

                $image = $request->file('logo');

                // initialize directory again to create folders
                $directory = public_path('stores/' . $store->id);

                $fileName = time() . '.' . $request->logo->getClientOriginalExtension();

                resizeAndSave($image, $directory, $fileName, 500, 500);

                $filePath = "stores/" . $store->id . "/" . $fileName;
                $store->update([
                    'logo' => $filePath
                ]);
            }

            return $this->sendResponse($store);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
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
            $store = Store::where('vendor_id', auth('sanctum')->user()->id)->withCount('reviews')->with('vendor')->find($id);
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
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'              => 'sometimes|required|string',
            'description'       => 'sometimes|required|string',
            'logo'              => 'sometimes|file|mimetypes:image/jpeg,image/png,image/tiff,image/bmp,image/webp|max:2000',
            'social_media'      => 'sometimes|required|url',
            'region'            => 'sometimes|required|string',
            'province'          => 'sometimes|required|string',
            'city'              => 'sometimes|required|string',
            'barangay'          => 'sometimes|required|string',
            'street'            => 'sometimes|required|string',
            'type'              => 'sometimes|required|integer',
            'schedule_day'      => 'sometimes|required|string',
            'schedule_time_in'  => 'sometimes|required|date_format:H:i',
            'schedule_time_out' => 'sometimes|required|date_format:H:i',
            'status'            => 'sometimes|required|integer',
        ]);

        try {
            $store = Store::withCount('reviews')->with('vendor')->where('vendor_id', auth('sanctum')->user()->id)->find($id);

            if (!$store) throw new \Exception("No store found.");

            $store->update($request->except(['id', 'logo']));

            if ($request->hasFile('logo')) {
                $directory = public_path('stores/' . $store->id);

                // delete all files
                recursiveDelete($directory);

                $image = $request->file('logo');

                // initialize directory again to create folders
                $directory = public_path('stores/' . $store->id);

                $fileName = time() . '.' . $request->logo->getClientOriginalExtension();

                resizeAndSave($image, $directory, $fileName, 500, 500);

                $filePath = "stores/" . $store->id . "/" . $fileName;
                $store->update([
                    'logo' => $filePath
                ]);
            }
            
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
}
