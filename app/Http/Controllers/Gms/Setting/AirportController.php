<?php

namespace App\Http\Controllers\Gms\Setting;

use App\Http\Controllers\Controller;
use App\Models\Gms\Airport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AirportController extends Controller
{
    //
    public function index()
    {
        $airports = Airport::all();
        return view('gms.setting.airport.list', compact('airports'));
    }

    public function get($id)
    {
        $op = Airport::findOrFail($id);
        return response()->json(['op' => $op]);
    }

    public function update(Request $request)
    {
        // Validation rules
        $rules = [
            'id' => 'required',
            'airport_name' => 'required',
            'airport_code' => 'required',
            'city' => 'required',
            'country' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $error = true;
            $message = implode($validator->errors()->all('<div>:message</div>'));
        } else {
            // Fetch the record
            $airport = Airport::findOrFail($request->id);

            // Update fields
            $airport->airport_name = $request->airport_name;
            $airport->airport_code = $request->airport_code;
            $airport->city = $request->city;
            $airport->country = $request->country;

            // Save the updated record
            $airport->save();

            $error = false;
            $message = 'airport updated successfully.';
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
        $airports = Airport::orderBy($sort, $order);

        if ($search) {
            $airports = $airports->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('id', 'like', '%' . $search . '%');
            });
        }
        $total = $airports->count();
        $airports = $airports->paginate(request("limit"))->through(function ($airports) {

            // $location = Location::find($airports->location_id);

            return  [
                'id' => $airports->id,
                // 'id' => '<div class="align-middle white-space-wrap fw-bold fs-8 ps-2">' .$airports->id. '</div>',
                'airport_name' => '<div class="align-middle white-space-wrap fs-9 ps-3">' . $airports->airport_name . '</div>',
                'airport_code' => '<div class="align-middle white-space-wrap fs-9 ps-3">' . $airports->airport_code . '</div>',
                'city' => '<div class="align-middle white-space-wrap fs-9 ps-3">' . $airports->city . '</div>',
                'country' => '<div class="align-middle white-space-wrap fs-9 ps-3">' . $airports->country . '</div>',
            ];
        });

        return response()->json([
            "rows" => $airports->items(),
            "total" => $total,
        ]);
    }

     public function store(Request $request)
    {
        $user_id = Auth::user()->id;
        $airports = new Airport();

        $rules = [
            'airport_name' => 'required',
            'airport_code' => 'required',
            'city' => 'required',
            'country' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            Log::info($validator->errors());
            $error = true;
            $message = implode($validator->errors()->all('<div>:message</div>'));  // use this for json/jquery
        } else {

            $error = false;
            $message = 'Airport created succesfully.' . $airports->id;

            $airports->airport_name = $request->airport_name;
            $airports->airport_code = $request->airport_code;
            $airports->city = $request->city;
            $airports->country = $request->country;
          

            $airports->save();


        }

        $notification = array(
            'message'       => 'Airport created successfully',
            'alert-type'    => 'success'
        );

        return response()->json(['error' => $error, 'message' => $message]);
    }

    public function delete($id)
    {
        $op = Airport::findOrFail($id);
        $op->delete();

        $error = false;
        $message = 'Airport deleted succesfully.';

        $notification = array(
            'message'       => 'Airport deleted successfully',
            'alert-type'    => 'success'
        );

        return response()->json(['error' => $error, 'message' => $message]);
        // return redirect()->route('tracki.setup.workspace')->with($notification);
    } // delete


}
