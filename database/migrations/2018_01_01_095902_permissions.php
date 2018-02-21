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

            $table->integer('entity_id')->unsigned();
            $table->integer('entity_type_id')->unsigned();

            $table->integer('permission_mask')->unsigned()->default(0);

            $table->foreign('entity_type_id')
                ->references('entity_type_id')->on('entity_types')
                ->onDelete('cascade');
            $table->foreign('entity_id')
                ->references('entity_id')->on('entities')
                ->onDelete('cascade');
        });

        Schema::create('permission_processed', function (Blueprint $table) {
            $table->increments('permission_processed_id');
            $table->integer('entity_type_id')->unsigned();

            $table->foreign('entity_type_id')
                ->references('entity_type_id')->on('entity_types')
                ->onDelete('cascade');
            $table->string('permission_processed_tag',25)->nullable();

            $table->integer('permission_processed_mask')->unsigned()->default(0);

            $table->index(['permission_processed_id','entity_type_id'],'idx_effective_permision_tag');
        });

//        $entity = \App\Models\PermissionProcessed::create([]);
//        $entity->save();
//        $entity->setAttribute($entity->getKeyName(), 0);
//        $entity->save();

        $this->seedPermissionActions();
        $this->seedPermissions();
    }

    private function seedPermissions()
    {
        (new \App\Models\Permission())->insert([
            [
                'entity_type_id' => 3,
                'entity_id' => \App\Models\Entity::USERS,
                'permission_mask' => 0b111,
            ],
            [
                'entity_type_id' => 3,
                'entity_id' => \App\Models\Entity::GROUPS,
                'permission_mask' => 0b1111,
            ],
            [
                'entity_type_id' => 3,
                'entity_id' => \App\Models\Entity::GROUP_MEMBERS,
                'permission_mask' => 0b1010,
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
}
