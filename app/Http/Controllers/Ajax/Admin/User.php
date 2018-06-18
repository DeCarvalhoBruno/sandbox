<?php

namespace App\Http\Controllers\Ajax\Admin;

use App\Contracts\Models\User as UserProvider;
use App\Contracts\Models\Permission as PermissionProvider;
use App\Contracts\RawQueries;
use App\Events\PermissionEntityUpdated;
use App\Filters\User as UserFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUser;
use App\Models\Entity;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class User extends Controller
{
    public function index(UserProvider $userProvider, UserFilter $userFilter)
    {
        $users = $userProvider
            ->select([
                \DB::raw('"" as selected'),
                'full_name as ' . trans('ajax.db_raw_inv.full_name'),
                'email as ' . trans('ajax.db_raw_inv.email'),
                'created_at as ' . trans('ajax.db_raw_inv.created_at'),
                'permission_mask',
                'username'
            ])->entityType()
            ->permissionRecord()
            ->permissionStore()
            ->permissionMask(auth()->user()->getEntityType())
            ->activated()
            ->filter($userFilter);
        $groups = (clone $users)->select('group_name')->groupBy('group_name');
        if (!$userFilter->hasFilter('group')) {
            $groups->groupMember();
        }
        return [
            'table' => $users->paginate(10),
            'groups' => $groups->pluck('group_name'),
            'columns' => $userProvider->createModel()->getColumnInfo([
                trans('ajax.db_raw_inv.full_name') => trans('ajax.db.full_name'),
                trans('ajax.db_raw_inv.email') => trans('ajax.general.email'),
                trans('ajax.db_raw_inv.created_at') => trans('ajax.db.user_created_at')
            ])
        ];
    }

    /**
     * @param $username
     * @param \App\Contracts\Models\User|\App\Support\Providers\User $userProvider
     * @return mixed
     */
    public function edit($username, UserProvider $userProvider)
    {
        $f = app()->make(RawQueries::class);
        $user = $userProvider->getOneByUsername($username,
            [
                'first_name',
                'last_name',
                'email',
                'username',
                'full_name',
                'entity_type_id'
            ])->entityType()->first()->toArray();
        $entityTypeId = $user['entity_type_id'];
        unset($user['entity_type_id']);
        return [
            'user' => $user,
            'permissions' => $f->getAllUserPermissions($entityTypeId)
        ];
    }

    /**
     * @param $username
     * @param \App\Http\Requests\Admin\UpdateUser $request
     * @param \App\Contracts\Models\User|\App\Support\Providers\User $userProvider
     * @param \App\Contracts\Models\Permission|\App\Support\Providers\Permission $permissionProvider
     * @return \Illuminate\Http\Response
     */
    public function update(
        $username,
        UpdateUser $request,
        UserProvider $userProvider,
        PermissionProvider $permissionProvider
    ) {
        $user = $userProvider->updateOneByUsername($username, $request->all());
        $permissions = $request->getPermissions();

        if (!is_null($permissions)) {
            $permissionProvider->updateIndividual($permissions, $user->getEntityType(), Entity::USERS);
            event(new PermissionEntityUpdated);
        }
        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param string $search
     * @param \App\Contracts\Models\User|\App\Support\Providers\User $userProvider
     * @return \Illuminate\Http\Response
     */
    public function search($search, UserProvider $userProvider)
    {
        return response(
            $userProvider->search(
                preg_replace('/[^\w\s\-\']/', '', strip_tags($search)),
                auth()->user()->getAttribute('entity_type_id')
            )->get(), Response::HTTP_OK);
    }

    /**
     * @param string $username
     * @param \App\Contracts\Models\User|\App\Support\Providers\User $userProvider
     * @return \Illuminate\Http\Response
     */
    public function destroy($username, UserProvider $userProvider)
    {
        $userProvider->deleteByUsername($username);
        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Contracts\Models\User|\App\Support\Providers\User $userProvider
     * @return \Illuminate\Http\Response
     */
    public function batchDestroy(Request $request, UserProvider $userProvider)
    {
        $userProvider->deleteByUsername($request->only('users'));
        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function session(UserProvider $userProvider)
    {
        $f = app()->make(RawQueries::class);
        $user = auth()->user();
        $entityTypeId = $user->getAttribute('entity_type_id');

        return [
            'user' => $user->only([
                'first_name',
                'last_name',
                'email',
                'username',
                'full_name',
            ]),
            'permissions' => $f->getAllUserPermissions($entityTypeId),
            'avatars'=>$userProvider->getAvatars($user->getKey())
        ];
    }


}
