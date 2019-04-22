<?php

namespace App\Policies;

use App\Company;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompanyPolicy
{
    use HandlesAuthorization;
    
    /**
     * 
     * Determine whether the user can view the Company
     * 
     */
    public function update(User $user, Company $company) {
        return isExistInCompanyTeam($company->id, $user->id);
    }
}
