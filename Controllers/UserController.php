<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Agent;
use App\Models\Office;
use App\Models\Config;
use App\Models\Chargebee;
use App\Services\ChargebeeServices;
use Illuminate\Support\Facades\Auth;
use DB;
use Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [];
        $imgCDN = Config::where('key', 'image_cdn')->value('value');
        if(Auth::user()->is_admin == '0'){

            // get the subscription plan list.
            if(Auth::user()->subscription == '0'){
                $data = ChargebeeServices::planList();
            }

            // get the cutomer name.
            if(Auth::user()->role == 'agent'){
                $agent = Agent::where('user_id', Auth::user()->id)->first();
                $data['customer'] = Office::whereIn('id', explode(',',$agent->office_id))->first()->customer->name;
            }elseif(Auth::user()->role == 'office'){
                $data['customer'] = Office::where('user_id', Auth::id())->first()->customer->name;
            }

            // get the user subscription details.
            $data['chargebee'] = Chargebee::where('user_id', Auth::id())->orderBy('id','desc')->get();
        }

        return view('admin.user.index',compact('data', 'imgCDN'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(Auth::user()->super_admin == '1'){
            return view('admin.user.create');
        }
        abort(401);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(Auth::user()->super_admin != '1'){
            abort(401);
        }
        $request->validate([
            'name' => 'required',
            'timezone' => 'required',
            // 'phone' => 'required|numeric|digits_between:5,20',
            'phone' => ['required', 'regex:/^[+0-9\s]+$/'],
            'email' => 'email|unique:users,email',
        ]);

        $user = new User;
        $user->name = $request->name;
        $user->timezone = $request->timezone;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->status = $request->status;
        $user->is_admin = '1';
        $user->subscription = '1';
        $user->role = 'admin';
        $user->login_method = $request->login;
        $user->save();

        return redirect()->route('admin.user')->with('success', 'Admin user has been created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(Auth::user()->super_admin == '1'){
            $user = User::where(['id' => $id, 'is_admin' => '1'])->first();
            if(empty($user)){
                abort(404);
            }else{
                return view('admin.user.edit',compact('user'));
            }
        }
        abort(401);
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
        $request->validate([
            'name' => 'required',
            // 'phone' => 'required|numeric|digits_between:5,20',
            'phone' => ['required', 'regex:/^[+0-9\s]+$/'],
            'timezone' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
        ]);

        $user = User::find($id);

        if(Auth::user()->is_admin == 1){
            $user->name = $request->name;
            $user->status = $request->status ?? $user->status;
            $user->login_method = $request->login ?? $user->login_method;
        }

        $user->timezone = $request->timezone;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->update();

        if($user->role == 'agent'){
            $agent = Agent::where('user_id', $id)->first();
            if(Auth::user()->is_admin == 1){
                $agent->name = $request->name;
            }
            $agent->instagram_handle = $request->instagram_handle;
            $agent->phone = $request->phone;
            $agent->update();
        }

        if($user->role == 'office'){
            $office = Office::where('user_id', $id)->first();
            if(Auth::user()->is_admin == 1){
                $office->name = $request->name;
            }
            $office->update();
        }

        return redirect()->back()->with('success', 'Your details are updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort(404);
    }

    public function password($id)
    {
        if(Auth::user()->is_admin == 1){
            $user = User::find($id);

            if(empty($user->email)){
                return redirect()->back()->with('info', ucfirst($user->role).' mail id is not updated.');
            }

            if(!empty($user->email)){
                $from = Config::where('key', 'from_email')->value('value');
                $to = $user->email;
                $rest = DB::table('password_resets')->where([
                    'email' => $user->email
                ])->count();

                if($rest > 0){
                    $rest = DB::table('password_resets')->where([
                        'email' => $user->email
                    ])->first();
                    $token = $rest->token;

                }else{
                    $token = Str::random(60);
                    DB::table('password_resets')->insert([
                        'email' => $to,
                        'token' => $token,
                        'created_at' => date(now())
                    ]);
                }

                $sub = "Password Reset link";
                $message = "
                <html>
                <head>
                    <title>Password Reset Link | The Real Estate Voice</title>
                </head>
                <body>
                    <h5 style='font-weight: 300; font-size: 1rem;'>Hi ".$user->name.",</h5>
                    <h5 style='font-weight: 300; font-size: 1rem;'>You have requested to reset your password for The Real Estate Voice Dashboard. If this was not you, please ignore this email. If you did request a password reset please <a href='".route('password.reset',[$token])."?email=".$to."'>click this link.</a></h5>
                    <h5 style='font-weight: 300; font-size: 1rem;'>Thanks</h5>
                    <h5 style='font-weight: 300; font-size: 1rem;'>The Real Estate Voice Team.</h5>
                </body>
                </html>
                ";
                // Set content-type header for sending HTML email
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $headers .= "From: The Real Estate Voice <".$from.">\r\n";

                mail($to, $sub, $message, $headers);

                return redirect()->back()->with('success', 'Rest password link sent successfully');
            }
        }else{
            abort(401);
        }
    }

    public function subscriptionDetails(){
        $data = [];
        if(Auth::user()->is_admin == '0'){

            // get the subscription plan list.
            if(Auth::user()->subscription == '0'){
                $data = ChargebeeServices::planList();
            }

            // get the cutomer name.
            if(Auth::user()->role == 'agent'){
                $agent = Agent::where('user_id', Auth::user()->id)->first();
                $data['customer'] = Office::whereIn('id', explode(',',$agent->office_id))->first()->customer->name;
            }elseif(Auth::user()->role == 'office'){
                $data['customer'] = Office::where('user_id', Auth::id())->first()->customer->name;
            }

            // get the user subscription details.
            $data['chargebee'] = Chargebee::where('user_id', Auth::id())->orderBy('id','desc')->get();
        }

        return view('admin.user.subscription-details',compact('data'));
    }
}
