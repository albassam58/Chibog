<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\API\v1\BaseController;
use App\Models\OrderNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderNotificationController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function vendor(Request $request)
    {
        try {
            $params = $request->all();

            $query = new OrderNotification();
            $query = $query->where('channel', 'order-' . auth('sanctum')->user()->id);

            $orderNotifications = $this->applySearch($query, $params);

            return $this->sendResponse($orderNotifications);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Display a listing of the resource limited to 5 only.
     *
     * @return \Illuminate\Http\Response
     */
    public function popup(Request $request)
    {
        try {
            $vendorId = auth('sanctum')->user()->id;
            $orderNotifications = OrderNotification::where('channel', 'order-' . $vendorId)->limit(5)->get();

            return $this->sendResponse($orderNotifications);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function countUnread()
    {
        try {
            $vendorId = auth('sanctum')->user()->id;
            $count = OrderNotification::where('channel', 'order-' . $vendorId)->where('status', 1)->count();

            return $this->sendResponse($count);
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
    public function store($data)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\OrderNotification  $orderNotification
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $vendorId = auth('sanctum')->user()->id;
            $orderNotification = OrderNotification::where('channel', 'order-' . $vendorId)->where('id', $id)->first();

            if ($orderNotification) {
                $orderNotification->update([
                    'status' => 2 // read
                ]);
            }

            return $this->sendResponse($orderNotification);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        } catch (\Error $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\OrderNotification  $orderNotification
     * @return \Illuminate\Http\Response
     */
    public function edit(OrderNotification $orderNotification)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\OrderNotification  $orderNotification
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, OrderNotification $orderNotification)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\OrderNotification  $orderNotification
     * @return \Illuminate\Http\Response
     */
    public function destroy(OrderNotification $orderNotification)
    {
        //
    }

    /**
     * Update multiple from the specified resource from storage.
     *
     * @param  string  $ids
     * @return \Illuminate\Http\Response
     */
    public function readChecked($ids)
    {
        try {
            $ids = explode(",", $ids);

            $vendorId = auth('sanctum')->user()->id;
            OrderNotification::whereIn('id', $ids)->where('channel', 'order-' . $vendorId)->update([
                'status' => 2, // read
            ]);

            $this->sendResponse();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Remove multiple from the specified resource from storage.
     *
     * @param  string  $ids
     * @return \Illuminate\Http\Response
     */
    public function destroyChecked($ids)
    {
        try {
            $ids = explode(",", $ids);

            $vendorId = auth('sanctum')->user()->id;
            OrderNotification::whereIn('id', $ids)->where('channel', 'order-' . $vendorId)->delete();

            $this->sendResponse();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
