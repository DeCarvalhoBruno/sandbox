<?php namespace Naraki\System\Controllers;

use App\Http\Controllers\Admin\Controller;
use Naraki\System\Requests\UpdateUser;
use App\Models\Entity;
use App\Support\Providers\User as UserProvider;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Naraki\Media\Facades\Media as MediaProvider;

class Profile extends Controller
{
    /**
     * Update the user's profile information.
     *
     * @param \Naraki\System\Requests\UpdateUser $request
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
     * @return \Illuminate\Http\Response
     */
    public function setAvatar(Request $request, UserProvider $user)
    {
        MediaProvider::image()->setAsUsed($request->get('uuid'));
        return response($this->getAvatars($user), Response::HTTP_OK);
    }

    /**
     * @param int $uuid
     * @param \App\Support\Providers\User $user
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function deleteAvatar($uuid, UserProvider $user)
    {
        MediaProvider::image()->delete(
            $uuid,
            Entity::USERS,
            \Naraki\Media\Models\Media::IMAGE_AVATAR
        );

        return response($this->getAvatars($user), Response::HTTP_OK);
    }

    /**
     * @param \App\Support\Providers\User $user
     * @return mixed
     */
    private function getAvatars(UserProvider $user)
    {
        return $user->getAvatars($this->user->getKey());
    }

}
