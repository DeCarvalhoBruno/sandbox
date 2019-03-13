<?php

use App\Support\Database\ElasticSearch\Index\Indexer as ElasticSearchIndexer;
use App\Support\Database\ElasticSearch\Index\Seeder;

class BlogTagIndexer extends ElasticSearchIndexer
{
    /**
     * Full name of the model that should be mapped
     *
     * @var \Naraki\Blog\Models\BlogPost
     */
    protected $modelClass = Naraki\Blog\Models\BlogTag::class;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Run the mapping.
     *
     * @return void
     */
    public function run()
    {
        $this->down();
        $this->up();
        $this->indexData($this->prepareData());
    }

    private function indexData($data)
    {
        foreach ($data as $lang => $posts) {
            $seeder = new Seeder(sprintf('%s.%s', $this->getIndexName(), $lang));
            $seeder->bulk($posts);
        }
    }

    private function prepareData()
    {
        $dbTags = \Naraki\Blog\Models\BlogPost::query()
            ->select([
                'blog_posts.blog_post_id as id',
                'language_id as lang',
                'blog_tag_name as name',
                'blog_tag_slug as slug'
            ])->tag()
            ->where('blog_status_id', \Naraki\Blog\Models\BlogStatus::BLOG_STATUS_PUBLISHED)
            ->get();
        $tags = [];
        foreach ($dbTags as $tag) {
            $lang = $tag->getAttribute('lang');
            if (!isset($tags[$tag->getAttribute('lang')])) {
                $tags[$lang] = [];
            }

            $name = $tag->getAttribute('name');
            if (!isset($tags[$lang][$name])) {
                $tags[$lang][$name] = [
                    'name' => $tag->getAttribute('name'),
                    'url' => route_i18n('blog.tag', ['slug' => $tag->getAttribute('slug')]),
                ];
            }
        }
        unset($dbTags);

        return [
            'en' => array_values($tags[\App\Models\Language::DB_LANGUAGE_ENGLISH_ID]),
            'fr' => array_values($tags[\App\Models\Language::DB_LANGUAGE_FRENCH_ID])
        ];
    }

    private function down()
    {
        Seeder::delete(sprintf('%s.%s', $this->getIndexName(), 'en'));
        Seeder::delete(sprintf('%s.%s', $this->getIndexName(), 'fr'));
    }

    private function up()
    {
        $mapping = [
            'name' => [
                'type' => 'text',
            ],
            'url' => [
                'enabled' => false
            ],
        ];

        $indexEn = new \App\Support\Database\ElasticSearch\Index\Mapping(
            sprintf('%s.%s', $this->getIndexName(), 'en'),
            $mapping
        );
//        dd($indexEn->toArray());
        Seeder::insert($indexEn->toArray());

        $indexFr = new \App\Support\Database\ElasticSearch\Index\Mapping(
            sprintf('%s.%s', $this->getIndexName(), 'fr'),
            $mapping
        );
        Seeder::insert($indexFr->toArray());
    }

}
