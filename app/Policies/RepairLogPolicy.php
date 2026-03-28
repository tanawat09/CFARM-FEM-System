<?php

namespace App\Policies;

use App\Models\RepairLog;
use App\Models\User;

class RepairLogPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('safety_officer');
    }

    public function view(User $user, RepairLog $repairLog): bool
    {
        return $user->hasRole('admin') || $repairLog->repaired_by === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('safety_officer');
    }

    public function update(User $user, RepairLog $repairLog): bool
    {
        return $user->hasRole('admin') || $repairLog->repaired_by === $user->id;
    }

    public function complete(User $user, RepairLog $repairLog): bool
    {
        return $this->update($user, $repairLog);
    }
}
