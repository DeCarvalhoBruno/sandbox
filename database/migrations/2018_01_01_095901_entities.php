<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Entity;

class Entities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entities', function (Blueprint $table) {
            $table->increments('entity_id');
            $table->string('entity_name', 75)->nullable();
        });

        Schema::create('entity_types', function (Blueprint $table) {
            $table->increments('entity_type_id');

            $table->integer('entity_id')->unsigned();
            $table->smallInteger('entity_type_target_id')->unsigned()->comment('the ID in the entity\'s (user,message,etc.) table');

            $table->foreign('entity_id')
                ->references('entity_id')->on('entities')
                ->onDelete('cascade');
        });

        $this->addEntities();
        $this->entityTypes();
        $this->createEntities();
        if (app()->environment() != 'testing') {
            $this->createTriggers();
        }

    }

    private function addEntities()
    {
        $entities = Entity::getConstants();
        foreach ($entities as $entityName => $key) {
            $this->entities[] = [
                'entity_id' => $key,
                'entity_name' => strtolower($entityName)
            ];
        }
        Entity::insert($this->entities);
    }

    private function entityTypes()
    {
        $entityTypes = [];

        //The system entity has an ID of one and doesn't match a table in the DB
        $entities = \DB::select('
            SELECT entity_id,entity_name AS name
            FROM entities
            WHERE entity_name != "system" ORDER BY entity_id ASC');
        $k = 1;

        //We keep track of each entity's ID
        foreach ($entities as $entity) {
            $class = Entity::getModelClassName($entity->entity_id);
            $primaryKey = (new $class)->getKeyName();
            $this->entityPrimaryKeyColumns[$entity->name] = $primaryKey;
            $ids = \DB::select(sprintf('
            SELECT %s
            FROM %s', $primaryKey, $entity->name));

            //Examples: Entity::USERS, Entity::ACTIVITIES
            $entityID = constant(
                sprintf('%s::%s', Entity::class, strtoupper($entity->name))
            );
            $this->entityIDtoEntityTypeIDTable[$entityID] = [];
        }
    }

    private function createTriggers()
    {
        $entities = $this->entities;
        //We don't want the first element in that list, it doesn't match a db table.
        foreach ($entities as $entity) {
            if ($entity['entity_name'] == 'system') {
                continue;
            }
            \DB::unprepared(sprintf('
                CREATE TRIGGER t_create_entity_type_%1$s AFTER INSERT ON %1$s
                    FOR EACH ROW
                        BEGIN
                            DECLARE var_entity INTEGER;
                                SELECT entity_id INTO var_entity from entities WHERE entity_name="%1$s";
                                INSERT into entity_types(entity_id,entity_type_target_id) VALUES (var_entity,NEW.%2$s);
                            END
                              ', $entity['entity_name'],
                    $this->entityPrimaryKeyColumns[$entity['entity_name']])
            );
            \DB::unprepared(sprintf('
                CREATE TRIGGER t_delete_entity_type_%1$s AFTER DELETE ON %1$s
                    FOR EACH ROW
                        BEGIN
                            DELETE FROM entity_types WHERE entity_type_target_id=OLD.%2$s;
                        END', $entity['entity_name'],
                    $this->entityPrimaryKeyColumns[$entity['entity_name']])
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

    private function createEntities()
    {
        $entity = \App\Models\User::create([]);
        $entity->save();
        $userPk = $entity->getKeyName();
        $entity->setAttribute($userPk, 0);
        $entity->save();
        \App\Models\EntityType::insert([
            'entity_id' => Entity::USERS,
            'entity_type_target_id' => 0
        ]);
        $entity = \App\Models\Person::create([$userPk => 0]);
        $entity->save();
        $entity->setAttribute($entity->getKeyName(), 0);
        $entity->save();
        \App\Models\EntityType::insert([
            'entity_id' => Entity::PEOPLE,
            'entity_type_target_id' => 0
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
