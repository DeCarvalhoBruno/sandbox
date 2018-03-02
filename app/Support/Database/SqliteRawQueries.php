<?php namespace App\Support\Database;

use App\Contracts\RawQueries;
use App\Support\Database\RawQueries as RawQueriesClass;

class SqliteRawQueries extends RawQueriesClass implements RawQueries
{

    public function triggerUserFullName()
    {
        \DB::unprepared('
                CREATE TRIGGER t_people_create_fullname after INSERT ON people
                    FOR EACH ROW 
                    BEGIN
                      update people SET full_name = NEW.first_name|| " " || NEW.last_name;
                    END
        ');
        \DB::unprepared('
                CREATE TRIGGER t_people_update_fullname after UPDATE ON people
                    FOR EACH ROW
                    BEGIN
                      update people SET full_name = NEW.first_name|| " " || NEW.last_name;
                    END
        ');
    }

}