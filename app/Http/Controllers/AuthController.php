<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Menu;
use Carbon\Carbon;
use DB;
use Validator;

class AuthController extends BaseController
{
    function __construct()
    {
        $this->user = new User();
        $this->menu = new Menu();
    }

    public function login()
    {
        $js = 'js/apps/auth/login.js?_='.rand();
        return view('auth/login', compact('js'));
    }

    public function register(Request $request)
    {
        $id = $request->id_user;

        $validator = Validator::make($request->all(), [
            'name'=> 'required',
            'company' => 'required',
            'email' => 'required|email:rfc,dns|unique:users, "email",'.$id,
            'phone' => 'required|numeric',
            'username' => 'required|unique:users, "username",'.$id,
            'role' => 'required_if:id_user, null',
        ]);
   
        if($validator->stopOnFirstFailure()->fails()){
            return $this->ajaxResponse(false, $validator->errors()->first());       
        }

        $data_user = [
            'name' => $request->name,
            'username' => strtolower(str_replace(' ','',$request->username)),
            'email' => $request->email,
            'phone' => $request->phone,
            'uid_company' => $request->company,
            'id_role' => $request->role,
        ];

        if(!empty($id)) {
            $data_user['updated_at'] = Carbon::now();
        }else{
            $data_user['password'] = Hash::make('POSJAYA24');
            $data_user['created_at'] = Carbon::now();
        }

        $user = DB::table('users')->updateOrInsert(
            ['id' => $id],
            $data_user
        );

        if($user) {
            return $this->ajaxResponse(true, 'Data save successfully');
        }else{
            return $this->ajaxResponse(false, 'Failed to save data');
        } 
    }

    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ]);
   
        if($validator->stopOnFirstFailure()->fails()){
            return $this->ajaxResponse(false, $validator->errors()->first());       
        }

        $credential = $request->only('username', 'password');
        if(Auth::attempt($credential)){
            $menu = $this->menu->menu();
            $request->session()->push('menu', $menu);

            return $this->ajaxResponse(true, 'Sign In Successfully');
        }else{
            return $this->ajaxResponse(false, 'Incorrect username or password. Please try again');
        }

    }

    public function logout(Request $request){
        $request->session()->flush();
        Auth::logout();
        return redirect('login');
    }

    public function change_password()
    {
        $title = 'Change Password';
        $js = 'js/apps/auth/change-password.js?_='.rand();
        return view('auth/change_password', compact('js','title'));
    }

    public function process_change_password(Request $request)
    {
        $validator = Validator::make($request->all(), 
            [
                'password'     => 'required|min:4|max:255',
                're_password'  => 'required|same:password',
            ]
        );

        if($validator->stopOnFirstFailure()->fails()){
            return $this->ajaxResponse(false, $validator->errors()->first());        
        }

        if(Hash::check($request->old_password, auth()->user()->password)){ 
            $time = Carbon::now();
            $success = User::find(auth()->user()->id)->update(['password'=> bcrypt($request->password), 'updated_at' => $time]);

            return $this->ajaxResponse(true, 'Password save successfully');
        } 
        else{ 
            return $this->ajaxResponse(false, 'Incorrect Current Password');
        }
    }

    public function reset_password(Request $request)
    {
        if(!$request->ajax()) {
            return abort(404);
        }

        $time = Carbon::now();
        $id = $request->id;

        $user = User::where('id', $id)
                ->update(['password'=> Hash::make('POSJAYA24'), 'updated_at' => $time]);
        
        if($user) {
            return $this->ajaxResponse(true, 'Data save successfully');
        }else{
            return $this->ajaxResponse(false, 'Failed to save data');
        }
    }

    public function user_activation(Request $request) 
    {
        if(!$request->ajax()) {
            return abort(404);
        }

        $time = Carbon::now();
        $id = $request->id;
        $user = User::find($id)->first();

        if($user->status == 1) {
            $status = 0;
        }else{
            $status = 1;
        }

        $user = User::where('id', $id)
                ->update(['status' => $status, 'updated_at' => $time]);
        
        if($user) {
            return $this->ajaxResponse(true, 'Data save successfully');
        }else{
            return $this->ajaxResponse(false, 'Failed to save data');
        }
    }

    public function edit_user(Request $request) 
    {
        $id = $request->id;
        $user = $this->user->editUser($id);
        return $this->ajaxResponse(true, 'Success!', $user);
    }

    public function user_authenticate(Request $request) 
    {
        $user = Auth::user();
        $user = $this->user->editUser($user->id);
        return $this->ajaxResponse(true, 'Success!', $user);
    }
}
