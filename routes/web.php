<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\MicrosoftController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\SendMailController;
// use App\Http\Controllers\Cms\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Ypi\Setting\EventController;
use App\Http\Controllers\GeneralSettings\AttachmentController;
use App\Http\Controllers\GeneralSettings\EventDocumentController;
use App\Http\Controllers\GeneralSettings\ParticipantDocumentController;
use App\Http\Controllers\GeneralSettings\GuardianDocumentController;
use App\Http\Controllers\GeneralSettings\UploadController;
use App\Http\Controllers\Ypi\Admin\AccommodationController;
use App\Http\Controllers\Ypi\Admin\FlightController;
use App\Http\Controllers\Ypi\Admin\GuestController;
use App\Http\Controllers\Ypi\Setting\AirlineController;
use App\Http\Controllers\Ypi\Setting\AirportController;
use App\Http\Controllers\Ypi\Setting\CabinTypeController;
use App\Http\Controllers\Ypi\Setting\ClientGroupController;
use App\Http\Controllers\Ypi\Setting\DesignationController;
use App\Http\Controllers\Ypi\Setting\FlightStatusController;
use App\Http\Controllers\Ypi\Setting\FlightTypeController;
use App\Http\Controllers\Ypi\Setting\ParticipantTypeController;
use App\Http\Controllers\Ypi\Setting\HostedByController;
use App\Http\Controllers\Ypi\Setting\NationalityController;
use App\Http\Controllers\Vapp\Setting\FunctionalAreaController;

// use App\Http\Controllers\Mds\Admin\DashboardController;
use App\Http\Controllers\Security\ActivityAuditController;
use App\Http\Controllers\Security\RoleController as SecurityRoleController;
use App\Http\Controllers\Ypi\Admin\UserController as AdminUserController;

use App\Http\Controllers\UserController;
use App\Http\Controllers\Ypi\Setting\VenueController;
use App\Http\Controllers\UtilController;
use App\Http\Controllers\Vapp\Admin\BookingController;
use App\Http\Controllers\Ypi\Auth\AdminController as GmsAuthAdminController;
use App\Http\Controllers\Ypi\Customer\GuardianController;
use App\Http\Controllers\Ypi\Customer\GuestController as CustomerGuestController;
use App\Http\Controllers\Ypi\Setting\SizeController;
use App\Http\Controllers\Vapp\Customer\BookingController as CustomerBookingController;
use App\Http\Controllers\Vapp\Operator\BookingController as OperatorBookingController;
use App\Http\Controllers\Ypi\Setting\AppSettingController;

