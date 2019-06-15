<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Mpociot\Teamwork\Traits\UserHasTeams;
use Lab404\Impersonate\Models\Impersonate;

class User extends Authenticatable {

    use Notifiable, HasRoles, UserHasTeams, Impersonate, SoftDeletes;

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

    public function subscriptions() {
        return $this->hasMany(Subscription::class);
    }

    public function payments()
    {
        return $this->hasManyThrough(
            'App\Payment',
            'App\Sales',
            'user_id',
            'sale_id',
            'id',
            'id'
        );
    }
    
    public function canImpersonate()
    {
        return $this->user_name == "alfgago";
    }

    public function addCompany() {
        $company = Company::create([
                    'user_id' => $this->id,
                    'type' => 'J'
        ]);

        $team = Team::create(
            [
                'name' => "(" . $company->id . ") -" . $this->id,
                'slug' => "slug_" . $company->id . "_" . $this->id,
                'owner_id' => $this->id,
                'company_id' => $company->id
            ]
        );
        $team->company_id = $company->id;
        $team->save();
        $this->attachTeam($team);

        return $company;
    }

    public function getUserData($email) {
        $user = User::where('email', '=', $email)->first();
        return ($user) ? $user : false;
    }

    /* Returns count of total available companies for the logged in user, depending on the plans they have currently bought. */

    public static function checkCountAvailableCompanies() {

        $user_id = auth()->user()->id;

        $subscriptions = Subscription::where('user_id', $user_id)->where('status', '1')->get();
        
        $availableCompanies = 0;
        foreach ($subscriptions as $subscription) {
            if ( $subscription->num_companies == 0 ) {
                return -1;
            } else {
                $countRegistered = \App\Company::where('user_id', $user_id)->where('subscription_id', $subscription->id)->count();
                $availableCompanies += $subscription->num_companies - $countRegistered;
            }
        }

        return $availableCompanies;
        
        
    }

}
