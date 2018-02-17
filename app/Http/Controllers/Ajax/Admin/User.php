<?php

namespace App\Http\Controllers\Ajax\Admin;

use App\Http\Controllers\Controller;
use App\Filters\User as UserFilter;
use App\Http\Requests\Admin\UpdateUser;
use App\Providers\Models\User as UserProvider;

class User extends Controller
{
    public function index(UserProvider $userProvider, UserFilter $userFilter)
    {
        $columns = [
            'full_name' => trans('full_name'),
            'email' => trans('email'),
            'created_at' => trans('created_at')
        ];
        return [
            'columns' => $columns,
            'table' => $userProvider->select([
                'full_name',
                'email',
                'users.user_id',
                'created_at'
            ])->activated()->filter($userFilter)->limit(10)->get()->toArray(),
            'sortableColumns' => $userProvider->createModel()->getSortableColumns()
        ];
    }

    public function show($userId){
        return (new UserProvider())->getOne($userId,['first_name','last_name','email','username']);
    }

    public function update($userId, UpdateUser $request, UserProvider $userProvider)
    {
//        $userProvider->getOne($userId);
        return response('');
    }


}
