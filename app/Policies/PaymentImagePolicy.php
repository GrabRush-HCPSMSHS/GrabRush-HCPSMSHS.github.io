<?php

namespace App\Policies;

use App\Models\PaymentImage;
use App\Models\User;

class PaymentImagePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PaymentImage $paymentImage): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PaymentImage $paymentImage): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PaymentImage $paymentImage): bool
    {
        return $user->id === $paymentImage->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PaymentImage $paymentImage): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PaymentImage $paymentImage): bool
    {
        return false;
    }
}
