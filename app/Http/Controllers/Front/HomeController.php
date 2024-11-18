<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use App\Models\Post;
use App\Models\Room;
use App\Models\Slide;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $slide_all = Slide::get();
        $feature_all = Feature::get();
        $testimonial_all = Testimonial::get();
        $post_all = Post::orderBy('id','desc')->limit(3)->get();
        $room_all = Room::get();

        return view('front.home', compact('slide_all','feature_all','testimonial_all','post_all','room_all'));
    }
}
