<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\User;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //dd(User::latest()->paginate(5));
        if (\Gate::allows('isAdmin') || \Gate::allows('isAuthor')) {
            return User::latest()->paginate(5);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' =>'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:users',
            'password'=> 'required|string|min:5'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }
        else {
            $user = User::create([
                'name' => $request['name'],
                'email' => $request['email'],
                'type' => $request['type'],
                'bio' => $request['bio'],
                'photo' => $request['photo'],
                'password' => Hash::make($request['password']),
            ]);

            return response()->json(['message' => 'User was created'], 201);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return User :: findOrFail($id);
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
     
        $user = User :: findOrFail($id);
        // $validator = $this->validate($request, [
        //     'name' =>'required|string|max:191',
        //     'email' => 'required|string|email|max:191|unique:users,email,'.$user->id,
        //     'password'=> 'sometimes|min:5'
        //     ]);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:users,email,' . $user->id,
            'password' => 'sometimes|min:5'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }
        else {
            $user->update($request->all());
            return response()->json(['message' => 'Update user\'s info'], 200);
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

        $this->authorize('isAdmin');
        $user = User::findOrFail($id);
        
        if($user->delete()){      
            return response()->json(['message' => 'User deleted'], 200);
        }
    }

    public function profile() {
        return auth('api')->user();
    }

    public function updateProfile(Request $request) {
        $user = auth('api')->user();
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:users,email,' . $user->id,
            'password' => 'sometimes|min:5'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }
        $currentPhoto = $user->photo;
        if($request->photo != $currentPhoto) {     
            $userPhoto = public_path('img/profile/').$currentPhoto;
            if(file_exists($userPhoto)) {
                unlink($userPhoto);
            }
         
            $name = time().'.' . explode('/', explode(':', substr($request->photo, 0, strpos($request->photo, ';')))[1])[1];
            \Image::make($request->photo)->save(public_path('img/profile/').$name);
     
            $request->merge(['photo' => $name]);
           
        }

        if(!empty($request->password)) {
            $request->merge(['password' => Hash::make($request['password'])]);
        }
        
        $user->update($request->all());

        return ['message' => 'User\'s profile was updated'];
    }
    public function findUser($param) {
        return ['message' =>$param];
    }

}
