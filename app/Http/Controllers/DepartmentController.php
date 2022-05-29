<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Controllers\DepartmentController; 
use App\Models\Department;
use Validator;
use App\Http\Resources\DepartmentResource;

class DepartmentController extends BaseController 
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // function __construct()
    // {
    //      $this->middleware('permission:department-list|department-create|department-edit|department-delete', ['only' => ['index','show']]);
    //      $this->middleware('permission:department-create', ['only' => ['create','store']]);
    //      $this->middleware('permission:department-edit', ['only' => ['edit','update']]);
    //      $this->middleware('permission:department-delete', ['only' => ['destroy']]);
    // }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $departments = Department::all();
    
        return $this->sendResponse(DepartmentResource::collection($departments), 'departments retrieved successfully.');
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
        $input = $request->all();
   
        $validator = Validator::make($input, [
            'name' => 'required',
            'description' => 'required'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
   
        $department = Department::create($input);
   
        return $this->sendResponse(new DepartmentResource($department), 'department created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $department = Department::find($id);
  
        if (is_null($department)) {
            return $this->sendError('Department not found.');
        }
   
        return $this->sendResponse(new DepartmentResource($department), 'Department retrieved successfully.');
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
    public function update(Request $request, Department $department)
    {
        $input = $request->all();
   
        $validator = Validator::make($input, [
            'name' => 'required',
            'description' => 'required'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
   
        $department->name = $input['name'];
        $department->description = $input['description'];
        $department->save();
   
        return $this->sendResponse(new DepartmentResource($department), 'department updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Department $department)
    {
        $department->delete();
   
        return $this->sendResponse([], 'department deleted successfully.');
    }
}
