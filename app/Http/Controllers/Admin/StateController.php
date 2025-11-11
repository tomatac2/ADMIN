<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStateRequest;
use App\Http\Requests\Admin\UpdateStateRequest;
use App\Models\Country;
use App\Models\State;
use Illuminate\Http\Request;

class StateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $states = State::latest()->paginate(5);
        return view("admin.state.index", [ "zones" => $states]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $countries = Country::all();
        return view("admin.state.create", ["countries"=> $countries]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStateRequest $request)
    {
        $data = $request->validated();
        if(State::create($data)){
            return to_route("admin.states.index")->with("success", "Successfully Create State");
        }

        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(State $state)
    {
        
        return view("admin.state.show", ["state"=> $state]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(State $state)
    {
        $countries = Country::all();
        return view("admin.state.edit", ["state"=>$state,"countries"=> $countries]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStateRequest $request, State $state)
    {
        

        $data = $request->validated();
        $state->fill($data);
        if($state->isDirty()){
            $state->save();
            return back()->with("success", "Successfully Update State");
        }
            return back()->with("warning", "No Data Change State");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(State $state)
    {
        $state->delete();
         return to_route("admin.states.index")->with("success", "Successfully Delete State");
    }
}
