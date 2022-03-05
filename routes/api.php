<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TrainerController;
use App\Http\Controllers\ContactUsController;
use App\Http\Controllers\CourseContentController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\payment;
use Illuminate\Http\Request;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::post('payment-intent',[PaymentController::class,'CreatePayIntent']);
// Route::post('store-intent', [PaymentController::class,'storeStripePayment']);
Route::post('payment-intent', [PaymentController::class,'CreatePayIntent']);
Route::post('store-intent', [PaymentController::class,'storeStripePayment']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::group([

    'middleware' => 'api',

], function ($router) {

    Route::post('login', 'App\Http\Controllers\AuthController@login');
    Route::post('logout', 'App\Http\Controllers\AuthController@logout');
    Route::post('refresh', 'App\Http\Controllers\AuthController@refresh');
    Route::post('me', 'App\Http\Controllers\AuthController@me');

});

//login student
Route::post('/students',[StudentController::class,'register']);

Route::post('login/student', [StudentController::class, 'login']);
Route::middleware('checkStudent:students')->group(function () {

    Route::post('/student/me', [StudentController::class, 'me']);
    Route::post('/student/logout', [StudentController::class, 'logout']);
    Route::post('/student/hello', [StudentController::class, 'sayHello']);
});
//login trainer
Route::post('/trainers/register', [TrainerController::class, 'register']);
Route::post('/trainers/login', [TrainerController::class, 'login']);
Route::middleware('checkTrainer:triners')->group(function () {

    Route::post('/trainers/me', [TrainerController::class, 'me']);
    Route::post('/trainers/logout', [TrainerController::class, 'logout']);
    Route::post('/trainers/hello', [TrainerController::class, 'sayHello']);

});



//get categories count
Route::get('/categories/count',[CategoryController::class,'getCount']);
//get courses count
Route::get('/courses/count',[CourseController::class,'getCount']);
//get students count
Route::get('/students/count',[StudentController::class,'getCount']);
//get trainers count
Route::get('/trainers/count',[TrainerController::class,'getCount']);

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);
Route::get('/categories/courses/{id}', [CategoryController::class, 'showCategoryCourses']);
Route::delete('/categories/{id}', [CategoryController::class, 'delete']);
Route::post('/categories', [CategoryController::class, 'store']);
Route::post('/categories/{id}', [CategoryController::class, 'update']);



Route::get('/questions',[QuestionController::class, 'index']);
Route::get('/questions/{id}',[QuestionController::class, 'show']);
Route::delete('/questions/{id}',[QuestionController::class, 'destroy']);
Route::post('/questions',[QuestionController::class, 'store']);
Route::post('/questions/{id}',[QuestionController::class, 'update']);


Route::get('/trainers',[TrainerController::class, 'index']);
Route::get('/trainers/{id}',[TrainerController::class, 'show']);
Route::delete('/trainers/{id}',[TrainerController::class, 'destroy']);
Route::post('/trainers',[TrainerController::class, 'store']);
Route::post('/trainers/{id}',[TrainerController::class, 'update']);


Route::get('/students',[StudentController::class, 'index']);
Route::get('/students/{id}',[StudentController::class, 'show']);
Route::delete('/students/{id}',[StudentController::class, 'destroy']);
//Route::post('/students',[StudentController::class, 'store']);
Route::post('/students/{id}',[StudentController::class, 'update']);



Route::get('/exams',[ExamController::class, 'index']);
Route::get('/exams/{id}',[ExamController::class, 'show']);
Route::delete('/exams/{id}',[ExamController::class, 'destroy']);
Route::post('/exams',[ExamController::class, 'store']);
Route::post('/exams/{id}',[ExamController::class, 'update']);
//get all questions by exam_id
Route::get('/exams/questions/{e_id}',[ExamController::class, 'getallExam']);



Route::get('/courses', [CourseController::class, 'index']);
Route::get('/courses/{id}', [CourseController::class, 'show']);
Route::delete('/courses/{id}', [CourseController::class, 'destroy']);
Route::post('/courses', [CourseController::class, 'store']);
Route::post('/courses/{id}', [CourseController::class, 'update']);
// Route::patch('/courses/{id}', [CourseController::class, 'update']);
Route::get('/course/search_course', [CourseController::class, 'searchCourse']);


Route::get('/Contact_us', [ContactUsController::class, 'index']);
Route::get('/Contact_us/{id}', [ContactUsController::class, 'show']);
Route::delete('/Contact_us/{id}', [ContactUsController::class, 'destroy']);
Route::post('/Contact_us', [ContactUsController::class, 'store']);
Route::put('/Contact_us/{id}', [ContactUsController::class, 'update']);
Route::patch('/Contact_us/{id}', [ContactUsController::class, 'update']);


//show Course content by Course id
Route::get('/Course_content/show/{c_id}', [CourseController::class, 'showvideo']);
//show courses by student id
Route::get('/student/showCourses/{id}', [CourseController::class, 'showCourses']);
//enrolle
Route::post('/student/storeCourse',[CourseController::class,'Enrollment']); 
//show student by Course id
Route::get('/student/showStudent/{id}', [CourseController::class, 'showStudent']);
// return student count in given course id
Route::get('/student/studentCount/{id}', [CourseController::class, 'studentCount']);


Route::get('/Course_content', [CourseContentController::class, 'index']);
Route::get('/Course_content/{id}', [CourseContentController::class, 'show']);
Route::delete('/Course_content/{id}', [CourseContentController::class, 'destroy']);
Route::post('/Course_content', [CourseContentController::class, 'store']);
Route::put('/Course_content/{id}', [CourseContentController::class, 'update']);
Route::patch('/Course_content/{id}', [CourseContentController::class, 'update']);


Route::get('/feedbacks', [FeedbackController::class, 'index']);
Route::get('/feedbacks/{id}', [FeedbackController::class, 'show']);
// Route::delete('/feedbacks/{id}',[FeedbackController::class, 'destroy']);
Route::post('/feedbacks', [FeedbackController::class, 'store']);
Route::put('/feedbacks/{id}', [FeedbackController::class, 'update']);
Route::patch('/feedbacks/{id}', [FeedbackController::class, 'update']);


