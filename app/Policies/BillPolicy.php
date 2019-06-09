<?php

namespace App\Policies;

use App\Bill;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BillPolicy
{
    use HandlesAuthorization;
    
    /**
     * 
     * Determine whether the user can view the Bill
     * 
     */
    public function update(User $user, Bill $bill) {
        $current_company = currentCompany();
        return $bill->company_id == $current_company;
    }
}
