<?php

namespace App\Listeners;

use App\Events\PackingLabelsGenerated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class StorePackingLabels
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
     * @param  PackingLabelsGenerated  $event
     * @return void
     */
    public function handle(PackingLabelsGenerated $event)
    {
       $event->Target->storePackingLabels($event->Asset);
    }
}