use App\Http\Controllers\Ypi\Setting\EventImageController;
use App\Models\Ypi\Participant;
use App\Models\Ypi\ParticipantDocument;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Guard;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     if (auth()->check()) {
//         if (auth()->user()->is_admin) {
//             return redirect()->route('vapp.admin');
//         } elseif (auth()->user()->hasRole('Customer')) {
//             appLog('Redirecting to vapp.customer');
//             return redirect()->route('vapp.customer');
//         }
//     } else {
//         return redirect()->route('login');
//     }
// })->name('home');

// Route::get('/debug', function () {
//     return [
//         'scheme' => request()->getScheme(),
//         'host'   => request()->getHost(),
//         'url'    => request()->fullUrl(),
//         'headers'=> request()->headers->all(),
//     ];
// });

Route::get('/', function () {
    appLog('In home route');
    if (!auth()->check()) {
        appLog('User is not authenticated');
        return redirect()->route('login');
    }

    $roleRoutes = [
        'SuperAdmin' => 'ypi.admin.participant',
        'Customer'   => 'ypi.customer',
    ];

    foreach ($roleRoutes as $role => $route) {
        if (auth()->user()->hasRole($role)) {
            appLog("Redirecting to $route for role $role");
            return redirect()->route($route);
        }
    }

    abort(403, 'Unauthorized role');
})->name('home');


Route::controller(MicrosoftController::class)->group(function () {
    Route::get('auth/microsoft', 'redirectToMicrosoft')->name('auth.microsoft');
    Route::get('auth/microsoft/callback', 'handleMicrosoftCallback');
});

// Image Uploader
Route::post('/uploads/process', [UploadController::class, 'process'])->name('uploads.process');
Route::delete('/uploads/revert', [UploadController::class, 'revert'])->name('uploads.revert');

Route::group(['middleware' => 'prevent-back-history', 'XssSanitizer'], function () {


    // Email Templates
    Route::middleware(['auth', 'otp', 'mutli.event', 'XssSanitizer', 'role:SuperAdmin|SuperMDS', 'prevent-back-history', 'auth.session'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::get('email-templates', [EmailTemplateController::class, 'index'])
                ->name('email-templates.index');

            Route::get('email-templates/list', [EmailTemplateController::class, 'list'])
                ->name('email-templates.list');

            Route::post('email-templates/{template}/toggle', [EmailTemplateController::class, 'toggle'])
                ->name('email-templates.toggle');

            Route::post('email-templates/{template}/preview', [EmailTemplateController::class, 'preview'])
                ->name('email-templates.preview');

            Route::post('email-templates/{template}/send-test', [EmailTemplateController::class, 'sendTest'])
                ->name('email-templates.sendTest');

            Route::get('email-templates/{template}/edit', [EmailTemplateController::class, 'edit'])
                ->name('email-templates.edit');

            Route::delete('email-templates/{template}', [EmailTemplateController::class, 'destroy'])
                ->name('email-templates.destroy');
        });
});


// Booking MANAGEMENT ******************************************************************** Admin All Route
Route::middleware(['auth', 'otp', 'mutli.event', 'XssSanitizer', 'role:SuperAdmin|SuperMDS', 'prevent-back-history', 'auth.session'])->group(function () {

    Route::controller(DashboardController::class)->group(function () {
        Route::get('/mds/admin/dashboard', 'dashboard')->name('mds.admin.dashboard');
    });


    // GMS
    Route::controller(GuestController::class)->group(function () {

        Route::post('/ypi/admin/participant/status/update', 'updateStatus')->name('ypi.admin.participant.status.update');
        Route::get('/ypi/admin/participant/status/get/{id}', 'getStatus')->name('ypi.admin.participant.status.get');
        Route::get('/ypi/admin/participant/create', function () {
            return view('/ypi/admin/participant/createme');
        })->name('ypi.admin.participant.create');

        // for event switching
        Route::get('/vapp/admin/events/{id}/switch',  'switch')->name('ypi.admin.booking.switch');
        Route::get('/vapp/admin/dashboard', 'dashboard')->name('ypi.admin.dashboard');

        // test dynamic email
        Route::get('/ypi/admin/participant/test-email', [SendMailController::class, 'testDynamicEmail'])->name('ypi.admin.participant.test.email');

        // guest routes
        Route::get('/ypi/admin/participant', 'index')->name('ypi.admin.participant');
        Route::get('/ypi/admin/participant/list', 'list')->name('ypi.admin.participant.list');
        Route::get('/ypi/admin/participant/detail/{id}', 'detail')->name('ypi.admin.participant.detail');
        Route::post('/ypi/admin/participant/store', 'store')->name('ypi.admin.participant.store');
        Route::post('ypi/admin/participant/update', 'update')->name('ypi.admin.participant.update');
        Route::delete('/ypi/admin/participant/delete/{id}', 'destroy')->name('ypi.admin.participant.delete');
        Route::get('/ypi/admin/participant/get/{id}', 'get')->name('ypi.admin.participant.get');
        Route::get('/ypi/admin/participant/mv/get/{id}', 'getView')->name('ypi.admin.participant.get.mv');


        //Booking note
        Route::get('/mds/admin/booking/mv/notes/{id}', 'getNotesView')->name('mds.admin.booking.mv.notes');
        Route::post('mds/admin/booking/note/store', 'noteStore')->name('mds.admin.booking.note.store');
        Route::delete('mds/admin/booking/note/delete/{id}', 'deleteNote')->name('mds.admin.booking.note.delete');

        //Booking file upload
        Route::post('mds/admin/booking/file/store', 'fileStore')->name('mds.admin.booking.file.store');
        Route::delete('mds/admin/booking/file/{id}/delete', 'fileDelete')->name('mds.admin.booking.file.delete');
    });

    Route::controller(ParticipantTypeController::class)->group(function () {
        Route::get('/ypi/setting/participant_type', 'index')->name('ypi.setting.participant_type');
        Route::get('/ypi/setting/participant_type/list', 'list')->name('ypi.setting.participant_type.list');
        Route::get('/ypi/setting/participant_type/get/{id}', 'get')->name('ypi.setting.participant_type.get');
        Route::post('ypi/setting/participant_type/update', 'update')->name('ypi.setting.participant_type.update');
        Route::delete('/ypi/setting/participant_type/delete/{id}', 'delete')->name('ypi.setting.participant_type.delete');
        Route::post('/ypi/setting/participant_type/store', 'store')->name('ypi.setting.participant_type.store');
        Route::get('/ypi/setting/participant_type/mv/get/{id}', 'getEventView')->name('ypi.setting.participant_type.get.mv');
        // Route::get('/mds/setting/event/file/{file}', 'getPrivateFile')->name('mds.setting.event.file');
    });

    // Route::controller(ClientGroupController::class)->group(function () {
    //     Route::get('/ypi/setting/client_group', 'index')->name('ypi.setting.client_group');
    //     Route::get('/ypi/setting/client_group/list', 'list')->name('ypi.setting.client_group.list');
    //     Route::get('/ypi/setting/client_group/get/{id}', 'get')->name('ypi.setting.client_group.get');
    //     Route::post('ypi/setting/client_group/update', 'update')->name('ypi.setting.client_group.update');
    //     Route::delete('/ypi/setting/client_group/delete/{id}', 'delete')->name('ypi.setting.client_group.delete');
    //     Route::post('/ypi/setting/client_group/store', 'store')->name('ypi.setting.client_group.store');
    //     Route::get('/ypi/setting/client_group/mv/get/{id}', 'getEventView')->name('ypi.setting.client_group.get.mv');
    //     // Route::get('/mds/setting/event/file/{file}', 'getPrivateFile')->name('mds.setting.event.file');
    // });

    // Route::controller(DesignationController::class)->group(function () {
    //     Route::get('/ypi/setting/designation', 'index')->name('ypi.setting.designation');
    //     Route::get('/ypi/setting/designation/list', 'list')->name('ypi.setting.designation.list');
    //     Route::get('/ypi/setting/designation/get/{id}', 'get')->name('ypi.setting.designation.get');
    //     Route::post('ypi/setting/designation/update', 'update')->name('ypi.setting.designation.update');
    //     Route::delete('/ypi/setting/designation/delete/{id}', 'delete')->name('ypi.setting.designation.delete');
    //     Route::post('/ypi/setting/designation/store', 'store')->name('ypi.setting.designation.store');
    //     Route::get('/ypi/setting/designation/mv/get/{id}', 'getEventView')->name('ypi.setting.designation.get.mv');
    //     // Route::get('/mds/setting/event/file/{file}', 'getPrivateFile')->name('mds.setting.event.file');
    // });

    Route::controller(NationalityController::class)->group(function () {
        Route::get('/ypi/setting/nationality', 'index')->name('ypi.setting.nationality');
        Route::get('/ypi/setting/nationality/list', 'list')->name('ypi.setting.nationality.list');
        Route::get('/ypi/setting/nationality/get/{id}', 'get')->name('ypi.setting.nationality.get');
        Route::post('ypi/setting/nationality/update', 'update')->name('ypi.setting.nationality.update');
        Route::delete('/ypi/setting/nationality/delete/{id}', 'delete')->name('ypi.setting.nationality.delete');
        Route::post('/ypi/setting/nationality/store', 'store')->name('ypi.setting.nationality.store');
        Route::get('/ypi/setting/nationality/mv/get/{id}', 'getEventView')->name('ypi.setting.nationality.get.mv');
        // Route::get('/mds/setting/event/file/{file}', 'getPrivateFile')->name('mds.setting.event.file');
    });

    Route::controller(SizeController::class)->group(function () {
        Route::get('/ypi/setting/sizes', 'index')->name('ypi.setting.sizes');
        Route::get('/ypi/setting/sizes/list', 'list')->name('ypi.setting.sizes.list');
        Route::get('/ypi/setting/sizes/get/{id}', 'get')->name('ypi.setting.sizes.get');
        Route::post('ypi/setting/sizes/update', 'update')->name('ypi.setting.sizes.update');
        Route::delete('/ypi/setting/sizes/delete/{id}', 'delete')->name('ypi.setting.sizes.delete');
        Route::post('/ypi/setting/sizes/store', 'store')->name('ypi.setting.sizes.store');
        Route::get('/ypi/setting/sizes/mv/get/{id}', 'getEventView')->name('ypi.setting.sizes.get.mv');
        // Route::get('/mds/setting/event/file/{file}', 'getPrivateFile')->name('mds.setting.event.file');
    });

    // Route::controller(HostedByController::class)->group(function () {
    //     Route::get('/ypi/setting/hosted_by', 'index')->name('ypi.setting.hosted_by');
    //     Route::get('/ypi/setting/hosted_by/list', 'list')->name('ypi.setting.hosted_by.list');
    //     Route::get('/ypi/setting/hosted_by/get/{id}', 'get')->name('ypi.setting.hosted_by.get');
    //     Route::post('ypi/setting/hosted_by/update', 'update')->name('ypi.setting.hosted_by.update');
    //     Route::delete('/ypi/setting/hosted_by/delete/{id}', 'delete')->name('ypi.setting.hosted_by.delete');
    //     Route::post('/ypi/setting/hosted_by/store', 'store')->name('ypi.setting.hosted_by.store');
    //     Route::get('/ypi/setting/hosted_by/mv/get/{id}', 'getEventView')->name('ypi.setting.hosted_by.get.mv');
    //     // Route::get('/mds/setting/event/file/{file}', 'getPrivateFile')->name('mds.setting.event.file');
    // });

    // // Flight
    // Route::controller(FlightController::class)->group(function () {
    //     Route::get('/ypi/admin/flight', 'index')->name('ypi.admin.flight');
    //     Route::get('/ypi/admin/flight/list', 'list')->name('ypi.admin.flight.list');
    //     Route::post('/ypi/admin/flight/store', 'store')->name('ypi.admin.flight.store');
    //     Route::get('/ypi/admin/flight/detail/{id}', 'detail')->name('ypi.admin.flight.detail');
    // });

    // Route::controller(FlightStatusController::class)->group(function () {
    //     Route::get('/ypi/setting/flight_status', 'index')->name('ypi.setting.flight_status');
    //     Route::get('/ypi/setting/flight_status/list', 'list')->name('ypi.setting.flight_status.list');
    //     Route::get('/ypi/setting/flight_status/get/{id}', 'get')->name('ypi.setting.flight_status.get');
    //     Route::post('ypi/setting/flight_status/update', 'update')->name('ypi.setting.flight_status.update');
    //     Route::delete('/ypi/setting/flight_status/delete/{id}', 'delete')->name('ypi.setting.flight_status.delete');
    //     Route::post('/ypi/setting/flight_status/store', 'store')->name('ypi.setting.flight_status.store');
    //     Route::get('/ypi/setting/flight_status/mv/get/{id}', 'getEventView')->name('ypi.setting.flight_status.get.mv');
    //     // Route::get('/mds/setting/event/file/{file}', 'getPrivateFile')->name('mds.setting.event.file');
    // });

    // Route::controller(AirlineController::class)->group(function () {
    //     Route::get('/ypi/setting/airline', 'index')->name('ypi.setting.airline');
    //     Route::get('/ypi/setting/airline/list', 'list')->name('ypi.setting.airline.list');
    //     Route::get('/ypi/setting/airline/get/{id}', 'get')->name('ypi.setting.airline.get');
    //     Route::post('ypi/setting/airline/update', 'update')->name('ypi.setting.airline.update');
    //     Route::delete('/ypi/setting/airline/delete/{id}', 'delete')->name('ypi.setting.airline.delete');
    //     Route::post('/ypi/setting/airline/store', 'store')->name('ypi.setting.airline.store');
    //     Route::get('/ypi/setting/airline/mv/get/{id}', 'getEventView')->name('ypi.setting.airline.get.mv');
    //     // Route::get('/mds/setting/event/file/{file}', 'getPrivateFile')->name('mds.setting.event.file');
    // });

    // Route::controller(CabinTypeController::class)->group(function () {
    //     Route::get('/ypi/setting/cabin_type', 'index')->name('ypi.setting.cabin_type');
    //     Route::get('/ypi/setting/cabin_type/list', 'list')->name('ypi.setting.cabin_type.list');
    //     Route::get('/ypi/setting/cabin_type/get/{id}', 'get')->name('ypi.setting.cabin_type.get');
    //     Route::post('ypi/setting/cabin_type/update', 'update')->name('ypi.setting.cabin_type.update');
    //     Route::delete('/ypi/setting/cabin_type/delete/{id}', 'delete')->name('ypi.setting.cabin_type.delete');
    //     Route::post('/ypi/setting/cabin_type/store', 'store')->name('ypi.setting.cabin_type.store');
    //     Route::get('/ypi/setting/cabin_type/mv/get/{id}', 'getEventView')->name('ypi.setting.cabin_type.get.mv');
    //     // Route::get('/mds/setting/event/file/{file}', 'getPrivateFile')->name('mds.setting.event.file');
    // });

    // Route::controller(FlightTypeController::class)->group(function () {
    //     Route::get('/ypi/setting/flight_type', 'index')->name('ypi.setting.flight_type');
    //     Route::get('/ypi/setting/flight_type/list', 'list')->name('ypi.setting.flight_type.list');
    //     Route::get('/ypi/setting/flight_type/get/{id}', 'get')->name('ypi.setting.flight_type.get');
    //     Route::post('ypi/setting/flight_type/update', 'update')->name('ypi.setting.flight_type.update');
    //     Route::delete('/ypi/setting/flight_type/delete/{id}', 'delete')->name('ypi.setting.flight_type.delete');
    //     Route::post('/ypi/setting/flight_type/store', 'store')->name('ypi.setting.flight_type.store');
    //     Route::get('/ypi/setting/flight_type/mv/get/{id}', 'getEventView')->name('ypi.setting.flight_type.get.mv');
    //     // Route::get('/mds/setting/event/file/{file}', 'getPrivateFile')->name('mds.setting.event.file');
    // });

    // Route::controller(AirportController::class)->group(function () {
    //     Route::get('/ypi/setting/airport', 'index')->name('ypi.setting.airport');
    //     Route::get('/ypi/setting/airport/list', 'list')->name('ypi.setting.airport.list');
    //     Route::get('/ypi/setting/airport/get/{id}', 'get')->name('ypi.setting.airport.get');
    //     Route::post('ypi/setting/airport/update', 'update')->name('ypi.setting.airport.update');
    //     Route::delete('/ypi/setting/airport/delete/{id}', 'delete')->name('ypi.setting.airport.delete');
    //     Route::post('/ypi/setting/airport/store', 'store')->name('ypi.setting.airport.store');
    //     Route::get('/ypi/setting/airport/mv/get/{id}', 'getEventView')->name('ypi.setting.airport.get.mv');
    //     // Route::get('/mds/setting/event/file/{file}', 'getPrivateFile')->name('mds.setting.event.file');
    // });

    // // Accomodation
    // Route::controller(AccommodationController::class)->group(function () {
    //     Route::get('/ypi/admin/accomm', 'index')->name('ypi.admin.accomm');
    //     Route::get('/ypi/admin/accomm/list', 'list')->name('ypi.admin.accomm.list');
    //     Route::post('/ypi/admin/accomm/store', 'store')->name('ypi.admin.accomm.store');
    // });

    //     // Venue
    Route::controller(VenueController::class)->group(function () {
        Route::get('/ypi/setting/venue', 'index')->name('ypi.setting.venue');
        Route::get('/ypi/setting/venue/list', 'list')->name('ypi.setting.venue.list');
        Route::get('/ypi/setting/venue/get/{id}', 'get')->name('ypi.setting.venue.get');
        Route::post('ypi/setting/venue/update', 'update')->name('ypi.setting.venue.update');
        Route::delete('/ypi/setting/venue/delete/{id}', 'delete')->name('ypi.setting.venue.delete');
        Route::post('/ypi/setting/venue/store', 'store')->name('ypi.setting.venue.store');
    });

    // // Functional Area
    // Route::controller(FunctionalAreaController::class)->group(function () {
    //     Route::get('/ypi/setting/funcareas', 'index')->name('ypi.setting.funcareas');
    //     Route::get('/ypi/setting/funcareas/list', 'list')->name('ypi.setting.funcareas.list');
    //     Route::get('/ypi/setting/funcareas/get/{id}', 'get')->name('ypi.setting.funcareas.get');
    //     Route::post('ypi/setting/funcareas/update', 'update')->name('ypi.setting.funcareas.update');
    //     Route::delete('/ypi/setting/funcareas/delete/{id}', 'delete')->name('ypi.setting.funcareas.delete');
    //     Route::post('/ypi/setting/funcareas/store', 'store')->name('ypi.setting.funcareas.store');
    // });

    //Event
    Route::controller(EventController::class)->group(function () {
        Route::get('/ypi/setting/event', 'index')->name('ypi.setting.event');
        Route::get('/ypi/setting/event/list', 'list')->name('ypi.setting.event.list');
        Route::get('/ypi/setting/event/get/{id}', 'get')->name('ypi.setting.event.get');
        Route::post('ypi/setting/event/update', 'update')->name('ypi.setting.event.update');
        Route::delete('/ypi/setting/event/delete/{id}', 'delete')->name('ypi.setting.event.delete');
        Route::post('/ypi/setting/event/store', 'store')->name('ypi.setting.event.store');
    });

    Route::get('/auth/ms-signup', [GmsAuthAdminController::class, 'msSignUp'])->name('auth.ms.signup');
    Route::post('/signup/ms/store', [UserController::class, 'msStore'])->name('admin.signup.ms.store');

    Route::controller(AdminUserController::class)->group(function () {
        Route::get('/vapp/admin/users/profile', 'profile')->name('vapp.admin.users.profile');
        Route::post('/vapp/admin/users/profile/update', 'update')->name('vapp.admin.users.profile.update');
        Route::post('/vapp/admin/users/profile/password/update', 'updatePassword')->name('vapp.admin.users.profile.password.update');
        Route::get('/ypi/admin/users/invite-user', 'showForm')->name('vapp.admin.users.invite.form');
        Route::post('/ypi/invite-user', 'sendInvite')->name('ypi.admin.users.invite.send');
    });

    //Applicaiton Setting
    Route::controller(AppSettingController::class)->group(function () {
        Route::get('/ypi/setting/application', 'index')->name('ypi.setting.application');
        Route::get('/ypi/setting/application/list', 'list')->name('ypi.setting.application.list');
        Route::get('/ypi/setting/application/get/{id}', 'get')->name('ypi.setting.application.get');
        Route::post('ypi/setting/application/update', 'update')->name('ypi.setting.application.update');
        Route::delete('/ypi/setting/application/delete/{id}', 'delete')->name('ypi.setting.application.delete');
        Route::post('/ypi/setting/application/store', 'store')->name('ypi.setting.application.store');
    });

    // // Event Image
    // Route::controller(EventImageController::class)->group(function () {
    //     Route::get('/ypi/setting/event/file/{id}', 'getPrivateFile')->name('ypi.setting.event.file');
    // });

    // // Event Image
    // Route::controller(EventImageController::class)->group(function () {
    //     Route::get('/ypi/private/file/{id}', 'getPrivateFile')->name('ypi.private.file.show');
    // });


    // docs
    Route::get('/event/docs/{document}/download', [EventDocumentController::class, 'download'])
        ->name('event.docs.download');

    Route::delete('/event/docs/{document}', [EventDocumentController::class, 'destroy'])
        ->name('event.docs.destroy');

    Route::controller(AdminUserController::class)->group(function () {
        Route::get('/vapp/admin/users/profile', 'profile')->name('admin.users.profile');
        Route::post('/vapp/admin/users/profile/update', 'update')->name('admin.users.profile.update');
        Route::post('/vapp/admin/users/profile/password/update', 'updatePassword')->name('admin.users.profile.password.update');
        Route::get('/ypi/admin/users/invite-user', 'showForm')->name('admin.users.invite.form');
        Route::post('/vapp/invite-user', 'sendInvite')->name('admin.users.invite.send');
    });
});



// shared routes between SuperAdmin and Customer
Route::middleware(['auth', 'otp', 'mutli.event', 'XssSanitizer', 'firstlogin', 'role:SuperAdmin|Customer',  'prevent-back-history', 'auth.session'])->group(function () {
    // docs
    Route::get('/participant/docs/{document}/download', [ParticipantDocumentController::class, 'download'])
        ->name('participant.docs.download');

    // Route::get('/participant/docs/{id}/view', [ParticipantDocumentController::class, 'view'])
    //     ->name('participant.docs.view');

    Route::middleware('auth')->get('/participant/docs/view/{id}', function ($id) {
    $doc = ParticipantDocument::findOrFail($id);

    abort_unless(Storage::disk($doc->disk)->exists($doc->path), 404);

    return response(
        Storage::disk($doc->disk)->get($doc->path),
        200,
        [
            'Content-Type' => Storage::disk($doc->disk)->mimeType($doc->path),
            'Content-Disposition' => 'inline; filename="'.basename($doc->path).'"',
        ]
    );
})->name('participant.docs.view');

    Route::delete('/participant/docs/{document}', [ParticipantDocumentController::class, 'destroy'])
        ->name('participant.docs.destroy');

    // Event Image
    Route::controller(EventImageController::class)->group(function () {
        Route::get('/ypi/setting/event/file/{id}', 'getPrivateFile')->name('ypi.setting.event.file');
    });

    Route::get('/guardian/docs/{document}/download', [GuardianDocumentController::class, 'download'])
        ->name('guardian.docs.download');

    Route::delete('/guardian/docs/{document}', [GuardianDocumentController::class, 'destroy'])
        ->name('guardian.docs.destroy');
});

Route::middleware(['auth', 'otp', 'mutli.event', 'XssSanitizer', 'firstlogin', 'role:Customer',  'prevent-back-history', 'auth.session'])->group(function () {

    Route::controller(GuardianController::class)->group(function () {
        Route::get('/ypi/customer', 'index')->name('ypi.customer');
        Route::get('/ypi/customer/guardian', 'index')->name('ypi.customer.guardian');
        Route::get('/ypi/customer/guardian/list', 'list')->name('ypi.customer.guardian.list');
        Route::get('/ypi/customer/guardian/create', 'create')->name('ypi.customer.guardian.create');
        Route::get('/ypi/customer/guardian/get/{id}', 'get')->name('ypi.customer.guardian.get');
        Route::post('/ypi/customer/guardian/update', 'update')->name('ypi.customer.guardian.update');
        Route::delete('/ypi/customer/guardian/delete/{id}', 'delete')->name('ypi.customer.guardian.delete');
        Route::post('/ypi/customer/guardian/store', 'store')->name('ypi.customer.guardian.store');
        Route::get('/ypi/customer/participant/create', 'create')->name('ypi.customer.participant.create');
        Route::get('/ypi/customer/participant/edit/{id}', 'edit')->name('ypi.customer.participant.edit');

        // for event switching
        Route::get('/ypi/customer/events/{id}/switch',  'switch')->name('ypi.customer.guardian.switch');
        // Route::get('/ypi/customer/dashboard', 'dashboard')->name('ypi.customer.dashboard');
    });
});


// Customer Pick an event
Route::get('/ypi/customer/guardian/pick', function () {
    return view('/ypi/customer/guardian/pick');
})->name('ypi.customer.guardian.pick')->middleware('role:Customer');
Route::post('/ypi/customer/events/switch', [GuardianController::class, 'pickEvent'])->name('ypi.customer.guardian.event.switch')->middleware('role:Customer');

Route::get('/ypi/logout', [GmsAuthAdminController::class, 'logout'])->name('ypi.logout');


Route::middleware(['auth', 'otp', 'mutli.event', 'XssSanitizer', 'firstlogin', 'role:Customer',  'prevent-back-history', 'auth.session'])->group(function () {

    // Route::controller(DashboardController::class)->group(function () {
    //     Route::get('/cms/admin/dashboard', 'dashboard')->name('cms.admin.dashboard');
    // });

    Route::controller(CustomerBookingController::class)->group(function () {
        Route::get('/vapp/customer', 'index')->name('vapp.customer');
        Route::get('/vapp/customer/booking', 'index')->name('vapp.customer.booking');
        Route::get('/vapp/customer/booking/list', 'list')->name('vapp.customer.booking.list');
        Route::get('/vapp/customer/booking/create', 'create')->name('vapp.customer.booking.create');
        Route::delete('/vapp/customer/booking/delete/{id}', 'delete')->name('vapp.customer.booking.delete');
        Route::post('/vapp/customer/request/store', 'store')->name('vapp.customer.request.store');

        // for event switching
        Route::get('/vapp/customer/events/{id}/switch',  'switch')->name('vapp.customer.booking.switch');
        // Route::get('/vapp/customer/dashboard', 'dashboard')->name('vapp.customer.dashboard');
    });
});
// Route::middleware(['auth', 'otp', 'mutli.event', 'XssSanitizer', 'firstlogin', 'role:Operator',  'prevent-back-history', 'auth.session'])->group(function () {

//     Route::controller(OperatorBookingController::class)->group(function () {
//         Route::get('/vapp/operator', 'index')->name('vapp.operator');
//         Route::get('/vapp/operator/booking', 'index')->name('vapp.operator.booking');
//         Route::get('/vapp/operator/booking/list', 'list')->name('vapp.operator.booking.list');
//         Route::post('/vapp/operator/rfc/status', 'updateStatus')->name('vapp.operator.rfc.status');
//         Route::post('/generate-pdf', 'generate')->name('vapp.pdf.receipt');
//         Route::post('/mark-as-collected', 'markAsCollected')->name('vapp.mark.collected');

//         // for event switching
//         Route::get('/vapp/operator/events/{id}/switch',  'switch')->name('vapp.operator.booking.switch');
//         // Route::get('/vapp/operator/dashboard', 'dashboard')->name('vapp.operator.dashboard');
//     });
// });
// });


// ****************** ADMIN *********************
Route::group(['middleware' => 'prevent-back-history'], function () {

    // Add User
    Route::get('/ypi/auth/signup', [GmsAuthAdminController::class, 'signUp'])->name('auth.signup')->middleware('signed');
    Route::post('/signup/store', [UserController::class, 'store'])->name('admin.signup.store');

    // Add User
    Route::get('/register/{event_id}', [GmsAuthAdminController::class, 'register'])->name('auth.register');
    Route::post('/register/store', [GmsAuthAdminController::class, 'storeRegister'])->name('admin.register.store');

    Route::middleware(['auth', 'prevent-back-history'])->group(function () {

        Route::get('auth/otp', [GmsAuthAdminController::class, 'showOtp'])->name('otp.get');
        Route::post('verify-otp', [GmsAuthAdminController::class, 'verifyOtpAndLogin'])->name('auth.otp.post');
        Route::get('auth/resend', [GmsAuthAdminController::class, 'resendOTP'])->name('otp.resend.get');

        //used to show images in private folder
        Route::get('/doc/{file}', [UtilController::class, 'showImage'])->name('a');

        /*************************************** Play ground */
        // Route::get('/a/{GlobalAttachment}', [UtilController::class, 'serve'])->name('a');
        Route::get('/doc/{file}', [UtilController::class, 'showImage'])->name('a');
        Route::get('/a', function () {
            return response()->file(storage_path('app/private/users/502828276250308124600avatar-2.png'));
        })->name('b');
        /*************************************** End Play ground */

        // Admin Booking Pick an event
        Route::get('/vapp/admin/booking/pick', function () {
            return view('/vapp/admin/booking/pick');
        })->name('vapp.admin.booking.pick')->middleware('role:SuperAdmin');
        Route::post('/vapp/admin/events/switch', [BookingController::class, 'pickEvent'])->name('vapp.admin.booking.event.switch')->middleware('role:SuperAdmin');

        // Customer Booking Pick an event
        Route::get('/vapp/customer/booking/pick', function () {
            return view('/vapp/customer/booking/pick');
        })->name('vapp.customer.booking.pick')->middleware('role:Customer');
        Route::post('/vapp/customer/events/switch', [CustomerBookingController::class, 'pickEvent'])->name('vapp.customer.booking.event.switch')->middleware('role:Customer');

        // Operator Booking Pick an event
        Route::get('/vapp/operator/booking/pick', function () {
            return view('/vapp/operator/booking/pick');
        })->name('vapp.operator.booking.pick')->middleware('role:Operator');
        Route::post('/vapp/operator/events/switch', [OperatorBookingController::class, 'pickEvent'])->name('vapp.operator.booking.event.switch')->middleware('role:Operator');


        Route::get('/vapp/logout', [GmsAuthAdminController::class, 'logout'])->name('vapp.logout');
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
    });

    require __DIR__ . '/auth.php';

    Route::middleware(['prevent-back-history'])->group(function () {
        Route::get('/auth/forgot', [AdminController::class, 'forgotPassword'])->name('auth.forgot');
        Route::post('forget-password', [AdminController::class, 'submitForgetPasswordForm'])->name('forgot.password.post');
        Route::get('/auth/reset/{token}', [AdminController::class, 'showResetPasswordForm'])->name('reset.password.get');
        Route::post('reset-password', [AdminController::class, 'submitResetPasswordForm'])->name('reset.password.post');
        Route::get('/send-mail', [SendMailController::class, 'index']);
    });

    Route::middleware(['auth', 'otp', 'XssSanitizer', 'role:SuperAdmin', 'prevent-back-history', 'auth.session'])->group(function () {

        Route::controller(SecurityRoleController::class)->group(function () {
            //Admin User
            Route::get('/sec/adminuser/list', 'listAdminUser')->name('sec.adminuser.list');
            Route::post('updateadminuser', 'updateAdminUser')->name('sec.adminuser.update');
            Route::post('createadminuser', 'createAdminUser')->name('sec.adminuser.create');
            Route::get('/sec/adminuser/{id}/edit', 'editAdminUser')->name('sec.adminuser.edit');
            Route::get('/sec/adminuser/{id}/delete', 'deleteAdminUser')->name('sec.adminuser.delete');
            Route::get('/sec/adminuser/add', 'addAdminUser')->name('sec.adminuser.add');
            Route::get('/sec/adminuser/add2', 'addAdminUser2')->name('sec.adminuser.add2');
        });
    });

    // HR Security Settings all routes
    Route::middleware(['auth', 'otp', 'XssSanitizer', 'role:SecurityRole', 'prevent-back-history', 'auth.session'])->group(function () {

        Route::controller(ActivityAuditController::class)->group(function () {
            Route::get('/sec/audit', 'index')->name('sec.audit');
            Route::get('/sec/audit/list', 'list')->name('sec.audit.list');
        });
        // Roles
        Route::controller(SecurityRoleController::class)->group(function () {

            Route::get('/sec/roles/add', function () {
                return view('/sec/roles/add');
            })->name('sec.roles.add');
            Route::get('/sec/roles/roles/list', 'listRole')->name('sec.roles.list');
            Route::post('updaterole', 'updateRole')->name('sec.roles.update');
            Route::post('createrole', 'createRole')->name('sec.roles.create');
            Route::get('/sec/roles/{id}/edit', 'editRole')->name('sec.roles.edit');
            Route::get('/sec/roles/{id}/delete', 'deleteRole')->name('sec.roles.delete');

            // group
            Route::get('/sec/groups/add', function () {
                return view('/sec/groups/add');
            })->name('sec.groups.add');
            Route::get('/sec/groups/list', 'listGroup')->name('sec.groups.list');
            Route::post('updategroup', 'updateGroup')->name('sec.groups.update');
            Route::post('creategroup', 'createGroup')->name('sec.groups.create');
            Route::get('/sec/groups/{id}/edit', 'editGroup')->name('sec.groups.edit');
            Route::get('/sec/groups/{id}/delete', 'deleteGroup')->name('sec.groups.delete');

            // Permission
            Route::get('/sec/permissions/list', 'listPermission')->name('sec.perm.list');
            Route::post('updatepermission', 'updatePermission')->name('sec.perm.update');
            Route::post('createpermission', 'createPermission')->name('sec.perm.create');
            Route::get('/sec/perm/{id}/edit', 'editPermission')->name('sec.perm.edit');
            Route::get('/sec/perm/{id}/delete', 'deletePermission')->name('sec.perm.delete');
            Route::get('/sec/permissions/add', 'addPermission')->name('sec.perm.add');

            Route::get('/sec/perm/import', 'ImportPermission')->name('sec.perm.import');
            Route::post('importnow', 'ImportNowPermission')->name('sec.perm.import.now');


            // Roles in Permission
            Route::get('/sec/rolesetup/list', 'listRolePermission')->name('sec.rolesetup.list');
            Route::post('updaterolesetup', 'updateRolePermission')->name('sec.rolesetup.update');
            Route::post('createrolesetup', 'createRolePermission')->name('sec.rolesetup.create');
            Route::get('/sec/rolesetup/{id}/edit', 'editRolePermission')->name('sec.rolesetup.edit');
            Route::get('/sec/rolesetup/{id}/delete', 'deleteRolePermission')->name('sec.rolesetup.delete');
            Route::get('/sec/rolesetup/add', 'addRolePermission')->name('sec.rolesetup.add');
        });  //
    });  //
    // Route::get('/run-migration', function () {
    //     Artisan::call('optimize:clear');

    //     Artisan::call('migrate:refresh --seed');
    //     return "Migration executed successfully";
    // });


});
