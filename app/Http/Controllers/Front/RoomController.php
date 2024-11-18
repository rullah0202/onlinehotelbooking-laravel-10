<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index()
    {
        $room_all = Room::paginate(12);
        return view('front.room', compact('room_all'));
    }

    public function single_room($id)
    {
        $single_room_data = Room::with('rRoomPhoto')->where('id',$id)->first();
        return view('front.room_detail', compact('single_room_data'));
    }
}
