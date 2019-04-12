<?php namespace Naraki\System\Controllers;

use App\Http\Controllers\Admin\Controller;
use Naraki\Sentry\Providers\User as UserProvider;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class Password extends Controller
{
    /**
     * Update the user's password.
     *
     * @param  \Illuminate\Http\Request $request
     * @param \Naraki\Sentry\Providers\User $userRepo
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, UserProvider $userRepo)
    {
        $user = auth()->user();
        \Validator::make($request->all(), [
            'password' => 'required|confirmed|min:6'
        ])->after(function ($validator) use ($request, $user) {
            if (!Hash::check($request->get('current_password'), $user->getAttribute('password'))) {
                $validator->errors()->add('current_password', trans('error.form.wrong_password'));
                return false;
            }
            if (Hash::check($request->get('password'), $user->getAttribute('password'))) {
                $validator->errors()->add('password', trans('error.form.identical_passwords'));
                return false;
            }
            return true;
        })->validate();

        $userRepo->updateOneByUsername(
            $this->user->getAttribute('username'),
            ['password' => bcrypt($request->get('password'))]
        );

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
