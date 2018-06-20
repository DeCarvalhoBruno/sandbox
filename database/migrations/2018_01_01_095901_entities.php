<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Entity;

class Entities extends Migration
{

    private $entityPrimaryKeyColumns = [];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entities', function (Blueprint $table) {
            $table->smallIncrements('entity_id');
            $table->string('entity_name', 75)->nullable();
        });

        Schema::create('entity_types', function (Blueprint $table) {
            $table->increments('entity_type_id');

            $table->unsignedSmallInteger('entity_id')->unsigned()->default(Entity::SYSTEM);
            $table->integer('entity_type_target_id')->unsigned()->comment('the ID in the entity\'s (users,products,etc.) table');

            $table->foreign('entity_id')
                ->references('entity_id')->on('entities');
        });

        $this->addEntities();
        $this->entityTypes();
        $this->createEntities();
        $this->createTriggers();
        $this->createGroups();

    }

    private static function createGroups()
    {
        $u = factory(App\Models\User::class)->create([
            'username' => 'root',
            'email' => 'system@localhost.local',
            'password' => bcrypt(config('auth.root_password')),
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
                'group_name' => 'Super Admins',
                'group_id' => 2,
                'group_mask' => 2
            ],
            [
                'group_name' => 'Admins',
                'group_id' => 3,
                'group_mask' => 2000
            ],
            [
                'group_name' => 'Users',
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
            $primaryKey = Entity::createModel($entity->entity_id)->getKeyName();
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
        $db = app()->make(\App\Contracts\RawQueries::class);
        //We don't want the first element in that list, it doesn't match a db table.
        foreach ($entities as $entity) {
            if (!isset($this->entityPrimaryKeyColumns[$entity['entity_name']]) || $entity['entity_name'] == 'system') {
                continue;
            }
            $db->triggerCreateEntityType(
                $entity['entity_name'],
                $this->entityPrimaryKeyColumns[$entity['entity_name']]
            );
            $db->triggerDeleteEntityType(
                $entity['entity_name'],
                $this->entityPrimaryKeyColumns[$entity['entity_name']],
                $entity['entity_id']
            );
        }
        $db->triggerUserFullName();
        $db->triggerUserUpdateActivated();

    }

    private function createEntities()
    {
        $entity = \App\Models\EntityType::create(['entity_type_target_id' => 0]);
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
            'entity_type_id' => 1
        ]);
        $entity = \App\Models\Person::create([$pk => 0]);
        $entity->save();
        $entity->setAttribute($entity->getKeyName(), 0);
        $entity->save();

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
