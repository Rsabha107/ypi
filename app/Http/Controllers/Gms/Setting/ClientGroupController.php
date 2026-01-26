<?php

namespace App\Http\Controllers\Gms\Setting;

use App\Http\Controllers\Controller;
use App\Models\GlobalStatus;
use App\Models\Gms\ClientGroup;
use App\Models\Gms\GuestType;
use App\Models\Mds\GlobalYN;
use App\Models\Mds\MdsEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ClientGroupController extends Controller
{
    //
    public function index()
    {
        $client_groups = ClientGroup::all();
        return view('gms.setting.client_group.list', compact('client_groups'));
    }

    public function get($id)
    {
        $op = ClientGroup::findOrFail($id);
        return response()->json(['op' => $op]);
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

    public function list()
    {
        $search = request('search');
        $sort = (request('sort')) ? request('sort') : "id";
        $order = (request('order')) ? request('order') : "DESC";
        $client_groups = ClientGroup::orderBy($sort, $order);

        if ($search) {
            $client_groups = $client_groups->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('id', 'like', '%' . $search . '%');
            });
        }
        $total = $client_groups->count();
        $client_groups = $client_groups->paginate(request("limit"))->through(function ($client_groups) {

        // $location = Location::find($client_groups->location_id);

            return  [
                'id' => $client_groups->id,
                // 'id' => '<div class="align-middle white-space-wrap fw-bold fs-8 ps-2">' .$client_groups->id. '</div>',
                'title' => '<div class="align-middle white-space-wrap fs-9 ps-3">' . $client_groups->title . '</div>',
                'created_at' => format_date($client_groups->created_at,  'H:i:s'),
                'updated_at' => format_date($client_groups->updated_at, 'H:i:s'),
            ];
        });

        return response()->json([
            "rows" => $client_groups->items(),
            "total" => $total,
        ]);
    }

    public function store(Request $request)
    {
        $user_id = Auth::user()->id;
        $client_groups = new ClientGroup();

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
            $message = 'Client Group created succesfully.' . $client_groups->id;

            $client_groups->title = $request->title;
            $client_groups->created_by = $user_id;
            $client_groups->updated_by = $user_id;

            $client_groups->save();


        }

        $notification = array(
            'message'       => 'Vehicle Type created successfully',
            'alert-type'    => 'success'
        );

        return response()->json(['error' => $error, 'message' => $message]);
    }

    public function delete($id)
    {
        $op = ClientGroup::findOrFail($id);
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
