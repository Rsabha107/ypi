<?php

namespace App\Http\Controllers\Gms\Setting;

use App\Http\Controllers\Controller;
use App\Models\Gms\Designation;
use App\Models\GlobalStatus;
use App\Models\Mds\GlobalYN;
use App\Models\Mds\MdsEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DesignationController extends Controller
{
    //
    public function index()
    {
        $designations = Designation::all();
        return view('gms.setting.designation.list', compact('designations'));
    }

    public function get($id)
    {
        $op = Designation::findOrFail($id);
        return response()->json(['op' => $op]);
    }


    public function list()
    {
        $search = request('search');
        $sort = (request('sort')) ? request('sort') : "id";
        $order = (request('order')) ? request('order') : "DESC";
        $designations = Designation::orderBy($sort, $order);

        if ($search) {
            $designations = $designations->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('id', 'like', '%' . $search . '%');
            });
        }
        $total = $designations->count();
        $designations = $designations->paginate(request("limit"))->through(function ($designations) {

            // $location = Location::find($designations->location_id);

            return  [
                'id' => $designations->id,
                // 'id' => '<div class="align-middle white-space-wrap fw-bold fs-8 ps-2">' .$designations->id. '</div>',
                'title' => '<div class="align-middle white-space-wrap fs-9 ps-3">' . $designations->title . '</div>',
                'created_at' => format_date($designations->created_at,  'H:i:s'),
                'updated_at' => format_date($designations->updated_at, 'H:i:s'),
            ];
        });

        return response()->json([
            "rows" => $designations->items(),
            "total" => $total,
        ]);
    }

    public function store(Request $request)
    {
        $user_id = Auth::user()->id;
        $designations = new Designation();

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
            $message = 'Client Group created succesfully.' . $designations->id;

            $designations->title = $request->title;
            $designations->created_by = $user_id;
            $designations->updated_by = $user_id;

            $designations->save();
        }

        $notification = array(
            'message'       => 'Vehicle Type created successfully',
            'alert-type'    => 'success'
        );

        return response()->json(['error' => $error, 'message' => $message]);
    }

    public function update(Request $request)
    {
        $rules = [
            'id' => ['required'],
            'name' => 'required',
            'active_flag' => 'required',
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


    public function delete($id)
    {
        $op = Designation::findOrFail($id);
        $op->delete();

        $error = false;
        $message = 'Event deleted succesfully.';

        $notification = array(
            'message'       => 'Event deleted successfully',
            'alert-type'    => 'success'
        );

        return response()->json(['error' => $error, 'message' => $message]);
        // return redirect()->route('tracki.setup.workspace')->with($notification);
    } // delete


}
