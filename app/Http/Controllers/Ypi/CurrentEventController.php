<?php

namespace App\Http\Controllers\Ypi;

use App\Http\Controllers\Controller;
use App\Models\Ypi\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CurrentEventController extends Controller
{
    /**
     * Switch the event context used across the whole application.
     */
    public function switch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_id' => ['nullable', 'integer', 'exists:events,id'],
        ]);

        if ($validator->fails()) {
            return $this->respond($request, true, implode($validator->errors()->all('<div>:message</div>')), 422);
        }

        set_current_event($request->input('event_id'));

        $event = current_event();

        return $this->respond($request, false, $event ? "Switched to {$event->name}." : 'Showing all events.');
    }

    public function clear(Request $request)
    {
        set_current_event(null);

        return $this->respond($request, false, 'Showing all events.');
    }

    private function respond(Request $request, bool $error, string $message, int $status = 200)
    {
        if ($request->expectsJson()) {
            $event = current_event();

            return response()->json([
                'error' => $error,
                'message' => $message,
                'event_id' => $event?->id,
                'event_name' => $event?->name,
            ], $status);
        }

        return back()->with($error ? 'error' : 'success', $message);
    }
}
