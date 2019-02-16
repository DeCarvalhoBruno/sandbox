<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Contracts\Models\Email as EmailProvider;
use App\Support\Requests\Sanitizer;

class SubscribeToNewsletter implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $input;

    /**
     *
     * @param $input
     */
    public function __construct($input)
    {
        $this->input = $input;
    }


    /**
     * Execute the job.
     *
     * @param \App\Contracts\Models\Email|\App\Support\Providers\Email $emailRepo
     * @return void
     */
    public function handle(EmailProvider $emailRepo)
    {
        try{
            $emailRepo->subscriber()->addPersonToLists(
                Sanitizer::clean($this->input)
            );
        }catch(\Exception $e){
            \Log::critical($e->getMessage(), ['trace' => $e->getTraceAsString()]);
        }
        $this->delete();
    }
}
