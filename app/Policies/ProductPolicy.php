<?php

namespace App\Policies;

use App\User;
use App\Product;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
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
     * Determine whether the user can view the Product
     * 
     */
    public function update(User $user, Product $product) {
        $current_company = $user->companies->first()->id;
        return $product->company_id == $current_company;
    }
    
}
