<?php

namespace App\Http\Controllers\Ajax\Admin;

use App\Http\Controllers\Controller;
use App\Models\Entity;
use Illuminate\Http\Response;
use App\Models\Media\Media as MediaModel;

class Media extends Controller
{
    public function add()
    {
//        dd($r->all());
        $input = (object)$this->request->only(['type', 'target', 'media', 'group', 'category', 'file']);

        $file = $this->request->file('file');

        if (!is_null($file)) {
            $originalFilename = $file->getClientOriginalName();
            if (!Entity::isValidName($input->type)) {
                throw new \UnexpectedValueException('This image entity type does not match anything on disk');
            }
            if (!MediaModel::isValidName($input->media)) {
                throw new \UnexpectedValueException('This media type does not match anything on disk');
            }
            $filename = makeFilename($input->target, $file->getClientOriginalExtension());
            $filePath = media_entity_root_path($input->type, $input->media);
            dd($filename,$filePath);
        }
        return response(null, Response::HTTP_NO_CONTENT);
    }

}