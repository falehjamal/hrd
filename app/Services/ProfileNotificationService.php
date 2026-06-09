<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\ProfileUpdatedNotification;

class ProfileNotificationService
{
    public function notifyProfileUpdated(User $user, string $type, ?string $plainPassword = null): void
    {
        $user->load('employee');
        $user->notify(new ProfileUpdatedNotification($type, $plainPassword));
    }
}
