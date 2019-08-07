<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Plan;
use DB;


/**
 * @group Deprecados
 *
 * Funciones de PlanController. Ahora se usa el SubscriptionPlanController
 */
class PlanController extends Controller {

    function __construct() {
        $this->middleware('auth');
        $this->middleware('permission:plan-list');
        $this->middleware('permission:plan-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:plan-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:plan-delete', ['only' => ['destroy']]);
        $this->middleware('permission:plan-cancel', ['only' => ['cancelPlan', 'confirmCancelPlan']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        if (auth()->user()->roles[0]->name != 'Super Admin') {
            abort(403);
        }

        $data = Plan::orderBy('id', 'DESC')->paginate(10);
        return view('plans.index', compact('data'))->with('i', ($request->input('page', 1) - 1) * 10);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('plans.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        $this->validate($request, [
            'plan_type' => 'required',
            'plan_name' => 'required',
            'no_of_companies' => 'required|numeric',
            'no_of_admin_user' => 'required|numeric',
            'no_of_invited_user' => 'required|numeric',
            'no_of_invoices' => 'required|numeric',
            'no_of_bills' => 'required|numeric',
            'chat_support' => 'required',
            'ticket_sla' => 'required|numeric',
            'calls_per_month' => 'nullable|numeric',
            'additional_call_rates' => 'nullable|numeric',
            'initial_setup_virtual' => 'required',
            'initial_setup_meeting' => 'required',
            'multicurrency' => 'required',
            'e_invoicing' => 'required',
            'pre_invoicing' => 'required',
            'vat_declaration' => 'required',
            'basic_report' => 'required',
            'custom_report' => 'required',
            'monthly_price' => 'required|numeric',
            'annual_price' => 'required|numeric'
                ], [
            'required' => 'This field is required.',
            'numeric' => 'This field should be in number.'
        ]);

        $input = $request->all();

        DB::beginTransaction();

        try {
            $input['plan_slug'] = str_replace(" ", "-", strtolower($input['plan_name']));

            $input['no_of_invoices'] = ($input['no_of_invoices'] != '-1') ? $input['no_of_invoices'] : null;
            $input['no_of_bills'] = ($input['no_of_bills'] != '-1') ? $input['no_of_bills'] : null;
            $input['no_of_companies'] = ($input['no_of_companies'] != '-1') ? $input['no_of_companies'] : null;
            $input['no_of_admin_user'] = ($input['no_of_admin_user'] != '-1') ? $input['no_of_admin_user'] : null;
            $input['no_of_invited_user'] = ($input['no_of_invited_user'] != '-1') ? $input['no_of_invited_user'] : null;

            Plan::create($input);

            DB::commit();
            return redirect()->route('plans.index')->with('success', 'Plan created successfully');
        } catch (QueryException $e) {
            DB::rollBack();
            return redirect()->route('plans.index')->with('error', 'Something went wrong, Please try again.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $plan = Plan::find($id);
        return view('plans.show', compact('plan'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $plan = Plan::find($id);

        return view('plans.edit', compact('plan'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        $this->validate($request, [
            'plan_type' => 'required',
            'plan_name' => 'required',
            'no_of_companies' => 'required|numeric',
            'no_of_admin_user' => 'required|numeric',
            'no_of_invited_user' => 'required|numeric',
            'no_of_invoices' => 'required|numeric',
            'no_of_bills' => 'required|numeric',
            'chat_support' => 'required',
            'ticket_sla' => 'required|numeric',
            'calls_per_month' => 'nullable|numeric',
            'additional_call_rates' => 'nullable|numeric',
            'initial_setup_virtual' => 'required',
            'initial_setup_meeting' => 'required',
            'multicurrency' => 'required',
            'e_invoicing' => 'required',
            'pre_invoicing' => 'required',
            'vat_declaration' => 'required',
            'basic_report' => 'required',
            'custom_report' => 'required',
            'monthly_price' => 'required|numeric',
            'annual_price' => 'required|numeric'
                ], [
            'required' => 'This field is required.',
            'numeric' => 'This field should be in number.'
        ]);

        $input = $request->all();

        DB::beginTransaction();

        try {
            $plan = Plan::find($id);

            $input['plan_slug'] = str_replace(" ", "-", strtolower($input['plan_name']));
            $input['plan_slug'] = str_replace(" ", "-", strtolower($input['plan_name']));

            $input['no_of_invoices'] = ($input['no_of_invoices'] != '-1') ? $input['no_of_invoices'] : null;
            $input['no_of_bills'] = ($input['no_of_bills'] != '-1') ? $input['no_of_bills'] : null;
            $input['no_of_companies'] = ($input['no_of_companies'] != '-1') ? $input['no_of_companies'] : null;
            $input['no_of_admin_user'] = ($input['no_of_admin_user'] != '-1') ? $input['no_of_admin_user'] : null;
            $input['no_of_invited_user'] = ($input['no_of_invited_user'] != '-1') ? $input['no_of_invited_user'] : null;

            $plan->update($input);

            DB::commit();
            return redirect()->route('plans.index')->with('success', 'Plan Details updated successfully');
        } catch (QueryException $e) {
            DB::rollBack();
            return redirect()->route('plans.index')->with('error', 'Something went wrong, Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        Plan::find($id)->delete();
        return redirect()->route('plans.index')->with('success', 'Plan deleted successfully');
    }

    public function show_plans() {
        $plans = Plan::orderBy('id', 'DESC')->get();
        $users = \App\User::orderBy('id', 'DESC')->get();
        return view('plans.purchase', compact('plans', 'users'));
    }

    /* Sends confirmation email to cancel selected plan */

    public function cancelPlan($planNo) {
        $plan = \App\UserSubscription::select('user_subscriptions_history.*', 'subscription_plans.plan_name', 'subscription_plans.plan_type')->leftJoin('subscription_plans', 'subscription_plans.id', '=', 'user_subscriptions_history.plan_id')->where('unique_no', $planNo)->first();

        if ($plan) {
            if ($plan->user_id == auth()->user()->id) {

                $plan->cancellation_token = base64_encode(date('Y-m-d H:i') . '|' . $planNo);
                $plan->save();
                \Mail::to(auth()->user()->email)->send(new \App\Mail\SubscriptionCancellationMail(['plan_name' => ucfirst($plan->plan_type) . '-' . $plan->plan_name . '(' . $plan->unique_no . ')', 'token' => $plan->cancellation_token]));
                return redirect()->route('User.plans')->with('success', 'Mail has been sent with confirmation link and is valid only for 24 Hours.');
            } else {
                return redirect()->route('User.plans')->with('error', 'You are not authorize to cancel this subscription plan.');
            }
        } else {
            return redirect()->route('User.plans')->with('error', 'Invalid subscription plan.');
        }
    }

    /* Cancel the plan on email confirmation */

    public function confirmCancelPlan($token) {

        $plan = \App\UserSubscription::where('cancellation_token', $token)->first();

        if ($plan) {

            /* Check Token validity for 24 Hours */
            if (!token_expired($plan->cancellation_token)) {
                if ($plan->user_id == auth()->user()->id) {
                    if ($plan->status == '1') {
                        $plan->cancellation_token = null;
                        $plan->status = '0';
                        $plan->save();
                        return redirect()->route('User.plans')->with('success', 'Subscription plan cancelled successfully.');
                    } else {
                        return redirect()->route('User.plans')->with('error', 'Subscription plan already cancelled.');
                    }
                } else {
                    return redirect()->route('User.plans')->with('error', 'You are not authorize to cancel this subscription plan.');
                }
            } else {
                return redirect()->route('User.plans')->with('error', 'Link has been Expired.');
            }
        } else {
            return redirect()->route('User.plans')->with('error', 'Invalid Link or Subscription plan already cancelled.');
        }
    }

    public function purchase(Request $request) {
        $this->validate($request, [
            'user_id' => 'required|numeric',
            'plan_id' => 'required|numeric'
        ]);

        $input = $request->all();
        $route = (auth()->user()->roles[0]->name == 'Super Admin') ? 'plans.index' : 'User.plans';
        DB::beginTransaction();

        try {
            $input['unique_no'] = $request->user_id . $request->plan_id . mt_rand(1000, 9999);
            $input['start_date'] = date('Y-m-d');
            $input['expiry_date'] = date('Y-m-d', strtotime("+1 month"));

            \App\UserSubscription::create($input);

            DB::commit();
            return redirect()->route($route)->with('success', 'Plan purchased successfully');
        } catch (QueryException $e) {
            DB::rollBack();
            return redirect()->route($route)->with('error', 'Something went wrong, Please try again.');
        }
    }

    /* Switch/Upgrade selected plan and Sends email telling the user about the new plan switch */

    public function switchPlan($currentPlan, $newPlan) {

        $plan = \App\UserSubscription::select('user_subscriptions_history.*', 'subscription_plans.plan_name', 'subscription_plans.plan_type')->leftJoin('subscription_plans', 'subscription_plans.id', '=', 'user_subscriptions_history.plan_id')->where('unique_no', $currentPlan)->first();

        if ($plan) {

            $plan->plan_id = $newPlan;
            $plan->save();

            $user = \App\User::find($plan->user_id);
            $newPlanDetails = Plan::find($newPlan);

            \Mail::to($user->email)->send(new \App\Mail\SwitchPlanMail(['new_plan_details' => $newPlanDetails, 'old_plan_details' => $plan]));

            return redirect()->route('User.plans')->with('success', 'Subscription plan has been upgraded successfully.');
        } else {
            return redirect()->route('User.plans')->with('error', 'No plan exist.');
        }
    }

}
