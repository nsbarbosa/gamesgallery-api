<?php
namespace App\Repositories;

use App\User;
use Illuminate\Support\Str;
use App\Repositories\UserRepository;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Repositories\LoginThrottleRepository;
use Illuminate\Validation\ValidationException;

class AuthRepository
{
    protected $user;
    protected $throttle;

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(
        UserRepository $user,
        LoginThrottleRepository $throttle
    ) {
        $this->user       = $user;
        $this->throttle   = $throttle;
    }

    /**
     * Authenticate an user.
     *
     * @param array $params
     * @return array
     */
    public function auth($params = array())
    {
        $this->throttle->validate();

        $token = $this->validateLogin($params);

        $auth_user = $this->user->findByEmail($params['email']);

        $this->validateStatus($auth_user);

        return [
            'token'           => $token,
            'user'            => $auth_user
        ];
    }

    /**
     * Validate login credentials.
     *
     * @param array $params
     * @return auth token
     */
    public function validateLogin($params = array())
    {
        $email = isset($params['email']) ? $params['email'] : null;
        $password = isset($params['password']) ? $params['password'] : null;

        try {
            if (! $token = \JWTAuth::attempt(['email' => $email, 'password' => $password])) {
                $this->throttle->update();

                throw ValidationException::withMessages(['email' => trans('auth.failed')]);
            }
        } catch (JWTException $e) {
            throw ValidationException::withMessages(['email' => trans('general.something_wrong')]);
        }

        $this->throttle->clearCache();

        return $token;
    }

    /**
     * Validate authenticated user status.
     *
     * @param authenticated user
     * @return null
     */
    public function validateStatus($auth_user)
    {
        if ($auth_user->status === 'pending_activation') {
            throw ValidationException::withMessages(['status' => trans('auth.pending_activation')]);
        }

        if ($auth_user->status != 'activated') {
            throw ValidationException::withMessages(['status' => trans('auth.not_activated')]);
        }
    }

    /**
     * Validate auth token.
     *
     * @return array
     */
    public function check()
    {
        try {
            \JWTAuth::parseToken()->authenticate();
        } catch (JWTException $e) {
            return ['authenticated' => false];
        }

        $authenticated                  = true;
        $auth_user                      = $this->user->findOrFail(\Auth::user()->id);

        return [
            'authenticated' => $authenticated,
            'user'          => $auth_user,
        ];
    }
}
