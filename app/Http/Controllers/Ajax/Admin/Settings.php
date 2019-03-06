<?php namespace App\Http\Controllers\Ajax\Admin;

use App\Http\Controllers\Admin\Controller;
use App\Http\Requests\Admin\UpdateSettings;
use App\Http\Requests\Admin\UpdateSocialSettings;
use App\Support\Frontend\Jsonld\Models\General as GeneralJsonldManager;
use App\Support\Frontend\Social\General;
use Illuminate\Http\Response;

class Settings extends Controller
{

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function edit()
    {
        $jsonldMgr = new GeneralJsonldManager();
        return response([
            'settings' => $jsonldMgr->getSettings(),
            'websites' => $jsonldMgr->websiteList(),
            'organizations' => $jsonldMgr->organizationList()
        ], Response::HTTP_OK);
    }

    /**
     * @param \App\Http\Requests\Admin\UpdateSettings $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update(UpdateSettings $request)
    {
        $jsonldMgr = new GeneralJsonldManager();
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
            \Cache::forever('meta_jsonld', $jsonldMgr->makeStructuredData($input));
        } else {
            \Cache::forever('meta_jsonld', '');
        }
        \Cache::forever('meta_robots', $input['robots'] === true ? 'index, follow' : 'noindex, nofollow');
        \Cache::forever('meta_description', $input['site_description']);
        \Cache::forever('meta_keywords', $input['site_keywords']);
        \Cache::forever('meta_title', $input['site_title']);

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function editSocial()
    {
        return response([
            'settings' => \Cache::get('settings_social'),
        ], Response::HTTP_OK);
    }

    public function updateSocial(UpdateSocialSettings $request)
    {
        $socialTagManager = new General();
        $input = $request->all();
        \Cache::forever('settings_social', $input);
        $description = \Cache::get('meta_description');
        $title = \Cache::get('meta_title');
        \Cache::forever('settings_has_facebook', $input['open_graph']);
        \Cache::forever('settings_has_twitter', $input['twitter_cards']);
        if ($input['open_graph'] === true) {
            \Cache::forever('meta_facebook', $socialTagManager->getFacebookTagList($title, $description, $input));
        }
        if ($input['twitter_cards'] === true) {
            \Cache::forever('meta_twitter', $socialTagManager->getTwitterTagList($title, $description, $input));
        }
        return response(null, Response::HTTP_NO_CONTENT);
    }


}