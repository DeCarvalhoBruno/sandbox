<?php

namespace App\Http\Controllers\Ajax\Admin;

use App\Http\Controllers\Controller;

class User extends Controller
{
    public function index()
    {
        return [
            [
                'code' => 'ZW',
                'name' => 'Zimbabwe',
                'created_at' => '2015-04-24T01:46:50.459583',
                'updated_at' => '2015-04-24T01:46:50.459593',
                'uri' => 'http://api.lobbyfacts.eu/api/1/country/245',
                'id' => 245
            ],
            [
                'code' => 'SC',
                'name' => 'Seychelles',
                'created_at' => '2015-04-24T01:46:50.344980',
                'updated_at' => '2015-04-24T01:46:50.344984',
                'uri' => 'http://api.lobbyfacts.eu/api/1/country/197',
                'id' => 197
            ]
        ];
    }
}
