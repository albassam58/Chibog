<?php

namespace App\Http\Controllers\API\v1;

use App\Events\Order as OrderNotification;
use App\Http\Controllers\API\v1\BaseController;
use App\Http\Controllers\SendMailController;
use App\Models\Order;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $search = [ "query" => "", "columns" => [] ];
            $filters = $request->filters;

            $query = new Order();
            $query = $query->with(['item', 'store']);

            // if filtered by status = 1 (Cart), don't paginate
            $paginate = true;
            $filter = json_decode($filters);
            if (isset($filter->status) && $filter->status == 1) $paginate = false;

            $orders = $this->applySearch($query, $search, $filters, $paginate);

            return $this->sendResponse($orders);
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
            $orders = Order::where('transaction_id', $query)->with(['item', 'store', 'store.vendor'])->get();
            return $this->sendResponse($orders);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Check if user has order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function check(Request $request)
    {
        try {
            $search = [ "query" => "", "columns" => [] ];
            $filters = $request->filters;

            $query = new Order();
            $orders = $this->applySearch($query, $search, $filters, false);
            
            return $this->sendResponse(count($orders) > 0 ? true : false);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Display a listing of the current user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function user(Request $request)
    {
        try {
            $search = [ "query" => "", "columns" => [] ];
            $filters = $request->filters;
            $query = new Order();
            $query = $query->where('created_by', Auth::user()->id)->where('status', '>', 1)->with(['item', 'store', 'store.vendor']);
            $orders = $this->applySearch($query, $search, $filters);
            return $this->sendResponse($orders);
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
            DB::beginTransaction();

            $form = $request->form;
            $item = $request->item;

            $order = Order::create([
                'transaction_id'            => "",
                'store_id'                  => $item->store_id,
                'item_id'                   => $item->id,
                'amount'                    => $item->amount,
                'quantity'                  => $item->quantity,
                'special_instruction'       => $item->special_instruction,
                'customer_first_name'       => $form['customer_first_name'],
                'customer_last_name'        => $form['customer_last_name'],
                'customer_region'           => $form['customer_region'],
                'customer_province'         => $form['customer_province'],
                'customer_city'             => $form['customer_city'],
                'customer_barangay'         => $form['customer_barangay'],
                'customer_street'           => $form['customer_street'],
                'customer_mobile_number'    => $form['customer_mobile_number'],
                'customer_email'            => $form['customer_email'],
                'status'                    => $form['status']
            ]);

            // make the transaction_id
            $transactionId = "CH" . $order->id . $order->store_id . time();
            $order->transaction_id = $transactionId;
            $order->save();

            // add store relationship
            $order->store = $item->store;

            DB::commit();

            return $this->sendResponse($order);
        } catch(\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Store a newly created resource by vendor's store in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeByStore(Request $request)
    {
        try {
            DB::beginTransaction();

            // remove all orders with cart status
            if (auth('api')->user()) {
                Order::where('status', 1)->where('created_by', auth('api')->user()->id)->delete();
            }

            $form = $request->form;
            $items = $request->items;

            $response = [];

            $transactionId = null;
            $vendor = null;
            foreach ($items as $item) {
                if (!($form['status'] == 2 && $item['quantity'] <= 0)) {
                    $order = Order::create([
                        'transaction_id'            => "",
                        'store_id'                  => $item['store_id'],
                        'item_id'                   => $item['id'],
                        'amount'                    => $item['amount'],
                        'quantity'                  => $item['quantity'],
                        'special_instruction'       => $item['special_instruction'],
                        'customer_first_name'       => $form['customer_first_name'],
                        'customer_last_name'        => $form['customer_last_name'],
                        'customer_region'           => $form['customer_region'],
                        'customer_province'         => $form['customer_province'],
                        'customer_city'             => $form['customer_city'],
                        'customer_barangay'         => $form['customer_barangay'],
                        'customer_street'           => $form['customer_street'],
                        'customer_mobile_number'    => $form['customer_mobile_number'],
                        'customer_email'            => $form['customer_email'],
                        'status'                    => $form['status']
                    ]);

                    if (!$transactionId) {
                        // make the transaction_id if no transaction id yet
                        $transactionId = "CH" . $order->id . $order->store_id . time();
                    }

                    $order->transaction_id = $transactionId;
                    $order->save();

                    // add store relationship
                    $order->store = $item['store'];

                    if (!$vendor) $vendor = Vendor::find($item['store']['vendor_id']);

                    array_push($response, $order);
                }
            }

            if ($form['status'] == 2) {
                $details = [
                    'subject'   => 'Chibog Order',
                    'data'      => $response
                ];

                $data = [
                    'job'       => '\App\Jobs\SendOrderEmail',
                    'to'        => $form['customer_email'],
                    'cc'        => $vendor->email,
                    'bcc'       => null,
                    'details'   => $details
                ];

                $sendMail = $this->mailController->sendMail($data);
            }

            DB::commit();

            // broadcast an event to notify the vendor
            // this will go to socket.js
            $notification = [
                'message' => 'An order has been made.',
                'data' => $response
            ];
            event(new OrderNotification("order-" . $vendor->id, $notification));

            return $this->sendResponse($response);
        } catch(\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
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
            DB::beginTransaction();

            $form = $request->form;
            $item = $request->item;

            $order->update([
                'store_id'                  => $item['store_id'],
                'item_id'                   => $item['item_id'],
                'amount'                    => $item['amount'],
                'quantity'                  => $item['quantity'],
                'special_instruction'       => $item['special_instruction'],
                'customer_first_name'       => $form['customer_first_name'],
                'customer_last_name'        => $form['customer_last_name'],
                'customer_region'           => $form['customer_region'],
                'customer_province'         => $form['customer_province'],
                'customer_city'             => $form['customer_city'],
                'customer_barangay'         => $form['customer_barangay'],
                'customer_street'           => $form['customer_street'],
                'customer_mobile_number'    => $form['customer_mobile_number'],
                'customer_email'            => $form['customer_email'],
                'status'                    => $form['status']
            ]);

            DB::commit();

            return $this->sendResponse($order);
        } catch(\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }
    }

    public function updateByStore(Request $request)
    {
        try {
            DB::beginTransaction();

            $form = $request->form;
            $items = $request->items;

            $response = [];

            foreach ($items as $item) {
                $order = Order::find($item['id']);
                
                if ($form['status'] == 2 && $item['quantity'] <= 0) {
                    // delete if status is pending (2) and quantity = 0
                    $order->delete();
                } else {
                    // update
                    $order->update([
                        'store_id'                  => $item['store_id'],
                        'item_id'                   => $item['item_id'],
                        'amount'                    => $item['amount'],
                        'quantity'                  => $item['quantity'],
                        'special_instruction'       => $item['special_instruction'],
                        'customer_first_name'       => $form['customer_first_name'],
                        'customer_last_name'        => $form['customer_last_name'],
                        'customer_region'           => $form['customer_region'],
                        'customer_province'         => $form['customer_province'],
                        'customer_city'             => $form['customer_city'],
                        'customer_barangay'         => $form['customer_barangay'],
                        'customer_street'           => $form['customer_street'],
                        'customer_mobile_number'    => $form['customer_mobile_number'],
                        'customer_email'            => $form['customer_email'],
                        'status'                    => $form['status']
                    ]);

                    array_push($response, $order);
                }
            }

            DB::commit();

            return $this->sendResponse($response);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
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
        return $this->sendResponse([]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  integer  $store_id
     * @return \Illuminate\Http\Response
     */
    public function destroyByStore(Request $request)
    {
        try {
            Order::whereIn('id', $request->id)->delete();
            return $this->sendResponse();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
