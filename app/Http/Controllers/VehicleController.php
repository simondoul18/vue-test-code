<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

use Inertia\Inertia;
use App\Models\Vehicle;
use App\Models\Category;

class VehicleController extends Controller
{
    public function index(){
        $vehicles_count = Vehicle::where('status',1)->count();
        return Inertia::render('Dashboard', [
            'total_vehicles' => $vehicles_count
        ]);
    }
    public function dt_vehicles(){
        $data=[];
        $data['vehicles'] = Vehicle::with('category:id,title')->orderBy('id','DESC')->get();
        $data['years']= Vehicle::distinct()->pluck('year')->toArray();
        $data['models'] = Vehicle::distinct()->pluck('model')->toArray();
        $data['comapnies']= Vehicle::distinct()->pluck('make')->toArray();
        $data['colors'] = Vehicle::distinct()->pluck('color')->toArray();

        $data['categories'] = Category::where('status',1)->orderBy('id',"DESC")->get();
        return Inertia::render('Vehicles', $data);
    }
    public function destroy(Request $request): RedirectResponse {
        $request->validate([
            'vehicle_id' => 'required'
        ]);
        $resp = Vehicle::where('id',$request->vehicle_id)->delete();
        
        if($resp){
            return Redirect::route('vehicles');
        }else{
            return Redirect::route('vehicles');
        }
    }
    public function store(Request $request) : RedirectResponse {
        $request->validate([
            'category_id' => 'required',
            'make' => 'required',
            'status' => 'required',
        ]);
        if(!empty($request->vehicle_id)){
            $vehicle = Vehicle::where('id',$request->vehicle_id)->first();
        }else{
            $vehicle = new Vehicle;
        }
        $vehicle->category_id = $request->category_id;
        $vehicle->make = $request->make;
        $vehicle->model = !empty($request->model) ? $request->model:null;
        $vehicle->year = !empty($request->year) ? $request->year:null;
        $vehicle->color = !empty($request->color) ? $request->color:null;
        $vehicle->registration_no = !empty($request->registration_no) ? $request->registration_no:null;
        $vehicle->price = !empty($request->price) ? $request->price:null;
        $vehicle->status = $request->status;
        $vehicle->save();
        return Redirect::route('vehicles');
    }

    public function getVehicles(Request $request){
        $query = Vehicle::with('category:id,title');
        if (!empty($request->category_id)) {
            $query->where('category_id',$request->category_id);
        }
        if (!empty($request->category_id)) {
            $query->where('category_id',$request->category_id);
        }
        if (!empty($request->make)) {
            $query->where('make',$request->make);
        }
        if (!empty($request->model)) {
            $query->where('model',$request->model);
        }
        if (!empty($request->year)) {
            $query->where('year',$request->year);
        }
        if (!empty($request->color)) {
            $query->where('color',$request->color);
        }
        return $query->orderBy('id','DESC')->get();
    }
}
