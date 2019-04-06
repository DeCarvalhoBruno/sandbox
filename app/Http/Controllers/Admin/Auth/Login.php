<?php namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Admin\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redis;
use Illuminate\Validation\ValidationException;
use Naraki\System\Facades\System;

class Login extends Controller
{
    use AuthenticatesUsers;

    private $permissions;
    private $entity;

    protected function guard()
    {
        return \Auth::guard('jwt');
    }

    protected function attemptLogin(Request $request)
    {
//        if ($request->get('remember')) {
//            $this->guard()->setTTL(config('jwt.ttl_remember_me'));
//        }
        $token = $this->guard()->attempt($this->credentials($request));

        $this->setEntity($this->guard()->user()->getAttribute('entity_type_id'));
        $this->setPermissions(System::getAllUserPermissions($this->entity));
        if (!isset($this->permissions->computed['system']['Login'])) {
            throw ValidationException::withMessages([
                $this->username() => [trans('auth.failed_not_allowed')],
            ]);
        }

        if ($token) {
            $this->guard()->setToken($token);
            return true;
        }
        return false;
    }

    protected function sendLoginResponse(Request $request)
    {
        $this->clearLoginAttempts($request);
        $token = (string)$this->guard()->getToken();
        $expiration = $this->guard()->getPayload()->get('exp');
        Redis::set('permissions.' . $this->entity, serialize((object)$this->permissions->computed));

        return [
            'user' => $this->guard()->user()->only([
                'username',
                'first_name',
                'last_name',
                'system_events_subscribed'
            ]),
            'token' => $token,
            'expires_in' => $expiration - time(),
        ];
    }

    /**
     * Validate the user login request.
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
            'remember' => 'boolean'
        ]);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();
        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param $value
     */
    private function setPermissions($value): void
    {
        $this->permissions = $value;
    }

    /**
     * @param mixed $entity
     */
    public function setEntity($entity): void
    {
        $this->entity = $entity;
    }


}