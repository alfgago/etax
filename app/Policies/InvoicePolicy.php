<?php

namespace App\Policies;

use App\User;
use App\Invoice;
use Illuminate\Auth\Access\HandlesAuthorization;

class InvoicePolicy
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
     * Determine whether the user can view the Invoice
     * 
     */
    public function update(User $user, Invoice $invoice) {
        $current_company = currentCompany();
        return $invoice->company_id == $current_company;
    }
    
}
