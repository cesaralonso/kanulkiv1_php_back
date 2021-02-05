<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Role;
use Illuminate\Support\Facades\Auth;
use Validator;
use DB;

class UserController extends Controller
{
    public $successStatus = 200;

    /**
     * Login
     *
     * @return \Illuminate\Http\Response
     */
    public function login()
    {
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();
            // $success['token'] = $user->createToken('kanulki')-> accessToken;

            if ($user->hasRole('admin')) {
                $role = 1;
                $user->role_id = $role;
            } else if ($user->hasRole('user')) {
                $role = 2;
                $user->role_id = $role;
            }

            // return response()->json(['successToken' => $success['token'], 'user' => $user], $this-> successStatus);
            return response()->json(['user' => $user], $this-> successStatus);
        }
        else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    /**
     * Register
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        // $success['token'] = $user->createToken('kanulki')->accessToken;
        $success['user'] = $user;

        $user->roles()->attach(Role::where('name', 'user')->first());

        return response()->json(['success' => $success], $this-> successStatus);
    }

    /**
     * Details
     *
     * @return \Illuminate\Http\Response
     */
    public function details()
    {
        $user = Auth::user();

        return response()->json(['success' => $user], $this-> successStatus);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = DB::table('users')
                ->join('role_user', 'role_user.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'role_user.role_id')
                ->select('users.*', 'roles.description as role_description', 'roles.id as role_id')
                ->get();

        return $users;
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

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = DB::table('users')
                ->join('role_user', 'role_user.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'role_user.role_id')
                ->select('users.*', 'roles.description as role_description', 'roles.id as role_id')
                ->where('users.id', $id)
                ->get();

        return response()->json($user);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function usersByCondoID($condo_id)
    {
        $query = "ABS(house_id) ASC";
        $user = DB::table('tenants')
                ->join('users', 'tenants.user_id', '=', 'users.id')
                ->join('houses', 'houses.id', '=', 'tenants.house_id')
                ->select('tenants.*', 'users.email', 'houses.name as house_name', 'houses.description as house_description')
                ->where('tenants.condo_id', $condo_id)
                ->orderByRaw($query)->get();

        return response()->json($user);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function userDetailByUserID($user_id)
    {

        $user = DB::table('users')
                ->join('role_user', 'role_user.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'role_user.role_id')
                ->join('tenants', 'tenants.user_id', '=', 'users.id')
                ->join('houses', 'houses.id', '=', 'tenants.house_id')
                ->select('users.*', 'roles.description as role_description', 'roles.id as role_id', 'houses.name as house_name', 'houses.description as house_description')
                ->where('users.id', $user_id)
                ->first();

        //dd($user);
        return response()->json($user);
    }

    public function totalUsers()
    {
        $total_users = DB::table('tenants')
                ->join('users', 'tenants.user_id', '=', 'users.id')
                ->select(DB::raw('count(*) total_users'))
                ->get();
        //dd($total_users);
        return response()->json($total_users);
    }

    public function totalUsersByCondoID($condo_id)
    {
        $total_users = DB::table('tenants')
                ->join('users', 'tenants.user_id', '=', 'users.id')
                ->select(DB::raw('count(*) total_users'))
                ->where('tenants.condo_id', $condo_id)
                ->first();

        return response()->json($total_users);
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
    public function update(Request $request, $id)
    {
        User::find($id)->update($request->all());

        $user = User::where('id', $id)->first();

        return response()->json(array('user' => $user), 200);
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

    public function changePassword(Request $request){
        //dd($request);
        $validator = Validator::make($request->all(), [
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }

        $user = User::where('id', $request->user_id)->get()->first();
        $user->password = bcrypt($request->password);
        $user->save();

        $success['user'] = $user;

        return response()->json(['success' => $success], $this-> successStatus);
    }
}
