<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Arr;
use App\Http\Resources\UserResource;
use App\Http\Resources\RoleResource;
use App\Http\Controllers\Controller as Controller;


class RegisterController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $this->authorize('create-delete-users'); // only an admin can create an employee
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'role_id' => 'required',
            'password' => 'required',
            'c_password' => 'required|same:password'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
   
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);

        $success['token'] =  $user->createToken('MyApp')->plainTextToken;
        $success['name'] =  $user->name;
        $success['role'] =  $user->role->name;
   
        return $this->sendResponse($success, 'User register successfully.');
    }
   
    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('MyApp')->plainTextToken; 
            $success['name'] =  $user->name;
            $success['role'] =  $user->role->name;
   
            return $this->sendResponse($success, 'User login successfully.');
        } 
        else{ 
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        } 
    }

    /**
     * logout api
     * 
     * @return \illuminate\Http\Response
     */

     public function logout(Request $request){
        auth()->user()->tokens()->delete();

        return $this->sendResponse('success', 'user logged out');
     }
     public function show($id)
     {
         $user = User::find($id);
         return $this->sendResponse(new UserResource($user), 'User retrieved successfully.');
     } 


     public function updateUser(Request $request, $id)
     {
        $this->authorize('create');
         $this->validate($request, [
             'name' => 'required',
             'email' => 'required|email|unique:users,email,'.$id,
             'password' => 'same:confirm-password',
             'role_id' => 'required'
         ]);
     
         $input = $request->all();
         if(!empty($input['password'])){ 
             $input['password'] = bcrypt($input['password']);
         }else{
             $input = Arr::except($input,array('password'));    
         }
     
         $user = User::find($id);
         $user->update($input);
         DB::table('model_has_roles')->where('model_id',$id)->delete();
     
         $user->assignRole($request->input('roles'));
     
         return $this->sendResponse(new UserResource($user), 'User updated successfully.');
     }

     public function destroy($id)
    {
        $this->authorize('create-delete-users');
        User::find($id)->delete();
        return $this->sendResponse(new UserResource($user), 'User deleted successfully.');
        ;
    }
}
