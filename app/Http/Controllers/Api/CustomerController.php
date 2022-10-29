<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Gardner;
use App\Models\Booking;
use Validator;

class CustomerController extends Controller
{
    public function profileView(Request $request){
        try{
            $id = $request->id;
        $Customer = User::find($id);
        if(isset($Customer)){
            return response()->json(['message'=>'Success','data'=>$Customer],200);

        }else{
            return response()->json(['message'=>'Failure','error'=>'Customer Does Not Exist'],422);

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
                $Customer = User::find($request->customer_id);
                $Customer->name = $request->name;
                $Customer->email = $request->email;
                $Customer->phone = $request->phone;
                $Customer->password = $request->password;
                $Customer->title = 'Customer';
                $Customer->save();
                return response()->json(['message' => 'Success', 'user' => $Customer], 200);

            }
            

        }catch (\Throwable $th) {
            return response(['message' => 'failure', 'error' => $th->getMessage()], 500);
        }

    }

    public function createBooking(Request $request){
        
        try{
            $validation = Validator::make($request->all(), [
                'booking_title' => 'required|max:255',
                'service' => 'required',
                'location' => 'required',
                'date' => 'required',
                'time' => 'required',
                'service_fee' => 'required',
                'description' => 'required|max:255',
            ]);
    
            if ($validation->fails()) {
                return response()->json(['message' => 'failure', 'error' => $validation->errors()], 422);
            }else{
                $createBooking = new Booking;
                $createBooking->booking_title = $request->booking_title;
                $createBooking->service = implode(',', $request->service);
                $createBooking->location = $request->location;
                $createBooking->date = $request->date;
                $createBooking->time = $request->time;
                $createBooking->service_fee = $request->service_fee;
                $createBooking->description = $request->description;
                $createBooking->customer_id = \Auth::guard('api')->id();
                $createBooking->save();
                if($createBooking){
                    return response(['message' => 'Success', 'data' => $createBooking], 200);

                }else{
                    return response(['message' => 'failure', 'error' => 'Booking Does Not Saved'], 500);

                }
            }

        }catch (\Throwable $th) {
            return response(['message' => 'failure', 'error' => $th->getMessage()], 500);
        }

    }
    public function MyBooking(){
        try{
            $myBookings = Booking::where('customer_id',\Auth::guard('api')->id())->get();
            if(count($myBookings) > 0){
                return response(['message' => 'Success', 'data' => $myBookings], 200);

            }else{
                return response(['message' => 'failure', 'error' => 'You Have Not Booking Yet'], 422);

            }

        }catch (\Throwable $th) {
            return response(['message' => 'failure', 'error' => $th->getMessage()], 500);
        }

    }
    public function searchGardner(Request $request){
        try{
            $gardner = Gardner::whereRaw("find_in_set($request->id,skills)")->get();
            if($gardner){
            return response(['message' => 'Success', 'data' => $gardner], 200);
            }else{
            return response(['message' => 'failure', 'error' => 'No Gardner Exit In This Skill'], 422);

            }
        }catch (\Throwable $th) {
            return response(['message' => 'failure', 'error' => $th->getMessage()], 500);
        }
        
    }
    public function AwardTo(Request $request){
        try{
            $booking = Booking::find($request->booking_id);
            $booking->status = 'Awarded';
            $booking->awarded_to = $request->award_id;
            $booking->save();
            return response(['message' => 'Success', 'data' => "Booking is Awarded To Designated Gardner"], 200);

        }catch (\Throwable $th) {
            return response(['message' => 'failure', 'error' => $th->getMessage()], 500);
        }
    }
    public function VerifyOtp_Customer(Request $request){
        try{
            $verify_customer = User::where('id',$request->id)->where('otp',$request->otp)->get();
            if(count($verify_customer) > 0){
                return response(['message' => 'Success', 'data' => $verify_customer], 200);
            }
            return response(['message' => 'failure', 'error' => 'Customer Not Found'], 422);
        }catch (\Throwable $th) {
            return response(['message' => 'failure', 'error' => $th->getMessage()], 500);
        }
        


    }
    
}
