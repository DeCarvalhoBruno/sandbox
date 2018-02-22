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

        Schema::create('permissions', function (Blueprint $table) {
            $table->increments('permission_id');

            $table->integer('entity_id')->unsigned();
            $table->integer('entity_type_id')->unsigned()->default(0);

            $table->integer('permission_mask')->unsigned()->default(0);

            $table->foreign('entity_id')
                ->references('entity_id')->on('entities');
        });

        Schema::create('permission_entities', function (Blueprint $table) {
            $table->increments('permission_entity_id');

            $table->integer('permission_id')->unsigned()->default(0);
            $table->integer('permission_entity_mask')->unsigned()->default(0);

            $table->foreign('permission_id')
                ->references('permission_id')->on('permissions');
        });

        Schema::create('entity_types', function (Blueprint $table) {
            $table->increments('entity_type_id');

            $table->integer('entity_id')->unsigned()->default(Entity::SYSTEM);
            $table->integer('entity_type_target_id')->unsigned()->comment('the ID in the entity\'s (users,products,etc.) table');
            $table->integer('permission_entity_id')->unsigned()->default(0);

            $table->foreign('entity_id')
                ->references('entity_id')->on('entities');
            $table->foreign('permission_entity_id')
                ->references('permission_entity_id')->on('permission_entities');
        });

        $this->addEntities();
        $this->entityTypes();
        $this->createEntities();
        if (app()->environment() != 'testing') {
            $this->createTriggers();
        }
        $this->createGroups();

    }

    private static function createGroups()
    {
        $u = factory(App\Models\User::class)->create([
            'username' => 'root',
            'email' => 'system@localhost.local',
            'password' => bcrypt(str_random(15)),
            'activated' => true,
            'user_id' => 1,
            'remember_token' => null,
        ]);
        factory(App\Models\Person::class)->create([
            'person_id' => 1,
            'first_name' => 'root',
            'last_name' => '',
            'user_id' => 1
        ]);

        (new \App\Models\Group)->insert([
            [
                'group_name' => 'root',
                'group_id' => 1,
                'group_mask' => 1
            ],
            [
                'group_name' => 'superadmins',
                'group_id' => 2,
                'group_mask' => 2
            ],
            [
                'group_name' => 'admins',
                'group_id' => 3,
                'group_mask' => 2000
            ],
            [
                'group_name' => 'users',
                'group_id' => 4,
                'group_mask' => 5000
            ],
        ]);
        factory(App\Models\GroupMember::class)->create([
            "group_id" => 1,
            'user_id' => 1
        ]);
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
            if ($primaryKey == 'id') {
                continue;
            }
            $this->entityPrimaryKeyColumns[$entity->name] = $primaryKey;
            $ids = \DB::select(sprintf('
            SELECT %s
            FROM %s', $primaryKey, $entity->name));

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
            if (!isset($this->entityPrimaryKeyColumns[$entity['entity_name']]) || $entity['entity_name'] == 'system') {
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
                        END
                        ', $entity['entity_name'],
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
    }

    private function createEntities()
    {
        $entity = \App\Models\Permission::create(['entity_id'=>Entity::SYSTEM]);
        $entity->save();
        $pk = $entity->getKeyName();
        $entity->setAttribute($pk, 0);
        $entity->save();

        $entity = \App\Models\PermissionEntity::create(['permission_id'=>0]);
        $entity->save();
        $pk = $entity->getKeyName();
        $entity->setAttribute($pk, 0);
        $entity->save();

        $entity = \App\Models\EntityType::create(['entity_type_target_id'=>0]);
        $entity->save();
        $pk = $entity->getKeyName();
        $entity->setAttribute($pk, 0);
        $entity->save();

        $entity = \App\Models\User::create([]);
        $entity->save();
        $pk = $entity->getKeyName();
        $entity->setAttribute($pk, 0);
        $entity->save();
        (new \App\Models\EntityType)->insert([
            'entity_id' => Entity::USERS,
            'entity_type_target_id' => 0,
            'entity_type_id'=>1
        ]);
        $entity = \App\Models\Person::create([$pk => 0]);
        $entity->save();
        $entity->setAttribute($entity->getKeyName(), 0);
        $entity->save();

        Schema::table('permissions', function (Blueprint $table) {
            $table->foreign('entity_type_id')
                ->references('entity_type_id')->on('entity_types')
                ->onDelete('cascade');
        });
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
