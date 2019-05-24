<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Mpociot\Teamwork\Traits\UserHasTeams;

class User extends Authenticatable {

    use Notifiable;
    use HasRoles;
    use UserHasTeams;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_name', 'email', 'password', 'first_name', 'last_name', 'last_name2', 'address', 'district', 'state', 'phone'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function companies() {
        return $this->hasMany(Company::class);
    }

    public function addCompany() {
        $company = Company::create([
                    'user_id' => $this->id,
                    'type' => 'J'
        ]);

        $team = Team::create(
            [
                'name' => "Empresa " . $company->id . "-" . $this->id,
                'slug' => "slug_" . $company->id . "-" . $this->id,
                'owner_id' => $this->id,
                'company_id' => $company->id
            ]
        );
        $team->company_id = $company->id;
        $team->save();

        $this->attachTeam($team);

        session(['current_company' => $company->id]);
        
        return $company;
    }

    public function getUserData($email) {
        $user = User::where('email', '=', $email)->first();
        return ($user) ? $user : false;
    }

    /* Returns count of total available companies for the logged in user, depending on the plans they have currently bought. */

    public static function checkCountAvailableCompanies() {

        $user_id = auth()->user()->id;

        $query = \App\UserSubscription::query();

        $query->leftJoin('subscription_plans', 'subscription_plans.id', '=', 'user_subscriptions_history.plan_id');
        $query->where(array('user_subscriptions_history.status' => '1', 'user_id' => $user_id));
        $query->whereRaw('DATE(start_date) <="' . date('Y-m-d') . '"')->whereRaw('DATE(expiry_date) >="' . date('Y-m-d') . '"');

        $plans = $query->select('user_subscriptions_history.*', 'subscription_plans.plan_type', 'subscription_plans.plan_name', 'subscription_plans.no_of_companies', 'subscription_plans.no_of_invited_user', 'user_subscriptions_history.user_id')->get();

        if (!empty($plans->toArray())) {

            $available_companies = 0;
            foreach ($plans as $row) {
                if (is_null($row->no_of_companies)) {//If companies are unlimited
                    return 'unlimited';
                } else {
                    $company_registered_on_plan = \App\Company::where(array('user_id' => $user_id, 'plan_no' => $row->unique_no))->count();
                    $available_companies += $row->no_of_companies - $company_registered_on_plan;
                }
            }

            return ($available_companies > 0) ? $available_companies : 0;
        } else {
            return 0;
        }
    }
}
