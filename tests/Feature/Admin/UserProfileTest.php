<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    use DatabaseMigrations, WithoutMiddleware;

    /**
     * The avatar uploading process is done through a wizard. The image is stored in a tmp folder first.
     * It's only when the user decides how to crop the file that it is uploaded and its reference stored in the DB.
     */
    public function test_user_profile_avatar_add()
    {
        $this->withoutJobs();
        $u = $this->createUser();
        $this->signIn($u);
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

    public function test_user_profile_set_new_password_with_wrong_current_password()
    {
        $this->withoutJobs();
        $this->withExceptionHandling();
        $this->signIn($this->createUser(),'jwt');

        $response = $this->patchJson(
            '/ajax/admin/user/password',
            [
                'current_password' => 'random',
                'password' => 'dfsdfsfsdfds',
                'password_confirmation' => 'dfsdfsfsdfds'
            ]
        );
        $response->assertStatus(422);
    }

    public function test_user_profile_set_new_password_same_password()
    {
        $this->withoutJobs();
        $this->withExceptionHandling();
        $this->signIn($this->createUser(),'jwt')->createUser();
        $response = $this->patchJson(
            '/ajax/admin/user/password',
            [
                'current_password' => 'secret',
                'password' => 'secret',
                'password_confirmation' => 'secret'
            ]
        );
        $response->assertStatus(422);
    }

    public function test_user_profile_set_new_password()
    {
        $this->withoutJobs();
        $this->withExceptionHandling();
        $u = $this->createUser();
        $this->signIn($u,'jwt');
        $response = $this->patchJson(
            '/ajax/admin/user/password',
            [
                'current_password' => 'secret',
                'password' => 'dfsdfsfsdfds',
                'password_confirmation' => 'dfsdfsfsdfds'
            ]
        );
        $response->assertStatus(204);

        $freshPassword = auth()->user()->fresh()->getAttribute('password');
        $this->assertTrue(Hash::check('dfsdfsfsdfds', $freshPassword));
    }

    public function test_user_profile_update_profile()
    {
        $this->withoutJobs();
        $this->withExceptionHandling();
        $response = $this->signIn($this->createUser(),'jwt')->patchJson(
            '/ajax/admin/user/profile',
            [
                'first_name' => 'Jane',
                'full_name' => 'Jane Doe',
                'last_name' => 'Doe',
                'username' => 'jane_doe',
                'email' => 'jane.doe@example.com'
            ]);
        $response->assertStatus(200);
        $this->assertNotNull(User::query()->where('email', '=', 'jane.doe@example.com')->first());
    }

}