<?php

namespace App\Http\Controllers\Ajax\Admin\Settings;

use App\Http\Controllers\Admin\Controller;
use App\Support\Providers\User as UserProvider;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class Password extends Controller
{
    use ValidatesRequests;

    /**
     * Update the user's password.
     *
     * @param  \Illuminate\Http\Request $request
     * @param \App\Support\Providers\User $user
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, UserProvider $user)
    {
        \Validator::make($request->all(), [
            'password' => 'required|confirmed|min:6'
        ])->after(function ($validator) use ($request) {
            if (!Hash::check($request->get('current_password'), $this->user->getAttribute('password'))) {
                $validator->errors()->add('current_password', trans('error.form.wrong_password'));
                return false;
            }
            if (Hash::check($request->get('password'), $this->user->getAttribute('password'))) {
                $validator->errors()->add('password', trans('error.form.identical_passwords'));
                return false;
            }
            return true;
        })->validate();

        $user->updateOneByUsername(
            $this->user->getAttribute('username'),
            ['password' => bcrypt($request->get('password'))]
        );

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
