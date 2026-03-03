<?php

namespace App\Http\Controllers;


use App\Models\user;
use App\Models\project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class ProjectController extends Controller
{
    //registration end point public

    public function register(Request  $request){
        //Vlidate all request to this end point

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users|max:255',
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => 'sometimes|in:student,company', //default will be student
            'company_name' => 'nullable|string|max:255',
        ]);

        //Now create user using the validated data
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role']?? 'student',
        ]);

        //if role is company, create the company record 

         if($user->role === 'company'){
            $user->company()->create([
                'company_name' => $validated['company_name']?? $validated['name'],
            ]);
         }
         $token = $user->createToken('auth-token')->plainTextToken;
         
         //customized registration feetback depending on roles
         $message = $user->role === 'company' ? 'Company account created successfully' : 'Student account created successfully';

         //Return Json
         return response()->json([
            'message' => $message,
            'user' => $user->load('company'), //loads company relationship for this user
            'token' => $token,
         ], 201);

    }

    //Login and redirect to dashboard

    public function login(Request $request){
            //validate incoming request
            $validated = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            //find the first user with this email
            $user = User::where('email', $validated['email'])->first(); //stores boolean : true of false

            //check the password from request against stored password
            if(! $user|| !Hash::check($validated['password'], $user->password) ){
                throw ValidationException::withMessages([
                    'email' => ['The provided credential are incorrect'],
                ]);
            }
            //create a token for this user
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login Successful',
                'user' => $user,
                'token' => $token,
            ]);
    }
}