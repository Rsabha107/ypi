<?php

namespace App\Http\Controllers\Gms\Setting;

use App\Http\Controllers\Controller;
use App\Models\Gms\HostedBy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class HostedByController extends Controller
{
    //
    public function index()
    {
        $hosted_bys = HostedBy::all();
        return view('gms.setting.hosted_by.list', compact('hosted_bys'));
    }

    public function get($id)
    {
        $op = HostedBy::findOrFail($id);
        return response()->json(['op' => $op]);
    }

    public function update(Request $request)
    {
        $rules = [
            'id' => ['required'],
            'title' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => implode($validator->errors()->all('<div>:message</div>')),
            ]);
        }

        $hosted_bys = HostedBy::findOrFail($request->id);

        $hosted_bys->title = $request->title;
        $hosted_bys->save();

        return response()->json([
            'error' => false,
            'message' => 'HostedBy updated successfully.',
        ]);
    }


    public function list()
    {
        $search = request('search');
        $sort = (request('sort')) ? request('sort') : "id";
        $order = (request('order')) ? request('order') : "DESC";
        $hosted_bys = HostedBy::orderBy($sort, $order);

        if ($search) {
            $hosted_bys = $hosted_bys->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('id', 'like', '%' . $search . '%');
            });
        }
        $total = $hosted_bys->count();
        $hosted_bys = $hosted_bys->paginate(request("limit"))->through(function ($hosted_bys) {

            // $location = Location::find($hosted_bys->location_id);

            return  [
                'id' => $hosted_bys->id,
                // 'id' => '<div class="align-middle white-space-wrap fw-bold fs-8 ps-2">' .$hosted_bys->id. '</div>',
                'title' => '<div class="align-middle white-space-wrap fs-9 ps-3">' . $hosted_bys->title . '</div>',
                'created_at' => format_date($hosted_bys->created_at,  'H:i:s'),
                'updated_at' => format_date($hosted_bys->updated_at, 'H:i:s'),
            ];
        });

        return response()->json([
            "rows" => $hosted_bys->items(),
            "total" => $total,
        ]);
    }

    public function store(Request $request)
    {
        $user_id = Auth::user()->id;
        $hosted_bys = new HostedBy();

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
            $message = 'HostedBy created succesfully.' . $hosted_bys->id;

            $hosted_bys->title = $request->title;
            $hosted_bys->created_by = $user_id;
            $hosted_bys->updated_by = $user_id;

            $hosted_bys->save();
        }

        $notification = array(
            'message'       => 'HostedBy created successfully',
            'alert-type'    => 'success'
        );

        return response()->json(['error' => $error, 'message' => $message]);
    }

    public function delete($id)
    {
        $op = HostedBy::findOrFail($id);
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
