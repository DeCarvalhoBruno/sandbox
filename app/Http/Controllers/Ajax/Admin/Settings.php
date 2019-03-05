<?php namespace App\Http\Controllers\Ajax\Admin;

use App\Contracts\Models\System as SystemProvider;
use App\Http\Controllers\Admin\Controller;
use App\Http\Requests\Admin\UpdateSettings;
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
            'settings'=>\Cache::get('settings_general'),
            'websites' => $systemRepo->settings()->websiteList(),
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
        $input = $request->all();
        if (!is_null($input['logo'])) {
            $file = $request->file('logo');
            $filename = sprintf('logo_jld.%s', $file->getClientOriginalExtension());
            $file->move(sprintf('%s/media/img/site', public_path()), $filename);
            $input['logo'] = asset('media/img/site/logo_jld.jpg');
        }
        \Cache::forever('settings_general', $input);
        \Cache::forever('settings_has_jsonld', $input['jsonld']);

        if ($input['jsonld'] === true) {
            \Cache::forever('meta_jsonld', $systemRepo->settings()->makeStructuredData($input));
        }else{
            \Cache::forever('meta_jsonld','');
        }
        \Cache::forever('meta_robots', $input['robots'] === true ? 'index, follow' : 'noindex, nofollow');
        \Cache::forever('meta_description', $input['site_description']);

        return response(null, Response::HTTP_NO_CONTENT);
    }


}