<?php

use App\Support\Database\ElasticSearch\Facades\ElasticSearchIndex;
use App\Support\Database\ElasticSearch\Index\Model as ElasticSearchModel;

class BlogPost extends ElasticSearchModel
{
    /**
     * Full name of the model that should be mapped
     *
     * @var \App\Models\Blog\BlogPost
     */
    protected $model = App\Models\Blog\BlogPost::class;

    /**
     * Run the mapping.
     *
     * @return void
     */
    public static function build()
    {
        $m = new static;
        ElasticSearchIndex::create([
            'index' => $m->model->getTable(),
            'body' => [
                'mappings' => [
                    '_default_' => [
                        'properties' => [
                            'blog_post_id' => [
                                'type' => 'integer',
                                'index' => 'not_analyzed'
                            ],
                            'title' => [
                                'type' => 'text'
                            ],
                            'content' => [
                                'type' => 'text'
                            ],
                            'image' => [
                                'index' => 'not_analyzed',
                                'properties' => [
                                    'url' => [
                                        'type' => 'text'
                                    ],
                                    'alt' => [
                                        'type' => 'text'
                                    ]
                                ]
                            ],
                            'tag' => [
                                'properties' => [
                                    'tag_id' => [
                                        'type' => 'integer',
                                        'index' => 'not_analyzed'
                                    ],
                                    'tag_name' => [
                                        'type' => 'text'
                                    ],
                                    'tag_url' => [
                                        'type' => 'text',
                                        'index' => 'not_analyzed'
                                    ]
                                ]
                            ],
                            'category' => [
                                'properties' => [
                                    'id' => [
                                        'type' => 'integer',
                                        'index' => 'not_analyzed'
                                    ],
                                    'name' => [
                                        'type' => 'text'
                                    ],
                                    'url' => [
                                        'type' => 'text',
                                        'index' => 'not_analyzed'
                                    ]
                                ]
                            ],
                            'author' => [
                                'properties' => [
                                    'id' => [
                                        'type' => 'integer',
                                        'index' => 'not_analyzed'
                                    ],
                                    'name' => [
                                        'type' => 'text'
                                    ],
                                    'url' => [
                                        'type' => 'text'
                                    ],
                                    'image' => [
                                        'index' => 'not_analyzed',
                                        'properties' => [
                                            'url' => [
                                                'type' => 'text'
                                            ],
                                            'alt' => [
                                                'type' => 'text'
                                            ]
                                        ]
                                    ],
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]);
    }
}
