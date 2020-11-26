<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\API\v1\BaseController;
use App\Models\OrderNotification;
use Illuminate\Http\Request;

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
        try {
            $orderNotification = OrderNotification::create($data);

            if (request()->ajax()) {
                return $this->sendResponse($orderNotification);
            }

            return $orderNotification;
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\OrderNotification  $orderNotification
     * @return \Illuminate\Http\Response
     */
    public function show(OrderNotification $orderNotification)
    {
        //
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
}
