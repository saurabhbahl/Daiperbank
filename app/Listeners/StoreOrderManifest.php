<?php

namespace App\Listeners;

use App\Events\OrderManifestGenerated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class StoreOrderManifest
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  OrderManifestGenerated  $event
     * @return void
     */
    public function handle(OrderManifestGenerated $event)
    {
        $event->Target->storeOrderManifest($event->Asset);
    }
}
