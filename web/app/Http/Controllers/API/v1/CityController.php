<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\API\v1\BaseController;
use App\Models\City;
use Illuminate\Http\Request;

class CityController extends BaseController
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
     * @return \Illuminate\Http\Response
     */
    public function show($provinceName)
    {
        $jsonString = file_get_contents(base_path('resources/address/philippines.json'));
        $data = json_decode($jsonString, true);

        foreach($data as $regionKey => $regionValue) {
            foreach ($regionValue as $regionChildKey => $regionChildValue) {
                if ($regionChildKey == "province_list") {
                    foreach ($regionChildValue as $provinceKey => $provinceValue) {
                        if ($provinceKey == $provinceName) {
                            return array_keys($provinceValue['municipality_list']);
                        }
                    }
                }
            }
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\City  $city
     * @return \Illuminate\Http\Response
     */
    public function edit(City $city)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\City  $city
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, City $city)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\City  $city
     * @return \Illuminate\Http\Response
     */
    public function destroy(City $city)
    {
        //
    }
}
