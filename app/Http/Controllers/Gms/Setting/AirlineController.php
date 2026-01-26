<?php

namespace App\Http\Controllers\Gms\Setting;

use App\Http\Controllers\Controller;
use App\Models\Gms\AirlineCarriers;
use App\Models\Mds\MdsEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AirlineController extends Controller
{
    //
    public function index()
    {
        $airlines = AirlineCarriers::all();
        return view('gms.setting.airline.list', compact('airlines'));
    }

    public function get($id)
    {
        $op = AirlineCarriers::findOrFail($id);
        return response()->json(['op' => $op]);
    }

    public function update(Request $request)
    {
        // Validation rules
        $rules = [
            'id' => 'required|exists:airline_carriers,id',
            'name' => 'required|string|max:255',
            'carrier_code' => 'required|string|max:10',
            'country' => 'required|string|max:100',
            'iata_code' => 'required|string|max:5',
            'icao_code' => 'required|string|max:5',
            'founded_year' => 'required|digits:4',
            'website' => 'required|url',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $error = true;
            $message = implode($validator->errors()->all('<div>:message</div>'));
        } else {
            // Fetch the record
            $airline = AirlineCarriers::findOrFail($request->id);

            // Update fields
            $airline->name         = $request->name;
            $airline->carrier_code = $request->carrier_code;
            $airline->country      = $request->country;
            $airline->iata_code    = $request->iata_code;
            $airline->icao_code    = $request->icao_code;
            $airline->founded_year = $request->founded_year;
            $airline->website      = $request->website;

            // Save the updated record
            $airline->save();

            $error = false;
            $message = 'Airline updated successfully.';
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
        $airlines = AirlineCarriers::orderBy($sort, $order);

        if ($search) {
            $airlines = $airlines->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('id', 'like', '%' . $search . '%');
            });
        }
        $total = $airlines->count();
        $airlines = $airlines->paginate(request("limit"))->through(function ($airlines) {

            // $location = Location::find($airlines->location_id);

            return  [
                'id' => $airlines->id,
                // 'id' => '<div class="align-middle white-space-wrap fw-bold fs-8 ps-2">' .$airlines->id. '</div>',
                'name' => '<div class="align-middle white-space-wrap fs-9 ps-3">' . $airlines->name . '</div>',
                'carrier_code' => '<div class="align-middle white-space-wrap fs-9 ps-3">' . $airlines->carrier_code . '</div>',
                'country' => '<div class="align-middle white-space-wrap fs-9 ps-3">' . $airlines->country . '</div>',
                'iata_code' => '<div class="align-middle white-space-wrap fs-9 ps-3">' . $airlines->iata_code . '</div>',
                'icao_code' => '<div class="align-middle white-space-wrap fs-9 ps-3">' . $airlines->icao_code . '</div>',
                'founded_year' => '<div class="align-middle white-space-wrap fs-9 ps-3">' . $airlines->founded_year . '</div>',
                'website' => '<div class="align-middle white-space-wrap fs-9 ps-3">' . $airlines->website . '</div>',
            ];
        });

        return response()->json([
            "rows" => $airlines->items(),
            "total" => $total,
        ]);
    }

    public function store(Request $request)
    {
        $user_id = Auth::user()->id;

        // ✅ Validation rules for Airlines
        $rules = [
            'name'         => 'required',
            'carrier_code' => 'required',
            'country'      => 'required',
            'iata_code'    => 'required',
            'icao_code'    => 'required',
            'founded_year' => 'required|integer|min:1900|max:' . date('Y'),
            'website'      => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            Log::info($validator->errors());
            return response()->json([
                'error'   => true,
                'message' => implode('', $validator->errors()->all('<div>:message</div>'))
            ]);
        }

        // ✅ Create airline record
        $airlines = new AirlineCarriers();
        $airlines->name         = $request->name;
        $airlines->carrier_code = $request->carrier_code;
        $airlines->country      = $request->country;
        $airlines->iata_code    = $request->iata_code;
        $airlines->icao_code    = $request->icao_code;
        $airlines->founded_year = $request->founded_year; // ✅ corrected field name
        $airlines->website      = $request->website;
        $airlines->save();

        return response()->json([
            'error'   => false,
            'message' => 'Airline created successfully : ' . $airlines->name
        ]);
    }

    public function delete($id)
    {
        $op = AirlineCarriers::findOrFail($id);
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
