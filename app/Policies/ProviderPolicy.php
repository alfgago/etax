<?php

namespace App\Policies;

use App\User;
use App\Provider;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProviderPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    
    /**
     * 
     * Determine whether the user can view the Provider
     * 
     */
    public function update(User $user, Provider $provider) {
        $current_company = $user->companies->first()->id;
        return $provider->company_id == $current_company;
    }
    
}
