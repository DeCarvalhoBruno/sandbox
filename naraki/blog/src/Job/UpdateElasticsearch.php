<?php namespace Naraki\Blog\Job;

use App\Jobs\Job;
use Naraki\Blog\Models\BlogPost;

class UpdateElasticsearch extends Job
{
    public $queue = 'db';
    private $blogPostData;
    private $requestData;
    private $categories;
    private $tags;

    /**
     *
     * @param \Naraki\Blog\Models\BlogPost $blogPostData
     * @param array $requestData
     * @param array $categories
     * @param array $tags
     */
    public function __construct(BlogPost $blogPostData, $requestData, $categories, $tags)
    {
        $this->blogPostData = $blogPostData;
        $this->requestData = $requestData;
        $this->categories = $categories;
        $this->tags = $tags;
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

        } catch (\Exception $e) {
            \Log::critical($e->getMessage(), ['trace' => $e->getTraceAsString()]);
//            app('bugsnag')->notifyException($e, ['mailData'=>$this->email->getData()], "error");
            $this->release(60);
        }

    }


}