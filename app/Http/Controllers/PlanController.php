<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Plan;
use DB;

class PlanController extends Controller {

    function __construct() {
        $this->middleware('auth');
        $this->middleware('permission:plan-list');
        $this->middleware('permission:plan-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:plan-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:plan-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $data = Plan::orderBy('id', 'DESC')->paginate(5);
        return view('plans.index', compact('data'))->with('i', ($request->input('page', 1) - 1) * 5);
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
            'no_of_admin_user' => 'required|numeric',
            'no_of_normal_user' => 'required|numeric',
            'no_of_invoices' => 'required|numeric',
            'no_of_bills' => 'required|numeric',
            'chat_support' => 'required',
            'initial_setup_virtual' => 'required',
            'initial_setup_meeting' => 'required',
            'multicurrency' => 'required',
            'e_invoicing' => 'required',
            'pre_invoicing' => 'required',
            'vat_declaration' => 'required',
            'basic_report' => 'required',
            'custom_report' => 'required',
            'calls_per_month' => 'required|numeric',
            'additional_call_rates' => 'required|numeric',
            'monthly_price' => 'required|numeric',
            'quaterly_price' => 'required|numeric',
            'half_yearly_price' => 'required|numeric',
            'annual_price' => 'required|numeric'
                ], [
            'required' => 'This field is required.',
            'numeric' => 'This field should be in number.'
        ]);

        $input = $request->all();

        DB::beginTransaction();

        try {
            $input['plan_slug'] = str_replace(" ", "-", strtolower($input['plan_name']));

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
            'no_of_admin_user' => 'required|numeric',
            'no_of_normal_user' => 'required|numeric',
            'no_of_invoices' => 'required|numeric',
            'no_of_bills' => 'required|numeric',
            'chat_support' => 'required',
            'initial_setup_virtual' => 'required',
            'initial_setup_meeting' => 'required',
            'multicurrency' => 'required',
            'e_invoicing' => 'required',
            'pre_invoicing' => 'required',
            'vat_declaration' => 'required',
            'basic_report' => 'required',
            'custom_report' => 'required',
            'calls_per_month' => 'required|numeric',
            'additional_call_rates' => 'required|numeric',
            'monthly_price' => 'required|numeric',
            'quaterly_price' => 'required|numeric',
            'half_yearly_price' => 'required|numeric',
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

}
