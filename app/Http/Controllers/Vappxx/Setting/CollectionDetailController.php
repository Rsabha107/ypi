<?php

namespace App\Http\Controllers\Vapp\Setting;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Vapp\CollectionDetail;
use App\Models\Vapp\Venue;
// use App\Models\Vapp\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

// use Illuminate\Support\Facades\Redirect;

class CollectionDetailController extends Controller
{
    //
    public function index()
    {
        $venues = Venue::all();
        $events = Event::all();
        // $locations = Location::all();
        return view('vapp.setting.collection.list', [
            'events' => $events,
        ]);
    }

    public function get($id)
    {
        $collection = CollectionDetail::findOrFail($id);
        return response()->json(['op' => $collection]);
    }

    public function list()
    {
        $search = request('search');
        $sort = (request('sort')) ? request('sort') : "id";
        $order = (request('order')) ? request('order') : "DESC";
        $ops = CollectionDetail::orderBy($sort, $order);

        if ($search) {
            $ops = $ops->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('short_name', 'like', '%' . $search . '%')
                    ->orWhere('id', 'like', '%' . $search . '%');
            });
        }
        $total = $ops->count();
        $limit = request("limit");
        $limit = max(1, min($limit, 100)); // min=1, max=100
        $ops = $ops->paginate($limit)->through(function ($op) {

            // $location = Location::find($venue->location_id);

            return  [
                'id' => $op->id,
                // 'id' => '<div class="align-middle white-space-wrap fw-bold fs-8 ps-2">' .$venue->id. '</div>',
                'event' => '<div class="align-middle white-space-wrap fs-9 ps-3">' . $op->event->name . '</div>',
                'collection_location' => '<div class="align-middle white-space-wrap fs-9">' . $op->collection_location . '</div>',
                'collection_time' => '<div class="align-middle white-space-wrap fs-9">' . $op->collection_time . '</div>',
                'created_at' => format_date($op->created_at,  'H:i:s'),
                'updated_at' => format_date($op->updated_at, 'H:i:s'),
            ];
        });

        return response()->json([
            "rows" => $ops->items(),
            "total" => $total,
        ]);
    }

    public function store(Request $request)
    {
        //
        // dd($request);
        $user_id = Auth::user()->id;
        $op = new CollectionDetail();

        $rules = [
            'event_id' => 'required|exists:events,id',
            'collection_location' => 'required|string|max:255',
            'collection_time' => 'required|string|max:255',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            appLog('validator: ' . $validator->errors());;
            $error = true;
            $message = implode($validator->errors()->all('<div>:message</div>'));  // use this for json/jquery
            return response()->json(['error' => $error, 'message' => $message]);
        }
        DB::beginTransaction();
        try {
            $error = false;
            $message = 'Collection created successfully.' . $op->id;

            $op->event_id = $request->event_id;
            $op->collection_location = $request->collection_location;
            $op->collection_time = $request->collection_time;
            $op->created_by = $user_id;
            $op->updated_by = $user_id;
            $op->active_flag = 1;

            $op->save();
            DB::commit();
            $notification = array(
                'message'       => 'Collection created successfully',
                'alert-type'    => 'success'
            );

            return response()->json(['error' => $error, 'message' => $message]);
        } catch (\Exception $e) {
            DB::rollBack();
            appLog('Error creating collection: ' . $e->getMessage());
            $error = true;
            $message = 'Collection could not be created.';
        }
    }

    public function update(Request $request)
    {
        $op = CollectionDetail::findOrFail($request->id);
        $user = Auth::user();;
        $rules = [
            'event_id' => 'required|exists:events,id',
            'collection_location' => 'required|string|max:255',
            'collection_time' => 'required|string|max:255',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            appLog('validator: ' . $validator->errors());;
            $error = true;
            $message = implode($validator->errors()->all('<div>:message</div>'));  // use this for json/jquery
            return response()->json(['error' => $error, 'message' => $message]);
        }
        DB::beginTransaction();
        try {
            $error = false;
            $message = 'Collection Updated successfully.' . $op->id;

            $op->event_id = $request->event_id;
            $op->collection_location = $request->collection_location;
            $op->collection_time = $request->collection_time;
            $op->created_by = $user->id;
            $op->updated_by = $user->id;
            $op->active_flag = 1;

            $op->save();
            DB::commit();
            $notification = array(
                'message'       => 'Collection updated successfully',
                'alert-type'    => 'success'
            );

            return response()->json(['error' => $error, 'message' => $message]);
        } catch (\Exception $e) {
            DB::rollBack();
            appLog('Error updating collection: ' . $e->getMessage());
            $error = true;
            $message = 'Collection could not be updated.';
        }
    }

    public function delete($id)
    {
        $ws = CollectionDetail::findOrFail($id);
        $ws->delete();

        $error = false;
        $message = 'Collection deleted succesfully.';

        $notification = array(
            'message'       => 'Collection deleted successfully',
            'alert-type'    => 'success'
        );

        return response()->json(['error' => $error, 'message' => $message]);
        // return redirect()->route('tracki.setup.workspace')->with($notification);
    } // delete

}
