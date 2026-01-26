<?php

namespace App\Http\Controllers\Ypi\Setting;

use App\Http\Controllers\Controller;
use App\Models\Ypi\ParticipantType;
use App\Models\Ypi\SizeLookup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SizeController extends Controller
{
    public function index()
    {
        $sizes = SizeLookup::all();
        return view('ypi.setting.sizes.list', compact('sizes'));
    }

    public function get($id)
    {
        $op = SizeLookup::findOrFail($id);
        return response()->json(['op' => $op]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'label' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255'],
            'sort_order' => ['required', 'integer'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => implode($validator->errors()->all('<div>:message</div>')),
            ], 422);
        }

        try {
            $userId = Auth::id();

            $op = new SizeLookup();
            $op->type = $request->type;
            $op->code = $request->code;
            $op->label = $request->label;
            $op->sort_order = $request->sort_order;
            $op->created_by = $userId;
            $op->updated_by = $userId;
            $op->save();

            return response()->json([
                'error' => false,
                'message' => 'Size created successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json([
                'error' => true,
                'message' => 'An error occurred while creating the Size.',
            ], 500);
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'exists:size_lookups,id'],
            'type' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255'],
            'label' => ['required', 'string', 'max:255'],
            'sort_order' => ['required', 'integer'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => implode($validator->errors()->all('<div>:message</div>')),
            ], 422);
        }

        try {
            $op = SizeLookup::findOrFail($request->id);
            $oldLabel = $op->label;

            $op->type = $request->type;
            $op->code = $request->code;
            $op->label = $request->label;
            $op->sort_order = $request->sort_order;
            $op->updated_by = Auth::id();
            $op->save();

            return response()->json([
                'error' => false,
                'message' => "Size {$oldLabel} successfully updated",
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json([
                'error' => true,
                'message' => 'An error occurred while updating the Size.',
            ], 500);
        }
    }

    public function list()
    {
        $search = request('search');

        $allowedSort = ['id', 'title', 'created_at', 'updated_at'];
        $sort = request('sort', 'id');
        if (!in_array($sort, $allowedSort, true)) {
            $sort = 'id';
        }

        $order = strtoupper(request('order', 'DESC'));
        $order = in_array($order, ['ASC', 'DESC'], true) ? $order : 'DESC';

        $limit = request("limit");
        $limit = max(1, min($limit, 100)); // min=1, max=100

        $q = SizeLookup::query()->orderBy($sort, $order);

        if ($search) {
            $q->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%");
            });
        }

        $total = $q->count();

        $ops = $q->paginate($limit)->through(function ($row) {
            return [
                'id' => $row->id,
                'type' => '<div class="align-middle white-space-wrap fs-9 ps-3">' . e($row->type) . '</div>',
                'code' => '<div class="align-middle white-space-wrap fs-9 ps-3">' . e($row->code) . '</div>',
                'label' => '<div class="align-middle white-space-wrap fs-9 ps-3">' . e($row->label) . '</div>',
                'sort_order' => '<div class="align-middle white-space-wrap fs-9 ps-3">' . e($row->sort_order) . '</div>',
                'created_at' => format_date($row->created_at, 'H:i:s'),
                'updated_at' => format_date($row->updated_at, 'H:i:s'),
            ];
        });

        return response()->json([
            'rows' => $ops->items(),
            'total' => $total,
        ]);
    }

    public function delete($id)
    {
        try {
            $op = SizeLookup::findOrFail($id);
            $op->delete();

            return response()->json([
                'error' => false,
                'message' => 'Size deleted successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json([
                'error' => true,
                'message' => 'An error occurred while deleting the Size.',
            ], 500);
        }
    }
}
