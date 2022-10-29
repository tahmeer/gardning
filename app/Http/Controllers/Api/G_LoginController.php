<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Auth;
use Hash;
use App\Models\Gardner;

class G_LoginController extends Controller
{
    
    public function register(Request $request)
    {
        try{
            $validation = Validator::make($request->all(), [
                'name' => 'required|max:255',
                'email' => 'required|email|max:255|unique:gardners',
                'phone' => 'required|string|regex:/\d+/|size:11',
                'password' => 'min:6|required_with:password_confirmation|same:password_confirmation',
                'password_confirmation' => 'min:6',
            ]);
    
            if ($validation->fails()) {
                return response()->json(['message' => 'failure', 'error' => $validation->errors()], 422);
            }else{
                $randomNumber = random_int(1000, 9999);

            $insertGardner = new Gardner;
            $insertGardner->name = $request->name;
            $insertGardner->email = $request->email;
            $insertGardner->phone = $request->phone;
            $insertGardner->password = bcrypt($request->password);
            $insertGardner->title = 'Gardner';
            $insertGardner->skills = implode(',', $request->skills);
            $insertGardner->otp = $randomNumber;
            $insertGardner->save();

            $gardner = Gardner::where('email', $insertGardner->email)->first();
            $token = $gardner->createToken('API Token')->accessToken;
           

            return response()->json(['message' => 'Success', 'user' => $gardner, 'token' => $token], 200);

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

                $gardners = Gardner::where('email', $request->email)->first();
                // dd($gardners);
                if (!empty($gardners)) {
                    if (!Hash::check(request()->password, $gardners->password)) {
                        // no they don't
                        return response()->json(['error' => 'Wrong Password'], 401);
                    }
                    Auth::login($gardners);
                    $gardner = Auth::user();
                    $token = $gardner->createToken('API Token')->accessToken;
                    // $token = $gardner->createToken('API Token',['place-orders'])->accessToken;

                    $data = [
                        'user' => $gardner,
                        'token' => $token
                    ];
                    return response()->json(['message' => 'Success', 'data' => $data], 200);
                        // if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                        //     $gardner = Auth::user();
                           
                        //     $token = $gardner->createToken('API Token')->accessToken;

                        //         $data = [
                        //             'user' => $gardner,
                        //             'token' => $token
                        //         ];
                        //         return response()->json(['message' => 'Success', 'data' => $data], 200);
                        //     // $user->dateofbirth = isset(Auth::user()->user_details->where('field', 'date_of_birth')->first()->value) ? Auth::user()->user_details->where('field', 'date_of_birth')->first()->value : null;


                        // }
                        //  else {
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
    
}
