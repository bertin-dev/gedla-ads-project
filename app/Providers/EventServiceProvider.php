<?php

namespace App\Providers;

use App\Events\DocumentAdded;
use App\Events\DocumentUpdated;
use App\Events\validationStepCompleted;
use App\Listeners\SendDocumentAddedNotification;
use App\Listeners\SendDocumentUpdatedNotification;
use App\Listeners\SendValidationStepCompletedNotification;
use App\Listeners\UserLoginAt;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        Login::class => [
            UserLoginAt::class,
        ],

        DocumentAdded::class => [
            SendDocumentAddedNotification::class,
        ],

        DocumentUpdated::class => [
            SendDocumentUpdatedNotification::class,
        ],

        validationStepCompleted::class => [
            SendValidationStepCompletedNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
