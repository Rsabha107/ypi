<?php

namespace App\Http\Controllers\Gms\Setting;

use App\Http\Controllers\Controller;
use App\Models\Gms\FlightType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FlightTypeController extends Controller
{
    //
    public function index()
    {
        $flight_types = FlightType::all();
        return view('gms.setting.flight_type.list', compact('flight_types'));
    }

    public function get($id)
    {
        $op = FlightType::findOrFail($id);
        return response()->json(['op' => $op]);
    }

    public function update(Request $request)
    {
        // Validation rules
        $rules = [
            'id' => 'required',
            'title' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $error = true;
            $message = implode($validator->errors()->all('<div>:message</div>'));
        } else {
            // Fetch the record
            $flight_type = FlightType::findOrFail($request->id);

            // Update fields
            $flight_type->title = $request->title;

            // Save the updated record
            $flight_type->save();

            $error = false;
            $message = 'flight_type updated successfully.';
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
        $flight_types = FlightType::orderBy($sort, $order);

        if ($search) {
            $flight_types = $flight_types->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('id', 'like', '%' . $search . '%');
            });
        }
        $total = $flight_types->count();
        $flight_types = $flight_types->paginate(request("limit"))->through(function ($flight_types) {

            // $location = Location::find($flight_types->location_id);

            return  [
                'id' => $flight_types->id,
                // 'id' => '<div class="align-middle white-space-wrap fw-bold fs-8 ps-2">' .$flight_types->id. '</div>',
                'title' => '<div class="align-middle white-space-wrap fs-9 ps-3">' . $flight_types->title . '</div>',
            ];
        });

        return response()->json([
            "rows" => $flight_types->items(),
            "total" => $total,
        ]);
    }

     public function store(Request $request)
    {
        $user_id = Auth::user()->id;
        $flight_types = new FlightType();

        $rules = [
            'title' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            Log::info($validator->errors());
            $error = true;
            $message = implode($validator->errors()->all('<div>:message</div>'));  // use this for json/jquery
        } else {

            $error = false;
            $message = 'Flight Type created succesfully.' . $flight_types->id;

            $flight_types->title = $request->title;
          

            $flight_types->save();


        }

        $notification = array(
            'message'       => 'Flight Type created successfully',
            'alert-type'    => 'success'
        );

        return response()->json(['error' => $error, 'message' => $message]);
    }

    public function delete($id)
    {
        $op = FlightType::findOrFail($id);
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
