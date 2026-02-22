<?php

namespace App\Listeners;

use App\Enums\UserRole;
use App\Models\User;
use App\Notifications\NewUserRegisteredNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Notification;

class NotifyAdminOfNewUser
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        $admins = User::where('role', UserRole::Admin)->get();

        if ($admins->isNotEmpty()) {
            Notification::send($admins, new NewUserRegisteredNotification($event->user));
        }
    }
}
