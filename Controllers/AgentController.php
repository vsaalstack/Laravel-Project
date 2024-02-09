<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Services\ChargebeeServices;
use Illuminate\Http\Request;
use App\Services\ArticleWebsiteServices;
use App\Models\Agent;
use App\Models\Config;
use App\Models\Automation;
use App\Models\AutomationArticle;
use App\Models\Office;
use App\Models\Customer;
use App\Models\Listing;
use App\Models\Sharejob;
use App\Models\Facebook;
use App\Models\Chargebee;
use App\Models\User;
use Illuminate\Support\Str;
use Auth;
use DB;
use DataTables;

class AgentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = [];
        $imgCDN = Config::where('key', 'image_cdn')->value('value');

        if(isset($request->office) && $request->office != null){
            $data['name'] = Office::where('id', $request->office)->value('name');
        }

        if(isset($request->customer) && $request->customer != null){
            $data['customer'] = Customer::where('id', $request->customer)->value('name');
        }

        if($request->ajax()){

            $agent = Agent::with(['customer','user','office'])->select('agents.*','users.email','users.status','customers.name as cname')
            ->leftJoin('users', 'users.id','=','agents.user_id')
            ->leftJoin('customers', 'customers.id','=','agents.customer_id');
            // $agent = Agent::with(['customer','user','office']);


            if(Auth::user()->role == 'office'){
                $office = Office::where('user_id', Auth::id())->first();
                $agent = $agent->whereRaw('FIND_IN_SET("'.$office->id.'",office_id)');
            }

            if(!empty($request->office) && $request->office != null ){
                $agent = $agent->whereRaw('FIND_IN_SET("'.$request->office.'",office_id)');
            };

            if(!empty($request->customer) && $request->customer != null ){
                $agent = $agent->where('customer_id', $request->customer);
            };

            return DataTables::of($agent)
            ->addColumn('office', function($data){
                $ids = explode(',',$data->office_id);
                $office_id = Office::whereIn('id',$ids)->pluck('name')->toArray();
                return implode(', ',$office_id);
            })
            ->addColumn('status', function($data){
                return $data->user->status == 1 ? 'Active' : 'Inactive';
            })
            ->addColumn('action', function($data) {
                $switch = '';
                // $data->office->user_id
                /* if($data->user->status == '1' && $data->user->term == '1' && Auth::user()->is_admin == '1'){
                    $switch = '<a title="Switch User" href='.route('switch.user',[$data->user_id]).' class="btn btn-sm px-1"><i class="fa-thin fa-people-arrows fa-lg"></i></a>';
                } */
                if(\Auth::user()->role == 'office' && $data->user->status == '1'){
                    $switch = '<a title="Switch User" href='.route('switch.user',[$data->user_id]).' class="btn btn-sm px-1"><i class="fa-thin fa-people-arrows fa-lg"></i></a>';
                }
                return '
                <div class="btn-group">
                    <a title="Edit" href='.route('agent.edit', $data->id).' class="btn btn-sm px-1"><i class="icon-Button-Edit"></i></a>
                    <a title="Listing" href='.route('listing.index').'?agent_id='.$data->id.' class="btn btn-sm px-1"><i class="icon-Button_Listings"></i></a>
                    '.$switch.'
                </div>';
            })
            ->editColumn('profile_photo', function($row) use ($imgCDN){
                $photo = '';
                if(!empty($row->user->profile_photo)){
                    $imgUrl = $imgCDN . $row->user->profile_photo . '?w=80&h=80&func=face&face_margin=40';
                    $photo = '<img src="'.$imgUrl.'" >';
                }

                return $photo;
            })
            ->escapeColumns([])
            ->make(true);
        }
        return view('admin.agent.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $offices = Office::orderBy('name','asc')->get();
        $customers = Customer::orderBy('name','asc')->get();
        if(Auth::user()->role == 'office'){
            $offices = $offices->where('user_id', Auth::id())->first();
            $customers = $customers->where('id', $offices->customer_id);
        }
        return view('admin.agent.create',compact('offices','customers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'customer_id' => 'required',
            'office_id' => 'required',
            'email' => 'nullable|email|unique:users,email',
            // 'phone' => 'required|numeric|digits_between:5,20',
            'phone' => ['nullable', 'regex:/^[+0-9\s]+$/'],
            'url' => 'active_url|nullable',
        ]);

        $user = New User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->status = isset($request->status) ? $request->status : '0';
        isset($request->login) ? $user->login_method = $request->login : '';
        $user->timezone = $request->timezone;
        $user->save();

        $agent = New Agent;
        $agent->name = $request->name;
        $agent->user_id = $user->id;
        $agent->phone = $request->phone;
        $agent->customer_id = $request->customer_id;
        $agent->office_id = implode(',',$request->office_id);
        $agent->url = $request->url;
        $agent->instagram_handle = $request->instagram_handle;
        $agent->save();

        $auto = New Automation;
        $auto->user_id = $user->id;
        $auto->save();

        return redirect()->route('agent.index')->with('success', 'Agent is created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $agent = Agent::find($id);
        empty($agent) ? abort(404) : '';

        $offices = Office::orderBy('id','desc')->get();
        return view('admin.agent.show',compact('offices','agent'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $agent = Agent::find($id);

        $offices = Office::orderBy('name','asc')->get(); // get all the office list.
        $customers = Customer::orderBy('name','asc')->get();
        if(Auth::user()->role == 'office'){
            $offices = $offices->where('user_id', Auth::id())->first(); // user role is office this will only one office.
            $customers = $customers->where('id', $offices->customer_id);
            $office = Office::where('user_id', Auth::id())->first();
            $agent = Agent::where('id', $id)->whereRaw('FIND_IN_SET("'.$office->id.'",office_id)')->first();
        }

        empty($agent) ? abort(404) : '';

        $data = [];
        $data['tab'] = isset($_GET['tab']) ? $_GET['tab'] : 'details';

        if($data['tab'] == 'subscriptions'){
            $chargebee = Chargebee::where('user_id', $agent->user_id)->orderBy('id','desc')->get(); // old subcription list.
            $data['customer'] = Office::whereIn('id', explode(',',$agent->office_id))->first()->customer->name; // get the cutomer name.
            if(str_contains($data['customer'],'Harcourts')){
                $data['plan'] = $agent->user->subscription == '0' ? ChargebeeServices::planList() : null; // Get plan list if subcription is not active.
            }
            return view('admin.agent.subscription',compact('customers','offices','agent','chargebee','data'));
        }

        if($data['tab'] == 'settings'){
            $data['auto'] = AutomationArticle::where('user_id', $agent->user_id)->first();
            if (Auth::user()->is_admin == 1){
                $config = Config::where('key', 'apify_api')->first();
                $url = "https://api.apify.com/v2/actor-tasks?token=".$config->value;
                $json = file_get_contents($url);
                $data['tasks'] = json_decode($json);
            }
            return view('admin.agent.setting',compact('customers','offices','agent','data'));
        }

        return view('admin.agent.edit',compact('customers','offices','agent','data'));
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
        $agent = Agent::find($id);
        $user = User::find($agent->user_id);

        $request->validate([
            'name' => 'required',
            'customer_id' => 'required',
            'office_id' => 'required',
            // 'phone' => 'required|numeric|digits_between:5,20',
            'phone' => ['nullable', 'regex:/^[+0-9\s]+$/'],
            'url' => 'active_url|nullable',
            'email' => 'nullable|email|unique:users,email,'.$user->id,
        ]);

        // Update agent table
        if(Auth::user()->is_admin == '1'){
            $agent->name = $request->name;
        }
        $agent->phone = $request->phone;
        $agent->customer_id = $request->customer_id;
        $agent->office_id = implode(',',$request->office_id);
        $agent->url = $request->url;
        $agent->instagram_handle = $request->instagram_handle;
        $agent->update();

        // Update user table
        if(Auth::user()->is_admin == '1'){
            $user->name = $request->name;
            $user->login_method = $request->login;
        }
        $user->email = $request->email;
        $user->status = $request->status;
        $user->phone = $request->phone;
        $user->timezone = $request->timezone;
        $user->update();

        return redirect()->back()->with('success', 'Agent is updated successfully');
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

        if(Auth::user()->is_admin == 1 || Auth::user()->role == 'office'){
            $agent = Agent::find($id);
            $user = User::find($agent->user_id);
            if(empty($user->email)){
                return redirect()->back()->with('info', 'Agent mail id is not updated.');
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

    public function setting(Request $request, $id)
    {
        $agent = Agent::find($id);
        $article = AutomationArticle::where('user_id', $agent->user_id)->first();
        if(empty($article)){
            $article = New AutomationArticle;
            $article->user_id = $agent->user_id;
            $article->feed = Str::random(20);
        }
        $article->web_feed = $request->shareArticleFeed;
        $article->share_status = isset($request->status) ? $request->status : '0';
        if(Auth::user()->is_admin == '1'){
            if(isset($request->shareArticleMonitor)){
                $temp = explode('#trev@',$request->shareArticleMonitor);
                $article->monitor = json_encode(['id' => $temp[0],'name' => $temp[1]]);
            }else{
                $article->monitor = null;
            }
        }

        $article->save();

        if($request->execute == 'true'){
            $user = User::find($agent->user_id);
            if($user->status == '0'){
                return redirect()->back()->with('info','Agent is not active.');
            }elseif(empty($article->monitor) && empty($article->web_feed)){
                return redirect()->back()->with('info','Please update web monitor or feed.');
            }
            ArticleWebsiteServices::create($user->id);
            return redirect()->back()->with('success','Execute successfully.');
        }

        return redirect()->back()->with('success','Agent settings is updated successfully.');
    }

    public function office($id, $ids)
    {
        $offices = Office::where('customer_id',$id)->get();
        $office_id = explode(',',$ids);

        if(Auth::user()->role == 'office'){
            $offices = $offices->where('user_id', Auth::id());
        }

        $html = '';
        foreach ($offices as $office) {
            if(in_array($office->id,$office_id)){
                $html .= '<option value="'.$office->id.'" selected>'.ucfirst($office->name).'</option>';
            }else{
                $html .= '<option value="'.$office->id.'">'.ucfirst($office->name).'</option>';
            }
        }

        $data = [
            'status' => 'success',
            'offices' => $html,
        ];

        return $data;
    }
}
