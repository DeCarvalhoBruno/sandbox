<?php

use App\Support\Database\ElasticSearch\Facades\ElasticSearchIndex;
use App\Support\Database\ElasticSearch\Index\Indexer as ElasticSearchIndexer;

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
//        $this->indexData($this->prepareData());
    }

    private function indexData($data)
    {
        $params = ['body' => []];
        $i = 0;
        $indexName = $this->getIndexName();
        foreach ($data as $lang => $posts) {
            foreach ($posts as $key => $item) {
                $params['body'][] = [
                    'index' => [
                        '_index' => sprintf('%s.%s', $indexName, $lang),
                        '_type' => 'post',
                        '_id' => $key
                    ]
                ];
                $params['body'][] = $item;
                if ($i % 1000 == 0) {
                    ElasticSearchIndex::bulk($params);
                    $params = ['body' => []];
                }
                $i++;
            }
        }
        if (!empty($params['body'])) {
            ElasticSearchIndex::bulk($params);
        }
    }

    private function prepareData()
    {
        $limit = 10;
        $dbPosts = \App\Models\Blog\BlogPost::query()
            ->entityType()
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
            ->person()
            ->where('blog_status_id', \App\Models\Blog\BlogStatus::BLOG_STATUS_PUBLISHED)
            ->limit($limit)
            ->get();

        $posts = [];
        $postIds = [];
        $languageIds = [];
        foreach ($dbPosts as $post) {
            $postIds[$post->getAttribute('id')] = $post->getAttribute('type');
            $languageIds[$post->getAttribute('type')] = $post->getAttribute('lang');
            $posts[$post->getAttribute('type')] = [
                'url' => route_i18n('blog', ['slug' => $post->getAttribute('slug')]),
                'title' => $post->getAttribute('title'),
                'content' => $post->getAttribute('content'),
                'excerpt' => $post->getAttribute('excerpt'),
                'date' => $post->getAttribute('published'),
                'author' => [
                    'name' => $post->getAttribute('person'),
                    'url' => route_i18n('blog.author', ['slug' => $post->getAttribute('person')]),
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
            ->where('blog_status_id', \App\Models\Blog\BlogStatus::BLOG_STATUS_PUBLISHED)
            ->category()
            ->categoryTree()
            ->limit($limit)
            ->get();

        foreach ($dbCategories as $category) {
            if (!isset($postIds[$category->getAttribute('id')])) {
                continue;
            }
            $index = $postIds[$category->getAttribute('id')];

            if (!isset($posts[$index]['category'])) {
                $posts[$index]['category'] = [];
            }
            $posts[$index]['category'][] = [
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
            ])->tag()
            ->where('blog_status_id', \App\Models\Blog\BlogStatus::BLOG_STATUS_PUBLISHED)
            ->orderBy('blog_posts.blog_post_id', 'asc')
            ->limit($limit)
            ->get();

        foreach ($dbTags as $tag) {
            if (!isset($postIds[$tag->getAttribute('id')])) {
                continue;
            }
            $index = $postIds[$tag->getAttribute('id')];
            if (!isset($posts[$index]['tag'])) {
                $posts[$index]['tag'] = [];
            }
            $posts[$index]['tag'][] = [
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
            ->limit($limit)
            ->get();

        foreach ($dbImages as $image) {
            $index = $image->getAttribute('type');
            if (!isset($posts[$index])) {
                continue;
            }
            if (!isset($posts[$index]['image'])) {
                $posts[$index]['image'] = [];
            }
            $posts[$index]['image'][] = [
                'name' => $image->getAttribute('name'),
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

        ElasticSearchIndex::delete(['index' => sprintf('%s.%s', $this->getIndexName(), 'en')]);
        ElasticSearchIndex::delete(['index' => sprintf('%s.%s', $this->getIndexName(), 'fr')]);
    }

    private function up()
    {
        $data = [
            'index' => '',
            'body' => [
                'settings' => [
                    'analyzer' => [
                        'standard_analyzer' => [
                            'type' => 'custom',
                            'tokenizer' => 'standard',
                            'filter' => [
                                'lowercase'
                            ]
                        ],
                        'standard_analyzer_strip' => [
                            'type' => 'custom',
                            'tokenizer' => 'standard',
                            'filter' => [
                                'lowercase'
                            ],
                            'char_filter' => 'html_strip'
                        ],
                        'stop_analyzer_en' => [
                            'type' => 'custom',
                            'tokenizer' => 'standard',
                            'filter' => [
                                'lowercase',
                                'english_stop'
                            ],
                            'char_filter' => 'html_strip'
                        ],
                        'stop_analyzer_fr' => [
                            'type' => 'custom',
                            'tokenizer' => 'standard',
                            'filter' => [
                                'lowercase',
                                'french_stop'
                            ],
                            'char_filter' => 'html_strip'
                        ],
                        'autocomplete' => [
                            'type' => 'custom',
                            'tokenizer' => 'standard',
                            'filter' => [
                                'lowercase',
                                'autocomplete_filter'
                            ]
                        ]
                    ],
                    'filter' => [
                        'english_stop' => [
                            'type' => 'stop',
                            'stopwords' => '_english_'
                        ],
                        'french_stop' => [
                            'type' => 'stop',
                            'stopwords' => '_french_'
                        ],
                        'autocomplete_filter' => [
                            'type' => 'edge_ngram',
                            'min_gram' => 1,
                            'max_gram' => 20
                        ]
                    ]
                ],
                'mappings' => [
                    'post' => [
                        'properties' => [
                            'url' => [
                                'type' => 'text',
                                'analyzer' => 'standard_analyzer_strip',
                                'search_quote_analyzer' => 'standard_analyzer_strip',
                                'index' => false
                            ],
                            'title' => [
                                'type' => 'text',
                            ],
                            'content' => [
                                'type' => 'text'
                            ],
                            'excerpt' => [
                                'type' => 'text',
                                'index' => false
                            ],
                            'date' => [
                                'type' => 'date',
                                'format' => 'yyyy-MM-dd HH:mm:ss',
                                'index' => false,
                            ],
                            'image' => [
                                'properties' => [
                                    'url' => [
                                        'type' => 'text',
                                        'index' => false
                                    ],
                                    'alt' => [
                                        'type' => 'text',
                                        'index' => false
                                    ]
                                ]
                            ],
                            'tag' => [
                                'properties' => [
                                    'name' => [
                                        'type' => 'keyword'
                                    ],
                                    'url' => [
                                        'type' => 'text',
                                        'index' => false
                                    ]
                                ]
                            ],
                            'category' => [
                                'properties' => [
                                    'name' => [
                                        'type' => 'keyword'
                                    ],
                                    'url' => [
                                        'type' => 'text',
                                        'index' => false
                                    ],
                                    'lvl' => [
                                        'type' => 'integer',
                                        'index' => false
                                    ]
                                ]
                            ],
                            'author' => [
                                'properties' => [
                                    'name' => [
                                        'type' => 'text'
                                    ],
                                    'url' => [
                                        'type' => 'text'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $data['index'] = sprintf('%s.%s', $this->getIndexName(), 'en');
//        $data['body']['mappings']['post']['properties']['title']

        ElasticSearchIndex::create($data);
        $data['index'] = sprintf('%s.%s', $this->getIndexName(), 'fr');
        ElasticSearchIndex::create($data);
    }
}
