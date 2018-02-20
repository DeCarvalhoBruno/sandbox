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

            $table->integer('permission_bit_value')->unsigned();

            $table->string('permission_action_name', 75);

            $table->foreign('entity_id')
                ->references('entity_id')->on('entities');
        });

        Schema::create('entity_type_owners', function (Blueprint $table) {
            $table->integer('entity_type_owner_id')->unsigned();
            $table->integer('entity_type_owned_id')->unsigned();

            $table->foreign('entity_type_owner_id')
                ->references('entity_type_id')->on('entity_types')
                ->onDelete('cascade');
            $table->foreign('entity_type_owned_id')
                ->references('entity_type_id')->on('entity_types')
                ->onDelete('cascade');
            $table->index(['entity_type_owner_id', 'entity_type_owned_id'], 'entity_owner_owned_idx');
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->increments('permission_id');

            $table->integer('entity_id')->unsigned();
            $table->integer('entity_type_id')->unsigned();

            $table->integer('permission_mask')->unsigned();

            $table->foreign('entity_type_id')
                ->references('entity_type_id')->on('entity_types')
                ->onDelete('cascade');
            $table->foreign('entity_id')
                ->references('entity_id')->on('entities')
                ->onDelete('cascade');
        });

        $this->seedPermissionActions();
        $this->seedPermissions();
        $this->seedOwners();
    }

    private function seedOwners()
    {
//        (new \App\Models\EntityTypeOwner())->insert([
//            [
//                'entity_type_owner_id' => 1,
//                'entity_type_owned_id' => 1,
//            ],
//            [
//                'entity_type_owner_id' => 2,
//                'entity_type_owned_id' => 2,
//            ]
//        ]);
    }

    private function seedPermissions()
    {
        (new \App\Models\Permission())->insert([
            [
                'entity_type_id' => 6,
                'entity_id' => \App\Models\Entity::USERS,
                'permission_mask' => 0b111,
            ],
            [
                'entity_type_id' => 6,
                'entity_id' => \App\Models\Entity::GROUPS,
                'permission_mask' => 0b1111,
            ],
            [
                'entity_type_id' => 6,
                'entity_id' => \App\Models\Entity::GROUP_MEMBERS,
                'permission_mask' => 0b1010,
            ],
            [
                'entity_type_id' => 7,
                'entity_id' => \App\Models\Entity::USERS,
                'permission_mask' => 0b111,
            ],
            [
                'entity_type_id' => 7,
                'entity_id' => \App\Models\Entity::GROUPS,
                'permission_mask' => 0b1111,
            ],
            [
                'entity_type_id' => 7,
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
                'permission_bit_value' => 0b1,
                'permission_action_name' => 'view'
            ],
            [
                'entity_id' => \App\Models\Entity::USERS,
                'permission_bit_value' => 0b10,
                'permission_action_name' => 'add'
            ],
            [
                'entity_id' => \App\Models\Entity::USERS,
                'permission_bit_value' => 0b100,
                'permission_action_name' => 'edit'
            ],
            [
                'entity_id' => \App\Models\Entity::GROUPS,
                'permission_bit_value' => 0b1,
                'permission_action_name' => 'view'
            ],
            [
                'entity_id' => \App\Models\Entity::GROUPS,
                'permission_bit_value' => 0b10,
                'permission_action_name' => 'add'
            ],
            [
                'entity_id' => \App\Models\Entity::GROUPS,
                'permission_bit_value' => 0b100,
                'permission_action_name' => 'edit'
            ],
            [
                'entity_id' => \App\Models\Entity::GROUPS,
                'permission_bit_value' => 0b1000,
                'permission_action_name' => 'delete'
            ],
            [
                'entity_id' => \App\Models\Entity::GROUP_MEMBERS,
                'permission_bit_value' => 0b10,
                'permission_action_name' => 'add'
            ],
            [
                'entity_id' => \App\Models\Entity::GROUP_MEMBERS,
                'permission_bit_value' => 0b1000,
                'permission_action_name' => 'delete'
            ],
        ]);

    }
}
