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
                    'user_id' => $this->id
        ]);
    }

    public function getUserData($email) {
        $user = User::where('email', '=', $email)->first();
        return ($user) ? $user : false;
    }

}
