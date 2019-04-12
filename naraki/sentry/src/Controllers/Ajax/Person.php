<?php namespace Naraki\Sentry\Controllers\Ajax;

use Naraki\Sentry\Contracts\User as UserProvider;
use Naraki\Core\Controllers\Admin\Controller;
use Illuminate\Http\Response;

class Person extends Controller
{
    /**
     * @param string $search
     * @param int $limit
     * @param \Naraki\Sentry\Contracts\User|\Naraki\Sentry\Providers\User $userProvider
     * @return \Illuminate\Http\Response
     */
    public function search($search, $limit, UserProvider $userProvider)
    {
        return response(
            $userProvider->person()->search(
                preg_replace('/[^\w\s\-\']/', '', strip_tags($search)),
                intval($limit)
            )->get(), Response::HTTP_OK);
    }

}
