<?php namespace Naraki\Forum\Controllers;

use App\Http\Controllers\Frontend\Controller;
use App\Models\Entity;
use App\Models\EntityType;
use Illuminate\Http\Response;
use Naraki\Forum\Facades\Forum;
use Naraki\Forum\Requests\CreateComment;

class Post extends Controller
{
    public function getComments($slug)
    {

    }

    public function postComment($type, $slug, CreateComment $request)
    {
        $input = $request->all();
        $entityTypeId = EntityType::getEntityTypeID(Entity::getConstant($type), $slug);
        Forum::post()->addComment(
            $input['txt'], $entityTypeId, auth()->user()->getKey()
        );


        return response(null, Response::HTTP_NO_CONTENT);
    }

}