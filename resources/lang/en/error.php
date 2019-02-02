<?php
return [
    'http'=>[
        '401'=> 'Non-authenticated users may not proceed.',
        '500'=>[
            'general_error'=>'The operation caused an error. We\'ll be tracking the source of the problem shortly.',
            'general_retrieval_error'=>'The requested resource could not be retrieved. It may have been deleted.',
            'user_not_found'=>'We could not find a user by that username.',
            'blog_post_not_found'=>'We could not find a blog post by that name.'
        ]
    ],
    'media'=>[
        'type_size'=>'The file could not be processed. Make sure to check accepted file types and maximum sizes.'
    ]
];