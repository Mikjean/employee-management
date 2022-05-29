<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\AttendanceController; 
use App\Models\Attendance;
use Validator;
use App\Http\Resources\AttendanceResource;
use Carbon\Carbon;
use App\Http\Controllers\ReportController; 


class AttendanceController extends BaseController
{




    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $attendances = Attendance::all();    
        return $this->sendResponse(AttendanceResource::collection($attendances), 'departments retrieved successfully.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create'); // only the employee can attend sign in  at work
        $user_id = auth()->user()->id;

        $latest = Attendance::where('user_id', '=',$user_id)
                            ->orderBy('created_at','desc')->first();
        $obj = $latest;
        // return response()->json($obj);
        // die();
        if($obj === null){
            $now =  Carbon::now();
            $input = [
            'user_id' => $user_id,
            'entrance' => $now->toDateTimeString(),
            'exit' => $now->format('Y-m-d'),
            'attended' => true
            
            ];
        
        // print_r($input);
        // die();
   
        $validator = Validator::make($input, [
            'entrance' => 'required', 

        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }; 

        $attendance = Attendance::create($input);
        $this->sendmail($attendance);  
            
        } elseif (!$obj->attended && $obj->entrance !== Carbon::today()) {
            $now =  Carbon::now();
            $input = [
            'user_id' => $user_id,
            'entrance' => $now->toDateTimeString(),
            'exit' => $now->format('Y-m-d'),
            'attended' => true
            
            ];
        
        // print_r($input);
        // die();
   
        $validator = Validator::make($input, [
            'entrance' => 'required', 

        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }; 

        $attendance = Attendance::create($input);
        $this->sendmail($attendance);

    }else{
        // print_r(Carbon::today()->toDateTimeString());
        return $this->sendResponse('message', 'you have already attended today. only exit');


        }
   
   
        
        
    }


    // function to send  attendance email to employees

    public function sendmail($attendance) {

        $user_email = auth()->user()->email;
        die();
        $details = [
            'title' => 'Mail from The Employee management System',
            'body' => 'this is a Attendance email sent to you',
            'time' => Carbon::today()
        ];
       
        \Mail::to($user_email)->send(new \App\Mail\MyTestMail($details));
       
        return $this->sendResponse(new AttendanceResource($attendance), 'attended successfully.');


    }
        

  

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function signout(Request $request)
    {
        $this->authorize('create'); // employee can sign out from work
        $user_id = auth()->user()->id;
        $latest = Attendance::where('user_id', '=',$user_id)
                            ->orderBy('created_at','desc')->first();
        
        
                            if($latest->attended == true ) {
                                $now =  Carbon::now();
                                
                                $input = [
                                'user_id' => $user_id,
                                'exit' => $now->toDateTimeString(),
                                'attended' => false
                                
                                ];
                            
                       
                            $validator = Validator::make($input, [
                                'user_id' => 'required',
                                'exit' => 'required', 
                    
                            ]);
                       
                            if($validator->fails()){
                                return $this->sendError('Validation Error.', $validator->errors());       
                            }; 
                    
                            $latest->exit = $now->toDateTimeString();
                            $latest->attended = false;
                            $latest->save();
                       
                            return $this->sendResponse(new AttendanceResource($latest), 'Signed out successfully.');
                    
                        }else{
                            // print_r(Carbon::today()->toDateTimeString());
                            return $this->sendResponse('message', 'you have already signed out today. only you can sign in again ');
                        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    // today;s report of the atttended employees

    public function todayAttendance(Request $request){
        $this->authorize('create-delete-users');
        $today = Attendance::whereDate('created_at', Carbon::today())->get();
        $rep = new ReportController();
        $rep->PdfReport($today->toArray());

        return $this->sendResponse( AttendanceResource::collection($today), 'this is the list of today attended emplyees ');

    }

    // this week's report of attended employees

    public function weekAttendance(Request $request){
        $this->authorize('create-delete-users');
        $current_week = Attendance::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->get();
        return $this->sendResponse( AttendanceResource::collection($current_week), 'this is the list of this week attended emplyees ');

    }   

    //this month's report of attended employees

    public function MonthAttendance(Request $request){
        $this->authorize('create-delete-users');
        $month = Attendance::whereMonth('created_at', date('m'))
                ->whereYear('created_at', date('Y'))
                ->get();
        return $this->sendResponse( AttendanceResource::collection($month), 'this is the list of this month attended emplyees ');


    }
}
