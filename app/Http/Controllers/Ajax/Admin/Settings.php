<?php namespace App\Http\Controllers\Ajax\Admin;

use App\Contracts\Models\System as SystemProvider;
use App\Http\Controllers\Admin\Controller;
use App\Http\Requests\Admin\UpdateSettings;
use App\Support\Providers\SystemSettings;
use Illuminate\Http\Response;

class Settings extends Controller
{
    /**
     * @param \App\Contracts\Models\System|\App\Support\Providers\System $systemRepo
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function edit(SystemProvider $systemRepo)
    {

        return response([
            'organizations' => $systemRepo->settings()->organizationList()
        ], Response::HTTP_OK);
    }

    /**
     * @param \App\Http\Requests\Admin\UpdateSettings $request
     * @param \App\Contracts\Models\System|\App\Support\Providers\System $systemRepo
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update(UpdateSettings $request, SystemProvider $systemRepo)
    {

        /*
            entity_type: "organization"
            jsonld: true
            links: "5,6"
            logo: {}
            org_type: "Corporation"
            person_name: "null"
            robots: true
            site_description: "1"
            website_type: "blog"
         */


        $input = $request->all();
//        $systemRepo->settings()->makeStructuredData($input);
//        \Cache::forever('site_description', $input['site_description']);
        return response($request->all(), 200);
    }


}