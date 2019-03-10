<?php

use App\Support\Database\ElasticSearch\Index\Indexer as ElasticSearchIndexer;
use App\Support\Database\ElasticSearch\Index\Seeder;

class BlogPostIndexer extends ElasticSearchIndexer
{
    /**
     * Full name of the model that should be mapped
     *
     * @var \App\Models\Blog\BlogPost
     */
    protected $modelClass = App\Models\Blog\BlogPost::class;

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
        $limit = 10;
        $dbPosts = \App\Models\Blog\BlogPost::query()
//            ->entityType()
            ->scopes(['entityType','person'])
            ->select([
                'blog_posts.blog_post_id as id',
                'entity_types.entity_type_id as type',
                'blog_post_excerpt as excerpt',
                'blog_post_content as content',
                'blog_post_slug as slug',
                'blog_post_title as title',
                'full_name as person',
                'person_slug as author',
                'blog_posts.updated_at as published',
                'language_id as lang'
            ])
            ->where('blog_status_id', \App\Models\Blog\BlogStatus::BLOG_STATUS_PUBLISHED)
//            ->limit($limit)
            ->get();

        $posts = [];
        $postIds = [];
        $languageIds = [];

        foreach ($dbPosts as $post) {
            $postIds[$post->getAttribute('id')] = $post->getAttribute('type');
            $languageIds[$post->getAttribute('type')] = $post->getAttribute('lang');
            $posts[$post->getAttribute('type')] = [
                'title' => $post->getAttribute('title'),
                'content' => $post->getAttribute('content'),
                'date' => $post->getAttribute('published'),
                'meta' => [
                    'url' => route_i18n('blog', ['slug' => $post->getAttribute('slug')]),
                    'excerpt' => $post->getAttribute('excerpt'),
                    'author' => [
                        'name' => $post->getAttribute('person'),
                        'url' => route_i18n('blog.author', ['slug' => $post->getAttribute('author')]),
                    ]
                ]
            ];
        }
        unset($dbPosts);

        $dbCategories = \App\Models\Blog\BlogPost::query()
            ->select([
                'blog_posts.blog_post_id as id',
                'blog_category_name as name',
                'blog_category_slug as slug',
                'lvl'
            ])
            ->scopes(['category','categoryTree'])
            ->where('blog_status_id', \App\Models\Blog\BlogStatus::BLOG_STATUS_PUBLISHED)
//            ->limit($limit)
            ->get();

        foreach ($dbCategories as $category) {
            if (!isset($postIds[$category->getAttribute('id')])) {
                continue;
            }
            $index = $postIds[$category->getAttribute('id')];

            if (!isset($posts[$index]['meta']['category'])) {
                $posts[$index]['meta']['category'] = [];
            }
            $posts[$index]['meta']['category'][] = [
                'name' => $category->getAttribute('name'),
                'url' => route_i18n('blog.category', ['slug' => $category->getAttribute('slug')]),
                'lvl' => intval($category->getAttribute('lvl'))
            ];
        }
        unset($dbCategories);

        $dbTags = \App\Models\Blog\BlogPost::query()
            ->select([
                'blog_posts.blog_post_id as id',
                'blog_tag_name as name',
                'blog_tag_slug as slug'
            ])
            ->scopes(['tag'])
            ->where('blog_status_id', \App\Models\Blog\BlogStatus::BLOG_STATUS_PUBLISHED)
            ->orderBy('blog_posts.blog_post_id', 'asc')
//            ->limit($limit)
            ->get();

        foreach ($dbTags as $tag) {
            if (!isset($postIds[$tag->getAttribute('id')])) {
                continue;
            }
            $index = $postIds[$tag->getAttribute('id')];
            if (!isset($posts[$index]['meta']['tag'])) {
                $posts[$index]['meta']['tag'] = [];
            }
            $posts[$index]['meta']['tag'][] = [
                'name' => $tag->getAttribute('name'),
                'url' => route_i18n('blog.tag', ['slug' => $tag->getAttribute('slug')]),
            ];
        }
        unset($dbTags);

        $dbImages = \App\Models\Media\MediaEntity::buildImages(null, [
            'media_uuid as uuid',
            'media_extension as ext',
            'entity_types.entity_type_id as type',
            'entity_id'
        ])->where('entity_types.entity_id', \App\Models\Entity::BLOG_POSTS)
//            ->limit($limit)
            ->get();

        foreach ($dbImages as $image) {
            $index = $image->getAttribute('type');
            if (!isset($posts[$index])) {
                continue;
            }
            if (!isset($posts[$index]['meta']['image'])) {
                $posts[$index]['meta']['image'] = [];
            }
            $posts[$index]['meta']['image'][] = [
                'url' => $image->present('asset'),
            ];
        }
        unset($dbImages);

