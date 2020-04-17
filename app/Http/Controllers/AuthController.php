<?php

namespace App\Http\Controllers;

use JWTAuth;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Repositories\AuthRepository;
use App\Repositories\UserRepository;
use App\Http\Requests\RegisterRequest;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected $request;
    protected $repo;
    protected $user;

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request, AuthRepository $repo, UserRepository $user)
    {
        $this->request = $request;
        $this->repo = $repo;
        $this->user = $user;
    }

    /**
     * Used to refresh token
     * @get ("/api/token/refresh")
     * @return  token
     */
    public function refreshToken()
    {
        $newToken = JWTAuth::parseToken()->refresh();
        return $newToken;
    }

    /**
     * Used to authenticate user
     * @post ("/api/auth/login")
     * @param ({
     *      @Parameter("email", type="email", required="true", description="Email of User"),
     *      @Parameter("password", type="password", required="true", description="Password of User"),
     * })
     * @return authentication token
     */
    public function authenticate(LoginRequest $request)
    {
        $auth = $this->repo->auth($this->request->all());

        return $this->success([
            'message'         => trans('auth.logged_in'),
            'token'           => $auth['token'],
            'user'            => $auth['user']
        ]);
    }

    /**
     * Used to check user authenticated or not
     * @post ("/api/auth/check")
     * @return Response
     */
    public function check()
    {
        return $this->success($this->repo->check());
    }

    /**
     * Used to logout user
     * @delete ("/api/logout")
     * @return Response
     */
    public function logout()
    {
        auth()->logout();

        return $this->success(['message' => trans('auth.logged_out')]);
    }

    /**
     * Used to create user
     * @post ("/api/auth/register")
     * @param ({
     *      @Parameter("name", type="text", required="true", description="Name of User")
     *      @Parameter("email", type="email", required="true", description="Email of User"),
     *      @Parameter("password", type="password", required="true", description="Password of User"),
     *      @Parameter("password_confirmation", type="password", required="true", description="Confirm Password of User")
     * })
     * @return Response
     */
    public function register(RegisterRequest $request)
    {
        $storeValues = $this->request->all();

        $new_user = $this->user->create($storeValues);

        return $this->success(compact('new_user'));
    }

    /**
     * Used to change user password
     * @param ({
     *      @Parameter("current_password", type="password", required="true", description="Current Password of User"),
     *      @Parameter("new_password", type="password", required="true", description="New Password of User"),
     *      @Parameter("new_password_confirmation", type="password", required="true", description="New Confirm Password of User"),
     * })
     * @return Response
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $this->repo->validateCurrentPassword(request('current_password'));

        $this->repo->resetPassword(request('new_password'));

        return $this->success(['message' => trans('passwords.change')]);
    }
}
