<?php

// namespace App\Http\Helpers;

use App\Models\Event;
use App\Models\Ypi\ParticipantStatus;
use App\Models\Vapp\ParkingCapacity;
use App\Models\Vapp\VappRequest;
use App\Models\Vapp\VappRequestStatus;
use App\Models\Vapp\Venue;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Spatie\Permission\Models\Role;

if (! function_exists('current_event_id')) {
    /**
     * ID of the event currently selected in the header switcher.
     * 'participant_filter_event_id' is the legacy key, kept in sync for older screens.
     */
    function current_event_id(): ?int
    {
        $id = session('EVENT_ID') ?: session('participant_filter_event_id');

        return $id ? (int) $id : null;
    }
}

if (! function_exists('get_current_event_id')) {
    function get_current_event_id()
    {
        return current_event_id();
    }
}

if (! function_exists('current_event')) {
    function current_event(): ?\App\Models\Ypi\Event
    {
        static $cached = [];

        $id = current_event_id();

        if (! $id) {
            return null;
        }

        return $cached[$id] ??= \App\Models\Ypi\Event::find($id);
    }
}

if (! function_exists('set_current_event')) {
    function set_current_event($eventId): void
    {
        if ($eventId) {
            session([
                'EVENT_ID' => (int) $eventId,
                'participant_filter_event_id' => (int) $eventId,
            ]);

            return;
        }

        session()->forget(['EVENT_ID', 'participant_filter_event_id']);
    }
}

if (! function_exists('event_scope_badge')) {
    /**
     * Renders whether a lookup row is shared or owned by a single event.
     */
    function event_scope_badge($model): string
    {
        if (is_null($model->event_id)) {
            return '<div class="align-middle white-space-wrap fs-9 ps-3"><span class="badge bg-secondary">All Events</span></div>';
        }

        return '<div class="align-middle white-space-wrap fs-9 ps-3"><span class="badge bg-info">'
            . e($model->event?->name ?? 'Unknown') . '</span></div>';
    }
}

if (! function_exists('selectable_events')) {
    /**
     * Events offered in the header switcher.
     */
    function selectable_events()
    {
        return \App\Models\Ypi\Event::where('active_flag', 1)
            ->where('name', 'not like', '%Admin%')
            ->orderBy('name')
            ->get(['id', 'name']);
    }
}

if (! function_exists('nextSequence')) {
    /**
     * Get the next value in a named sequence
     *
     * @param string $key
     * @return int
     * @throws \Exception
     */

    function nextSequence(string $key): int
    {
        return DB::transaction(function () use ($key) {
            $row = DB::table('sequences')
                ->where('key', $key)
                ->lockForUpdate()
                ->first();

            if (!$row) {
                throw new Exception("Sequence '{$key}' not found");
            }

            $next = $row->value + 1;

            DB::table('sequences')
                ->where('key', $key)
                ->update(['value' => $next]);

            return $next;
        });
    }
}

if (!function_exists('registerUrl')) {
    function registerUrl(int $id): string
    {
        $event_id = Crypt::encrypt((string) $id);   // encrypted payload
        return route('auth.register', $event_id);
    }
}

if (! function_exists('age_from_dob')) {
    function age_from_dob($dob, $format = null): ?int
    {
        if (empty($dob)) {
            return null;
        }

        try {
            $date = $format
                ? Carbon::createFromFormat($format, $dob)
                : Carbon::parse($dob);

            return $date->age; // Carbon handles leap years correctly
        } catch (\Exception $e) {
            return null;
        }
    }
}
// get Event name by id
if (! function_exists('getEventNameById')) {
    function getEventNameById($id)
    {
        return DB::table('events')->where('id', $id)->value('name');
    }
} 

if (! function_exists('getNameById')) {
    /**
     * Get a column value (default: name) from any table by ID
     *
     * @param string $table
     * @param int|string $id
     * @param string $column
     * @return string|null
     */
    function getNameById(string $table, $id, string $column = 'name')
    {
        return DB::table($table)->where('id', $id)->value($column);
    }
}

if (! function_exists('getIdByName')) {
    /**
     * Get a column value (default: name) from any table by ID
     *
     * @param string $table
     * @param int|string $id
     * @param string $column
     * @return string|null
     */
    function getIdByName(string $table, $name, string $column = 'title')
    {
        return DB::table($table)->where($column, $name)->value('id');
    }
}

if (!function_exists('getVenueIdByLabel')) {
    function getVenueIdByLabel(string $label): ?int
    {
        $op_id = Venue::where('short_name', $label)->pluck('id')->first();

        return $op_id ?? null;
    }
}

if (!function_exists('getStatusIdByLabel')) {
    function getStatusIdByLabel(string $label): ?int
    {
        // appLog('getStatusIdByLabel called with label: ' . $label);
        $status_id = ParticipantStatus::where('title', $label)->pluck('id')->first();

        // appLog('getStatusIdByLabel found status_id: ' . ($status_id ?? 'null'));
        return $status_id ?? null;
    }
}

