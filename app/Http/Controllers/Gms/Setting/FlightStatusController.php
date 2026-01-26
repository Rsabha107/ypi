<?php

namespace App\Http\Controllers\Gms\Setting;

use App\Http\Controllers\Controller;
use App\Models\Gms\FlightStatus;
use App\Models\Mds\MdsEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FlightStatusController extends Controller
{
    //
    public function index()
    {
        $flight_statuses = FlightStatus::all();
        return view('gms.setting.flight_status.list', compact('flight_statuses'));
    }

    public function get($id)
    {
        $op = FlightStatus::findOrFail($id);
        return response()->json(['op' => $op]);
    }

    public function update(Request $request)
    {
        $rules = [
            'id' => ['required'],
            'name' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            // Log::info($validator->errors());
            $error = true;
            // $message = 'Employee not create.' . $op->id;
            $message = implode($validator->errors()->all('<div>:message</div>'));
        } else {
            $op = MdsEvent::findOrFail($request->id);

            $error = false;
            $message = 'Event ' . $op->name . ' successfully updated';

            if ($request->hasFile('file_name')) {

                $file = $request->file('file_name');
                $fileNameWithExt = $request->file('file_name')->getClientOriginalName();
                // get file name
                $filename = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
                // get extension
                $extension = $request->file('file_name')->getClientOriginalExtension();
    
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;
                $fileNameToStore = rand() . date('ymdHis') . $file->getClientOriginalName();  // use this
    
                Log::info($fileNameWithExt);
                Log::info($filename);
                Log::info($extension);
                Log::info($fileNameToStore);
    
                // upload
                if ($op->event_logo != 'default.png') {
                    Storage::delete('mds/event/logo/' . $op->event_logo);
                }
    
                // $path = $request->file('file_name')->storeAs('private/mds/event/logo', $fileNameToStore);
                Storage::disk('private')->putFileAs('mds/event/logo', $file, $fileNameToStore);

                // $path = $file->move('upload/profile_images/', $fileNameToStore);
                // Log::info($path);
    
    
            } else {
                $fileNameToStore = 'noimage.jpg';
            }
    
            $op->event_logo = $fileNameToStore;

            
            $op->name = $request->name;
            $op->active_flag = $request->active_flag;
            $op->updated_by = auth()->user()->id;

            $op->save();
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
        $flight_statuses = FlightStatus::orderBy($sort, $order);

        if ($search) {
            $flight_statuses = $flight_statuses->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('id', 'like', '%' . $search . '%');
            });
        }
        $total = $flight_statuses->count();
        $flight_statuses = $flight_statuses->paginate(request("limit"))->through(function ($flight_statuses) {

        // $location = Location::find($flight_statuses->location_id);

            return  [
                'id' => $flight_statuses->id,
                // 'id' => '<div class="align-middle white-space-wrap fw-bold fs-8 ps-2">' .$flight_statuses->id. '</div>',
                'title' => '<div class="align-middle white-space-wrap fs-9 ps-3">' . $flight_statuses->status_name . '</div>',
                'description' => '<div class="align-middle white-space-wrap fs-9 ps-3">' . $flight_statuses->description . '</div>',
            ];
        });

        return response()->json([
            "rows" => $flight_statuses->items(),
            "total" => $total,
        ]);
    }

    public function store(Request $request)
    {
        $user_id = Auth::user()->id;
        $flight_statuses = new FlightStatus();

        $rules = [
            'status_name' => 'required',
            'description' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            Log::info($validator->errors());
            $error = true;
            $message = implode($validator->errors()->all('<div>:message</div>'));  // use this for json/jquery
        } else {

            $error = false;
            $message = 'Fight Stataus created succesfully.' . $flight_statuses->id;

            $flight_statuses->status_name = $request->status_name;
            $flight_statuses->description = $request->description;

            $flight_statuses->save();
        }

        $notification = array(
            'message'       => 'Flight Status created successfully',
            'alert-type'    => 'success'
        );

        return response()->json(['error' => $error, 'message' => $message]);
    }

    public function delete($id)
    {
        $op = FlightStatus::findOrFail($id);
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
