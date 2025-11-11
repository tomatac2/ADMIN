<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCityRequest;
use App\Http\Requests\Admin\UpdateCityRequest;
use App\Models\City;
use App\Models\State;
use Illuminate\Http\Request;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cities = City::latest()->paginate(5);
        return view("admin.city.index", ["cities"=>$cities]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $states = State::all();
        return view("admin.city.create", ["states"=> $states]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCityRequest $request)
    {
        if(City::create($request->all())){
            return back()->with("success" ,"Successfully Create City");
        }
            return back()->with("warning" ,"UnSuccessfully Create City");

    }

    /**
     * Display the specified resource.
     */
    public function show(city $city)
    {
        return view("admin.city.show", ["city"=>$city]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(city $city)
    {
        $states = State::all();
        return view("admin.city.edit", ["city"=>$city, "states"=> $states]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCityRequest $request, City $city)
    {
        $data = $request->validated();
        $city->fill($data);
        if($city->isDirty()){
            $city->save();
            return back()->with("success", "Successfully Update City");
        }
            return back()->with("warning", "No Data Change City");

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(City $city)
    {
        $city->delete();
        return to_route("admin.cities.index")->with("success", "Successfully Delete City");

    }
}
