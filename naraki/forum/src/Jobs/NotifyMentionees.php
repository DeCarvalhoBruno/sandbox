<?php namespace Naraki\Forum\Jobs;

use App\Jobs\Job;
use App\Models\User;
use Illuminate\Bus\Dispatcher;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Naraki\Mail\Jobs\SendMail;

class NotifyMentionees extends Job
{
    private $mentions;
    private $comment;
    private $user;
    private $entityId;
    private $entityTypeId;

    /**
     *
     * @param $mentions
     * @param $comment
     * @param $user
     * @param $entityId
     * @param $entityTypeId
     */
    public function __construct($mentions, $comment, $user, $entityId, $entityTypeId)
    {
        $this->mentions = $mentions;
        $this->comment = $comment;
        $this->user = $user;
        $this->entityId = $entityId;
        $this->entityTypeId = $entityTypeId;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        parent::handle();
        try {
            $this->processMentions();
        } catch (\Exception $e) {
            \Log::critical($e->getMessage(), ['trace' => $e->getTraceAsString()]);
//            app('bugsnag')->notifyException($e, ['mailData'=>$this->email->getData()], "error");
        }
        $this->delete();
    }

    private function processMentions()
    {
        $user = new User();
        $users = $user->newQueryWithoutScopes()->select([$user->getKeyName(), 'username'])
            ->whereIn('username', $this->mentions)->pluck($user->getKeyName(), 'username');
        foreach ($this->mentions as $mention) {
            $userNotifOptions = Redis::hgetall(sprintf('comment_notif_opt.%s', $users));
            if (isset($userNotifOptions['mention'])) {
                if ($userNotifOptions['mention'] == true) {
                    app(Dispatcher::class)
                        ->dispatch(new SendMail());

                }
            }

        }

    }


}