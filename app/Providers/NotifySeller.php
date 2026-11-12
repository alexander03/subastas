<?php

namespace App\Providers;

use App\Providers\BidPlaced;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifySeller
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
     * @param  \App\Providers\BidPlaced  $event
     * @return void
     */
    public function handle(BidPlaced $event)
    {
        //
    }
}
