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
        Schema::create('permission_actions', function (Blueprint $table) {
            $table->increments('permission_action_id');

            $table->integer('entity_id')->unsigned();

            $table->integer('permission_action_bits')->unsigned()->default(0);

            $table->string('permission_action_name', 75);

            $table->foreign('entity_id')
                ->references('entity_id')->on('entities');
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->increments('permission_id');

            $table->integer('entity_type_id')->unsigned();
            $table->integer('entity_id')->unsigned();

            $table->integer('permission_mask')->unsigned()->default(0);

            $table->foreign('entity_id')
                ->references('entity_id')->on('entities');
            $table->foreign('entity_type_id')
                ->references('entity_type_id')->on('entity_types')
                ->onDelete('cascade');
        });

        Schema::create('permission_stores', function (Blueprint $table) {
            $table->increments('permission_store_id');
            $table->string('permission_store_name', 75);
        });

        Schema::create('permission_store_types', function (Blueprint $table) {
            $table->increments('permission_store_type_id');

            $table->integer('permission_store_target_id')->unsigned();

        });

        Schema::create('permission_store_records', function (Blueprint $table) {
            $table->increments('permission_store_record_id');

            $table->integer('entity_id')->unsigned();
            $table->integer('permission_store_id')->unsigned();

            $table->integer('permission_target_id')->unsigned();
            $table->integer('permission_holder_id')->unsigned();

            $table->integer('permission_store_type_id')->unsigned();

            $table->foreign('entity_id')
                ->references('entity_id')->on('entities');
            $table->foreign('permission_store_id')
                ->references('permission_store_id')->on('permission_stores');
            $table->foreign('permission_target_id')
                ->references('entity_type_id')->on('entity_types');
            $table->foreign('permission_holder_id')
                ->references('entity_type_id')->on('entity_types');
            $table->foreign('permission_store_type_id')
                ->references('permission_store_type_id')->on('permission_store_types');

//            $table->index(['entity_id', 'permission_store_target_id', 'permission_store_holder_id'],
//                'idx_permission_store');
        });

        Schema::create('permission_store_default', function (Blueprint $table) {
            $table->increments('permission_store_default_id');

            $table->integer('permission_store_type_id')->unsigned();
            $table->integer('permission_mask')->unsigned();

            $table->foreign('permission_store_type_id')
                ->references('permission_store_type_id')->on('permission_store_types');
        });

        Schema::create('permission_store_computed', function (Blueprint $table) {
            $table->increments('permission_store_computed_id');

            $table->integer('permission_store_type_id')->unsigned();

            $table->integer('permission_mask')->unsigned();

            $table->foreign('permission_store_type_id')
                ->references('permission_store_type_id')->on('permission_store_types');
        });
//        $entity = \App\Models\PermissionStore::create([]);
//        $entity->save();
//        $pk = $entity->getKeyName();
//        $entity->setAttribute($pk, 0);
//        $entity->save();

        $this->seedPermissions();
        $this->seedPermissionActions();
        $this->seedPermissionStore();
    }

    private function seedPermissions()
    {
        (new \App\Models\Permission())->insert([
            [
                'entity_type_id' => 3,
                'entity_id' => \App\Models\Entity::USERS,
                'permission_mask' => 0b111
            ],
            [
                'entity_type_id' => 3,
                'entity_id' => \App\Models\Entity::GROUPS,
                'permission_mask' => 0b1111
            ],
            [
                'entity_type_id' => 3,
                'entity_id' => \App\Models\Entity::GROUP_MEMBERS,
                'permission_mask' => 0b1010
            ],
            [
                'entity_type_id' => 4,
                'entity_id' => \App\Models\Entity::USERS,
                'permission_mask' => 0b111,
            ],
            [
                'entity_type_id' => 4,
                'entity_id' => \App\Models\Entity::GROUPS,
                'permission_mask' => 0b1111,
            ],
            [
                'entity_type_id' => 4,
                'entity_id' => \App\Models\Entity::GROUP_MEMBERS,
                'permission_mask' => 0b1010,
            ],
            [
                'entity_type_id' => 5,
                'entity_id' => \App\Models\Entity::USERS,
                'permission_mask' => 0b101,
            ],
        ]);

    }

    private function seedPermissionActions()
    {
        (new \App\Models\PermissionAction())->insert([
            [
                'entity_id' => \App\Models\Entity::USERS,
                'permission_action_bits' => 0b1,
                'permission_action_name' => 'view'
            ],
            [
                'entity_id' => \App\Models\Entity::USERS,
                'permission_action_bits' => 0b10,
                'permission_action_name' => 'add'
            ],
            [
                'entity_id' => \App\Models\Entity::USERS,
                'permission_action_bits' => 0b100,
                'permission_action_name' => 'edit'
            ],
            [
                'entity_id' => \App\Models\Entity::GROUPS,
                'permission_action_bits' => 0b1,
                'permission_action_name' => 'view'
            ],
            [
                'entity_id' => \App\Models\Entity::GROUPS,
                'permission_action_bits' => 0b10,
                'permission_action_name' => 'add'
            ],
            [
                'entity_id' => \App\Models\Entity::GROUPS,
                'permission_action_bits' => 0b100,
                'permission_action_name' => 'edit'
            ],
            [
                'entity_id' => \App\Models\Entity::GROUPS,
                'permission_action_bits' => 0b1000,
                'permission_action_name' => 'delete'
            ],
            [
                'entity_id' => \App\Models\Entity::GROUP_MEMBERS,
                'permission_action_bits' => 0b10,
                'permission_action_name' => 'add'
            ],
            [
                'entity_id' => \App\Models\Entity::GROUP_MEMBERS,
                'permission_action_bits' => 0b1000,
                'permission_action_name' => 'delete'
            ],
        ]);

    }

    private function seedPermissionStore()
    {
        (new \App\Models\PermissionStore())->insert([
            [
                'permission_store_name' => 'default'
            ],
            [
                'permission_store_name' => 'computed'
            ],
        ]);
    }
}
