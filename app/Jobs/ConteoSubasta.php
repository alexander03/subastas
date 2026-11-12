<?php

namespace App\Jobs;

use App\Providers\Contador;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ConteoSubasta implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $bien;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($bien)
    {
        $this->bien = $bien;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        
    }
}
