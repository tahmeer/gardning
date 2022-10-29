<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Auth;
use Hash;


class LoginController extends Controller
{
    public function register(Request $request)
    {
        try{
            $validation = Validator::make($request->all(), [
                'name' => 'required|max:255',
                'email' => 'required|email|max:255|unique:users',
                'phone' => 'required|string|regex:/\d+/|size:11',
                'password' => 'min:6|required_with:password_confirmation|same:password_confirmation',
                'password_confirmation' => 'min:6',
            ]);
    
            if ($validation->fails()) {
                return response()->json(['message' => 'failure', 'error' => $validation->errors()], 422);
            }else{
                $insertUser = new User;
                $insertUser->name = $request->name;
                $insertUser->email = $request->email;
                $insertUser->phone = $request->phone;
                $insertUser->password = bcrypt($request->password);
                $insertUser->title = 'Customer';
                $insertUser->save();

                $user = User::where('email', $insertUser->email)->first();
                $token = $user->createToken('API Token')->accessToken;
            

                return response()->json(['message' => 'Success', 'user' => $user, 'token' => $token], 200);

            }
    
            

        }catch (\Throwable $th) {
            return response(['status' => 'failure', "errors" => $th->getMessage()], 500);
        }
        
    }
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email'    => 'required|email|max:200',
                'password' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['message' => 'failure', 'error' => $validator->errors()], 422);
            } 
            else {

                $users = User::where('email', $request->email)->first();

                if (!empty($users)) {
                    if (!Hash::check(request()->password, $users->password)) {
                        // no they don't
                        return response()->json(['error' => 'Wrong Password'], 401);
                    }
                    Auth::login($users);
                    $users = Auth::user();
                    $token = $users->createToken('API Token')->accessToken;
                    // $token = $users->createToken('API Token',['check-status'])->accessToken;

                    $data = [
                        'user' => $users,
                        'token' => $token
                    ];
                    return response()->json(['message' => 'Success', 'data' => $data], 200);
                        // if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                        //     $user = Auth::user();
                        //     $token = auth()->user()->createToken('API Token',['check-status'])->accessToken;

                        //         $data = [
                        //             'user' => $user,
                        //             'token' => $token
                        //         ];
                        //         return response()->json(['message' => 'Success', 'data' => $data], 200);
                        //     // $user->dateofbirth = isset(Auth::user()->user_details->where('field', 'date_of_birth')->first()->value) ? Auth::user()->user_details->where('field', 'date_of_birth')->first()->value : null;


                        // } else {
                        //     return response()->json(['message' => 'Failure', 'data' => 'Invalid Credentials'], 422);

                        // }
                    } 
                    else {
                        return response()->json(['message' => 'failure', "data" => "There isn't an account associated with this email address."], 500);
                   
                }
            }
        
        } catch (\Throwable $th) {
            return response(['message' => 'failure', 'error' => $th->getMessage()], 500);
        }
    }
    public function index(){
        return "yes Your are Done";
    }
    public function logout(){
        $user = Auth::guard('api')->user()->token();
        $user->revoke();
        return response()->json(['message'=>'Success','data'=>'Logout Successfully'],200);
    }
}
