<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\Websitemail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminLoginController extends Controller
{
    public function index()
    {
        return view('admin.login');
    }

    public function forget_password()
    {
        return view('admin.forget_password');
    }

    public function forget_password_submit(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $admin_data = User::where('email',$request->email)->first();
        if(!$admin_data) {
            return redirect()->back()->with('error','Email address not found!');
        }

        $token = hash('sha256',time());

        $admin_data->token = $token;
        $admin_data->update();

        $reset_link = url('admin/reset-password/'.$token.'/'.$request->email);
        $subject = 'Reset Password';
        $message = 'Please click on the following link: <br>';
        $message .= '<a href="'.$reset_link.'">Click here</a>';

        \Mail::to($request->email)->send(new Websitemail($subject,$message));

        return redirect()->route('admin_login')->with('success','Please check your email and follow the steps there');

    }

    public function login_submit(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credential = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if(Auth::guard('web')->attempt($credential)) {

            $url = '';

            if ($request->user()->role === 'admin') {
                $url = '/admin/home';
            } elseif ($request->user()->role === 'customer') {
                $url = '/customer/home';
            } 

            return redirect()->intended($url)->with('success', 'Login Successfully');
            // return redirect()->route('admin_home');
        } else {
            return redirect()->route('admin_login')->with('error', 'Information is not correct!');
        }
    }

    public function logout()
    {
        Auth::guard('web')->logout();
        return redirect()->route('admin_login');
    }

    public function reset_password($token,$email)
    {
        $admin_data = User::where('token',$token)->where('email',$email)->first();
        if(!$admin_data) {
            return redirect()->route('admin_login');
        }

        return view('admin.reset_password', compact('token','email'));

    }

    public function reset_password_submit(Request $request)
    {
        $request->validate([
            'password' => 'required',
            'retype_password' => 'required|same:password'
        ]);

        $admin_data = User::where('token',$request->token)->where('email',$request->email)->first();

        $admin_data->password = Hash::make($request->password);
        $admin_data->token = '';
        $admin_data->update();

        return redirect()->route('admin_login')->with('success', 'Password is reset successfully');

    }
}
