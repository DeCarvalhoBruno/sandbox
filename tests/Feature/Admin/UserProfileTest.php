<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    use DatabaseMigrations, WithoutMiddleware;

    /**
     * The avatar uploading process is done through a wizard. The image is stored in a tmp folder first.
     * It's only when the user decides how to crop the file that it is uploaded and its reference stored in the DB.
     */
    public function test_avatar_add()
    {
        $u = $this->signIn()->createUser();
        $imageTitle = 'mean_mug';
        $imageExtension = 'jpg';
        $imageFilename = sprintf('%s.%s', $imageTitle, $imageExtension);
        Storage::fake($imageFilename);

        $file = UploadedFile::fake()->image($imageFilename, 1224, 864)->size(1500);

        $response = $this->postJson(
            "/ajax/admin/media/add",
            [
                'type' => 'users',
                'target' => slugify($u->getAttribute('username')),
                'media' => 'image_avatar',
                'file' => $file
            ]
        );
        $responseArray = $response->json();
        $imageDiskFilename = $responseArray['filename'];

        $path = sprintf('%s/media/tmp/%s', public_path(), $imageDiskFilename);
        $this->assertFileExists($path);
    }

    public function test_set_new_password()
    {
        
    }

    public function test_update_profile()
    {
        
    }

}