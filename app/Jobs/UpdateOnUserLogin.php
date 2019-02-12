<?php namespace App\Jobs;

use App\Models\Stats\StatUser;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateOnUserLogin extends Job
{
    use InteractsWithQueue, SerializesModels;

//    public $queue = 'db';
    private $user;

    /**
     * Create a new job instance.
     *
     * @param $user \Illuminate\Contracts\Auth\Authenticatable
     */
    public function __construct($user)
    {
        $this->user = $user;
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
            StatUser::query()->where('user_id',
                $this->user->getKey())
                ->update(['stat_user_last_visit' => \Carbon\Carbon::now()->toDateTimeString()]);
        } catch (\Exception $e) {
            \Log::critical($e->getMessage(), ['trace' => $e->getTraceAsString()]);
//            app('bugsnag')->notifyException($e, ['user'=>$this->user->toArray()], "error");
        }
        $this->delete();
    }

}