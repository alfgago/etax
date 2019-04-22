<?php

namespace App\Policies;

use App\User;
use App\CalculatedTax;
use Illuminate\Auth\Access\HandlesAuthorization;

class CalculatedTaxPolicy
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
     * Determine whether the user can view the Client
     * 
     */
    public function update(User $user, CalculatedTax $calc) {
        $current_company = $user->companies->first()->id;
        return $calc->company_id == $current_company;
    }
    
}
