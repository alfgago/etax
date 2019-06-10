<?php

namespace App\Policies;

use App\User;
use App\Client;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClientPolicy
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
    public function update(User $user, Client $client) {
        $current_company = currentCompany();
        return $client->company_id == $current_company;
    }
    
}
