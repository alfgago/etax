<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Mpociot\Teamwork\Traits\UserHasTeams;
use Lab404\Impersonate\Models\Impersonate;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use App\Coupon;
use App\SubscriptionPlan;

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
    
    public function sales() {
        return $this->hasMany(Sales::class);
    }    
    
    public function canImpersonate()
    {
        $allow = false;
        if( 
            $this->user_name == "alfgago" || 
            $this->user_name == "alfredo@5e.cr" || 
            $this->user_name == "aligguillen@gmail.com" 
        ) {
            $allow = true;
        }
        return $allow;
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

        $subscription = getCurrentSubscription();
        
        $availableCompanies = 25;
        
        /*if ( $subscription->num_companies == 0 ) {
            return -1;
        } else {
            $countRegistered = \App\Company::where('user_id', $user_id)->where('subscription_id', $subscription->id)->count();
            $availableCompanies += $subscription->num_companies - $countRegistered;
        }*/

        return $availableCompanies;
        
        
    }
    
    public function createKlapUser() {
        $phone = $this->phone;
        if(!isset($phone)){
            $phone = '22802130';
        }
        $client = new Client();
        $result = $client->request('POST', "https://emcom.oneklap.com:2263/api/createUser", [
            'headers' => [
                'Content-Type'  => "application/json",
            ],
            'json' => ['applicationName' => 'ETAX',
                'userName' => $this->email,
                'userFirstName' => $this->first_name,
                'userLastName' => $this->last_name,
                'userPassword' => 'Etax-' . $this->id . 'Klap',
                'userEmail' => $this->email,
                'userCallerId' => $phone
            ],
            'verify' => false,
        ]);
        $output = json_decode($result->getBody()->getContents(), true);
        
        $this->has_klap_user = true;
        $this->save();
        
        return $output;
    }
    
    /*public function isContador() {
        try{
            $company = currentCompanyModel();
            $plan_tier = "Pro (".$company->user_id.")";
            $contador = SubscriptionPlan::where('plan_tier',$plan_tier)->count();
            return $contador;
        }catch( \Throwable $e) { return false; }
        
        return false;
    }*/
    
    public function isContador() {
        try{
            $plan = getCurrentSubscription()->plan;
            $userID = auth()->user()->id;
            $plan_tier = "Pro ($userID)";
            $isContador = $plan_tier == $plan->plan_tier;
            //dd("$plan_tier . . . $plan->plan_tier");
            if($plan->id == 7){
                $isContador = true;
            }
            return $isContador;
        }catch( \Throwable $e) { return false; }
        
        return false;
    }

    public function isInfluencers() {
        try{
            $cupones = Coupon::where('user_id', auth()->user()->id)->count();
            if( $cupones > 0 ) {
                return true;
            }
        }catch( \Throwable $e) { return false; }
        
        return false;
    }


}
