<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;
use Hash;
use Auth;

class UserController extends Controller {

    function __construct() {
        $this->middleware('permission:user-list');
        $this->middleware('permission:user-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:user-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:user-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $data = User::orderBy('id', 'DESC')->paginate(5);
        return view('users.index', compact('data'))
                        ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function profile() {
        return view('users.profile');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $roles = Role::pluck('name', 'name')->all();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        //prd($request->all());
        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'nullable',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'roles' => 'required'
        ]);

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);

        $user = User::create($input);
        $user->assignRole($request->input('roles'));


        return redirect()->route('users.index')
                        ->with('success', 'User created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $user = User::find($id);
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $user = User::find($id);
        $roles = Role::pluck('name', 'name')->all();
        $userRole = $user->roles->pluck('name', 'name')->all();

        return view('users.edit', compact('user', 'roles', 'userRole'));
    }

    public function update(Request $request, User $user) {
        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'same:confirm_password',
            'roles' => 'required'
        ]);

        $input = $request->all();
        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = array_except($input, array('password'));
        }

        $user->update($input);
        $user->syncRoles($request->roles);
        return redirect()->route('users.index')->withSuccess('User Updated Successfully!');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update_profile(Request $request, $id) { //die("hi update user");
        if (isset($request->form_type) && $request->form_type == 'account-form') {
            $this->validate($request, [
                'first_name' => 'required',
                'phone' => 'digits:10|unique:users,phone,' . $id,
                'address' => 'required',
                'profile_pic' => 'nullable|image'
            ]);
        } else {
            $this->validate($request, [
                'old_password' => 'required',
                'password' => 'required|same:confirm_password'
            ]);
        }

        $user = User::find($id);
        $input = $request->all();

        if (!empty($input['password'])) {
            $current_password = $user->password;
            if (!Hash::check($request->old_password, $current_password)) {
                return redirect()->back()->with('error', 'Invalid Old Password');
            } elseif (Hash::check($request->password, $current_password)) {
                return redirect()->back()->with('error', 'New Password should not be same as old password');
            }
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = array_except($input, array('password'));
        }

        if ($request->profile_pic != null) {
            $input['image'] = time() . '.' . $request->profile_pic->getClientOriginalExtension();
            $request->profile_pic->move(public_path('profile_pics'), $input['image']);
        }

        //$name_arr = explode(" ", $input['full_name']);
        //$input['first_name'] = isset($name_arr[0]) ? $name_arr[0] : null;
        //$input['last_name'] = isset($name_arr[1]) ? $name_arr[1] : null;
        // print_R($user);
        // print_R($input); die();
        $user->update($input);
        return redirect()->back()->with('success', 'Profile information updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        User::find($id)->delete();
        return redirect()->route('users.index')
                        ->with('success', 'User deleted successfully');
    }

}
