<?php namespace App\Http\Controllers\Admin;

use App\Filters\User as UserFilter;
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


        $f = \DB::select('SELECT gm.user_id AS group_user,u.user_id, permission_mask FROM permissions
JOIN entity_types et ON permissions.entity_type_id = et.entity_type_id
AND permissions.entity_id=3100
LEFT JOIN group_members gm ON (gm.group_id=et.entity_type_target_id AND et.entity_id=1100)
LEFT JOIN users u ON (u.user_id = et.entity_type_target_id AND et.entity_id=3100)
ORDER BY group_user,u.user_id'
        );
        $usersWithPermissions = $tmp = [];
        foreach ($f as $result) {
            if (!is_null($result->group_user) && (!isset($tmp[$result->group_user]))) {
                $result->user_id = $result->group_user;
                $usersWithPermissions[$result->group_user] = $result;
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
        $userList = $permissions = [];
        foreach ($users as $user) {
            if (!isset($userList[$user->user_id]) || (isset($userList[$user->user_id]) && $user->group_mask < $userList[$user->user_id]->group_mask)) {
                $userList[$user->user_id] = $user;
            }
        }

        $fullPermissions = array_sum(User::getConstants('PERMISSION'));

        foreach ($userList as $user) {
            foreach ($usersWithPermissions as $userWithPermission) {
                if ($user->group_mask < $userList[$userWithPermission->user_id]->group_mask) {
//                    echo $user->user_id . ' <' . $userWithPermission->user_id;
//                    echo "<br/><br/>";
                    $permissions[$user->entity_type_id][$userList[$userWithPermission->user_id]->entity_type_id] = 0;
                } elseif ($user->user_id == $userWithPermission->user_id) {
                    $permissions[$user->entity_type_id][$userList[$userWithPermission->user_id]->entity_type_id] = $fullPermissions;
                } else {
                    $permissions[$user->entity_type_id][$userList[$userWithPermission->user_id]->entity_type_id] = $userWithPermission->permission_mask;
                }
            }

        }


        dd($permissions);
        return view('admin.layouts.test');
    }

}