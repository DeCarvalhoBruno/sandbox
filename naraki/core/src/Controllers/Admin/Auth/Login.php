<?php namespace Naraki\Core\Controllers\Admin\Auth;

use Naraki\Core\Controllers\Admin\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Naraki\Permission\Facades\Permission;

class Login extends Controller
{
    use AuthenticatesUsers;

    protected function guard()
    {
        return \Auth::guard('jwt');
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
//        if ($request->get('remember')) {
//            $this->guard()->setTTL(config('jwt.ttl_remember_me'));
//        }
        $token = $this->guard()->attempt($this->credentials($request));

        $permissions = Permission::cacheUserPermissions($this->guard()->user()->getEntityType());
        if (is_null($permissions)) {
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

    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    protected function sendLoginResponse(Request $request)
    {
        $this->clearLoginAttempts($request);
        $token = (string)$this->guard()->getToken();
        $expiration = $this->guard()->getPayload()->get('exp');

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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function logout()
    {
        $this->guard()->logout();
        return response(null, Response::HTTP_NO_CONTENT);
    }

}