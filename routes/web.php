<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ContentPlannerController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\ResearchController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SuggestionController;
use App\Http\Livewire\LessonArchitect;
use App\Http\Controllers\PayPalPaymentController;
use App\Http\Livewire\Course;
use App\Services\ChatGptService;
use Illuminate\Support\Facades\Route;
use App\Events\JobCompleted;
use App\Http\Controllers\CourseSettingsController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SubscribeController;
use App\Http\Controllers\userController;
use App\Http\Livewire\CourseContent;
use App\Listeners\JobCompletedListener;
// use App\Models\Course;
use App\Models\User;
use OpenAI\Laravel\Facades\OpenAI;
use PHPUnit\Event\TestSuite\Loaded;

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

Route::view('/', 'welcome')->name('home');

Route::middleware('guest')->group(function () {
    Route::view('register', 'auth.register')->name('register');
    Route::view('login', 'auth.login')->name('login');
});

Route::controller(AuthController::class)->name('auth.')->group(function () {
    Route::post('login', 'login')->name('login');
    Route::post('register', 'register')->name('register');
    Route::post('logout', 'destroy')->middleware('auth')->name('logout');
});



Route::get('/share/courses/{course_slug}', [CourseController::class, 'share'])->name('courses.share');
Route::post('price/courses/{course}', [CourseController::class, 'coursePrice'])->name('courses.coursePrice');
Route::put('/courses/{image}', [CourseController::class, 'courseImage'])->name('courses.courseImage');
Route::resource('courses', CourseController::class);
// Route::post('products/{id}/purchase', [ProductController::class ,'purchase'])->name('products.purchase');

Route::middleware('auth')->group(function () {
    Route::group(['middleware' => 'restrictUserRole:user'], function () {
        Route::get('/dashboard', DashboardController::class)->name('dashboard')->middleware('admin');
    });
    Route::group(['middleware' => 'restrictUserRole:admin'], function () {
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::resource('profile', ProfileController::class)->only(['edit', 'update', 'destroy']);
        Route::resource('library', LibraryController::class);
        Route::view('index', 'user.content-planner');
        Route::view('coming-soon', 'pages.users.coming-soon')->name('coming-soon');
        Route::get('export-books', [BookController::class, 'export'])->name('export.books');
        Route::resource('books', BookController::class);
        Route::resource('course-validation', ScoreController::class);
        Route::get('course', Course::class)->name('course');
        Route::post('course-setting/{courseId}/save-setting', [CourseSettingsController::class, 'saveSetting'])->name('course-setting.saveSetting');
        Route::resource('course-setting', CourseSettingsController::class);
        Route::put('course-setting/{courseId}', [CourseSettingsController::class, 'updateCheckout'])->name('course-setting.updateCheckout');
        Route::resource('research', ResearchController::class);
        Route::resource('search', SearchController::class);
        Route::get('/export-text', [ContentPlannerController::class, 'exportText'])->name('export.text');
        Route::resource('content-planner', ContentPlannerController::class);
        Route::get('/suggestions', [SuggestionController::class, 'suggestions']);
        Route::resource('/setting', SettingController::class);
        Route::resource('/subscribe', SubscribeController::class);
        Route::resource('lesson', LessonController::class);
        //     Route::get('export/{contentType}', ContentExportController::class)->name('export');
    });
    
    Route::group(['middleware' => 'restrictUserRole:user'], function () {
        Route::resource('/user-dashboard', userController::class);
    });
    Route::controller(PayPalPaymentController::class)->group(function () {
        Route::get('payment', 'payment')->name('payment');
        Route::get('cancel', 'cancel')->name('payment.cancel');
        Route::get('payment/success', 'success')->name('payment.success');
    });

});

Route::get('test', function () {

    // $question = 'write about the evolution of man';

    // $response = OpenAI::completion()->create([
    //     'model' => 'gpt-3.5-turbo',
    //     'max_tokens' => 3000,
    //     'temperature' => 0.8,
    //     'messages' => [
    //         ["role" => "system", "content" => "You are a knowledgeable assistant that provides detailed explanations about topics."],
    //         ["role" => "user", "content" => $question]
    //     ]
    // ]);

    // $completionText = $response['choices'][0]['text'];

    // return response()->stream(function () use ($completionText) {
    //     echo "data: " . $completionText . "\n\n";
    //     ob_flush();
    //     flush();
    // }, 200, [
    //     'Cache-Control' => 'no-cache',
    //     'X-Accel-Buffering' => 'no',
    //     'Content-Type' => 'text/event-stream',
    // ]);

    // $client = new MailchimpMarketing\ApiClient();
    // $client->setConfig([
    //     'apiKey' => 'e1f7a2e06cf128d61784d6b0db1a24f0-us21',
    //     'server' => 'us21',
    // ]);

    // $response = $client->lists->getAllLists();
    // dd($response);


    // $client = new MailchimpMarketing\ApiClient();
    // $client->setConfig([
    //     'apiKey' => 'e1f7a2e06cf128d61784d6b0db1a24f0-us21',
    //     'server' => 'us21',
    // ]);

    // $response = $client->lists->addListMember("3c54a618ea", [
    //     "email_address" => "peace.black@hotmail.com",
    //     "status" => "pending",
    // ]);
    // dd($response);

    $client = new MailchimpMarketing\ApiClient();
    $client->setConfig([
        'apiKey' => 'e1f7a2e06cf128d61784d6b0db1a24f0-us21',
        'server' => 'us21',
    ]);

    $response = $client->lists->getListMembersInfo("3c54a618ea");
    dd($response);
});
