<?php

namespace App\Http\Controllers\Ypi\Auth;

use App\Http\Controllers\Controller;
use App\Mail\AccountCreationMail;
use App\Mail\OtpMail;
use App\Mail\SendForgotPasswordMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\ItemCategory;
use App\Models\LogicalSpaceCategory;
use App\Models\LogicalSpaceSubcategory;
use App\Models\LogicalSpaceName;
use App\Models\ItemSubcategory;
use App\Models\Product;
use App\Models\SiteCategory;
use App\Models\Site;
use App\Models\VenueType;
use App\Models\Ypi\Event;
use App\Models\Ypi\Guardian;
use App\Models\Ypi\GuardianDocument;
use App\Models\Ypi\TempUpload;
use App\Models\Task;
use App\Models\Vapp\FunctionalArea;
use App\Notifications\EmailOtpVerification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use TechEd\SimplOtp\SimplOtp;
use TechEd\SimplOtp\Models\SimplOtp as OTPModel;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

// use Brian2694\Toastr\Facades\Toastr;


class AdminController extends Controller
{
    //
    // public function adminDashboard(){

    //     return view('admin.index');
    // }  // End method


    public function showEncryptedUrl(string $token)
{
    try {
        $id = (int) Crypt::encrypt($token);
    } catch (DecryptException $e) {
        abort(404);
    }

    // now load your record using $id
    // $participant = Participant::findOrFail($id);

    return view('register.show', compact('id'));
}

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('login');
    } // End method

    public function login()
    {
        Auth::guard('web')->logout();
        return view('vapp.auth.sign-in');
    }

    public function verifyOtpAndLoginxx(Request $request)
    {
        // $maxAttempts = (int) config('simple-otp.otp_max_attempts');
        // $otp_attempts = SimpleOTP::where('identity', auth()->user()->email)
        //     ->where('validated_at', null)
        //     ->latest()
        //     ->first();

        // if($otp_attempts->attempts >= $maxAttempts){
        //     $notification = array(
        //         'message' => 'Max attempts reached',
        //         'alert-type' => 'error'
        //     );
        //     return redirect('/tracki/auth/signin')->with($notification);
        //     // return redirect('tracki/auth/otp')->with($notification);
        // };

        $user = auth()->user();

        // $isValid = SimpleOTP::verify(auth()->user()->email, $request->otp);
        $isValid = SimplOtp::validate($user->email, $request->otp);
        // dd($isValid);
        if ($isValid->status) {
            session()->put('OTPSESSIONKEY', true);
        }

        $isvalid_string = $isValid ? 'true' : 'false';

        appLog('AdminController::verifyOtpErrors => isValid: ' . $isvalid_string);
        if (auth()->check() && session()->get('OTPSESSIONKEY')) {
            appLog('AdminController::verifyOtpErrors => inside if');
            return redirect()->intended('/');
        } else {
            $notification = array(
                'message' => 'Invalid OTP code Entered',
                'alert-type' => 'error'
            );
            return redirect('vapp/auth/otp')->with($notification);
        }
    }

    public function verifyOtpAndLogin(Request $request)
    {

        $user = auth()->user();
        $key = 'otp-attempts:' . $user->id;
        appLog('AdminController::verifyOtpErrors => key: ' . $key);
        // $remaining = max(0, 5 - RateLimiter::attempts($key));

        // 1. Check if user is locked
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            appLog('AdminController::verifyOtpErrors => tooManyAttempts: ' . $seconds);
            OTPModel::where('identifier', $user->email)->where('is_valid', true)->delete();
            $notification = [
                'message' => "Too many invalid OTP attempts.",
                'alert-type' => 'danger'
            ];
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            return redirect()->route('login')->with($notification);
        }

        // 2. Verify OTP
        $isValid = SimplOtp::validate($user->email, $request->otp);

        // $isvalid_string = $isValid->status ? 'true' : 'false';
        // appLog('AdminController::verifyOtpErrors => isValid: ' . $isvalid_string);

        if ($isValid->status) {
            // ✅ Success: reset attempts
            RateLimiter::clear($key);
            session()->put('OTPSESSIONKEY', true);

            if (auth()->check() && session()->get('OTPSESSIONKEY')) {
                appLog('AdminController::verifyOtpErrors => inside if');
                return redirect()->intended('/');
            }
        }

        // 3. Invalid OTP → count attempt + lock if max reached
        RateLimiter::hit($key, 100); // lock for 15 minutes

        $remaining = 5 - RateLimiter::attempts($key);

        $notification = [
            'message' => "Invalid OTP code entered. Attempts left: {$remaining}",
            'alert-type' => 'warning',
        ];
        return redirect('auth/otp')->with($notification);
    }

    public function showOtp()
    {
        // $key = 'otp-attempts:' . auth()->id();
        // $remaining = RateLimiter::attempts($key);

        // appLog('AdminController::showOtp => key: ' . $key);
        // appLog('AdminController::showOtp => attempts: ' . RateLimiter::attempts($key));
        // appLog('AdminController::showOtp => remaining attempts: ' . $remaining);
        return view('auth.otp');
    }

    public function resendOTP()
    {
        // dd( Session::all());
        $user = auth()->user();
        if (config('settings.otp_enabled')) {

            $key = Str::lower($user->id);

            // Allow 3 attempts every 5 minutes
            if (RateLimiter::tooManyAttempts($key, 3)) {
                $seconds = RateLimiter::availableIn($key);
                $minutes = floor($seconds / 60);
                $remainingSeconds = $seconds % 60;

                $timeMessage = $minutes > 0
                    ? "{$minutes} minute(s) and {$remainingSeconds} second(s)"
                    : "{$remainingSeconds} second(s)";

                $notification = array(
                    'message' => 'Too many OTP requests. Try again in ' . $timeMessage,
                    'alert-type' => 'danger'
                );

                return redirect('/auth/otp')->with($notification);

                return response()->json([
                    'message' => 'Too many OTP requests. Try again in ' . $seconds . ' seconds.'
                ], 429);
            }

            // Hit the rate limiter
            RateLimiter::hit($key, 300); // 300 seconds = 5 minutes

            $otp = SimplOtp::generate($user->email);
            if ($otp->status === true) {
                $details = [
                    'otp_token' => $otp->token,
                    'body' => 'Your One-Time Password (OTP) is: ' . $otp->token,
                ];
                Mail::to($user->email)->send(new OtpMail($details));
            }
            $notification = array(
                'message' => 'We have a sent a new OTP code to your email, please check',
                'alert-type' => 'success'
            );

            return redirect('/auth/otp')->with($notification);
            // return redirect('tracki/auth/otp')->with('message', 'OTP re-sent to your email');
        }
    }

    public function signUp()
    {
        $events = Event::all();
        $functional_areas = FunctionalArea::all();
        return view('auth.sign-up', compact('events', 'functional_areas'));
    }

    public function register()
    {
        Log::info("Register page accessed");
        // No event required - guardians register without event context
        // They will select events when adding participants
        return view('auth.register');
    }

    public function checkEmail(Request $request)
    {
        $email = $request->email;
        
        // Check if user exists with this email
        $user = User::where('email', $email)->first();
        
        if ($user) {
            return response()->json([
                'exists' => true,
                'message' => 'This email is already registered.',
                'login_url' => route('login')
            ]);
        }
        
        return response()->json([
            'exists' => false
        ]);
    }

    public function storeRegister(Request $request)
    {
        appLog('AdminController@store - Request: ' . json_encode($request->all()));

        $rules = [
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|max:15',
            'qid' => 'required|max:50|unique:guardians,qid',
            'password' => ['required', 'confirmed', Password::defaults()],
            // 'qid_files' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            // 'qid_files' => 'nullable|integer|exists:temp_uploads,id',
        ];

        $message = '
            [
                "name.required" => "Name is required",
                "email.required" => "Email is required",
                "email.email" => "Provide a valid email",
                "email.unique" => "Email already exists",
                "phone.required" => "Phone is required",
                "password.required" => "Password is required",
                "password.confirmed" => "Password confirmation does not match",
                "password.min" => "Password must be at least 8 characters",
                "password.max" => "Password must not exceed 16 characters",
            ]';

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withInput()
                ->withErrors($validator);
        }

        DB::beginTransaction();
        try {

            // @unlink(public_path('upload/instructor_images/' . $data->photo));
            // $id = Auth::user()->id;
            $user = new User();
            $guardian = new Guardian();

            $user->employee_id = 0;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password);
            $user->status = 1;
            $user->usertype = 'user';
            $user->is_admin = 0;
            $user->role = 'user';

            $user->save();

            $guardian->full_name = $request->name;
            $guardian->email = $request->email;
            $guardian->phone_main = $request->phone;
            $guardian->qid = $request->qid;
            $guardian->phone_secondary = $request->phone;
            $guardian->user_id = $user->id;
            // No event_id - guardians are not tied to specific events
            // Participants will have their own event_id
            $guardian->save();

            // handle multiple QID files upload
            $qidFiles = $request->input('qid_files', []);

            // If somehow a single value comes, normalize to array
            if (!is_array($qidFiles) && $qidFiles) {
                $qidFiles = [$qidFiles];
            }

            foreach ($qidFiles as $tempId) {

                Log::info("Processing QID file temp ID: {$tempId} for guardian ID: {$guardian->id}");
                $temp = TempUpload::where('path', $tempId)->first();

                if (!$temp) {
                    throw new \Exception("Invalid uploaded file reference: {$tempId}");
                }

                $ext = pathinfo($temp->path, PATHINFO_EXTENSION) ?: 'jpg';
                $fileName = time() . '_' . uniqid() . '.' . $ext;

                $finalDir  = "uploads/guardians/{$guardian->id}/";
                $finalPath = $finalDir . $fileName;

                // move from temp disk -> private disk
                $contents = Storage::disk($temp->disk)->get($temp->path);
                Storage::disk('private')->put($finalPath, $contents);

                // create document row
                $doc = new GuardianDocument();
                $doc->guardian_id = $guardian->id;
                $doc->disk = 'private';
                $doc->path = $finalPath; // full file path
                $doc->original_name = $temp->original_name ?? $fileName;
                $doc->mime = $temp->mime ?? 'image/' . $ext;
                $doc->size = $temp->size ?? strlen($contents);
                $doc->created_by = $user->id;
                $doc->save();

                // cleanup temp
                Storage::disk($temp->disk)->delete($temp->path);
                $temp->delete();
            }
            // Handle FilePond temp upload -> move to private guardian_documents
            // if ($request->filled('qid_files')) {

            //     $temp = TempUpload::where('path', (int)$request->qid_files)
            //         ->when(Auth::check(), fn($q) => $q->where('user_id', Auth::id()))
            //         ->first();

            //     if (!$temp) {
            //         throw new \Exception('Invalid uploaded file reference.');
            //     }

            //     $ext = pathinfo($temp->path, PATHINFO_EXTENSION) ?: 'jpg';
            //     $imageName = time() . '_' . uniqid() . '.' . $ext;

            //     // temp is on $temp->disk (example: public), final should be private
            //     $finalDir  = "uploads/guardians/{$guardian->id}/";
            //     $finalPath = $finalDir . $imageName;

            //     // read from temp disk and write to private disk
            //     $contents = Storage::disk($temp->disk)->get($temp->path);
            //     Storage::disk('private')->put($finalPath, $contents);

            //     // cleanup temp
            //     Storage::disk($temp->disk)->delete($temp->path);
            //     $temp->delete();

            //     $guardianDocument = new GuardianDocument();
            //     $guardianDocument->guardian_id = $guardian->id;
            //     $guardianDocument->disk = 'private';
            //     $guardianDocument->path = $finalPath; // IMPORTANT: save full path including file name
            //     $guardianDocument->original_name = $temp->original_name ?? $imageName;
            //     $guardianDocument->mime = $temp->mime ?? 'image/' . $ext;
            //     $guardianDocument->size = $temp->size ?? strlen($contents);
            //     $guardianDocument->created_by = $user->id;
            //     $guardianDocument->save();

            //     appLog('Profile image moved from temp to private guardian_documents: ' . $finalPath);
            // }


            // $roles = $request->roles;
            $role_id = getRoleIdByLabel('Customer');
            // $roles = ['Customer']; // Customer role ;
            // $roles = [18]; // Customer role ;

            $intRoles = collect([$role_id])->map(function ($role) {
                return (int)$role;
            });

            $user->assignRole($intRoles);

            appLog('Assigning roles: ' . json_encode($intRoles));

            // if ($request->roles) {
            //     $user->assignRole($intRoles);
            // }Cat123456!  

            if (!empty($request->event_id)) {
                // foreach ($request->event_id as $key => $event) {
                //     appLog('Attaching event ID: ' . $event);
                //     appLog('User ID: ' . $user->id);
                //     appLog('Event ID: ' . $event);
                $user->events()->attach($request->event_id);
                // }
            }

            // if (!empty($request->functional_id)) {
            //     foreach ($request->functional_id as $key => $functional) {
            //         appLog('Attaching functional ID: ' . $functional);
            //         appLog('User ID: ' . $user->id);
            //         appLog('Functional ID: ' . $functional);
            //         $user->fa()->attach($request->functional_id[$key]);
            //     }
            // }

            // $this->UtilController->save_files($request, $data->id);

            $notification = array(
                'message'       => 'User created successfully',
                'alert-type'    => 'success'
            );

            if (config('settings.send_notifications')) {
                $eventNames = $user->events()->exists()
                    ? $user->events->pluck('name')->implode(', ')
                    : 'N/A';
                $details = [
                    'name' => $user->name,
                    'email' => $user->email,
                    'password' => $request->password,
                ];
                // Send email notification
                Mail::to($user->email)->send(new AccountCreationMail($details));
                // SendAccountCreationEmailJob::dispatch($details);
            }
            DB::commit();

            return Redirect::route('login')->with($notification);
        } catch (\Exception $e) {
            DB::rollBack();

            appLog('Validation error in AdminController@store: ' . $e->getMessage());
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }

        // Toastr::success('Has been add successfully :)','Success');
        // return redirect()->back()->with($notification);
        //mainProfileStore

    }

    public function msSignUp()
    {
        $events = Event::all();
        $roles = Role::all();
        return view('auth.ms-sign-up', compact('events', 'roles'));
    }

    public function forgotPassword()
    {
        return view('auth.forgot');
    }

    public function submitForgetPasswordForm(Request $request): RedirectResponse
    {
        appLog('inside submitForgetPasswordForm');
        $rules = [
            'email' => 'required|email|exists:users',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return redirect()->back()
                ->withInput()
                ->withErrors($validator);
        }

        $token = sha1(time() . config('global.key'));

        appLog('token: ' . $token);
        try {
            DB::table('password_reset_tokens')->insert([
                'email' => $request->email,
                'token' => $token,
                'created_at' => Carbon::now()
            ]);
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors('A reset password was already sent to your email.  please check your inbox');
            // return $e->getMessage();
        }

        appLog('after insert');

        $content = [
            'token'     => $token,
            'subject'   => 'Tracki: Reset Password Link',
            'url'       => "route('reset.password.get', $token)",
        ];

        Mail::to($request->email)->queue(new SendForgotPasswordMail($content));

        // Mail::send('emails.forgetPassword', ['token' => $token], function($message) use($request){
        //     $message->to($request->email);
        //     $message->subject('Reset Password');
        // });

        return back()->with('message', 'We have e-mailed your password reset link!');
    } //submitForgetPasswordForm

    public function showResetPasswordForm($token): View
    {
        return view('tracki.auth.reset', ['token' => $token]);
    } //showResetPasswordForm

    public function submitResetPasswordForm(Request $request): RedirectResponse
    {
        $rules = [
            'email' => 'required|email|exists:users',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withInput()
                ->withErrors($validator);
        }



        $updatePassword = DB::table('password_reset_tokens')
            ->where([
                'email' => $request->email,
                'token' => $request->token
            ])
            ->first();

        if (!$updatePassword) {
            appLog('update failed');
            return back()->withInput()->withErrors(['error' => 'Invalid token!']);
        }

        $user = User::where('email', $request->email)
            ->update(['password' => Hash::make($request->password)]);

        DB::table('password_reset_tokens')->where(['email' => $request->email])->delete();

        return redirect('/tracki/auth/login')->with('message', 'Your password has been changed!');
    } //submitResetPasswordForm

    public function createUser(Request $request)
    {

        $rules = [
            'username' => 'required|unique:users',
            'password' => 'required|confirmed|min:8|max:16',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            //return ($request->get('password').' - '.$request->get('password_confirmation'));
            //return ($request->input());
            return redirect()->back()
                ->withInput()
                ->withErrors($validator);
        }

        $activate_value = sha1(time() . config('global.key'));

        // $id = Auth::user()->id;
        $data = new User;

        $data->username = $request->username;
        $data->name = $request->name;
        $data->email = $request->email;
        $data->address = $request->address;
        $data->phone = $request->phone;
        $data->department_assignment_id = $request->department_id;
        $data->password = Hash::make($request->password);
        $data->department_assignment_id = $request->department_id;
        $data->functional_area_id = $request->functional_area_id;
        $data->status = 'active';
        $data->role = 'admin';
        $data->address = 'doha';


        $data->save();

        $notification = array(
            'message'       => 'User created successfully',
            'alert-type'    => 'success'
        );

        // Toastr::success('Has been add successfully :)','Success');
        // return redirect()->back()->with($notification);
        return Redirect::route('tracki.auth.signup')->with($notification);
        //mainProfileStore

    }

    public function store(Request $request)
    {

        $rules = [
            'username' => 'required|unique:users',
            'password' => 'required|confirmed|min:8|max:16',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            //return ($request->get('password').' - '.$request->get('password_confirmation'));
            //return ($request->input());
            return redirect()->back()
                ->withInput()
                ->withErrors($validator);
        }

        $activate_value = sha1(time() . config('global.key'));

        // $id = Auth::user()->id;
        $data = new User;

        $data->username = $request->username;
        $data->name = $request->name;
        $data->email = $request->email;
        $data->address = $request->address;
        $data->phone = $request->phone;
        $data->department_assignment_id = $request->department_id;
        $data->password = Hash::make($request->password);
        $data->department_assignment_id = $request->department_id;
        $data->functional_area_id = $request->functional_area_id;
        $data->status = 'active';
        $data->role = 'admin';
        $data->address = 'doha';


        $data->save();

        $notification = array(
            'message'       => 'User created successfully',
            'alert-type'    => 'success'
        );

        // Toastr::success('Has been add successfully :)','Success');
        // return redirect()->back()->with($notification);
        return Redirect::route('tracki.auth.signup')->with($notification);
        //mainProfileStore

    } // store

    public function reportList()
    {
        return view('tracki.report');
    }

    public function userProfile()
    {
        // first get the auth user
        $id = Auth::user()->id;
        $profileData = User::find($id);

        // dd($profileData);

        return view('tracki.profile-view', compact('profileData'));
    }


    public function mainProfileStore(Request $request)
    {

        $id = Auth::user()->id;
        $data = User::find($id);

        $data->username = $request->username;
        $data->name = $request->name;
        $data->email = $request->email;
        $data->address = $request->address;
        $data->phone = $request->phone;
        $data->address = $request->address;

        if ($request->file('photo')) {
            $file = $request->file('photo');
            $filename = rand() . date('ymdHis') . $file->getClientOriginalName();
            $file->move(public_path('upload/admin_images'), $filename);
            $data['photo'] = $filename;
        }

        $data->save();

        $notification = array(
            'message'       => 'Profile updated successfully',
            'alert-type'    => 'success'
        );

        // Toastr::success('Has been add successfully :)','Success');
        return redirect()->back()->with($notification);
    }  //mainProfileStore

    public function getOrderData(Request $request)
    {
        // dd('getPlannerData');
        $draw            = $request->get('draw');
        $start           = $request->get("start");
        $rowPerPage      = $request->get("length"); // total number of rows per page
        $columnIndex_arr = $request->get('order');
        $columnName_arr  = $request->get('columns');
        $order_arr       = $request->get('order');
        $search_arr      = $request->get('search');

        // dd($search_arr);
        appLog($draw . ' ' . $start . ' ' . $rowPerPage . ' ' . $columnIndex_arr . ' ' . $order_arr . ' ' . $search_arr);
        // echo $draw.' '.$start.' '.$rowPerPage;


        $columnIndex     = $columnIndex_arr[0]['column']; // Column index

        // log::debug('colunmIndex: '.$columnIndex);

        $columnName      = $columnName_arr[$columnIndex]['data']; // Column name
        // log::debug('columnName: '.$columnName);

        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue     = $search_arr['value']; // Search value

        $orderDetails = DB::table('order_h');

        $totalRecords = $orderDetails
            ->join('order_item_h', 'order_h.order_id', '=', 'order_item_h.order_id')
            ->join('product', 'order_item_h.product_id', '=', 'product.product_id')
            ->join('project', 'order_h.project_id', '=', 'project.project_id')
            ->select(
                'order_h.order_id',
                'order_item_h.item_order_status',
                'project.project_name',
                'product.product_name as item_name'
            )->count();

        // Log::debug("totalRecords: " . $totalRecords);

        $totalRecordsWithFilter = $orderDetails->where(function ($query) use ($searchValue) {
            $query->join('order_item_h', 'order_h.order_id', '=', 'order_item_h.order_id');
            $query->join('product', 'order_item_h.product_id', '=', 'product.product_id');
            $query->join('project', 'order_h.project_id', '=', 'project.project_id');
            $query->select(
                'order_h.order_id',
                'order_item_h.item_order_status',
                'project.project_name',
                'product.product_name as item_name'
            );
            $query->where('order_h.order_id', 'like', '%' . $searchValue . '%');
            $query->orWhere('item_order_status', 'like', '%' . $searchValue . '%');
            $query->orWhere('project_name', 'like', '%' . $searchValue . '%');
            $query->orWhere('product_name', 'like', '%' . $searchValue . '%');
        })->count();

        // Log::debug("totalRecordsWithFilter: " . $totalRecordsWithFilter);

        $records = $orderDetails->orderBy($columnName, $columnSortOrder)
            ->where(function ($query) use ($searchValue) {
                $query->join('order_item_h', 'order_h.order_id', '=', 'order_item_h.order_id');
                $query->join('product', 'order_item_h.product_id', '=', 'product.product_id');
                $query->join('project', 'order_h.project_id', '=', 'project.project_id');
                $query->select(
                    'order_h.order_id',
                    'order_item_h.item_order_status',
                    'project.project_name',
                    'product.product_name as item_name'
                );
                $query->where('order_h.order_id', 'like', '%' . $searchValue . '%');
                $query->orWhere('item_order_status', 'like', '%' . $searchValue . '%');
                $query->orWhere('project_name', 'like', '%' . $searchValue . '%');
                $query->orWhere('product_name', 'like', '%' . $searchValue . '%');
            })
            ->skip($start)
            ->take($rowPerPage)
            ->get();

        // Log::debug("records: ".$records);

        $data_arr = [];
        // $records = $orderDetails;

        foreach ($records as $key => $record) {

            if ($record->item_order_status == '1') {
                $status = '<td><span class="badge badge-phoenix badge-phoenix-success">Approved</span></td>';
            } else {
                $status = '<td><span class="badge badge-phoenix badge-phoenix-warning">Rejected</span></td>';
            }

            $hidden_id = '<td hidden class="user_id">' . $record->order_id . '</td>';

            $modify = '
                <td class="text-end">
                    <div class="actions">
                        <a href="#" class="btn btn-sm bg-danger-light">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        <a class="btn btn-sm bg-danger-light delete user_id" data-bs-toggle="modal" data-user_id="' . $record->order_id . '" data-bs-target="#plannerDelete">
                        <i class="fa-solid fa-trash"></i>
                        </a>
                    </div>
                </td>
            ';

            $data_arr[] = [
                "order_id"         => $record->order_id,
                "status"        => $status, //$record->item_order_status,
                "project_name"  => $record->project_name,
                "item"          => $record->item_name,
                // "active_flag"       => $status,
                "modify"        => $modify,
            ];
        }

        $response = [
            "draw"                 => intval($draw),
            "iTotalRecords"        => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordsWithFilter,
            "aaData"               => $data_arr
        ];

        // dd(response()->json($response));
        return response()->json($response);
    }  //getPlannerData

    public function getProjectData(Request $request)
    {
        // dd('getPlannerData');
        $draw            = $request->get('draw');
        $start           = $request->get("start");
        $rowPerPage      = $request->get("length"); // total number of rows per page
        $columnIndex_arr = $request->get('order');
        $columnName_arr  = $request->get('columns');
        $order_arr       = $request->get('order');
        $search_arr      = $request->get('search');

        // dd($search_arr);
        appLog($draw . ' ' . $start . ' ' . $rowPerPage . ' ' . $columnIndex_arr . ' ' . $order_arr . ' ' . $search_arr);
        // echo $draw.' '.$start.' '.$rowPerPage;


        $columnIndex     = $columnIndex_arr[0]['column']; // Column index

        // log::debug('colunmIndex: '.$columnIndex);

        $columnName      = $columnName_arr[$columnIndex]['data']; // Column name
        // log::debug('columnName: '.$columnName);

        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue     = $search_arr['value']; // Search value

        $orderDetails = DB::table('order_h');

        $totalRecords = $orderDetails
            ->join('order_item_h', 'order_h.order_id', '=', 'order_item_h.order_id')
            ->join('product', 'order_item_h.product_id', '=', 'product.product_id')
            ->join('project', 'order_h.project_id', '=', 'project.project_id')
            ->select(
                'order_h.order_id',
                'order_item_h.item_order_status',
                'project.project_name',
                'product.product_name as item_name'
            )->count();

        // Log::debug("totalRecords: " . $totalRecords);

        $totalRecordsWithFilter = $orderDetails->where(function ($query) use ($searchValue) {
            $query->join('order_item_h', 'order_h.order_id', '=', 'order_item_h.order_id');
            $query->join('product', 'order_item_h.product_id', '=', 'product.product_id');
            $query->join('project', 'order_h.project_id', '=', 'project.project_id');
            $query->select(
                'order_h.order_id',
                'order_item_h.item_order_status',
                'project.project_name',
                'product.product_name as item_name'
            );
            $query->where('order_h.order_id', 'like', '%' . $searchValue . '%');
            $query->orWhere('item_order_status', 'like', '%' . $searchValue . '%');
            $query->orWhere('project_name', 'like', '%' . $searchValue . '%');
            $query->orWhere('product_name', 'like', '%' . $searchValue . '%');
        })->count();

        // Log::debug("totalRecordsWithFilter: " . $totalRecordsWithFilter);

        $records = $orderDetails->orderBy($columnName, $columnSortOrder)
            ->where(function ($query) use ($searchValue) {
                $query->join('order_item_h', 'order_h.order_id', '=', 'order_item_h.order_id');
                $query->join('product', 'order_item_h.product_id', '=', 'product.product_id');
                $query->join('project', 'order_h.project_id', '=', 'project.project_id');
                $query->select(
                    'order_h.order_id',
                    'order_item_h.item_order_status',
                    'project.project_name',
                    'product.product_name as item_name'
                );
                $query->where('order_h.order_id', 'like', '%' . $searchValue . '%');
                $query->orWhere('item_order_status', 'like', '%' . $searchValue . '%');
                $query->orWhere('project_name', 'like', '%' . $searchValue . '%');
                $query->orWhere('product_name', 'like', '%' . $searchValue . '%');
            })
            ->skip($start)
            ->take($rowPerPage)
            ->get();

        // Log::debug("records: ".$records);

        $data_arr = [];
        // $records = $orderDetails;

        foreach ($records as $key => $record) {

            if ($record->item_order_status == '1') {
                $status = '<td><span class="badge badge-phoenix badge-phoenix-success">Approved</span></td>';
            } else {
                $status = '<td><span class="badge badge-phoenix badge-phoenix-warning">Rejected</span></td>';
            }

            $hidden_id = '<td hidden class="user_id">' . $record->order_id . '</td>';

            $modify = '
                <td class="text-end">
                    <div class="actions">
                        <a href="#" class="btn btn-sm bg-danger-light">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        <a class="btn btn-sm bg-danger-light delete user_id" data-bs-toggle="modal" data-user_id="' . $record->order_id . '" data-bs-target="#plannerDelete">
                        <i class="fa-solid fa-trash"></i>
                        </a>
                    </div>
                </td>
            ';

            $data_arr[] = [
                "order_id"         => $record->order_id,
                "status"        => $status, //$record->item_order_status,
                "project_name"  => $record->project_name,
                "item"          => $record->item_name,
                // "active_flag"       => $status,
                "modify"        => $modify,
            ];
        }

        $response = [
            "draw"                 => intval($draw),
            "iTotalRecords"        => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordsWithFilter,
            "aaData"               => $data_arr
        ];

        // dd(response()->json($response));
        return response()->json($response);
    }  //getPlannerData


}  // end of class
