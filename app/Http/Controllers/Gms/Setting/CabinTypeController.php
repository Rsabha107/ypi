<?php

namespace App\Http\Controllers\Gms\Setting;

use App\Http\Controllers\Controller;
use App\Models\Gms\FlightCabin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CabinTypeController extends Controller
{
    //
    public function index()
    {
        $cabin_types = FlightCabin::all();
        return view('gms.setting.cabin_type.list', compact('cabin_types'));
    }

    public function get($id)
    {
        $op = FlightCabin::findOrFail($id);
        return response()->json(['op' => $op]);
    }

    public function update(Request $request)
    {
        // Validation rules
        $rules = [
            'id' => 'required',
            'name' => 'required',
            'carrier_code' => 'required|string|max:10',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $error = true;
            $message = implode($validator->errors()->all('<div>:message</div>'));
        } else {
            // Fetch the record
            $cabin_type = FlightCabin::findOrFail($request->id);

            // Update fields
            $cabin_type->name         = $request->name;
            $cabin_type->carrier_code = $request->carrier_code;

            // Save the updated record
            $cabin_type->save();

            $error = false;
            $message = 'cabin_type updated successfully.';
        }

        return response()->json([
            'error' => $error,
            'message' => $message,
        ]);
    }


    public function list()
    {
        $search = request('search');
        $sort = (request('sort')) ? request('sort') : "id";
        $order = (request('order')) ? request('order') : "DESC";
        $cabin_types = FlightCabin::orderBy($sort, $order);

        if ($search) {
            $cabin_types = $cabin_types->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('id', 'like', '%' . $search . '%');
            });
        }
        $total = $cabin_types->count();
        $cabin_types = $cabin_types->paginate(request("limit"))->through(function ($cabin_types) {

            // $location = Location::find($cabin_types->location_id);

            return  [
                'id' => $cabin_types->id,
                // 'id' => '<div class="align-middle white-space-wrap fw-bold fs-8 ps-2">' .$cabin_types->id. '</div>',
                'cabin_name' => '<div class="align-middle white-space-wrap fs-9 ps-3">' . $cabin_types->cabin_name . '</div>',
                'description' => '<div class="align-middle white-space-wrap fs-9 ps-3">' . $cabin_types->description . '</div>',  
            ];
        });

        return response()->json([
            "rows" => $cabin_types->items(),
            "total" => $total,
        ]);
    }

     public function store(Request $request)
    {
        $user_id = Auth::user()->id;
        $cabin_types = new FlightCabin();

        $rules = [
            'cabin_name' => 'required',
            'description' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            Log::info($validator->errors());
            $error = true;
            $message = implode($validator->errors()->all('<div>:message</div>'));  // use this for json/jquery
        } else {

            $error = false;
            $message = 'Cabin Type created succesfully.' . $cabin_types->id;

            $cabin_types->cabin_name = $request->cabin_name;
            $cabin_types->description = $request->description;
          

            $cabin_types->save();


        }

        $notification = array(
            'message'       => 'Vehicle Type created successfully',
            'alert-type'    => 'success'
        );

        return response()->json(['error' => $error, 'message' => $message]);
    }

    public function delete($id)
    {
        $op = FlightCabin::findOrFail($id);
        $op->delete();

        $error = false;
        $message = 'Flight Stataus deleted succesfully.';

        $notification = array(
            'message'       => 'Flight Stataus deleted successfully',
            'alert-type'    => 'success'
        );

        return response()->json(['error' => $error, 'message' => $message]);
        // return redirect()->route('tracki.setup.workspace')->with($notification);
    } // delete


}
