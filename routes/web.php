<?php

use Illuminate\Support\Facades\Route;

// Welcome page
Route::get('/', function () {
    return view('welcome');
});

// Authentication routes
Auth::routes();

// Home route (authenticated)
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// ===========================
// AUTHENTICATED ROUTES
// ===========================
Route::middleware('auth')->group(function () {
    
    // ===========================
    // TEACHER ROUTES
    // ===========================
    // Teacher management
    Route::resource('teachers', App\Http\Controllers\TeacherController::class);
    
    // Teacher grading
    Route::get('/teacher/classroom/{classroom}/student/{student}/submissions', 
        [App\Http\Controllers\TeacherController::class, 'studentSubmissions'])
        ->name('teacher.student.submissions');
    Route::get('/teacher/submission/{submission}/grade', 
        [App\Http\Controllers\TeacherController::class, 'gradeSubmission'])
        ->name('teacher.submission.grade');
    Route::post('/teacher/submission/{submission}/update-grade', 
        [App\Http\Controllers\TeacherController::class, 'updateGrade'])
        ->name('teacher.submission.update.grade');
    
    // Teacher class progress dashboard
    Route::get('/teacher/classroom/{classroom}/progress', 
        [App\Http\Controllers\TeacherController::class, 'classProgress'])
        ->name('teacher.class.progress');
    
    // ===========================
    // STUDENT ROUTES
    // ===========================
    // Student management (admin)
    Route::resource('students', App\Http\Controllers\StudentController::class);
    
    // Student dashboard/portal routes
    Route::get('/student/dashboard', [App\Http\Controllers\StudentController::class, 'index'])
        ->name('student.dashboard');
    Route::get('/student/classes', [App\Http\Controllers\StudentController::class, 'classes'])
        ->name('student.classes');
    Route::post('/student/enroll', [App\Http\Controllers\StudentController::class, 'enrollClass'])
        ->name('student.enroll');
    
    // Student practice mode
    Route::get('/student/practice', [App\Http\Controllers\StudentController::class, 'practice'])
        ->name('student.practice');
    Route::post('/student/practice/submit', [App\Http\Controllers\StudentController::class, 'submitPractice'])
        ->name('student.practice.submit');
    
    // Student materials
    Route::get('/student/materials', [App\Http\Controllers\StudentController::class, 'materials'])
        ->name('student.materials');
    Route::get('/student/materials/{id}', [App\Http\Controllers\StudentController::class, 'showMaterial'])
        ->name('student.material.show');
    
    // Student progress dashboard
    Route::get('/student/progress', [App\Http\Controllers\StudentController::class, 'progress'])
        ->name('student.progress');
    
    // Student assignment submission
    Route::get('/student/assignment/{assignment}/submit', 
        [App\Http\Controllers\StudentController::class, 'submitAssignment'])
        ->name('student.assignment.submit');
    Route::post('/student/assignment/{assignment}/store', 
        [App\Http\Controllers\StudentController::class, 'storeSubmission'])
        ->name('student.assignment.store');
    Route::get('/student/assignment/{assignment}/view', 
        [App\Http\Controllers\StudentController::class, 'viewSubmission'])
        ->name('student.assignment.view');
    
    // AssemblyAI temporary token endpoint
    Route::post('/api/assemblyai/token', 
        [App\Http\Controllers\StudentController::class, 'getAssemblyAIToken'])
        ->name('api.assemblyai.token');
    
    // Teacher's student list view
    Route::get('/students-list', [App\Http\Controllers\StudentListController::class, 'index'])
        ->name('students.list');
    
    // Teacher's view of student profile
    Route::get('/teacher/student/{student}', [App\Http\Controllers\StudentListController::class, 'show'])
        ->name('teacher.student.profile');
    
    // ===========================
    // CLASSROOM ROUTES
    // ===========================
    Route::get('/classes', [App\Http\Controllers\ClassroomController::class, 'index'])
        ->name('classroom.index');
    Route::get('/classes/create', [App\Http\Controllers\ClassroomController::class, 'create'])
        ->name('classroom.create');
    Route::post('/classes', [App\Http\Controllers\ClassroomController::class, 'store'])
        ->name('classroom.store');
    Route::get('/classes/{classroom}', [App\Http\Controllers\ClassroomController::class, 'show'])
        ->name('classroom.show');
    Route::get('/classes/{classroom}/edit', [App\Http\Controllers\ClassroomController::class, 'edit'])
        ->name('classroom.edit');
    Route::put('/classes/{classroom}', [App\Http\Controllers\ClassroomController::class, 'update'])
        ->name('classroom.update');
    Route::patch('/classes/{classroom}/regenerate', 
        [App\Http\Controllers\ClassroomController::class, 'regenerateAccessCode'])
        ->name('classroom.regenerate');
    Route::delete('/classes/{classroom}', [App\Http\Controllers\ClassroomController::class, 'destroy'])
        ->name('classroom.destroy');
    
    // ===========================
    // ASSIGNMENT ROUTES
    // ===========================
    Route::get('/classes/{classroom}/assignments/create', 
        [App\Http\Controllers\AssignmentController::class, 'create'])
        ->name('assignment.create');
    Route::post('/assignments', [App\Http\Controllers\AssignmentController::class, 'store'])
        ->name('assignment.store');
    Route::get('/assignments/{assignment}', [App\Http\Controllers\AssignmentController::class, 'show'])
        ->name('assignment.show');
    Route::get('/assignments/{assignment}/edit', [App\Http\Controllers\AssignmentController::class, 'edit'])
        ->name('assignment.edit');
    Route::put('/assignments/{assignment}', [App\Http\Controllers\AssignmentController::class, 'update'])
        ->name('assignment.update');
    Route::delete('/assignments/{assignment}', [App\Http\Controllers\AssignmentController::class, 'destroy'])
        ->name('assignment.destroy');
    
    // ===========================
    // MATERIALS ROUTES
    // ===========================
    Route::get('/Materials', [App\Http\Controllers\MaterialController::class, 'index'])
        ->name('materials.index');
    Route::get('/Materials/create', [App\Http\Controllers\MaterialController::class, 'create'])
        ->name('materials.create');
    Route::post('/Materials', [App\Http\Controllers\MaterialController::class, 'store'])
        ->name('materials.store');
    Route::get('/Materials/{material}', [App\Http\Controllers\MaterialController::class, 'show'])
        ->name('materials.show');
    Route::get('/Materials/{material}/edit', [App\Http\Controllers\MaterialController::class, 'edit'])
        ->name('materials.edit');
    Route::put('/Materials/{material}', [App\Http\Controllers\MaterialController::class, 'update'])
        ->name('materials.update');
    Route::delete('/Materials/{material}', [App\Http\Controllers\MaterialController::class, 'destroy'])
        ->name('materials.destroy');
});

// ===========================
// TELEGRAM BOT WEBHOOK (no auth)
// ===========================
Route::post('/telegram/webhook', [App\Http\Controllers\TelegramBotController::class, 'webhook'])
    ->name('telegram.webhook');
