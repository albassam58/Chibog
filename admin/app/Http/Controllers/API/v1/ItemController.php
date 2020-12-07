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
        $filters = $request->filters;
        $search = [
            'query' => null,
            'columns' => []
        ];

        $query = new Item();
        $query = $query->with(['store', 'foodType', 'cuisine'])->orderBy('meal', 'ASC');
        $items = $this->applySearch($query, $search, $filters);

        return $this->sendResponse($items);
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
            $limit = 15;

            $countItems = Item::where('store_id', $request->store_id)->where('created_by', auth('sanctum')->user()->id)->count();

            if ($countItems >= $limit) throw new \Exception("Items limit exceeded. You are only allowed to make $limit items per store.");

            $item = Item::create($request->except('image'));

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
    public function update(Request $request, Item $item)
    {
        try {
            $item->update($request->except('image'));

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
