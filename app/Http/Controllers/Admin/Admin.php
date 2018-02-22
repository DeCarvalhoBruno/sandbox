<?php namespace App\Http\Controllers\Admin;

use App\Filters\User as UserFilter;
use App\Models\PermissionStore;
use App\Models\User;
use App\Providers\Models\User as UserProvider;
use App\Http\Controllers\Controller;
use App\Models\Person;
use App\Support\GroupHierarchy\GroupHierarchy;

class Admin extends Controller
{

    public function index()
    {
//        dd(app('router')->getCurrentRoute());
        return view('admin.layouts.default');

    }

    public function test(UserProvider $userProvider, UserFilter $userFilter)
    {


        $f = \DB::select('SELECT gm.user_id AS group_user_id,u.user_id, permission_mask,pe.permission_entity_id FROM permissions
                JOIN entity_types et ON permissions.entity_type_id = et.entity_type_id
                JOIN permission_entities pe ON (permissions.permission_entity_id = pe.permission_entity_id AND pe.entity_id=3100)
                LEFT JOIN group_members gm ON (gm.group_id=et.entity_type_target_id AND et.entity_id=1100)
                LEFT JOIN users u ON (u.user_id = et.entity_type_target_id AND et.entity_id=3100)
                ORDER BY group_user_id,u.user_id'
        );
        $usersWithPermissions = $tmp = [];
        foreach ($f as $result) {
            if (!is_null($result->group_user_id) && (!isset($tmp[$result->group_user_id]))) {
                $result->user_id = $result->group_user_id;
                $usersWithPermissions[$result->group_user_id] = $result;
            } elseif (!is_null($result->user_id)) {
                $tmp[$result->user_id] = $result;
                $usersWithPermissions[$result->user_id] = $result;
            }
        }
//        $groupHierarchy = GroupHierarchy::getTree($groupPermission);
        $users = \DB::select(
            'SELECT et.entity_type_id,u.user_id, groups.group_mask FROM group_members gu
                  JOIN groups ON gu.group_id = groups.group_id
                  JOIN users u ON gu.user_id = u.user_id
                  JOIN entity_types et ON (et.entity_type_target_id=gu.user_id AND et.entity_id=3100)'
        );
        $userList = $permissions = $entity_types = $permissionStore = [];
        foreach ($users as $user) {
            if (!isset($userList[$user->user_id]) || (isset($userList[$user->user_id]) && $user->group_mask < $userList[$user->user_id]->group_mask)) {
                $userList[$user->user_id] = $user;
            }
        }

        $fullPermissions = array_sum(User::getConstants('PERMISSION'));

        foreach ($userList as $user) {
            $settledToDefaultPermissions = true;
            foreach ($usersWithPermissions as $userWithPermission) {
                if ($user->group_mask < $userList[$userWithPermission->user_id]->group_mask) {
                    $permissions[$user->entity_type_id][$userList[$userWithPermission->user_id]->entity_type_id] = 0;
                    $permissionStore[$user->entity_type_id][] = [
                        'permission_entity_id' => $userWithPermission->permission_entity_id,
                        'permission_store_target_id' => $user->entity_type_id,
                        'permission_store_holder_id' => $userList[$userWithPermission->user_id]->entity_type_id,
                        'permission_store_mask'=>0
                    ];
                    $settledToDefaultPermissions = false;
                } elseif ($user->user_id == $userWithPermission->user_id) {
                    $permissions[$user->entity_type_id][$userList[$userWithPermission->user_id]->entity_type_id] = $fullPermissions;
                    $permissionStore[$user->entity_type_id][] = [
                        'permission_entity_id' => $userWithPermission->permission_entity_id,
                        'permission_store_target_id' => $user->entity_type_id,
                        'permission_store_holder_id' => $userList[$userWithPermission->user_id]->entity_type_id,
                        'permission_store_mask'=>$fullPermissions
                    ];
                    $settledToDefaultPermissions = false;
                } else {
                    $permissions[$user->entity_type_id][$userList[$userWithPermission->user_id]->entity_type_id] = $userWithPermission->permission_mask;
                    $permissionStore[$user->entity_type_id][] = [
                        'permission_entity_id' => $userWithPermission->permission_entity_id,
                        'permission_store_target_id' => $user->entity_type_id,
                        'permission_store_holder_id' => $userList[$userWithPermission->user_id]->entity_type_id,
                        'permission_store_mask'=>$userWithPermission->permission_mask
                    ];
                }
            }
            if ($settledToDefaultPermissions) {
                if (!isset($permissionStore[0])) {
                    $permissionStore[0]=array_map(function($value){$value['permission_store_target_id']=0;return $value;},$permissionStore[$user->entity_type_id]);
                }
                unset($permissions[$user->entity_type_id],$permissionStore[$user->entity_type_id]);
            }
        }
//        dd($permissionStore);
        (new PermissionStore())->insert(array_flatten($permissionStore,1));



        return view('admin.layouts.test');
    }

}