<?php

namespace App\Http\Controllers\Ajax\Admin\Settings;

use App\Contracts\Models\Media as MediaProvider;
use App\Http\Controllers\Admin\Controller;
use App\Http\Requests\Admin\UpdateUser;
use App\Models\Entity;
use App\Support\Providers\User as UserProvider;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Profile extends Controller
{
    /**
     * Update the user's profile information.
     *
     * @param \App\Http\Requests\Admin\UpdateUser $request
     * @param \App\Support\Providers\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUser $request, UserProvider $user)
    {
        $savedUser = $user->updateOneByUsername(
            $this->user->getAttribute('username'),
            $request->all()
        );

        return response([
            'user' => $savedUser->only(['username', 'first_name', 'last_name', 'email']),
        ],Response::HTTP_OK);
    }

    /**
     * @param \App\Support\Providers\User $user
     * @return \Illuminate\Http\Response
     */
    public function avatar(UserProvider $user)
    {
        return response($this->getAvatars($user), Response::HTTP_OK);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Support\Providers\User $user
     * @param \App\Contracts\Models\Media|\App\Support\Providers\Media $mediaRepo
     * @return \Illuminate\Http\Response
     */
    public function setAvatar(Request $request, UserProvider $user, MediaProvider $mediaRepo)
    {
        $mediaRepo->image()->setAsUsed($request->get('uuid'));
        return response($this->getAvatars($user), Response::HTTP_OK);
    }

    /**
     * @param int $uuid
     * @param \App\Support\Providers\User $user
     * @param \App\Contracts\Models\Media|\App\Support\Providers\Media $mediaRepo
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function deleteAvatar($uuid, UserProvider $user, MediaProvider $mediaRepo)
    {
        $mediaRepo->image()->delete(
            $uuid,
            Entity::USERS,
            \App\Models\Media\Media::IMAGE_AVATAR
        );

        return response($this->getAvatars($user), Response::HTTP_OK);
    }

    private function getAvatars(UserProvider $user)
    {
        return $user->getAvatars($this->user->getKey());
    }

}
