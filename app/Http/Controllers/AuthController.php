<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\Enrollment;
use DB;
class AuthController extends Controller
{
     /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
       // $this->middleware('auth:api', ['except' => ['login']]);
        $this->middleware('auth:api', ['except' => ['login','register','update']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Invalid user name or password'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    public function register(Request $request)
    {
    	$validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|unique:users|max:255|email',
        'password' => 'required|string|min:8|confirmed',
     ]);

    	$usr= new User;

        $usr->name = $request->name;
        $usr->email = $request->email;
        $usr->password =Hash::make($request->password);
        $usr->save();
        return $this->login($request);
    }

//new
    public function update(Request $request,$id)
    {
    	$validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|unique:users|max:255|email',
        'password' => 'required|string|min:8|confirmed',
     ]);

        $usr = User::find($id);
        $usr->name = $request->name;
        $usr->email = $request->email;
        $usr->password =Hash::make($request->password);
        $usr->save();
    	//return $this->login($request);
    	return response()->json('updated');
    }

    public function allStudent()
    {
    	$all = User::all();
    	return response()->json($all);
    }

    public function courseEnroll(Request $request){
        
    	$enroll= new Enrollment;

        $enroll->student_id = auth()->user()->id;
        $enroll->course_id = $request->course_id;
        $enroll->save();
        return response()->json('enrolled');
    }

    public function takenCourses(){
         $tknCourses = DB::table('enrollments')
         	->join('users','users.id', '=', 'enrollments.student_id')
         	->join('courses','courses.id','=','enrollments.course_id')
         	->select('users.name','courses.course_name')
         	->get();

         	return response()->json($tknCourses);
    }

    public function specificStudentCourse(){

    		$student = auth()->user()->id;
         	$tknCourses = DB::table('enrollments')
         	->join('users','users.id', '=', 'enrollments.student_id')
         	->join('courses','courses.id','=','enrollments.course_id')
         	->select('users.name','courses.course_name')
        	->where('enrollments.student_id',$student)
         	->get();
    		return response()->json($tknCourses);

    }

    public function dropCourse($id){
    	$delete = Enrollment::findorfail($id);
        $delete->delete();
        return response()->json('deleted');
    }


}
