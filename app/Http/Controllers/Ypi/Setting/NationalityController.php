<?php

namespace App\Http\Controllers\Ypi\Setting;

use App\Http\Controllers\Controller;
use App\Models\Ypi\Nationality;
use App\Models\Mds\MdsEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class NationalityController extends Controller
{
    //
    public function index()
    {
        $nationalities = Nationality::all();
        return view('ypi.setting.nationality.list', compact('nationalities'));
    }

    public function get($id)
    {
        $op = Nationality::findOrFail($id);
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

        $nationalities = Nationality::findOrFail($request->id);

        $nationalities->title = $request->title;
        $nationalities->num_code = $request->num_code;
        $nationalities->alpha_2_code = $request->alpha_2_code;
        $nationalities->alpha_3_code = $request->alpha_3_code;
        $nationalities->en_short_name = $request->en_short_name;
        $nationalities->save();

        return response()->json([
            'error' => false,
            'message' => 'Nationality updated successfully.',
        ]);
    }


    public function list()
    {
        $search = request('search');
        $sort = (request('sort')) ? request('sort') : "id";
        $order = (request('order')) ? request('order') : "DESC";
        $nationalities = Nationality::with('event')->orderBy($sort, $order);

        if ($search) {
            $nationalities = $nationalities->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('id', 'like', '%' . $search . '%');
            });
        }
        $total = $nationalities->count();
        $nationalities = $nationalities->paginate(request("limit"))->through(function ($nationalities) {

            // $location = Location::find($nationalities->location_id);

            return  [
                'id' => $nationalities->id,
                // 'id' => '<div class="align-middle white-space-wrap fw-bold fs-8 ps-2">' .$nationalities->id. '</div>',
                'title' => '<div class="align-middle white-space-wrap fs-9 ps-3">' . $nationalities->title . '</div>',
                'alpha_3_code' => '<div class="align-middle white-space-wrap fs-9 ps-3">' . $nationalities->alpha_3_code . '</div>',
                'event' => event_scope_badge($nationalities),
                'created_at' => format_date($nationalities->created_at,  'H:i:s'),
                'updated_at' => format_date($nationalities->updated_at, 'H:i:s'),
            ];
        });

        return response()->json([
            "rows" => $nationalities->items(),
            "total" => $total,
        ]);
    }

    public function store(Request $request)
    {
        $user_id = Auth::user()->id;
        $nationalities = new Nationality();

        $rules = [
            'title' => 'required',
            'event_scope' => ['nullable', 'in:current,global'],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            Log::info($validator->errors());
            $error = true;
            $message = implode($validator->errors()->all('<div>:message</div>'));  // use this for json/jquery
        } else {

            $error = false;
            $message = 'Nationality created succesfully.' . $nationalities->id;

            $nationalities->event_id = $request->input('event_scope') === 'global' ? null : current_event_id();
            $nationalities->title = $request->title;
            $nationalities->num_code = $request->num_code;
            $nationalities->alpha_2_code = $request->alpha_2_code;
            $nationalities->alpha_3_code = $request->alpha_3_code;
            $nationalities->en_short_name = $request->en_short_name;

            $nationalities->save();
        }

        $notification = array(
            'message'       => 'Nationality created successfully',
            'alert-type'    => 'success'
        );

        return response()->json(['error' => $error, 'message' => $message]);
    }

    public function delete($id)
    {
        $op = Nationality::findOrFail($id);
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
