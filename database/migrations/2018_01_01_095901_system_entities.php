<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\SystemEntity;

class SystemEntities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_entities', function (Blueprint $table) {
            $table->increments('system_entity_id');
            $table->string('system_entity_name', 75)->nullable();
        });

        Schema::create('system_entity_types', function (Blueprint $table) {
            $table->increments('system_entity_type_id');

            $table->integer('system_entity_id')->unsigned();
            $table->smallInteger('system_entity_type_target_id')->unsigned()->comment('the ID in the entity\'s (user,message,etc.) table');

            $table->foreign('system_entity_id')
                ->references('system_entity_id')->on('system_entities')
                ->onDelete('cascade');
        });

        $this->addEntities();
        $this->systemEntityTypes();
        $this->createSystemEntities();
        $this->createTriggers();

    }

    private function addEntities()
    {
        $entities = SystemEntity::getConstants();
        foreach ($entities as $entityName => $key) {
            $this->entities[] = [
                'system_entity_id' => $key,
                'system_entity_name' => strtolower($entityName)
            ];
        }
        SystemEntity::insert($this->entities);
    }

    private function systemEntityTypes()
    {
        $systemEntityTypes = [];

        //The system entity has an ID of one and doesn't match a table in the DB
        $entities = \DB::select('
            SELECT system_entity_id,system_entity_name AS name
            FROM system_entities
            WHERE system_entity_name != "system" ORDER BY system_entity_id ASC');
        $k = 1;

        //We keep track of each entity's ID
        foreach ($entities as $entity) {
            $class = SystemEntity::getModelClassName($entity->system_entity_id);
            $primaryKey = (new $class)->getKeyName();
            $this->entityPrimaryKeyColumns[$entity->name] = $primaryKey;
            $ids = \DB::select(sprintf('
            SELECT %s
            FROM %s', $primaryKey, $entity->name));

            //Examples: SystemEntity::USERS, SystemEntity::ACTIVITIES
            $systemEntityID = constant(
                sprintf('%s::%s', SystemEntity::class, strtoupper($entity->name))
            );
            $this->entityIDtoEntityTypeIDTable[$systemEntityID] = [];
        }
    }

    private function createTriggers()
    {
        $entities = $this->entities;
        //We don't want the first element in that list, it doesn't match a db table.
        foreach ($entities as $entity) {
            if ($entity['system_entity_name'] == 'system') {
                continue;
            }
            \DB::unprepared(sprintf('
                CREATE TRIGGER t_create_system_entity_type_%1$s AFTER INSERT ON %1$s
                    FOR EACH ROW
                        BEGIN
                            DECLARE var_entity INTEGER;
                                SELECT system_entity_id INTO var_entity from system_entities WHERE system_entity_name="%1$s";
                                INSERT into system_entity_types(system_entity_id,system_entity_type_target_id) VALUES (var_entity,NEW.%2$s);
                            END
                              ', $entity['system_entity_name'],
                    $this->entityPrimaryKeyColumns[$entity['system_entity_name']])
            );
            \DB::unprepared(sprintf('
                CREATE TRIGGER t_delete_system_entity_type_%1$s AFTER DELETE ON %1$s
                    FOR EACH ROW
                        BEGIN
                            DELETE FROM system_entity_types WHERE system_entity_type_target_id=OLD.%2$s;
                        END', $entity['system_entity_name'],
                    $this->entityPrimaryKeyColumns[$entity['system_entity_name']])
            );
        }
        \DB::unprepared('
                CREATE TRIGGER t_people_create_fullname BEFORE INSERT ON people
                    FOR EACH ROW 
                    BEGIN
                      SET NEW.full_name = CONCAT(NEW.first_name, " ", NEW.last_name);
                    END
            ');
        \DB::unprepared('
                CREATE TRIGGER t_people_update_fullname BEFORE UPDATE ON people
                    FOR EACH ROW 
                    BEGIN
                      SET NEW.full_name = CONCAT(NEW.first_name, " ", NEW.last_name);
                    END
            ');
        $u = factory(App\Models\User::class)->create([
            'username' => 'system',
            'email' => 'system@localhost.local',
            'password' => bcrypt(str_random(15)),
            'activated' => false,
            'user_id' => 1,
            'remember_token' => null,
        ]);
        factory(App\Models\Person::class)->create([
            'person_id' => 1,
            'first_name' => 'system',
            'last_name' => '',
            'user_id' => 1
        ]);
    }

    private function createSystemEntities()
    {
        $entity = \App\Models\User::create([]);
        $entity->save();
        $userPk = $entity->getKeyName();
        $entity->setAttribute($userPk, 0);
        $entity->save();
        \App\Models\SystemEntityType::insert([
            'system_entity_id' => SystemEntity::USERS,
            'system_entity_type_target_id' => 0
        ]);
        $entity = \App\Models\Person::create([$userPk => 0]);
        $entity->save();
        $entity->setAttribute($entity->getKeyName(), 0);
        $entity->save();
        \App\Models\SystemEntityType::insert([
            'system_entity_id' => SystemEntity::PEOPLE,
            'system_entity_type_target_id' => 0
        ]);

    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
