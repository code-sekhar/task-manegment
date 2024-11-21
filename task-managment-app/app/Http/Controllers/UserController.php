<?php

namespace App\Http\Controllers;

use App\Helper\JWTToken;
use App\Mail\OTPMail;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function register(Request $request)
    {
        try{
            $validation = $request->validate([
                'name' => 'required',
                'email' => 'required|unique:users',
                'password' => 'required',
            ]);

            $result = User::create([
                'name'=>$validation['name'],
                'email'=>$validation['email'],
                'password'=>Hash::make($validation['password'])
            ]);
            return response()->json([
                "status" => "success",
                "message" => "user registered successfully",
                "data" => $result
            ]);
        }catch (Exception $e){
            return response()->json([
                "status" => "error",
                "message" => "server error",
                "error" => $e->getMessage()
            ],500);
        }
    }
    //Login User
    public function login(Request $request){
        try{
            $user = User::where('email', $request->input('email'))->first();
            if($user && Hash::check($request->input('password'), $user->password)){
                $token = JWTToken::CreateToken($request->input('email'),$user->id);
                return response()->json([
                    "status" => "success",
                    "message" => "login successfully",
                    "token" => $token
                ],200)->cookie('token',$token,time()+60*60*30);
            }else{
                return response()->json([
                    "status" => "failed",
                    "message" => "login failed"
                ],404);
            }
        }catch (Exception $e){
            return response()->json([
                "status" => "failed",
                "message" => "Some thing Wrong!"
            ],500);
        }
    }
    //sendOTP Code
    public function sendOtpCode(Request $request){
        $email = $request->input('email');
        $otp = rand(100000, 999999);
        $count = User::where('email', $email)->count();
        if($count==1){
            //otp send mail
            Mail::to($email)->send(new OTPMail($otp));
            //otp code update database
            User::where('email', $email)->update(['otp' => $otp]);
            return response()->json([
                "status" => "success",
                "message" => "otp sent successfully",
            ],200);

        }else{
            return response()->json([
                "status" => "failed",
                "message" => "email not registered"
            ],404);
        }
    }
    //VerifyOTP
    public function verifyOtp(Request $request){
        $email = $request->input('email');
        $otp = $request->input('otp');
        $count = User::where('email','=',$email)->where('otp','=', $otp)->count();
        if($count==1){
            User::where('email', $email)->update(['otp' => '0']);
            $token = JWTToken::CreateTokenForSetPassword($request->input('email'));
            return response()->json([
                "status" => "success",
                "message" => "otp Verify successfully",
                "token" => $token
            ],200)->cookie('token',$token,time()+60*24*30);
        }else{
            return response()->json([
                "status" => "failed",
                "message" => "email not registered"
            ],404);
        }
    }
    //Reset Password
    public function resetPassword(Request $request){
        $email = $request->input('email');
        $password = $request->input('password');
        $check = User::where('email', $email)->update(['password' => Hash::make($password)]);
        if($check){
            return response()->json([
                "status" => "success",
                "message" => "password reset successfully"
            ],200);
        }else{
            return response()->json([
                "status" => "failed",
                "message" => "email not registered"
            ],500);
        }
    }
    //User Logout
    public function logout(Request $request){
        return response()->json([
            "status" => "success",
            "message" => "Logged out successfully"
        ],200)->cookie('token','',-1);
    }
}
