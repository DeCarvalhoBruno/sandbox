<?php namespace Naraki\Forum\Jobs;

use Naraki\Core\Job;
use Naraki\Sentry\Models\User;
use Illuminate\Bus\Dispatcher;
use Illuminate\Support\Facades\Redis;
use Naraki\Forum\Emails\Reply;
use Naraki\Forum\Events\PostCreated;
use Naraki\Forum\Facades\Forum;
use Naraki\Mail\Jobs\SendMail;

class NotifyReplySubscribers extends Job
{
    /**
     * @var PostCreated
     */
    private $event;

    public function __construct(PostCreated $event)
    {
        $this->event = $event;
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
            $this->processUsers();
        } catch (\Exception $e) {
            \Log::critical($e->getMessage(), ['trace' => $e->getTraceAsString()]);
//            app('bugsnag')->notifyException($e, ['mailData'=>$this->email->getData()], "error");
        }
        $this->delete();
    }

    private function processUsers()
    {
        $userModel = new User();
        $usersInConversation = Forum::post()->getUserPostTreeBySlug($this->event->commentData->forum_post_slug);

        foreach ($usersInConversation as $user) {
            if ($user->username != $this->event->user->getAttribute('username')) {
                $userNotifOptions = Redis::hgetall(sprintf('comment_notif_opt.%s', $user->user_id));

                //The user has to have set notification preferences
                //Also we don't need to notify the person who's posting if the person replies to themselves.
                if (is_array($userNotifOptions) && isset($userNotifOptions['reply'])) {
                    $commentPosterInfo = $userModel->newQuery()
                        ->select(['username', 'full_name'])
                        ->where('users.user_id', $this->event->commentData->post_user_id)
                        ->first();
                    if ($userNotifOptions['reply'] == true) {
                        app(Dispatcher::class)
                            ->dispatch(new SendMail(new Reply([
                                'user' => $user,
                                'slug' => $this->event->entityData->slug,
                                'comment_slug' => $this->event->commentData->forum_post_slug,
                                'reply_user' => sprintf(
                                    '%s (@%s)',
                                    $commentPosterInfo->getAttribute('full_name'),
                                    $commentPosterInfo->getAttribute('username')
                                ),
                                'post_title' => $this->event->entityData->title
                            ])));

                    }
                }
            }
        }

//        Forum::post()->getPostersBySlug();

        //I need the tree of replies based on the one that was just made
        //we need to notify every poster in this thread

    }


}