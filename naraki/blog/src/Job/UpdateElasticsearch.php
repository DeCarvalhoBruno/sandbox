<?php namespace Naraki\Blog\Job;

use App\Jobs\Job;
use App\Models\Person;
use Naraki\Blog\Facades\Blog;
use Naraki\Blog\Models\BlogPost;
use Naraki\Elasticsearch\Facades\ElasticSearchIndex;

class UpdateElasticsearch extends Job
{
    const WRITE_MODE_CREATE = 1;
    const WRITE_MODE_UPDATE = 2;
    const WRITE_MODE_DELETE = 3;
    public $queue = 'db';
    /**
     * @var int
     */
    private $writeMode;
    /**
     * @var \Naraki\Blog\Models\BlogPost
     */
    private $blogPostData;
    /**
     * @var \stdClass
     */
    private $requestData;
    private $refreshCategories = false;
    /**
     * @var \stdClass
     */
    private $tags;
    private $documentContents = [];
    private $refreshedFields = [
        'blog_post_title' => ['title', 'meta' => false],
        'blog_post_content' => ['content', 'meta' => false],
        'blog_post_excerpt' => ['excerpt', 'meta' => true],
        'tag' => ['tag', 'meta' => true],
        'url' => ['url', 'meta' => true],
        'author' => ['author', 'meta' => true],
        'category' => ['category', 'meta' => true],
        'image' => ['image', 'meta' => true],
        'published_at' => ['date', 'meta' => false]
    ];

    /**
     *
     * @param int $writeMode
     * @param \Naraki\Blog\Models\BlogPost $blogPostData
     * @param \stdClass $requestData
     * @param \stdClass $tags
     * @param bool $refreshCategories
     */
    public function __construct(
        int $writeMode,
        BlogPost $blogPostData,
        \stdClass $requestData,
        \stdClass $tags,
        bool $refreshCategories
    ) {
        $this->writeMode = $writeMode;
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
            switch ($this->writeMode) {
                case self::WRITE_MODE_CREATE:
                case self::WRITE_MODE_UPDATE:
                    $this->processPost();

                    if (isset($this->requestData->person_id)) {
                        $this->processAuthor();
                    }

                    if ($this->refreshCategories === true) {
                        $this->processCategories();
                    }

                    if (!is_null($this->tags)) {
                        $this->processTags();
                    }

                    if (!empty($this->documentContents)) {
                        if ($this->writeMode === self::WRITE_MODE_CREATE) {
                            $this->createDocument();
                        } else {
                            $this->updateDocument();
                        }
                    }
                    break;
                case self::WRITE_MODE_DELETE:
                    $this->deleteDocument();
                    break;
            }


            $this->delete();
        } catch (\Exception $e) {
            \Log::critical($e->getMessage(), ['trace' => $e->getTraceAsString()]);
//            app('bugsnag')->notifyException($e, ['mailData'=>$this->email->getData()], "error");
            $this->release(60);
        }
    }

    private function createDocument()
    {
        $model = new BlogPost();
        $this->documentContents['meta']['url'] = route_i18n(
            'blog',
            ['slug' => $this->blogPostData->getAttribute('blog_post_slug')]
        );
        $document = [
            'index' => $model->getLocaleDocumentIndex(),
            'type' => 'main',
            'id' => $this->blogPostData->getAttribute('entity_type_id'),
            'body' => $this->documentContents
        ];

        ElasticSearchIndex::create($document);
        return $document;
    }

    private function updateDocument()
    {
        dd($this->documentContents);
        $model = new BlogPost();
        $document = [
            'index' => $model->getLocaleDocumentIndex(),
            'type' => 'main',
            'id' => $this->blogPostData->getAttribute('entity_type_id'),
            'body' => [
                'doc' => [
                    $this->documentContents
                ]
            ]
        ];
        ElasticSearchIndex::update($document);
        return $document;
    }

    private function deleteDocument()
    {

    }

    private function processPost()
    {
        foreach ($this->refreshedFields as $field => $esField) {
            if (isset($this->requestData->{$field})) {
                if ($esField['meta']) {
                    $this->documentContents['meta'][$esField[0]] = $this->requestData->{$field};
                } else {
                    $this->documentContents[$esField[0]] = $this->requestData->{$field};
                }
            } else {
                if ($this->writeMode === self::WRITE_MODE_CREATE) {
                    if ($esField['meta']) {
                        $this->documentContents['meta'][$esField[0]] = null;
                    } else {
                        $this->documentContents[$esField[0]] = null;
                    }
                }
            }
        }
    }

    private function processAuthor()
    {
        $person = Person::query()->select(['full_name', 'person_slug'])
            ->whereKey($this->requestData->person_id)->first();
        if (!is_null($person)) {
            if (!isset($this->documentContents['meta'])) {
                $this->documentContents['meta'] = [];
            }
            $this->documentContents['meta']['author'] = [
                'name' => $person->getAttribute('full_name'),
                'url' => route_i18n('blog.author', ['slug' => $person->getAttribute('person_slug')])
            ];
        }
    }

    private function processTags()
    {
        if (!empty($this->tags->added)) {
            $tags = Blog::tag()->getByPost($this->blogPostData->getAttribute('blog_post_id'));
            if (!isset($this->documentContents['meta'])) {
                $this->documentContents['meta'] = [];
            }
            foreach ($tags as $slug => $name) {
                $this->documentContents['meta']['tag'][] = [
                    'name' => $name,
                    'url' => route_i18n('blog.tag', ['slug' => $slug]),
                ];
            }
        }

        if (isset($this->tags->removed) && !empty($this->tags->removed)) {

        }
    }

    private function processCategories()
    {
        $cat = Blog::buildWithScopes([
            'blog_category_name as name',
            'blog_category_slug as slug',
        ], ['category'])
            ->where(Blog::getQualifiedKeyName(), $this->blogPostData->getAttribute('blog_post_id'))
            ->first();
        if (!is_null($cat)) {
            $tmp = $cat->toArray();
            $cats = Blog::category()->getHierarchy($tmp['slug']);
            $categories = [];
            foreach ($cats as $mp) {
                $categories[] = [
                    'name' => $mp->label,
                    'url' => route_i18n('blog.category', ['slug' => $mp->id]),
                ];
            }
            if (!isset($this->documentContents['meta'])) {
                $this->documentContents['meta'] = [];
            }
            $this->documentContents['meta']['category'] = $categories;
        }
    }

    public function __get($value)
    {
        return $this->{$value};
    }

}