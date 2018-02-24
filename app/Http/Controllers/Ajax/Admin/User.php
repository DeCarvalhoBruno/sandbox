<?php

namespace App\Http\Controllers\Ajax\Admin;

use App\Contracts\Models\User as UserProvider;
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
                    'permission_mask'
                ])->entityType()->permissionRecord()->permissionStore()->permissionMask(auth()->user()->getAttribute('entity_type_id'))->activated()->filter($userFilter)->paginate(10),
            'columns' => $userProvider->createModel()->getColumnInfo([
                'full_name' => trans('ajax.db.full_name'),
                'email' => trans('ajax.general.email'),
                'created_at' => trans('ajax.db.user_created_at')
            ])
        ];
    }

    public function show($userId, UserProvider $userProvider)
    {
        return $userProvider->getOne($userId, ['first_name', 'last_name', 'email', 'username']);
    }

    public function update($userId, UpdateUser $request, UserProvider $userProvider)
    {
        $userProvider->updateOne($userId, $request->all());
        return response(null, Response::HTTP_NO_CONTENT);
    }


}
