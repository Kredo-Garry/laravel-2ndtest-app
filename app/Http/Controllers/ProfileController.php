<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ProfileController extends Controller
{   
    const LOCAL_STORAGE_FOLDER = 'public/avatars/';
    private $user;

    public function __construct(User $user){
        $this->user = $user;
    }

    public function show($id){
        //retrieve a specific user based on $id
        $user = $this->user->findOrFail($id);
        return view('users.profile.show')->with('user', $user);
    }

    public function edit(){
        $user = $this->user->findOrFail(Auth::user()->id);
        return view('users.profile.edit')->with('user', $user);
    }

    public function update(Request $request){
        $request->validate([
            'name' => 'required|min:1|max:50',
            'email' => 'required|email|max:50|unique:users,email,' . Auth::user()->id,
            'avatar' => 'mimes:jpg,jpeg,png,gif|max:1048',
            'introduction' => 'max:100'
        ]);
        
        $user = $this->user->findOrFail(Auth::user()->id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->introduction = $request->introduction;

        //check if the user uploads an avatar
        if ($request->avatar) {
            //If the user currently has an avatar, delete the old one
            if ($user->avatar) {
                $this->deleteAvatar($user->avatar); // we don't have this yet
            }

            //save the new avatar into the storage
            $user->avatar = $this->saveAvatar($request);
        }

        $user->save();

        return redirect()->route('profile.show', Auth::user()->id);
    }

    public function deleteAvatar($avatar_name){
        $avatar_path = self::LOCAL_STORAGE_FOLDER . $avatar_name;
        if (Storage::disk('local')->exists($avatar_path)) {
            Storage::disk('local')->delete($avatar_path);
        }
    }

    public function saveAvatar($request){
        // rename the name of the avater into time format to avoid overwriting
        $avatar_name = time() . "." . $request->avatar->extension();

        //move the avatar to the local storage
        $request->avatar->storeAs(self::LOCAL_STORAGE_FOLDER, $avatar_name);

        return $avatar_name;
    }


}
