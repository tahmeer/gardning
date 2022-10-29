<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Gardner;
use Validator;

class GardnerController extends Controller
{
    // public function __construct() {
    //     $this->middleware('auth:gardner');
    // }
    public function profileView(Request $request){
        try{
            $id = $request->id;
        $Gardner = Gardner::find($id);
        if(isset($Gardner)){
            return response()->json(['message'=>'Success','data'=>$Gardner],200);

        }else{
            return response()->json(['message'=>'Failure','error'=>'Gardner Does Not Exist'],422);

        }

        }catch (\Throwable $th) {
            return response(['message' => 'failure', 'error' => $th->getMessage()], 500);
        }

    }
    public function updateProfile(Request $request){
        try{
            $validation = Validator::make($request->all(), [
                'name' => 'required|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|regex:/\d+/|size:11',
                'password' => 'required|min:6'
            ]);
    
            if ($validation->fails()) {
                return response()->json(['message' => 'failure', 'error' => $validation->errors()], 422);
            }else{
                $Gardner = Gardner::find($request->gardner_id);
                $Gardner->name = $request->name;
                $Gardner->email = $request->email;
                $Gardner->phone = $request->phone;
                $Gardner->password = bcrypt($request->password);
                $Gardner->skills = implode(',', $request->skills);
                $Gardner->save();
                return response()->json(['message' => 'Success', 'user' => $Gardner], 200);

            }
            

        }catch (\Throwable $th) {
            return response(['message' => 'failure', 'error' => $th->getMessage()], 500);
        }

    }
    public function MyBooking(){
        try{
            $myBookings = Booking::where('awarded_to',\Auth::id())->get();
            if(count($myBookings) > 0){
                return response(['message' => 'Success', 'data' => $myBookings], 200);

            }else{
                return response(['message' => 'failure', 'error' => 'You Have Not Booking Yet'], 422);

            }

        }catch (\Throwable $th) {
            return response(['message' => 'failure', 'error' => $th->getMessage()], 500);
        }

    }
    public function MySkills(){
        $myskills = Gardner::select('skills')->find(\Auth::id());
        return response()->json(['message'=>'Success','data'=>$myskills],200);

    }
    public function Accept(Request $request){
        $accpetBooking = Booking::find($request->id);
        $accpetBooking->status = 'Complete';
        $accpetBooking->save();
        return response()->json(['message'=>'Success','data'=>'Accepted Successfully'],200);

    }
    public function Reject(Request $request){
        $accpetBooking = Booking::find($request->id);
        $accpetBooking->status = 'Rejected';
        $accpetBooking->save();
        return response()->json(['message'=>'Success','data'=>'Rejected Successfully'],200);
    }
    public function logout(){
        $user = Auth::guard('gardner')->user()->token();
        $user->revoke();
        return response()->json(['message'=>'Success','data'=>'Logout Successfully'],200);
    }
    public function index(){
        return "yes your are gardner";
    }
    public function ForgotEmail(Request $request){
        try{
        \Mail::to($request->email)->send(new \App\Mail\ForgotMail());

            // $data = array('email'=>$request->email);
            // \Mail::send(['text'=>'mail'],[], function($message) {
            //     $message->to($data['email'], 'Tutorials Point')->subject
            //        ('Laravel Basic Testing Mail');
            //     $message->from('tahmeerhussain1@gmail.com','Tahmeer Hussain');
            // });
             return response()->json(['message'=>'Success'],200);
        }catch (\Throwable $th) {
            return response(['message' => 'failure', 'error' => $th->getMessage()], 500);
        }
        
        

    }

}
