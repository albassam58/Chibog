<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\API\v1\BaseController;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends BaseController
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
        $filters = $request->filters;
        $userId = Auth::user()->id;

        $query = new Order();
        $query = $query->whereHas('store', function($query) use($userId) {
                        $query->where('vendor_id', $userId);
                    })
                    ->where('orders.status', '>', 1)
                    ->selectRaw('GROUP_CONCAT(orders.id) AS order_id, transaction_id, special_instruction, orders.store_id AS store_id, GROUP_CONCAT(item_id) AS item_id, GROUP_CONCAT(items.name) AS item_name, GROUP_CONCAT(orders.amount) AS amount, GROUP_CONCAT(quantity) AS quantity, customer_first_name, customer_last_name, customer_region, customer_province, customer_city, customer_barangay, customer_street, customer_mobile_number, customer_email, orders.status AS status')
                    ->leftJoin('items', 'items.id', '=', 'orders.item_id')
                    ->groupBy('transaction_id');

        $search = [
            'query' => $request->search,
            'columns' => ['transaction_id', [
                'raw' => true,
                'column' => 'CONCAT(customer_first_name, " ", customer_last_name)',
                'operator' => 'LIKE',
                'value' => '%' . $request->search . '%'
            ], [
                'raw' => true,
                'column' => 'CONCAT(customer_last_name, ", ", customer_first_name)',
                'operator' => 'LIKE',
                'value' => '%' . $request->search . '%'
            ], 'customer_mobile_number', 'customer_email']
        ];

        $orders = $this->applySearch($query, $search, $filters);

        return $this->sendResponse($orders);
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
            DB::beginTransaction();

            $order = Order::create($request->except('item'));

            DB::commit();

            return $this->sendResponse($order);
        } catch(\Exception $error) {
            DB::rollBack();
            return $this->sendError($error->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        try {
            $order->update($request->all());
            return $this->sendResponse($order);
        } catch (\Exception $e) {
            return $this->sendResponse($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request)
    {
        try {
            $transactionId = $request->transactionId;
            $status = $request->status;

            Order::where('transaction_id', $transactionId)->update([
                'status' => $status
            ]);

            return $this->sendResponse();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        $order->delete();
    }
}
