<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     * This is overridden by redirectTo() method below.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Get the post register redirect path based on user role.
     *
     * @return string
     */
    public function redirectTo()
    {
        $user = auth()->user();
        
        if (!$user) {
            return '/home';
        }

        // Role-based redirect
        switch ($user->role_id) {
            case 2: // Student
                return '/student/classes';
            case 3: // Teacher
                return '/home'; // Teacher dashboard
            default:
                return '/home';
        }
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        $roles = Role::all();
        return view('auth.register', compact('roles'));
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role_id' => ['required', 'exists:role,id'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id' => $data['role_id'],
        ]);

        // Create corresponding Student or Teacher record with name
        $this->createRoleSpecificRecord($user, $data['role_id'], $data['name']);

        return $user;
    }

    /**
     * Create role-specific records (Student or Teacher)
     *
     * @param  \App\Models\User  $user
     * @param  int  $roleId
     * @param  string  $name
     * @return void
     */
    protected function createRoleSpecificRecord($user, $roleId, $name)
    {
        switch ($roleId) {
            case 2: // Student
                \App\Models\Student::create([
                    'id' => $user->id,
                    'name' => $name,
                    'biodata' => null,
                    'current_level' => 'Beginner',
                ]);
                break;
            
            case 3: // Teacher
                \App\Models\Teacher::create([
                    'id' => $user->id,
                    'name' => $name,
                    'biodata' => null,
                    'title' => 'Teacher', // Default title
                ]);
                break;
        }
    }
}
