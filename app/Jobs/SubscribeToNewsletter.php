<?php

namespace App\Jobs;

use App\Events\UserSubscribedToNewsletter;
use App\Models\System\SystemEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Contracts\Models\Email as EmailProvider;
use App\Support\Requests\Sanitizer;
use App\Contracts\Models\System as SystemProvider;

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
     * @param \App\Contracts\Models\System|\App\Support\Providers\System $systemRepo
     * @return void
     */
    public function handle(EmailProvider $emailRepo, SystemProvider $systemRepo)
    {
        try {
            $data = $emailRepo->subscriber()->addPersonToLists(
                Sanitizer::clean($this->input)
            );
            if (is_array($data)) {
                event(new UserSubscribedToNewsletter($data));
                $systemRepo->log()->log(
                    SystemEvent::NEWSLETTER_SUBSCRIPTION,
                    $this->input
                );
            }
        } catch (\Exception $e) {
            \Log::critical($e->getMessage(), ['trace' => $e->getTraceAsString()]);
        }
        $this->delete();
    }
}
