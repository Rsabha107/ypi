<?php

namespace App\Http\Controllers\Ypi\Setting;

use App\Http\Controllers\Controller;
use App\Models\Ypi\ParticipantType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ParticipantTypeController extends Controller
{
    public function index()
    {
        $participant_types = ParticipantType::all();
        return view('ypi.setting.participant_type.list', compact('participant_types'));
    }

    public function get($id)
    {
        $op = ParticipantType::findOrFail($id);
        return response()->json(['op' => $op]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'active_flag' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => implode($validator->errors()->all('<div>:message</div>')),
            ], 422);
        }

        try {
            $userId = Auth::id();

            $op = new ParticipantType();
            $op->title = $request->title;
            $op->active_flag = $request->input('active_flag', true);
            $op->created_by = $userId;
            $op->updated_by = $userId;
            $op->save();

            return response()->json([
                'error' => false,
                'message' => 'Participant Type created successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json([
                'error' => true,
                'message' => 'An error occurred while creating the Participant Type.',
            ], 500);
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'exists:participant_types,id'],
            'title' => ['required', 'string', 'max:255'],
            'active_flag' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => implode($validator->errors()->all('<div>:message</div>')),
            ], 422);
        }

        try {
            $op = ParticipantType::findOrFail($request->id);
            $oldTitle = $op->title;

            $op->title = $request->title;
            $op->active_flag = $request->input('active_flag', true);
            $op->updated_by = Auth::id();
            $op->save();

            return response()->json([
                'error' => false,
                'message' => "Participant Type {$oldTitle} successfully updated",
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json([
                'error' => true,
                'message' => 'An error occurred while updating the Participant Type.',
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

        $q = ParticipantType::query()->orderBy($sort, $order);

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
                'title' => '<div class="align-middle white-space-wrap fs-9 ps-3">' . e($row->title) . '</div>',
                'active_flag' => '<div class="align-middle text-center">' . ($row->active_flag ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>') . '</div>',
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
            $op = ParticipantType::findOrFail($id);
            $op->delete();

            return response()->json([
                'error' => false,
                'message' => 'Participant Type deleted successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json([
                'error' => true,
                'message' => 'An error occurred while deleting the Participant Type.',
            ], 500);
        }
    }
}
