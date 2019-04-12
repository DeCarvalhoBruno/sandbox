<?php namespace Naraki\Mail\Jobs;

use Naraki\Sentry\Events\UserSubscribedToNewsletter;
use App\Support\Requests\Sanitizer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Naraki\Mail\Facades\NarakiMail;
use Naraki\System\Facades\System;
use Naraki\System\Models\SystemEvent;

class SubscribeToNewsletter implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var array
     */
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
     * @return void
     */
    public function handle()
    {
        try {
            $data = NarakiMail::subscriber()->addPersonToLists(
                Sanitizer::clean($this->input)
            );
            if (is_array($data)) {
                event(new UserSubscribedToNewsletter($data));
                System::log()->log(
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
