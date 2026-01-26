<?php

namespace App\Http\Controllers\Vapp\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

// use Illuminate\Support\Facades\Redirect;

class EventImageController extends Controller
{
    //
    public function getPrivateFile($file)
    {
        $file_path = 'app/private/vapp/event/logo/' . $file;
        $path = storage_path($file_path);

        appLog('path: '.$path);

        return response()->file($path);
    }

}
