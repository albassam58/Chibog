<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\API\v1\BaseController;
use App\Http\Controllers\SendMailController;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends BaseController
{
    protected $mailController;

    public function __construct(SendMailController $mailController)
    {
        $this->mailController = $mailController;
    }

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
        $params = $request->all();
        $searchColumns = ['transaction_id', [
                'raw' => true,
                'column' => 'CONCAT(customer_first_name, " ", customer_last_name)',
                'operator' => 'LIKE',
                'value' => '%' . $request->search . '%'
            ], [
                'raw' => true,
                'column' => 'CONCAT(customer_last_name, ", ", customer_first_name)',
                'operator' => 'LIKE',
                'value' => '%' . $request->search . '%'
            ], 'customer_mobile_number', 'customer_email'
        ];

        $query = new Order();
        $query = $query->whereHas('store', function($query) {
                        $query->where('vendor_id', auth('sanctum')->user()->id);
                    })
                    ->where('orders.status', '>', 1) // not cart status
                    ->selectRaw('GROUP_CONCAT(orders.id) AS order_id, transaction_id, special_instruction, orders.is_paid, orders.store_id AS store_id, GROUP_CONCAT(item_id) AS item_id, GROUP_CONCAT(items.name) AS item_name, GROUP_CONCAT(orders.amount) AS amount, GROUP_CONCAT(quantity) AS quantity, customer_first_name, customer_last_name, customer_region, customer_province, customer_city, customer_barangay, customer_street, customer_mobile_number, customer_email, orders.status AS status')
                    ->leftJoin('items', 'items.id', '=', 'orders.item_id')
                    ->groupBy('transaction_id');


        $orders = $this->applySearch($query, $params, $searchColumns);

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
        $request->validate([
            'transaction_id'            => 'required|string',
            'store_id'                  => 'required|integer',
            'item_id'                   => 'required|integer',
            'amount'                    => 'required|numeric',
            'quantity'                  => 'required|numeric',
            'customer_first_name'       => 'required|string',
            'customer_last_name'        => 'required|string',
            'customer_mobile_number'    => 'required|string',
            'customer_email'            => 'required|email',
            'customer_region'           => 'required|string',
            'customer_province'         => 'required|string',
            'customer_city'             => 'required|string',
            'customer_barangay'         => 'required|string',
            'customer_street'           => 'required|string',
            'status'                    => 'required|integer'
        ]);

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
    public function update(Request $request, $id)
    {
        $request->validate([
            'transaction_id'            => 'sometimes|required|string',
            'store_id'                  => 'sometimes|required|integer',
            'item_id'                   => 'sometimes|required|integer',
            'amount'                    => 'sometimes|required|numeric',
            'quantity'                  => 'sometimes|required|numeric',
            'customer_first_name'       => 'sometimes|required|string',
            'customer_last_name'        => 'sometimes|required|string',
            'customer_mobile_number'    => 'sometimes|required|string',
            'customer_email'            => 'sometimes|required|email',
            'customer_region'           => 'sometimes|required|string',
            'customer_province'         => 'sometimes|required|string',
            'customer_city'             => 'sometimes|required|string',
            'customer_barangay'         => 'sometimes|required|string',
            'customer_street'           => 'sometimes|required|string',
            'status'                    => 'sometimes|required|integer',
            'is_paid'                   => 'sometimes|required|integer'
        ]);

        try {
            $order = Order::whereHas('store', function($query) {
                $query->where('vendor_id', auth('sanctum')->user()->id);
            })->find($id);

            if (!$order) throw new \Exception("No order found.");

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
        $request->validate([
            'transactionId' => 'required|string',
            'status'        => 'required|integer'
        ]);

        try {
            $transactionId = $request->transactionId;
            $status = $request->status;

            $query = new Order();
            $query = $query->whereHas('store', function($q) {
                $q->where('vendor_id', auth('sanctum')->user()->id);
            })->where('transaction_id', $transactionId);
            
            $orders = $query->get();

            if (empty($orders)) throw new \Exception("No orders found.");

            $query->update([
                'status' => $status
            ]);

            // send update order status email
            $details = [
                'subject' => 'Chibog - ' . $orders[0]->status['name'],
                'data' => [
                    'first_name' => $orders[0]->customer_first_name,
                    'transaction_id' => $transactionId,
                    'status' => $orders[0]->status['name']
                ]
            ];

            $data = [
                'job'       => '\App\Jobs\SendUpdateOrderStatusEmail',
                'to'        => $orders[0]->customer_email,
                'cc'        => null,
                'bcc'       => null,
                'details'   => $details,
            ];

            $this->mailController->sendMail($data);

            return $this->sendResponse($orders);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePaid($transactionId)
    {
        try {
            $query = new Order();
            $query = $query->whereHas('store', function($q) {
                $q->where('vendor_id', auth('sanctum')->user()->id);
            })->where('transaction_id', $transactionId);
            
            $orders = $query->get();

            if (empty($orders)) throw new \Exception("No orders found.");

            $query->update([
                'is_paid' => 1
            ]);

            // send update order status email
            $details = [
                'subject' => 'Chibog - Paid',
                'data' => [
                    'first_name' => $orders[0]->customer_first_name,
                    'transaction_id' => $transactionId
                ]
            ];

            $data = [
                'job'       => '\App\Jobs\SendAlreadyPaidEmail',
                'to'        => $orders[0]->customer_email,
                'cc'        => null,
                'bcc'       => null,
                'details'   => $details,
            ];

            $this->mailController->sendMail($data);

            return $this->sendResponse($orders);
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
    public function destroy($id)
    {
        try {
            $order = Order::whereHas('store', function($query) {
                $query->where('vendor_id', auth('sanctum')->user()->id);
            })->find($id);

            if (!$order) throw new \Exception("No order found.");

            $order->delete();

            return $this->sendResponse();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
