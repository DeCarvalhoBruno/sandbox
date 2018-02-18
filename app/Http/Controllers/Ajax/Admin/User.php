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

    public function show($userId, UserProvider $userProvider){
        return $userProvider->getOne($userId,['first_name','last_name','email','username']);
    }

    public function update($userId, UpdateUser $request, UserProvider $userProvider)
    {
        $userProvider->updateOne($userId,$request->all());

        return response(null,Response::HTTP_NO_CONTENT);
    }


}
