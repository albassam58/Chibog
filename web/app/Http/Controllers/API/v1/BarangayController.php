<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\API\v1\BaseController;
use App\Models\Barangay;
use Illuminate\Http\Request;

class BarangayController extends BaseController
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $provinceName
     * @param  string  $cityName
     * @return \Illuminate\Http\Response
     */
    public function show($provinceName, $cityName)
    {
        try {
            $jsonString = file_get_contents(base_path('resources/address/philippines.json'));
            $data = json_decode($jsonString, true);

            foreach($data as $regionKey => $regionValue) {
                foreach ($regionValue as $regionChildKey => $regionChildValue) {
                    if ($regionChildKey == "province_list") {
                        foreach ($regionChildValue as $provinceKey => $provinceValue) {
                            if ($provinceKey == $provinceName) {
                                foreach ($provinceValue['municipality_list'] as $cityKey => $cityValue) {
                                    if ($cityKey == $cityName) {
                                        return $this->sendResponse($cityValue['barangay_list']);
                                    }
                                }
                            }
                        }
                    }
                }
            }

            return $this->sendResponse();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Barangay  $barangay
     * @return \Illuminate\Http\Response
     */
    public function edit(Barangay $barangay)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Barangay  $barangay
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Barangay $barangay)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Barangay  $barangay
     * @return \Illuminate\Http\Response
     */
    public function destroy(Barangay $barangay)
    {
        //
    }
}
