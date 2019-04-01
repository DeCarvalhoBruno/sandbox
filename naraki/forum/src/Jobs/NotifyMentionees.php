<?php namespace Naraki\Forum\Jobs;

use App\Jobs\Job;
use App\Models\User;
use Illuminate\Bus\Dispatcher;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Redis;
use Naraki\Forum\Emails\Mention;
use Naraki\Mail\Jobs\SendMail;

class NotifyMentionees extends Job
{
    /**
     * @var array
     */
    private $mentioned_usernames;
    /**
     * @var \Illuminate\Contracts\Auth\Authenticatable
     */
    private $user;
    /**
     * @var \stdClass
     */
    private $entityData;
    /**
     * @var string
     */
    private $commentSlug;

    /**
     *
     * @param $mentioned_usernames
     * @param $user
     * @param $entityData
     * @param $commentSlug
     */
    public function __construct(
        array $mentioned_usernames,
        Authenticatable $user,
        \stdClass $entityData,
        string $commentSlug
    ) {
        $this->mentioned_usernames = $mentioned_usernames;
        $this->user = $user;
        $this->entityData = $entityData;
        $this->commentSlug = $commentSlug;
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
            ->whereIn('username', $this->mentioned_usernames)->pluck($user->getKeyName(), 'username');

        foreach ($this->mentioned_usernames as $username) {
            $userNotifOptions = Redis::hgetall(sprintf('comment_notif_opt.%s', $users[$username]));

            if (is_array($userNotifOptions) && isset($userNotifOptions['mention'])) {
                if ($userNotifOptions['mention'] == true) {
                    app(Dispatcher::class)
                        ->dispatch(new SendMail(new Mention([
                            'user' => $this->user,
                            'slug' => $this->entityData->slug,
                            'comment_slug' => $this->commentSlug,
                            'mention_user' => $username,
                            'post_title' => $this->entityData->title
                        ])));

                }
            }

        }
    }


}