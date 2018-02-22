<?php

use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        (new \App\Models\Permission())->insert([
            [
                'entity_type_id'=>7,
                'permission_entity_id'=>1,
                'permission_mask'=>0b101,
            ],
            [
                'entity_type_id'=>100,
                'permission_entity_id'=>1,
                'permission_mask'=>0b111,
            ],
            [
                'entity_type_id'=>100,
                'permission_entity_id'=>2,
                'permission_mask'=>0b1111,
            ],
            [
                'entity_type_id'=>100,
                'permission_entity_id'=>3,
                'permission_mask'=>0b1010,
            ],
            [
                'entity_type_id'=>101,
                'permission_entity_id'=>3,
                'permission_mask'=>0b1010,
            ],
        ]);
    }
}
