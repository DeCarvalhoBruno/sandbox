<?php

namespace App\Http\Controllers\Ajax\Admin;

use App\Contracts\Models\User as UserProvider;
use App\Contracts\RawQueries;
use App\Filters\User as UserFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUser;
use Illuminate\Http\Response;

class User extends Controller
{
    public function index(UserProvider $userProvider, UserFilter $userFilter)
    {
        return [
            'table' => $userProvider
                ->select([
                    'full_name',
                    'email',
                    'users.user_id',
                    'created_at',
                    'permission_mask',
                    'username'
                ])->entityType()
                ->permissionRecord()
                ->permissionStore()
                ->permissionMask(auth()->user()->getEntityType())
                ->activated()
                ->filter($userFilter)->paginate(10),
            'columns' => $userProvider->createModel()->getColumnInfo([
                'full_name' => trans('ajax.db.full_name'),
                'email' => trans('ajax.general.email'),
                'created_at' => trans('ajax.db.user_created_at')
            ])
        ];
    }

    /**
     * @param $username
     * @param \App\Contracts\Models\User|\App\Providers\Models\User $userProvider
     * @return mixed
     */
    public function edit($username, UserProvider $userProvider)
    {
        $f = app()->make(RawQueries::class);
        return [
            'user' => $userProvider->getOneByUsername($username,
                ['first_name', 'last_name', 'email', 'username', 'full_name'])->first(),
            'permissions'=>$f->getAllUserPermissions(auth()->user()->getEntityType())
        ];
    }

    /**
     * @param $username
     * @param \App\Http\Requests\Admin\UpdateUser $request
     * @param \App\Contracts\Models\User|\App\Providers\Models\User $userProvider
     * @return \Illuminate\Http\Response
     */
    public function update($username, UpdateUser $request, UserProvider $userProvider)
    {
        $userProvider->updateOneByUsername($username, $request->all());
        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function search($search, UserProvider $userProvider)
    {
        return response(
            $userProvider->search(
                preg_replace('/[^\w\s\-\']/', '', strip_tags($search)),
                auth()->user()->getAttribute('entity_type_id')
            )->get(), Response::HTTP_OK);
    }


}
