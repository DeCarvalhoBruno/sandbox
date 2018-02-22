<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Permissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permission_entities', function (Blueprint $table) {
            $table->increments('permission_entity_id');

            $table->integer('entity_id')->unsigned();
            $table->foreign('entity_id')
                ->references('entity_id')->on('entities');
        });

        Schema::create('permission_actions', function (Blueprint $table) {
            $table->increments('permission_action_id');

            $table->integer('permission_entity_id')->unsigned();

            $table->integer('permission_action_bits')->unsigned()->default(0);

            $table->string('permission_action_name', 75);

            $table->foreign('permission_entity_id')
                ->references('permission_entity_id')->on('permission_entities');
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->increments('permission_id');

            $table->integer('entity_type_id')->unsigned();
            $table->integer('permission_entity_id')->unsigned();

            $table->integer('permission_mask')->unsigned()->default(0);

            $table->foreign('permission_entity_id')
                ->references('permission_entity_id')->on('permission_entities');
            $table->foreign('entity_type_id')
                ->references('entity_type_id')->on('entity_types')
                ->onDelete('cascade');
        });

        Schema::create('permission_stores', function (Blueprint $table) {
            $table->integer('permission_entity_id')->unsigned();
            $table->integer('permission_store_target_id')->unsigned();
            $table->integer('permission_store_holder_id')->unsigned();
            $table->integer('permission_store_mask')->unsigned()->default(0);

            $table->foreign('permission_store_target_id')
                ->references('entity_type_id')->on('entity_types')
                ->onDelete('cascade');
            $table->foreign('permission_store_holder_id')
                ->references('entity_type_id')->on('entity_types')
                ->onDelete('cascade');
            $table->foreign('permission_entity_id')
                ->references('permission_entity_id')->on('permission_entities');
            $table->index(['permission_entity_id', 'permission_store_target_id', 'permission_store_holder_id'],
                'idx_permission_store');
        });

//        $entity = \App\Models\PermissionStore::create([]);
//        $entity->save();
//        $pk = $entity->getKeyName();
//        $entity->setAttribute($pk, 0);
//        $entity->save();

        $this->seedPermissions();
        $this->seedPermissionActions();
    }

    private function seedPermissions()
    {
        (new \App\Models\PermissionEntity())->insert([
            [
                'entity_id' => \App\Models\Entity::USERS,
            ],
            [
                'entity_id' => \App\Models\Entity::GROUPS,
            ],
            [
                'entity_id' => \App\Models\Entity::GROUP_MEMBERS,
            ]
        ]);

        (new \App\Models\Permission())->insert([
            [
                'entity_type_id' => 3,
                'permission_mask' => 0b111,
                'permission_entity_id'=>1
            ],
            [
                'entity_type_id' => 3,
                'permission_mask' => 0b1111,
                'permission_entity_id'=>2
            ],
            [
                'entity_type_id' => 3,
                'permission_mask' => 0b1010,
                'permission_entity_id'=>3
            ],
            [
                'entity_type_id' => 4,
                'permission_entity_id'=>1,
                'permission_mask' => 0b111,
            ],
            [
                'entity_type_id' => 4,
                'permission_entity_id'=>2,
                'permission_mask' => 0b1111,
            ],
            [
                'entity_type_id' => 4,
                'permission_entity_id'=>3,
                'permission_mask' => 0b1010,
            ],
            [
                'entity_type_id' => 5,
                'permission_entity_id'=>1,
                'permission_mask' => 0b101,
            ],
        ]);

    }

    private function seedPermissionActions()
    {
        (new \App\Models\PermissionAction())->insert([
            [
                'permission_entity_id'=>1,
                'permission_action_bits' => 0b1,
                'permission_action_name' => 'view'
            ],
            [
                'permission_entity_id'=>1,
                'permission_action_bits' => 0b10,
                'permission_action_name' => 'add'
            ],
            [
                'permission_entity_id'=>1,
                'permission_action_bits' => 0b100,
                'permission_action_name' => 'edit'
            ],
            [
                'permission_entity_id'=>2,
                'permission_action_bits' => 0b1,
                'permission_action_name' => 'view'
            ],
            [
                'permission_entity_id'=>2,
                'permission_action_bits' => 0b10,
                'permission_action_name' => 'add'
            ],
            [
                'permission_entity_id'=>2,
                'permission_action_bits' => 0b100,
                'permission_action_name' => 'edit'
            ],
            [
                'permission_entity_id'=>2,
                'permission_action_bits' => 0b1000,
                'permission_action_name' => 'delete'
            ],
            [
                'permission_entity_id'=>3,
                'permission_action_bits' => 0b10,
                'permission_action_name' => 'add'
            ],
            [
                'permission_entity_id'=>3,
                'permission_action_bits' => 0b1000,
                'permission_action_name' => 'delete'
            ],
        ]);

    }
}
