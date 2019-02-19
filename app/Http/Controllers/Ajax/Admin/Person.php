<?php

namespace App\Http\Controllers\Ajax\Admin;

use App\Contracts\Models\User as UserProvider;
use App\Http\Controllers\Admin\Controller;
use Illuminate\Http\Response;

class Person extends Controller
{
    /**
     * @param string $search
     * @param int $limit
     * @param \App\Contracts\Models\User|\App\Support\Providers\User $userProvider
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
