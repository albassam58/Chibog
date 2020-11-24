<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BaseController extends Controller
{
	public function applySearch($builder, $search = [ "query" => "", "columns" => [] ], $filters = "", $paginate = true)
	{
		$query = $search['query'];
		$columns = $search['columns'];
		$filters = @json_decode($filters);

		if (!($filters === null && json_last_error() !== JSON_ERROR_NONE)) {
			foreach ($filters as $key => $value) {
				if (is_array($value)) {
					if (!empty($value)) $builder = $builder->whereIn($key, $value);
				} else {
					$builder = $builder->where($key, $value);
				}
			}
		}

		if ($query) {
			$builder = $builder->where(function($q) use($columns, $query) {
				for($i = 0; $i < count($columns); $i++) {
					if (is_array($columns[$i])) {
						if ($columns[$i]['raw']) {
							$q = $q->orWhere(DB::raw($columns[$i]['column']), $columns[$i]['operator'], $columns[$i]['value']);
						} else {
							$q = $q->orWhere($columns[$i], $columns[$i]['operator'], $columns[$i]['value']);
						}
					} else {
						$q = $q->orWhere($columns[$i], 'LIKE', "%$query%");
					}
				}
			});
		}

		if ($paginate) {
			return $builder->paginate(50);
		}

		return $builder->get();
	}

	public function sendResponse($result = [], $message = "Success")
	{
		$response = [
			'success' 	=> true,
			'data' 		=> $result,
			'message' 	=> $message
		];

		return response()->json($response, 200);
	}

	public function sendError($error, $errorMessages = [], $code = 500)
	{
		$response = [
			'success' => false,
			'message' => $error
		];

		if (!empty($errorMessages)) {
			$response['data'] = $errorMessages;
		}

		Log::error(json_encode($response));

		return response()->json($response, $code);
	}
}