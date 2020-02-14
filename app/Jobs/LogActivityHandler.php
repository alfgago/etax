<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class LogActivityHandler implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $causedBy = '';
    private $performedOn = '';
    private $withProperties = [];
    private $log = '';
    /**
    * Create a new job instance.
    *
    * @return void
    */
    public function __construct($causedBy = '', $performedOn = '', $withProperties = '', $log = '') {
        $this->causedBy = $causedBy;
        $this->performedOn = $performedOn;
        $this->withProperties = $withProperties;
        $this->log = $log;
    }
    /**
    * Execute the job.
    *
    * @return void
    */
    public function handle() {
        //saves the activity
        return false;
        activity()
            ->causedBy($this->causedBy)
            ->performedOn($this->performedOn)
            ->withProperties($this->withProperties)
            ->log($this->log);
    }
}