if (!function_exists('getRoleIdByLabel')) {
    function getRoleIdByLabel(string $label): ?int
    {
        $op_id = Role::where('name', $label)->pluck('id')->first();

        return $op_id ?? null;
    }
}


if (!function_exists('get_label')) {

    function get_label($label, $default, $locale = '')
    {
        if (Lang::has('labels.' . $label, $locale)) {
            return trans('labels.' . $label, [], $locale);
        } else {
            return $default;
        }
    }
}

if (!function_exists('getQrCode')) {

    function getQrCode($id, $size)
    {
        // $qr_code = QrCode::size($size)->generate($id);
        $qr_code = base64_encode(QrCode::format('svg')->size($size)->errorCorrection('H')->generate($id));

        return ($qr_code);
    }
}

if (!function_exists('time_range_segment')) {

    function time_range_segment($time_range, $segment)
    {
        if ($segment == 'from') {
            $return_segment = Str::substr($time_range, 0,  Str::position($time_range, "-") - 1);
            return $return_segment;
        } elseif ($segment == 'to') {
            $return_segment = Str::substr($time_range, Str::position($time_range, "-") + 1);
            return $return_segment;
        } else {
            return null;
        }
    }
}

if (!function_exists('generateSecurePassword')) {
    function generateSecurePassword($length = 12)
    {
        $lowercase    = 'abcdefghijklmnopqrstuvwxyz';
        $uppercase    = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers      = '0123456789';
        $specialChars = '!@#$%^&*()-_=+<>?';

        // Ensure at least one of each
        $password = '';
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $specialChars[random_int(0, strlen($specialChars) - 1)];

        // Fill the rest
        $all = $lowercase . $uppercase . $numbers . $specialChars;
        for ($i = strlen($password); $i < $length; $i++) {
            $password .= $all[random_int(0, strlen($all) - 1)];
        }

        // Shuffle to randomize order
        return str_shuffle($password);
    }
}

/**
 * Generate initials from a name
 *
 * @param string $name
 * @return string
 */
if (!function_exists('generate')) {
    function generateInitials(string $name): string
    {
        $words = explode(' ', $name);
        if (count($words) >= 2) {
            return mb_strtoupper(
                mb_substr($words[0], 0, 1, 'UTF-8') .
                    mb_substr(end($words), 0, 1, 'UTF-8'),
                'UTF-8'
            );
        }
        return makeInitialsFromSingleWord($name);
    }
}

/**
 * Make initials from a word with no spaces
 *
 * @param string $name
 * @return string
 */
if (!function_exists('makeInitialsFromSingleWord')) {
    function makeInitialsFromSingleWord(string $name): string
    {
        preg_match_all('#([A-Z]+)#', $name, $capitals);
        if (count($capitals[1]) >= 2) {
            return mb_substr(implode('', $capitals[1]), 0, 2, 'UTF-8');
        }
        return mb_strtoupper(mb_substr($name, 0, 2, 'UTF-8'), 'UTF-8');
    }
}


if (!function_exists('format_date')) {
    function format_date($date, $time = null, $format = null, $apply_timezone = true)
    {
        if ($date) {
            // appLog('date: '.$date);
            // appLog('time: '.$time);
            // appLog('format: '.$format);
            $format = $format ?? get_php_date_format();
            $time = $time ?? '';

            $date = $time != '' ? \Carbon\Carbon::parse($date) : \Carbon\Carbon::parse($date);

            // appLog('date: '.$date);

            // if ($time !== '') {
            //     if ($apply_timezone) {
            //         $date->setTimezone(config('app.timezone'));
            //     }
            //     $format .= ' ' . $time;
            // }

            // appLog($date->format($format));

            return $date->format($format);
        } else {
            return '-';
        }
    }
}

if (!function_exists('get_php_date_format')) {
    function get_php_date_format()
    {
        // $general_settings = get_settings('general_settings');
        $date_format = 'DD-MM-YYYY|d-m-Y';
        // $date_format = $general_settings['date_format'] ?? 'DD-MM-YYYY|d-m-Y';
        $date_format = explode('|', $date_format);
        return $date_format[1];
    }
}

if (!function_exists('get_approval_id')) {

    function get_approval_id($variable)
    {
        $ret = VappRequestStatus::where('title', $variable)->first();
        if ($ret) {
            return $ret->id;
        } else {
            return null;
        }
    }
}

if (!function_exists('getPublicIp')) {
    function getPublicIp()
    {
        try {
            return Http::timeout(5)
                ->get('https://api64.ipify.org?format=json')
                ->json()['ip'];
        } catch (\Exception $e) {
            return 'Unable to fetch public IP';
        }
    }
}