        $langPosts = ['fr' => [], 'en' => []];
        foreach ($languageIds as $typeId => $item) {
            if (intval($item) == 1) {
                $langPosts['en'][$typeId] = $posts[$typeId];
            } else {
                $langPosts['fr'][$typeId] = $posts[$typeId];
            }
        }
        unset($posts);
        return $langPosts;
    }

    private function down()
    {
        Seeder::delete(sprintf('%s.%s', $this->getIndexName(), 'en'));
        Seeder::delete(sprintf('%s.%s', $this->getIndexName(), 'fr'));
    }

    private function up()
    {
        $mapping = [
            'title' => [
                'type' => 'text',
                'analyzer' => 'std_stop_en',
                'search_analyzer' => 'std_stop_en',
                'search_quote_analyzer' => 'standard'
            ],
            'content' => [
                'type' => 'text',
                'analyzer' => 'std_strip_en',
                'search_analyzer' => 'std_strip_en',
                'search_quote_analyzer' => 'standard'
            ],
            'date' => [
                'type' => 'date',
                'format' => 'yyyy-MM-dd HH:mm:ss',
            ],
            'meta' => [
                'enabled' => false
            ],
        ];
        $source = ['includes'=>['title','meta','date']];

        $indexEn = new \App\Support\Database\ElasticSearch\Index\Mapping(
            sprintf('%s.%s', $this->getIndexName(), 'en'),
            $mapping,
            $source
        );
        Seeder::insert($indexEn->toArray());

        $mapping['title']['analyzer'] = 'std_stop_fr';
        $mapping['title']['search_analyzer'] = 'std_stop_fr';
        $mapping['content']['analyzer'] = 'std_strip_fr';
        $mapping['content']['search_analyzer'] = 'std_strip_fr';

        $indexFr = new \App\Support\Database\ElasticSearch\Index\Mapping(
            sprintf('%s.%s', $this->getIndexName(), 'fr'),
            $mapping,
            $source
        );
        Seeder::insert($indexFr->toArray());
    }

    /**
     * @TODO: delete later
     */
    private function delete_later()
    {
        $data = [
            'index' => '',
            'body' => [
                'settings' => [
                    'analysis' => [
                        'filter' => [
                            'filter_stop_en' => [
                                'type' => 'stop',
                                'stopwords' => '_english_'
                            ],
                            'filter_stop_fr' => [
                                'type' => 'stop',
                                'stopwords' => '_french_'
                            ],
                            'filter_snow_en' => [
                                'type' => 'snowball',
                                'language' => 'English'
                            ],
                            'filter_snow_fr' => [
                                'type' => 'snowball',
                                'language' => 'French'
                            ],
                        ],
                        'char_filter' => [
                            'quotes' => [
                                'type' => 'mapping',
                                'mappings' => [
                                    '\\u0091=>\\u0027',
                                    '\\u0092=>\\u0027',
                                    '\\u2018=>\\u0027',
                                    '\\u2019=>\\u0027',
                                    '\\u201B=>\\u0027',
                                    '\\u201C=>\\u0022',
                                    '\\u201D=>\\u0022',
                                    '\\u00AB=>\\u0022',
                                    '\\u00BB=>\\u0022',
                                ]
                            ]
                        ],
                        'analyzer' => [
                            'std_strip_en' => [
                                'type' => 'custom',
                                'tokenizer' => 'standard',
                                'filter' => [
                                    'lowercase',
                                    'filter_stop_en',
                                    'filter_snow_en',
                                ],
                                'char_filter' => ['html_strip', 'quotes']
                            ],
                            'std_strip_fr' => [
                                'type' => 'custom',
                                'tokenizer' => 'standard',
                                'filter' => [
                                    'lowercase',
                                    'filter_stop_fr',
                                    'filter_snow_en',
                                    'asciifolding'
                                ],
                                'char_filter' => ['html_strip', 'quotes']
                            ],
                            'stop_en' => [
                                'type' => 'custom',
                                'tokenizer' => 'standard',
                                'filter' => [
                                    'lowercase',
                                    'filter_stop_en',
                                    'filter_snow_en',
                                ]
                            ],
                            'stop_fr' => [
                                'type' => 'custom',
                                'tokenizer' => 'standard',
                                'filter' => [
                                    'lowercase',
                                    'filter_stop_fr',
                                    'filter_snow_en',
                                    'asciifolding'
                                ]
                            ],
                        ]
                    ]
                ],
                'mappings' => [
                    'post' => [
                        'properties' => [
                            'title' => [
                                'type' => 'text',
                            ],
                            'content' => [
                                'type' => 'text'
                            ],
                            'date' => [
                                'type' => 'date',
                                'format' => 'yyyy-MM-dd HH:mm:ss',
                            ],
                            'meta' => [
                                'enabled' => false
                            ],
                        ]
                    ]
                ]
            ]
        ];

    }
}
