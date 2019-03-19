<?php namespace Naraki\Blog\Job;

use App\Jobs\Job;
use App\Models\Person;
use Naraki\Blog\Facades\Blog;
use Naraki\Blog\Models\BlogPost;
use Naraki\Elasticsearch\Facades\ElasticSearchIndex;

class UpdateElasticsearch extends Job
{
    public $queue = 'db';
    private $blogPostData;
    private $requestData;
    private $refreshCategories = false;
    private $tags = false;
    private $refreshedFields = [
        'blog_post_title' => ['title', 'meta' => false],
        'blog_post_content' => ['content', 'meta' => false],
        'blog_post_excerpt' => ['excerpt', 'meta' => true],
        'published_at' => ['date', 'meta' => false]
    ];

    /**
     *
     * @param \stdClass $blogPostData
     * @param \stdClass $requestData
     * @param array $tags
     * @param bool $refreshCategories
     */
    public function __construct(\stdClass $blogPostData, \stdClass $requestData, array $tags, bool $refreshCategories)
    {
        $this->blogPostData = $blogPostData;
        $this->requestData = $requestData;
        $this->refreshCategories = $refreshCategories;
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
            $this->processPost();
            $this->delete();
        } catch (\Exception $e) {
            \Log::critical($e->getMessage(), ['trace' => $e->getTraceAsString()]);
//            app('bugsnag')->notifyException($e, ['mailData'=>$this->email->getData()], "error");
            $this->release(60);
        }
    }

    private function processPost()
    {
        $documentContents = [];
        foreach ($this->refreshedFields as $field => $esField) {
            if (isset($this->requestData->{$field})) {
                if ($esField['meta']) {
                    $documentContents['meta'][$field] = $this->requestData->{$field};
                } else {
                    $documentContents[$field] = $this->requestData->{$field};
                }
            }
        }
        if (isset($this->requestData->person_id)) {
            $person = Person::query()->select(['full_name', 'person_slug'])
                ->whereKey($this->requestData->person_id)->first();
            if (!is_null($person)) {
                $documentContents['meta']['author'] = [
                    'name' => $person->getAttribute('full_name'),
                    'url' => route_i18n('blog.author', ['slug' => $person->getAttribute('person_slug')])
                ];
            }
        }
        if ($this->refreshCategories === true) {
            $cat = Blog::buildWithScopes([
                'blog_category_name as name',
                'blog_category_slug as slug',
            ], ['category'])
                ->where(Blog::getQualifiedKeyName(), $this->blogPostData->blog_post_id)
                ->first();
            if (!is_null($cat)) {
                $tmp = $cat->toArray();
                $cats = Blog::category()->getHierarchy($tmp['slug']);
                $categories=[];
                foreach ($cats as $mp) {
                    $categories[] = [
                        'name' => $mp->label,
                        'url' => route_i18n('blog.category', ['slug' => $mp->id]),
                    ];
                }
                $documentContents['meta']['category'] = $categories;
            }
        }
        if (!empty($documentContents)) {
            $model = new BlogPost();
            ElasticSearchIndex::update([
                'index' => $model->getLocaleDocumentIndex(),
                'type' => 'main',
                'id' => $this->blogPostData->entity_type_id,
                'body' => [
                    'doc' => [
                        $documentContents
                    ]
                ]
            ]);
        }
    }

}