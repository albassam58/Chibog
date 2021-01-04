<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BaseController extends Controller
{
	public function applySearch($builder, $options = null, $searchColumns = [])
	{
		// search
		if (!empty($searchColumns) && isset($options['search']))
		{
			$searchQuery = $options['search'];
			$builder = $this->search($builder, $searchQuery, $searchColumns);
		}

		// filter
		if (isset($options['filters']))
		{
			$filters = $options['filters'];
			$builder = $this->filter($builder, $filters);
		}

		// check if needed to be sorted
		if (isset($options['sortBy']))
		{
			$sortBy = json_decode($options['sortBy']);
			$sortDesc = json_decode($options['sortDesc']);

			for ($i = 0; $i < count($sortBy); $i++)
			{
				$order = $sortBy[$i];
				$direction = $sortDesc[$i] ? "DESC" : "ASC";
				$builder = $builder->orderBy($order, $direction);
			}
		}

		// paginate if there's items per page
		if (isset($options['itemsPerPage']) && $options['itemsPerPage'] > 0)
		{
			return $builder->paginate($options['itemsPerPage']);
		}

		return $builder->get();
	}

	public function search($builder, $search = null, $columns = [])
	{
		// nothing to search
		if ($search === null) return $builder;

		// replace double quote " to blank
		$query = preg_replace('~[\\"]~', '', urldecode($search));

		if ($query && $query != "")
		{
			$builder = $builder->where(function($q) use($columns, $query) {
				for($i = 0; $i < count($columns); $i++)
				{
					if (is_array($columns[$i]))
					{
						// search with custom operator aside from like
						if ($columns[$i]['raw'])
						{
							// search with raw column
							// e.g. CONCAT(firstname, ' ', lastname)
							$q = $q->orWhere(DB::raw($columns[$i]['column']), $columns[$i]['operator'], $columns[$i]['value']);
						}
						else
						{
							$q = $q->orWhere($columns[$i], $columns[$i]['operator'], $columns[$i]['value']);
						}
					}
					else
					{
						// normal search with like operator
						$q = $q->orWhere($columns[$i], 'LIKE', "%$query%");
					}
				}
			});
		}

		return $builder;
	}

	public function filter($builder, $filters = null)
	{
		if ($filters === null) return $builder;

		$decodedFilters = @json_decode($filters);

		// json decode successful
		if (!($decodedFilters === null && json_last_error() !== JSON_ERROR_NONE))
		{
			foreach ($decodedFilters as $key => $value)
			{
				if (is_array($value))
				{
					// filter with array
					// e.g. where status = pending and approved [1,2]
					if (!empty($value))
					{
						$builder = $builder->whereIn($key, $value);
					}
				}
				else
				{
					$builder = $builder->where($key, $value);
				}
			}
		}

		return $builder;
	}

	public function sendResponse($result = null, $message = "Success")
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

		Log::error("Manual Error Logging: " . json_encode($response));

		return response()->json($response, $code);
	}
}