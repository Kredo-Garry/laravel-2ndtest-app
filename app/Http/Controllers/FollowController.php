<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Follow;

class FollowController extends Controller
{
    private $follow;

    public function __construct(Follow $follow){
        $this->follow = $follow;
    }

    public function store($user_id){
        $this->follow->follow_id = Auth::user()->id;
        $this->follow->following_id = $user_id;
        $this->follow->save(); // INSERT INTO follows(follower_id, following_id) VALUES('Auth::user()->id', '$user_id');

        return redirect()->back();
    }
}
