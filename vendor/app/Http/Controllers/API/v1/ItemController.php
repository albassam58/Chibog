<?php

namespace App\Http\Controllers\API\v1;

use App\Models\Item;
use Illuminate\Http\Request;
use App\Http\Controllers\API\v1\BaseController;
use Image;
use Log;

class ItemController extends BaseController
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
            
            $query = new Item();
            $query = $query->whereHas('store', function($q) {
                $q->where('vendor_id', auth('sanctum')->user()->id);
            })->with(['store']);
            $items = $this->applySearch($query, $params, $searchColumns);

            return $this->sendResponse($items);
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
            'store_id'      => 'required|integer',
            'name'          => 'required|string',
            'description'   => 'required|string',
            'image'         => 'required|file|mimetypes:image/jpeg,image/png,image/tiff,image/bmp,image/webp|max:1000',
            'dish'          => 'required|integer',
            'amount'        => 'required|numeric',
            'status'        => 'required|integer',
        ]);

        try {
            $limit = 15;

            $countItems = Item::where('store_id', $request->store_id)->where('created_by', auth('sanctum')->user()->id)->count();

            if ($countItems >= $limit) throw new \Exception("Item limit exceeded. You are only allowed to make $limit items per store.");

            $item = Item::create($request->except('image'));

            if ($request->hasFile('image')) {
                $directory = public_path('items/' . $item->id);

                // delete all files
                recursiveDelete($directory);

                $image = $request->file('image');

                // initialize directory again to create folders
                $directory = public_path('items/' . $item->id);
                $fileName = time() . '.' . $request->image->getClientOriginalExtension();

                resizeAndSave($image, $directory, $fileName, 150, 150);

                $filePath = "items/" . $item->id . "/" . $fileName;
                $item->update([
                    'image' => $filePath
                ]);
            }

            return $this->sendResponse($item);
        } catch(\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function show(Item $item)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function edit(Item $item)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'store_id'      => 'sometimes|required|integer',
            'name'          => 'sometimes|required|string',
            'description'   => 'sometimes|required|string',
            'image'         => 'sometimes|required|file|mimetypes:image/jpeg,image/png,image/tiff,image/bmp,image/webp|max:1000',
            'dish'          => 'sometimes|required|integer',
            'amount'        => 'sometimes|required|numeric',
            'status'        => 'sometimes|required|integer',
        ]);

        try {
            $item = Item::whereHas('store', function($query) {
                $query->where('vendor_id', auth('sanctum')->user()->id);
            })->find($id);

            if (!$item) throw new \Exception("No item found.");

            $item->update($request->except(['id', 'image']));

            if ($request->hasFile('image')) {
                $directory = public_path('items/' . $item->id);

                // delete all files
                recursiveDelete($directory);

                $image = $request->file('image');

                // initialize directory again to create folders
                $directory = public_path('items/' . $item->id);

                $fileName = time() . '.' . $request->image->getClientOriginalExtension();

                resizeAndSave($image, $directory, $fileName, 150, 150);

                $filePath = "items/" . $item->id . "/" . $fileName;
                $item->update([
                    'image' => $filePath
                ]);
            }

            return $this->sendResponse($item);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy(Item $item)
    {
        //
    }
}
