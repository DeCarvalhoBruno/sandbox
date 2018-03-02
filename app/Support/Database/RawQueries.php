<?php namespace App\Support\Database;

abstract class RawQueries
{
    public function getUsersInArrayNotInGroup($testedArray, $group)
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

    public function triggerCreateEntityType($name, $primaryKey)
    {
        \DB::unprepared(
            sprintf('
                CREATE TRIGGER t_create_entity_type_%1$s AFTER INSERT ON %1$s
                    FOR EACH ROW
                        BEGIN
                            INSERT into entity_types(entity_id,entity_type_target_id)
                            SELECT entity_id,NEW.%2$s as entity_type_target_id FROM entities WHERE entity_name="%1$s";
                        END
                ',
                $name, $primaryKey
            )
        );
    }

    public function triggerDeleteEntityType($name, $primaryKey)
    {
        \DB::unprepared(
            sprintf('
                CREATE TRIGGER t_delete_entity_type_%1$s AFTER DELETE ON %1$s
                    FOR EACH ROW
                        BEGIN
                            DELETE FROM entity_types WHERE entity_type_target_id=OLD.%2$s;
                        END
                ',
                $name, $primaryKey
            )
        );
    }

}