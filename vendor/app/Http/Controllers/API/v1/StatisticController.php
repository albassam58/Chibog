<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\API\v1\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StatisticController extends BaseController
{
	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function sales(Request $request)
    {
        $user = auth('sanctum')->user()->id;
        $stores = DB::select("
            SELECT id FROM stores WHERE vendor_id = $user
            AND deleted_at IS NULL
        ");

        $storeQuery = null;
        if (!empty($stores)) {
            $storeIds = array_column($stores, "id");
            $storeIds = implode(",", $storeIds);
            $storeQuery = "AND orders.store_id IN ($storeIds)";
        }

        $sales = [];
        if ($storeQuery) {
        	$sales = DB::select("
        		SELECT
        		items.name AS name,
        		SUM(orders.amount * orders.quantity) AS amount
    			FROM orders
    			INNER JOIN items ON items.id = orders.item_id
    			WHERE orders.status = 5 -- delivered
                $storeQuery
    			AND orders.deleted_at IS NULL
    			GROUP BY orders.item_id;
        	");
        }

    	return $this->sendResponse($sales);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function orders(Request $request)
    {
        $user = auth('sanctum')->user()->id;
        $stores = DB::select("
            SELECT id FROM stores WHERE vendor_id = $user
            AND deleted_at IS NULL
        ");

        $storeQuery = null;
        if (!empty($stores)) {
            $storeIds = array_column($stores, "id");
            $storeIds = implode(",", $storeIds);
            $storeQuery = "AND orders.store_id IN ($storeIds)";
        }

        $orders = [];
        if ($storeQuery) {
        	$orders = DB::select("
        		SELECT
        		items.name AS name,
        		SUM(orders.quantity) AS volume
    			FROM orders
    			INNER JOIN items ON items.id = orders.item_id
    			WHERE orders.status = 5 -- delivered
                $storeQuery
                AND orders.deleted_at IS NULL
    			GROUP BY orders.item_id;
        	");
        }

    	return $this->sendResponse($orders);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function totalSales(Request $request)
    {
        $user = auth('sanctum')->user()->id;
        $stores = DB::select("
            SELECT id FROM stores WHERE vendor_id = $user
            AND deleted_at IS NULL
        ");

        $storeQuery = null;
        if (!empty($stores)) {
            $storeIds = array_column($stores, "id");
            $storeIds = implode(",", $storeIds);
            $storeQuery = "AND orders.store_id IN ($storeIds)";
        }

        $totalSales = 0;
        if ($storeQuery) {
        	$totalSales = DB::select("
        		SELECT
        		IFNULL(SUM(orders.amount * orders.quantity), 0) AS total
    			FROM orders
    			WHERE orders.status = 5 -- delivered
                $storeQuery
    			AND orders.deleted_at IS NULL;
        	")[0]->total;
        }

    	return $this->sendResponse($totalSales);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function totalOrders(Request $request)
    {
        $user = auth('sanctum')->user()->id;
        $stores = DB::select("
            SELECT id FROM stores WHERE vendor_id = $user
            AND deleted_at IS NULL
        ");

        $storeQuery = null;
        if (!empty($stores)) {
            $storeIds = array_column($stores, "id");
            $storeIds = implode(",", $storeIds);
            $storeQuery = "AND orders.store_id IN ($storeIds)";
        }

        $totalOrders = 0;
        if ($storeQuery) {
        	$totalOrders = DB::select("
        		SELECT
        		IFNULL(COUNT(orders.quantity), 0) AS total
    			FROM orders
    			WHERE orders.status <> 1 -- not cart
                $storeQuery
    			AND orders.deleted_at IS NULL;
        	")[0]->total;
        }

    	return $this->sendResponse($totalOrders);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function ordersPerStatus(Request $request)
    {
        $user = auth('sanctum')->user()->id;
        $stores = DB::select("
            SELECT id FROM stores WHERE vendor_id = $user
            AND deleted_at IS NULL
        ");

        $storeQuery = null;
        if (!empty($stores)) {
            $storeIds = array_column($stores, "id");
            $storeIds = implode(",", $storeIds);
            $storeQuery = "AND orders.store_id IN ($storeIds)";
        }

        $ordersPerStatus = 0;
        if ($storeQuery) {
        	$ordersPerStatus = DB::select("
                SELECT
                status,
                IFNULL(COUNT(a.id), 0) AS total
                FROM (
                    SELECT
                    status,
                    id
                    FROM orders
                    WHERE orders.status > 1 -- cart
                    $storeQuery
                    AND orders.deleted_at IS NULL
                    GROUP BY transaction_id
                ) a
    			GROUP BY a.status;
        	");
        }

    	return $this->sendResponse($ordersPerStatus);
    }
}
