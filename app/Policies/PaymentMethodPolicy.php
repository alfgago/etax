<?php

namespace App\Policies;

use App\User;
use App\PaymentMethod;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class PaymentMethodPolicy
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
    public function update(User $user, PaymentMethod $paymentMethod) {
        return $paymentMethod->user_id ==  auth()->user()->id;
    }
}
