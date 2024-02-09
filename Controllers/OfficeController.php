<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Office;
use App\Models\User;
use App\Models\Agent;
use App\Models\ArticlesWebsite;
use App\Models\Automation;
use App\Models\Config;
use App\Models\Listing;
use App\Models\Facebook;
use App\Models\Chargebee;
use App\Models\AutomationArticle;
use App\Models\AutomationInstagramLibrary;
use App\Models\Brand;
use App\Models\ListingImage;
use App\Models\MyArticle;
use App\Models\MyInstagramLibrary;
use App\Models\SharedJobHistory;
use App\Models\Sharejob;
use App\Services\ArticleWebsiteServices;
use App\Services\ChargebeeServices;
use Illuminate\Support\Str;
use Auth;
use ChargeBee\ChargeBee\Models\Subscription;
use DataTables;

class OfficeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $office = Office::with('customer')->select('offices.*', 'customers.name as cname')
                ->leftJoin('customers', 'customers.id', '=', 'offices.customer_id');

            if (Auth::user()->role == 'agent') {
                $agent = Agent::where('user_id', Auth::user()->id)->first();
                foreach (explode(',', $agent->office_id) as $row) {
                    $office = $office->orWhere('id', $row);
                }
            }
            if (!empty($request->id)) {
                $office = $office->where('customer_id', $request->id);
            }

            return DataTables::of($office)
                ->editColumn('apify', function ($data) {
                    return !empty($data->apify_task_id) ? ucfirst(json_decode($data->apify_task_id)->name) : '';
                })
                ->editColumn('status', function ($data) {
                    return $data->status == 1 ? 'Enable' : 'Disable';
                })
                ->addColumn('action', function ($data) {
                    $switch = '';
                    $config = Config::where('key', 'free_trial_customer')->first();
                    $delete = '';
                    if ($config->value == $data->customer_id) {
                        $delete = '<a href="JavaScript:void(0);" data-url="' . route('office.destroy', $data->id) . '" class="btn btn-sm px-1 deleteOffice" title="Delete Office"><i class="fa-light fa-trash-can"></i></a>';
                    }
                    if ($data->user->status == '1' && $data->user->term == '1') {
                        $switch = '<a title="Switch User" href=' . route('switch.user', [$data->user_id]) . ' class="btn btn-sm px-1"><i class="fa-thin fa-people-arrows fa-lg"></i></a>';
                    }
                    return '
                <div class="btn-group">
                    <a title="Edit" href=' . route('office.edit', $data->id) . ' class="btn btn-sm px-1"><i class="icon-Button-Edit"></i></a>
                    <a title="Show" href=' . route('office.show', $data->id) . ' class="btn btn-sm px-1"><i class="icon-Button-View2"></i></a>
                    <a title="Listing" href=' . route('listing.index') . '?office_id=' . $data->id . ' class="btn btn-sm px-1"><i class="icon-Button_Listings"></i></a>
                    <a title="Agents" href="' . route('agent.index') . '?office=' . $data->id . '" class="btn btn-sm px-1"><i class="icon-Button_Agents"></i></a>
                    ' . $delete . '
                    ' . $switch . '
                </div>';
                })
                ->escapeColumns([])
                ->make(true);
        }

        return view('admin.office.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $customers = Customer::orderBy('id', 'desc')->get();
        return view('admin.office.create', compact('customers'));
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
            // 'phone' => 'required|numeric|digits_between:5,20',
            'phone' => ['nullable', 'regex:/^[+0-9\s]+$/'],
            'website_url' => 'active_url|nullable',
            'listing_url' => 'active_url|nullable',
            'email' => 'required|email|unique:users,email',
        ]);

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->role = 'office';
        $user->status = $request->officeStatus;
        $user->login_method = $request->login;
        $user->save();

        $office = new Office;
        $office->user_id = $user->id;
        $office->customer_id = $request->customer_id;
        $office->name = $request->name;
        $office->website_url = $request->website_url;
        $office->listing_url = $request->listing_url;
        if (Auth::user()->is_admin == '1') {
            $office->status = $request->status;
        }
        if (isset($request->apify_task_id) && Auth::user()->is_admin == '1') {
            $temp = explode('#trev@', $request->apify_task_id);
            $office->apify_task_id = json_encode(['id' => $temp[0], 'name' => $temp[1]]);
        }
        $office->save();

        // Agents do not currently have the authority to create an office.
        /**if(Auth::user()->role == 'agent'){
            $agent = Agent::where('user_id', Auth::user()->id)->first();
            $offices = explode(',',$agent->office_id);
            array_push($offices, $office->id);
            $agent->office_id = implode(',',$offices);
            $agent->update();
        }*/

        return redirect()->route('office.index')->with('success', 'Office details added successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $office = Office::find($id);
        empty($office) ? abort(404) : '';

        if (Auth::user()->role == 'agent') {
            $agent = Agent::where('user_id', Auth::user()->id)->where('office_id', 'LIKE', "%$office->id%")->first();
            empty($agent) ? abort(401) : '';
        }
        $chargebee = Chargebee::where('user_id', $office->user_id)->orderBy('id', 'desc')->get();
        return view('admin.office.show', compact('office', 'chargebee'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $office = Office::find($id);
        empty($office) ? abort(404) : '';

        $customers = Customer::orderBy('id', 'desc')->get(); // get all customer list.
        $user = User::find($office->user_id);

        $data = [];
        $data['tab'] = isset($_GET['tab']) ? $_GET['tab'] : 'details';

        if ($data['tab'] == 'subscriptions') {
            $chargebee = Chargebee::where('user_id', $office->user_id)->orderBy('id', 'desc')->get(); // old subcription list.
            $data['customer'] = $office->customer->name; // get the cutomer name.
            if (str_contains($data['customer'], 'Harcourts')) {
                $data['plan'] = $office->user->subscription == '0' ? ChargebeeServices::planList() : null; // Get plan list if subcription is not active.
            }
            return view('admin.office.subscription', compact('customers', 'office', 'user', 'chargebee', 'data', 'user'));
        }

        if ($data['tab'] == 'settings') {

            $data['auto'] = AutomationArticle::where('user_id', $office->user_id)->first();
            if (Auth::user()->is_admin == '1') {
                $config = Config::where('key', 'apify_api')->first();
                $url = "https://api.apify.com/v2/actor-tasks?token=" . $config->value;
                $json = file_get_contents($url);
                $data['tasks'] = json_decode($json);
            }
            return view('admin.office.setting', compact('customers', 'office', 'user', 'data', 'user'));
        }

        return view('admin.office.edit', compact('customers', 'office', 'user', 'data'));
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

        $office = Office::find($id);
        $user = User::where('id', $office->user_id)->first();

        $request->validate([
            'name' => 'required',
            'customer_id' => 'required',
            // 'phone' => 'required|numeric|digits_between:5,20',
            'phone' => ['nullable', 'regex:/^[+0-9\s]+$/'],
            'website_url' => 'active_url|nullable',
            'listing_url' => 'active_url|nullable',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        if ($request->customer_id != $office->customer_id) {
            Agent::whereRaw('FIND_IN_SET(' . $office->id . ', office_id)')->update(['customer_id' => $request->customer_id]);
        }

        $office->customer_id = $request->customer_id;
        $office->website_url = $request->website_url;
        $office->listing_url = $request->listing_url;

        if (Auth::user()->is_admin == '1') {
            $office->name = $request->name;
            $office->status = $request->status;
            if (isset($request->apify_task_id)) {
                $temp = explode('#trev@', $request->apify_task_id);
                $office->apify_task_id = json_encode(['id' => $temp[0], 'name' => $temp[1]]);
            } else {
                $office->apify_task_id = null;
            }
        }
        $office->update();


        if (Auth::user()->is_admin == '1') {
            $user->name = $request->name;
        }
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->login_method = $request->login;
        $user->status = $request->officeStatus;
        $user->timezone = $request->timezone;
        $user->save();

        return redirect()->back()->with('success', 'Office details updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $office = Office::find($id);
            $agntUserId =  [];
            $officeUserIds =  $office->user_id;
            $agents = Agent::whereRaw('FIND_IN_SET(' . $office->id . ', office_id)')->get();
            foreach ($agents as $agent) {
                $officeIds = explode(',', $agent->office_id);
                if (count($officeIds) > 1) {
                    $valueToRemove = $id;
                    $result = array_filter($officeIds, function ($value) use ($valueToRemove) {
                        return $value !== $valueToRemove;
                    });
                    $newOfficeId = implode(',', $result);
                    $agent->office_id = $newOfficeId;
                    $agent->save();
                } else {
                    $agntUserId[] = $agent->user_id;
                    $agent->delete();
                }
            }
            $userId = [];
            if (!empty($agntUserId)) {
                $userId = array_merge_recursive($userId, $agntUserId);
            }
            $userId[] = $officeUserIds;

            Chargebee::whereIn('user_id', $userId)->delete();
            MyInstagramLibrary::whereIn('user_id', $userId)->delete();
            MyArticle::whereIn('user_id', $userId)->delete();
            ArticlesWebsite::whereIn('user_id', $userId)->delete();
            Facebook::whereIn('user_id', $userId)->delete();
            Brand::whereIn('user_id', $userId)->delete();
            $sharejobIds = Sharejob::whereIn('user_id',  $userId)->pluck('id')->toArray();
            Sharejob::whereIn('user_id', $userId)->delete();
            SharedJobHistory::whereIn('sharejob_id', $sharejobIds)->delete();
            Automation::whereIn('user_id', $userId)->delete();
            AutomationArticle::whereIn('user_id', $userId)->delete();
            AutomationInstagramLibrary::whereIn('user_id', $userId)->delete();
            $listingIds = Listing::where('office_id', $id)->pluck('id')->toArray();
            Listing::where('office_id', $id)->delete();
            ListingImage::whereIn('listing_id', $listingIds)->delete();
            User::whereIn('id', $userId)->delete();
            $office->delete();

            return response()->json([
                'status' => 'success',
                'msg' => 'Office has been deleted successfully.',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'fail',
                'msg' => 'Something went wrong please try again.',
            ]);
        }
    }

    public function listing($id)
    {
        $data['name'] = Office::find($id)->name;
        $facebook = Facebook::where('user_id', Auth::user()->id)->orderBy('id', 'desc')->first();

        if (Auth::user()->is_admin == '1') {
            $listings = Listing::with('images')->where('office_id', $id)->orderBy('id', 'desc')->get();
        } elseif (Auth::user()->role == 'agent') {
            $agent = Agent::where('user_id', Auth::user()->id)->where('office_id', 'LIKE', "%$id%")->first();
            if (empty($agent)) {
                return view('errors.401');
            }
            $listings = Listing::with('images')->where('agent_id', $agent->id)->where('office_id', $id)->orderBy('id', 'desc')->get();
        }

        return view('admin.listing.sale', compact('listings', 'facebook', 'data'));
    }

    public function apifyTask($id)
    {
        $config = Config::where('key', 'apify_api')->first();
        $url = "https://api.apify.com/v2/actor-tasks?token=" . $config->value;
        $json = file_get_contents($url);
        $ids = json_decode($json);


        if ($id != 'null') {
            $customers = Customer::find($id);
            !empty($customers->apify_actor_id) ? $actId = json_decode($customers->apify_actor_id)->id : $actId = null;
        } else {
            $actId = null;
        }

        $html = '';
        $html = !empty($actId) ? '<option selected  value="">Select Apify Task Id </option>' : '<option selected  value="">There is no option available</option>';

        foreach ($ids as $id) {
            foreach ($id->items as $item) {
                if ($item->actId == $actId && !str_contains($item->name, 'Blogs')) {
                    $html .= '<option value="' . $item->id . '#trev@' . $item->name . '">' . ucfirst($item->name) . '</option>';
                }
            }
        }

        $data = [
            'status' => 'success',
            'taskList' => $html,
        ];

        return $data;
    }

    public function setting(Request $request, $id)
    {
        $office = Office::find($id);
        $article = AutomationArticle::where('user_id', $office->user_id)->first();
        if (empty($article)) {
            $article = new AutomationArticle;
            $article->user_id = $office->user_id;
            $article->feed = Str::random(20);
        }
        $article->web_feed = $request->shareArticleFeed;
        $article->share_status = isset($request->status) ? $request->status : '0';
        if (Auth::user()->is_admin == '1') {
            if (isset($request->shareArticleMonitor)) {
                $temp = explode('#trev@', $request->shareArticleMonitor);
                $article->monitor = json_encode(['id' => $temp[0], 'name' => $temp[1]]);
            } else {
                $article->monitor = null;
            }
        }

        $article->save();

        if ($request->execute == 'true') {
            $user = User::find($office->user_id);
            if ($user->status == '0') {
                return redirect()->back()->with('info', 'Office is not active.');
            } elseif (empty($article->monitor) && empty($article->web_feed)) {
                return redirect()->back()->with('info', 'Please update web monitor or feed.');
            }
            ArticleWebsiteServices::create($user->id);
            return redirect()->back()->with('success', 'Execute successfully.');
        }

        return redirect()->back()->with('success', 'Office settings is updated successfully.');
    }
}
