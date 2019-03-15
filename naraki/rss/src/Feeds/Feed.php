<?php namespace Naraki\Rss\Feeds;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Response;

abstract class Feed implements Responsable
{
    protected $type;
    protected $slug;

    public function toResponse($request): Response
    {
        return new Response($this, 200, [
            'Content-Type' => 'application/xml;charset=UTF-8',
        ]);
    }


}