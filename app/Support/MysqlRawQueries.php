<?php namespace App\Support;

class MysqlRawQueries
{

    public static function getUsersInArrayNotInGroup($testedArray, $group)
    {
        $userIds = \DB::select(
            sprintf('
                    SELECT user_id FROM users
                    WHERE users.username IN (%s)
                    AND user_id NOT IN (
                      SELECT users.user_id FROM users
                        JOIN group_members member ON users.user_id = member.user_id
                        JOIN groups ON member.group_id = groups.group_id
                                       AND group_name=?
                    )
                ', implode(',', array_fill(0, count($testedArray), '?')), $group
            ), array_merge($testedArray, [$group])
        );
        return array_map(function ($v) {
            return $v->user_id;
        }, $userIds);
    }

}